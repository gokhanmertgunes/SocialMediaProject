<?php
session_start();

require_once"includes/db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    extract($_POST);

    if (checkUser($email, $password)) {
        $_SESSION["user"] = getUser($email);

        // redirect to the home page
        header("Location: home.php");
        exit;
    } else {
        // redirect to the login page
        header("Location: index.php?error=login");
        exit;
    }
}