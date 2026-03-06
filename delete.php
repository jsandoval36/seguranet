<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: index.html");
    exit();
}

if (!isset($_GET["file"]) || empty($_GET["file"])) {
    header("Location: dashboard.php");
    exit();
}

$file = basename($_GET["file"]);
$path = __DIR__ . "/uploads/" . $file;

if (is_file($path)) {
    unlink($path);
}

header("Location: dashboard.php");
exit();


