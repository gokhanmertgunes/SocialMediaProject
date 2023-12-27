<?php
session_start();

require_once "includes/db.php";
if (!isset($_SESSION["user"])) {
    header("Location: index.php?error=unauthenticated");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    extract($_GET);

    try {
        if (isset($post_id)) {
            global $db;
            $stmt = $db->prepare("SELECT u.user_id, u.name, u.surname, u.profile_picture, c.content 
            FROM users u
            JOIN comments c ON c.user_id = u.user_id
            WHERE c.post_id = ?
            ORDER BY c.timestamp ASC");

            $stmt->execute([$post_id]);

            $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Return the comments with user information
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode($comments, JSON_UNESCAPED_UNICODE);
        }
    } catch (PDOException $e) {
        // Return error response
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode(['success' => false], JSON_UNESCAPED_UNICODE);
    }
}
?>
