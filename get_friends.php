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
    $stmt = $db->prepare("SELECT u.user_id, u.name, u.surname, u.profile_picture
    FROM Users u
    INNER JOIN Friends f ON ((u.user_id = f.friend_id  OR u.user_id = f.user_id) AND f.status = '1')
    WHERE (f.user_id = ? OR f.friend_id = ?) AND u.user_id <> ?
    ORDER BY u.name ASC");

    $stmt->execute([$_SESSION["user"]["user_id"], $_SESSION["user"]["user_id"], $_SESSION["user"]["user_id"]]);


    header("Content-Type: application/json; charset=utf-8");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE);
}
?>