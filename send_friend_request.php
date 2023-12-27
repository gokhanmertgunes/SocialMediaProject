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

    // Check if there is an existing friend request or friendship
    $stmt = $db->prepare("SELECT * FROM friends WHERE (user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?)");
    $stmt->execute([$friend_id, $_SESSION["user"]["user_id"], $_SESSION["user"]["user_id"], $friend_id]);
    $existingFriendRequest = $stmt->fetch();

    // Delete existing notifications
    $stmt = $db->prepare("DELETE FROM notifications WHERE (user_id = ? AND sender_id = ?) OR (user_id = ? AND sender_id = ?) AND type = 'friend_request'");
    $stmt->execute([$friend_id, $_SESSION["user"]["user_id"], $_SESSION["user"]["user_id"], $friend_id]);

    if ($existingFriendRequest) {
        // Update the existing friend request status
        $stmt = $db->prepare("UPDATE friends SET status = '2' WHERE (user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?)");
        $stmt->execute([$friend_id, $_SESSION["user"]["user_id"], $_SESSION["user"]["user_id"], $friend_id]);

        // Insert a new notification
        $stmt = $db->prepare("INSERT INTO notifications (user_id, sender_id, type, timestamp) VALUES (?, ?, ?, ?)");
        $stmt->execute([$friend_id, $_SESSION["user"]["user_id"], "friend_request", $currentTimestamp]);
    } else {
        // Insert into Friends table
        $stmt = $db->prepare("INSERT INTO friends (user_id, friend_id, status) VALUES (?, ?, '2')");
        $stmt->execute([$_SESSION["user"]["user_id"], $friend_id]);

        // Insert into Notifications table
        $stmt = $db->prepare("INSERT INTO notifications (user_id, sender_id, type, timestamp) VALUES (?, ?, 'friend_request', ?)");
        $stmt->execute([$friend_id, $_SESSION["user"]["user_id"], $currentTimestamp]);
    }



    header("Content-Type: application/json; charset=utf-8");
    echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);

}
?>