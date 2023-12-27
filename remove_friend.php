<?php
session_start();

require_once "includes/db.php";
if (!isset($_SESSION["user"])) {
    header("Location: index.php?error=unauthenticated");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    extract($_POST);

    global $db;

    $currentTimestamp = date('Y-m-d H:i:s');

    // Update Notifications table
    $stmt = $db->prepare("INSERT INTO notifications (user_id, sender_id, type, timestamp) SELECT ?, ?, ?, ? FROM dual WHERE NOT EXISTS (SELECT * FROM notifications WHERE (user_id = ? AND sender_id = ?) AND (type = 'friend_removed'))");
    $stmt->execute([$friend_id, $_SESSION["user"]["user_id"], "friend_removed", $currentTimestamp, $friend_id, $_SESSION["user"]["user_id"]]);

    // Update Friends table
    $stmt = $db->prepare("UPDATE friends SET status = '0' WHERE (user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?)");
    $stmt->execute([$friend_id, $_SESSION["user"]["user_id"], $_SESSION["user"]["user_id"], $friend_id]);


    header("Content-Type: application/json; charset=utf-8");
    echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);
}
?>