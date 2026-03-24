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
$currentFolder = $_GET["folder"] ?? "";
$currentFolder = trim($currentFolder, "/");
$file = basename($_GET["file"]);

$baseUserDir = __DIR__ . "/uploads/" . $userId;
$path = $baseUserDir . ($currentFolder !== "" ? "/" . $currentFolder : "") . "/" . $file;

if (!is_file($path)) {
    die("File not found.");
}

$mime = mime_content_type($path);
if (!$mime) {
    $mime = "application/octet-stream";
}

header("Content-Description: File Transfer");
header("Content-Type: " . $mime);
header("Content-Disposition: attachment; filename=\"" . basename($file) . "\"");
header("Content-Length: " . filesize($path));
header("Cache-Control: must-revalidate");
header("Pragma: public");
header("Expires: 0");

readfile($path);
exit();
?>
