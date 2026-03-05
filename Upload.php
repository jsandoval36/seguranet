<?php
session_start();
if (!isset($_SESSION["user_id"])) {
  header("Location: index.html");
  exit();
}

// Folder where files are stored
$uploadDir = __DIR__ . "/uploads/";
if (!is_dir($uploadDir)) {
  mkdir($uploadDir, 0755, true);
}

// Only allow POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  http_response_code(405);
  exit("Method not allowed.");
}

// Make sure the file input name matches upload.html: name="file"
if (!isset($_FILES["file"])) {
  http_response_code(400);
  exit("No file uploaded.");
}

$file = $_FILES["file"];

// Handle upload errors
if ($file["error"] !== UPLOAD_ERR_OK) {
  http_response_code(400);
  exit("Upload error code: " . $file["error"]);
}

// Limit file size (500MB)
$maxSize = 500 * 1024 * 1024;
if ($file["size"] > $maxSize) {
  http_response_code(413);
  exit("File too large. Max is 500MB.");
}

// Extension + allow list
$originalName = basename($file["name"]);
$ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

$allowedExt = [
  "jpg","jpeg","png","gif","webp",
  "mp4","mov","webm","mkv","avi",
  "pdf"
];

if (!in_array($ext, $allowedExt)) {
  http_response_code(400);
  exit("File type not allowed.");
}

// Safe unique file name
$safeName = "upload_" . bin2hex(random_bytes(8)) . "." . $ext;
$targetPath = $uploadDir . $safeName;

// Save the file
if (!move_uploaded_file($file["tmp_name"], $targetPath)) {
  http_response_code(500);
  exit("Failed to save uploaded file.");
}

// Go back to dashboard after upload
header("Location: dashboard.php");
exit();
