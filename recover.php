<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit();
}

if (!isset($_GET["file"]) || $_GET["file"] === "") {
    die("File not found.");
}

$userId = $_SESSION["user_id"];
$file = basename($_GET["file"]);

$userDir = __DIR__ . "/uploads/" . $userId . "/";
$oldPath = $userDir . $file;

if (!is_file($oldPath)) {
    die("File not found.");
}

if (stripos($file, "deleted_") !== 0) {
    header("Location: dashboard.php");
    exit();
}

$restoredName = substr($file, 8);
$newPath = $userDir . $restoredName;

if (rename($oldPath, $newPath)) {
    header("Location: dashboard.php?filter=deleted");
    exit();
} else {
    die("Could not recover file.");
}
?>
