<?php
session_start();
if (!isset($_SESSION["user_id"])) {
  header("Location: index.html");
  exit();
}

$allowed = [
  "Resume.pdf" => "application/pdf",
  "Photo.jpg"  => "image/jpeg",
  "Video.mov"  => "video/quicktime"
];

$file = $_GET["file"] ?? "";
$file = basename($file);

$path = __DIR__ . "/private_uploads/" . $file;

if (!isset($allowed[$file]) || !file_exists($path)) {
  exit("File not found");
}

header("Content-Type: " . $allowed[$file]);
header('Content-Disposition: inline; filename="' . $file . '"');
readfile($path);
exit();
