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
            $stmt = $db->prepare("SELECT COUNT(post_id) AS like_count FROM likes WHERE post_id = ?");

            $stmt->execute([$post_id]);


            header("Content-Type: application/json; charset=utf-8");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE);
        }
    } catch (PDOException $e) {
        // Return error response
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode(['success' => false], JSON_UNESCAPED_UNICODE);
    }

}
?>