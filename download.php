<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: index.html");
    exit();
}

$userId = (int)$_SESSION["user_id"];
$file = trim($_GET["file"] ?? "");
$file = str_replace("\\", "/", $file);
$file = trim($file, "/");

$baseDir = __DIR__ . "/uploads/" . $userId . "/";
$baseReal = realpath($baseDir);

if ($baseReal === false || $file === "") {
    die("Invalid request.");
}

$targetPath = realpath($baseReal . "/" . $file);

if ($targetPath === false || strpos($targetPath, $baseReal) !== 0 || is_dir($targetPath)) {
    die("File not found.");
}

header("Content-Description: File Transfer");
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"" . basename($targetPath) . "\"");
header("Content-Length: " . filesize($targetPath));
readfile($targetPath);
exit();
?>
