<?php
session_start();

require_once "includes/db.php";
if (!isset($_SESSION["user"])) {
    header("Location: index.php?error=unauthenticated");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    extract($_POST);

    try {
        $userId = $_SESSION["user"]["user_id"];
        $currentTimestamp = date('Y-m-d H:i:s');

        global $db;

        $stmt = $db->prepare("INSERT INTO comments (post_id, user_id, content, timestamp) VALUES (?, ?, ?, ?)");
        $stmt->execute([$postId, $userId, $comment_content, $currentTimestamp]);

        // Get the updated number of comments
        $stmt = $db->prepare("SELECT COUNT(*) AS comment_count FROM comments WHERE post_id = ?");
        $stmt->execute([$postId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $commentCount = $result['comment_count'];

        // Return success response with the like count
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode(['success' => true, 'comment_count' => $commentCount], JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        // Return error response
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode(['success' => false], JSON_UNESCAPED_UNICODE);
    }
}
?>