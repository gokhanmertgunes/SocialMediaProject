<?php
session_start();

require_once "includes/db.php";
if (!isset($_SESSION["user"])) {
    header("Location: index.php?error=unauthenticated");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    extract($_GET);

    global $db;

    if (!isset($offset)) {
        $offset = 0;
    }
    $stmt = $db->prepare("SELECT p.post_id, p.content, p.image, p.timestamp, u.user_id, u.name, u.surname, u.profile_picture,
    (SELECT EXISTS(SELECT l.like_id FROM likes l WHERE l.post_id = p.post_id AND l.user_id = ?)) AS i_liked,
    (SELECT COUNT(c.comment_id) FROM comments c WHERE c.post_id = p.post_id) AS comment_count,
    (SELECT COUNT(l.like_id) FROM likes l WHERE l.post_id = p.post_id) AS like_count
    FROM posts p
    INNER JOIN users u ON p.user_id = u.user_id
    LEFT JOIN friends f1 ON f1.friend_id = p.user_id AND f1.user_id = ?
    LEFT JOIN friends f2 ON f2.user_id = p.user_id AND f2.friend_id = ?
    WHERE (f1.status = '1' OR f2.status = '1' OR p.user_id = ?)
    ORDER BY p.timestamp DESC
    LIMIT 10 OFFSET " . (int) $offset);

    $stmt->execute([$_SESSION["user"]["user_id"], $_SESSION["user"]["user_id"], $_SESSION["user"]["user_id"], $_SESSION["user"]["user_id"]]);



    header("Content-Type: application/json; charset=utf-8");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE);
}
?>