<?php
session_start();

require_once "includes/db.php";
if (!isset($_SESSION["user"])) {
    header("Location: index.php?error=unauthenticated");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    extract($_GET);
    if (isset($search)) {
        if (strlen($search) > 1) {

            global $db;
            $stmt = $db->prepare("SELECT u.user_id, u.name, u.surname, u.profile_picture, 
        CASE WHEN f.status IS NULL THEN 0 WHEN f.status = '1' THEN 1 WHEN f.status = '2' THEN 2 ELSE 0
        END AS is_friend
        FROM Users u
        LEFT JOIN Friends f ON (u.user_id = f.friend_id AND f.user_id = ?) OR (u.user_id = f.user_id AND f.friend_id = ?)
        WHERE (u.name LIKE ? OR u.surname LIKE ? OR u.email LIKE ?) AND NOT u.user_id = ?
        ORDER BY u.name ASC");
            $stmt->execute([$_SESSION["user"]["user_id"], $_SESSION["user"]["user_id"], '%' . $search . '%', '%' . $search . '%', '%' . $search . '%', $_SESSION["user"]["user_id"]]);


            header("Content-Type: application/json; charset=utf-8");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE);
        }
    }
}
?>