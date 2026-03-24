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
$path = __DIR__ . "/uploads/" . $userId . "/" . $file;

if (!is_file($path)) {
    die("File not found.");
}

header("Content-Description: File Transfer");
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"" . basename($file) . "\"");
header("Content-Length: " . filesize($path));
readfile($path);
exit();
?>
