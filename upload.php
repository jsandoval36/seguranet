<?php
session_start();
if (!isset($_SESSION["user_id"])) {
  header("Location: index.html");
  exit();
}

echo "upload_max_filesize: " . ini_get("upload_max_filesize") . "<br>";
echo "post_max_size: " . ini_get("post_max_size") . "<br>";
echo "max_execution_time: " . ini_get("max_execution_time") . "<br>";
echo "max_input_time: " . ini_get("max_input_time") . "<br>";

$uploadDir = __DIR__ . "/uploads/";
if (!is_dir($uploadDir)) {
  mkdir($uploadDir, 0755, true);
}

echo "Upload dir: " . $uploadDir . "<br>";
echo "Writable: " . (is_writable($uploadDir) ? "YES" : "NO") . "<br>";

$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  echo "<pre>";
  print_r($_FILES);
  echo "</pre>";

  if (!isset($_FILES["file"])) {
    $errorMessage = "No file uploaded.";
  } else {
    $file = $_FILES["file"];

    if ($file["error"] !== UPLOAD_ERR_OK) {
      $errorMessage = "Upload error code: " . $file["error"];
    } else {
      $maxSize = 2 * 1024 * 1024 * 1024;

      if ($file["size"] > $maxSize) {
        $errorMessage = "File too large. Max is 2GB.";
      } else {
        $originalName = basename($file["name"]);
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        $allowedExt = [
          "jpg","jpeg","png","gif","webp",
          "mp4","mov","webm","mkv","avi",
          "pdf","doc","docx","txt","ppt","pptx","xls","xlsx","csv"
        ];

        if (!in_array($ext, $allowedExt)) {
          $errorMessage = "File type not allowed.";
        } else {
          $safeName = "upload_" . bin2hex(random_bytes(8)) . "." . $ext;
          $targetPath = $uploadDir . $safeName;

          echo "Temp file: " . $file["tmp_name"] . "<br>";
          echo "Target path: " . $targetPath . "<br>";

          if (move_uploaded_file($file["tmp_name"], $targetPath)) {
            echo "Upload successful!";
            exit();
          } else {
            $errorMessage = "Failed to save uploaded file.";
          }
        }
      }
    }
  }
}
?>
