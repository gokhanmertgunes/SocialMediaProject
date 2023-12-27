<?php

const DSN = "*********************************************";
const USER = "******"; 
const PASSWORD = "**********";

$db = new PDO(DSN, USER, PASSWORD);

function getUser($email)
{
    global $db;
    $stmt = $db->prepare("SELECT * FROM users WHERE email=?");
    # DO 
    $stmt->execute([$email]);
    return $stmt->fetch();
}

function checkUser($email, $rawPass)
{
    global $db;
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount()) {
        // email exists
        $user = $stmt->fetch();
        return password_verify($rawPass, $user["password"]);
    }
    return false;
}

function registerNewUser($name, $surname, $email, $password, $birthDate)
{
    global $db;

    require_once"upload.php";
    $profile =  new CloudinaryUpload("profilePicture", "../uploads/profile-pictures");

    $hashPassw = password_hash($password, PASSWORD_BCRYPT);

    $sql = "INSERT INTO users (name,surname,email,password,birth_date,profile_picture) VALUES (?,?,?,?,?,?)";
    $stmt = $db->prepare($sql);
    $stmt->execute([$name, $surname, $email, $hashPassw, $birthDate, $profile->filename]);
}
