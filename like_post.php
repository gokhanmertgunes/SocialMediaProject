<?php
session_start();

require_once "includes/db.php";
if (!isset($_SESSION["user"])) {
    header("Location: index.php?error=unauthenticated");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $postId = $_POST["postId"];
    $type = $_POST["type"];

    try {
        $userId = $_SESSION["user"]["user_id"];
        $currentTimestamp = date('Y-m-d H:i:s');

        global $db;

        if ($type === "like") {
            // Insert a new like
            $stmt = $db->prepare("INSERT INTO likes (post_id, user_id, timestamp) VALUES (?, ?, ?)");
            $stmt->execute([$postId, $userId, $currentTimestamp]);
        } elseif ($type === "unlike") {
            // Remove the like
            $stmt = $db->prepare("DELETE FROM likes WHERE post_id = ? AND user_id = ?");
            $stmt->execute([$postId, $userId]);
        }

        // Get the updated number of likes
        $stmt = $db->prepare("SELECT COUNT(*) AS like_count FROM likes WHERE post_id = ?");
        $stmt->execute([$postId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $likeCount = $result['like_count'];

        // Return success response with the like count
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode(['success' => true, 'like_count' => $likeCount], JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        // Return error response
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode(['success' => false], JSON_UNESCAPED_UNICODE);
    }
}
?>
