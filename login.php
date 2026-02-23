<?php
session_start();

$email = $_POST["email"] ?? "";
$password = $_POST["password"] ?? "";

// TEMP test login (replace with DB later)
if ($email === "test@test.com" && $password === "password123") {
    $_SESSION["user_id"] = 1;
    $_SESSION["email"] = $email;

    header("Location: dashboard.php");
    exit();
} else {
    echo "Invalid login. <a href='index.html'>Try again</a>";
}
