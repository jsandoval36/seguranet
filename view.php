<?php
// view.php

$uploadDir = __DIR__ . DIRECTORY_SEPARATOR . "uploads";

if (!isset($_GET["file"])) {
  http_response_code(400);
  exit("Missing file parameter.");
}

$file = basename($_GET["file"]); // prevents path traversal
$path = $uploadDir . DIRECTORY_SEPARATOR . $file;

if (!file_exists($path)) {
  http_response_code(404);
  exit("File not found.");
}

// Detect MIME type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $path);
finfo_close($finfo);

// Send file
header("Content-Type: " . $mime);
header('Content-Disposition: inline; filename="' . $file . '"');
header("Content-Length: " . filesize($path));

readfile($path);
exit;
