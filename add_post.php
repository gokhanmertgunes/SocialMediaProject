<?php
session_start();

require_once "includes/db.php";
if (!isset($_SESSION["user"])) {
    header("Location: index.php?error=unauthenticated");
    exit;
}


   if($_SERVER["REQUEST_METHOD"] == "POST"){
    extract($_POST);
    
    require_once "includes/upload.php";

    global $db;

    
    $USER = getUser($_SESSION["user"]["email"]);
    $timestamp = date('Y-m-d H:i:s');
    $image = new CloudinaryUpload("postImage", "../uploads/post-pictures");
    
    $sql = "insert into posts (user_id, image, content, timestamp) values (?, ?, ?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->execute([$USER["user_id"], $image->filename, $content, $timestamp]);
    
    header("Location: home.php");

    
}
