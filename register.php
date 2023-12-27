<?php
session_start();

require_once"includes/db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    extract($_POST);

    if (!getUser($email)) {
        registerNewUser($firstName, $lastName, $email, $password, $birthday);
        $_SESSION["user"] = getUser($email);

        // redirect to the login page
        header("Location: home.php");
        exit;
    } else {
        header("Location: index.php?error=email");
    }
}