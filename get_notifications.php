<?php
session_start();

require_once "includes/db.php";
if (!isset($_SESSION["user"])) {
    header("Location: index.php?error=unauthenticated");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    //extract($_GET);

    global $db;
    $stmt = $db->prepare("SELECT n.notification_id, n.type, n.timestamp, n.sender_id, 
        s.name AS sender_name, s.surname AS sender_surname, s.profile_picture AS sender_profile_picture
        FROM Notifications n
        INNER JOIN Users u ON n.user_id = u.user_id
        INNER JOIN Users s ON n.sender_id = s.user_id
        WHERE (n.user_id = ? AND NOT n.sender_id = ?) AND NOT n.type = 'friend_accepted' 
        ORDER BY n.timestamp DESC;
");

    $stmt->execute([$_SESSION["user"]["user_id"], $_SESSION["user"]["user_id"]]);


    header("Content-Type: application/json; charset=utf-8");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE);
}
?>