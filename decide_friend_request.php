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

    if ($type == "friend_accepted") {
        $status = '1';

        // Delete friend request notification
        $stmt = $db->prepare("DELETE FROM notifications WHERE (user_id = ? AND sender_id = ?) OR (user_id = ? AND sender_id = ?) AND type = 'friend_request'");
        $stmt->execute([$friend_id, $_SESSION["user"]["user_id"], $_SESSION["user"]["user_id"], $friend_id]);
    } else {
        $status = '0';

        // Update Notifications table
        $stmt = $db->prepare("UPDATE notifications SET type = ?, timestamp = ? WHERE (type = 'friend_request') AND (user_id = ? AND sender_id = ?)");
        $stmt->execute([$type, $currentTimestamp, $_SESSION["user"]["user_id"], $friend_id]);
    }

    // Update Friends table
    $stmt = $db->prepare("UPDATE friends SET status = ? WHERE (user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?)");
    $stmt->execute([$status, $friend_id, $_SESSION["user"]["user_id"], $_SESSION["user"]["user_id"], $friend_id]);


    header("Content-Type: application/json; charset=utf-8");
    echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);
}
?>