<?php
// Upload.php

// Create uploads folder if it doesn't exist
$uploadDir = __DIR__ . DIRECTORY_SEPARATOR . "uploads";
if (!is_dir($uploadDir)) {
  mkdir($uploadDir, 0777, true);
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  http_response_code(405);
  exit("Method not allowed.");
}

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

// OPTIONAL: limit to 500MB
$maxSize = 500 * 1024 * 1024;
if ($file["size"] > $maxSize) {
  http_response_code(413);
  exit("File too large. Max is 500MB.");
}

// OPTIONAL: allow only certain file types (edit if you want)
// $allowedExt = ["jpg","jpeg","png","pdf","docx","xlsx","txt","zip","mp4"];
// $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
// if (!in_array($ext, $allowedExt)) {
//   http_response_code(400);
//   exit("File type not allowed.");
// }

$originalName = basename($file["name"]);
$ext = pathinfo($originalName, PATHINFO_EXTENSION);

// Safe unique file name
$safeName = uniqid("upload_", true) . ($ext ? "." . $ext : "");
$targetPath = $uploadDir . DIRECTORY_SEPARATOR . $safeName;

if (!move_uploaded_file($file["tmp_name"], $targetPath)) {
  http_response_code(500);
  exit("Failed to save uploaded file.");
}

// Link to view
$viewUrl = "view.php?file=" . urlencode($safeName);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Upload Success</title>
  <style>
    body{font-family:Segoe UI, sans-serif; background:#0b0c0c; color:#fff; display:flex; height:100vh; align-items:center; justify-content:center;}
    .box{background:#1b1c1c; padding:28px 32px; border-radius:14px; width:520px;}
    a{color:#63c6ff; text-decoration:none;}
    a:hover{text-decoration:underline;}
    .btn{display:inline-block; margin-top:14px;}
  </style>
</head>
<body>
  <div class="box">
    <h2>Upload successful!</h2>
    <p><strong>Saved as:</strong> <?php echo htmlspecialchars($safeName); ?></p>
    <p class="btn"><a href="upload.html">Upload another file</a></p>
    <p class="btn"><a href="<?php echo htmlspecialchars($viewUrl); ?>" target="_blank">View uploaded file</a></p>
  </div>
</body>
</html>
