<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit();
}

if (!isset($_GET["folder"]) || trim($_GET["folder"]) === "") {
    header("Location: dashboard.php");
    exit();
}

$userId = $_SESSION["user_id"];
$folderName = trim($_GET["folder"]);

// remove dangerous characters
$folderName = preg_replace('/[^a-zA-Z0-9_\- ]/', '', $folderName);

if ($folderName === "") {
    header("Location: dashboard.php");
    exit();
}

$userDir = __DIR__ . "/uploads/" . $userId . "/";
$newFolderPath = $userDir . $folderName;

if (!is_dir($userDir)) {
    mkdir($userDir, 0755, true);
}

if (!is_dir($newFolderPath)) {
    mkdir($newFolderPath, 0755, true);
}

header("Location: dashboard.php");
exit();
?>
