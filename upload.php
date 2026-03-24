<?php
session_start();
if (!isset($_SESSION["user_id"])) {
  header("Location: index.html");
  exit();
}

$userId = $_SESSION["user_id"];
$uploadDir = __DIR__ . "/uploads/" . $userId . "/";

if (!is_dir($uploadDir)) {
  mkdir($uploadDir, 0755, true);
}

$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

  if (!isset($_FILES["file"])) {
    $errorMessage = "No file uploaded.";
  } else {
    $file = $_FILES["file"];

    if ($file["error"] !== UPLOAD_ERR_OK) {
      $errorMessage = "Upload error code: " . $file["error"];
    } else {
      $maxSize = 600 * 1024 * 1024;

      if ($file["size"] > $maxSize) {
        $errorMessage = "File too large. Max is 600MB.";
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

          if (move_uploaded_file($file["tmp_name"], $targetPath)) {
            header("Location: dashboard.php");
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
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link href="seguraNet-icon.png" rel="icon" type="image/x-icon">
<title>SeguraNet | File Upload</title>

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI', sans-serif;}
body{
  height:100vh;
  background:
    radial-gradient(900px 500px at 20% 10%, rgba(47,128,237,.15), transparent 60%),
    radial-gradient(900px 500px at 80% 90%, rgba(46,204,113,.12), transparent 60%),
    linear-gradient(180deg, #f8fafc, #eef2f7);
  display:flex;
  justify-content:center;
  align-items:center;
}
.card{
  width:520px;
  background:#f4f6fb;
  border-radius:28px;
  padding:50px 60px;
  text-align:center;
  box-shadow:0 20px 50px rgba(0,0,0,0.08);
}
.card h1{
  font-size:34px;
  color:#2f346f;
  margin-bottom:8px;
}
.subtitle{
  color:#8e97c6;
  margin-bottom:40px;
}
.drop-area{
  width:240px;
  height:150px;
  margin:0 auto 20px auto;
  border:2px dashed #63c6ff;
  border-radius:18px;
  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:center;
  cursor:pointer;
  transition:0.3s ease;
  background:#eef1ff;
}
.drop-area:hover{ background:#e3e7ff; }
.drop-area.dragover{
  background:#dbe2ff;
  transform:scale(1.02);
}
.drop-area img{
  width:65px;
  margin-bottom:15px;
}
.drop-area span{
  color:#79c5f4;
  font-size:15px;
}
input[type="file"]{ display:none; }
.file-name{
  margin: 0 0 25px 0;
  color:#2f346f;
  font-size:14px;
}
button{
  width:320px;
  padding:14px;
  border:none;
  border-radius:12px;
  background:linear-gradient(135deg, #2f80ed, #2ecc71);
  color:white;
  font-size:18px;
  cursor:pointer;
  transition:0.3s;
}
button:hover{ transform:scale(1.02); }
.back{
  display:inline-block;
  margin-top:16px;
  color:#2f346f;
  text-decoration:none;
  font-weight:600;
}
.back:hover{ text-decoration:underline; }
.error{
  background:#ffe3e3;
  color:#b00020;
  padding:12px;
  border-radius:10px;
  margin-bottom:18px;
  font-size:14px;
}
</style>
</head>

<body>
  <div class="card">
    <h1>Upload your files</h1>
    <div class="subtitle">fast and easy way</div>

    <?php if (!empty($errorMessage)): ?>
      <div class="error"><?= htmlspecialchars($errorMessage) ?></div>
    <?php endif; ?>

    <form action="upload.php" method="POST" enctype="multipart/form-data">
      <label class="drop-area" id="dropArea">
        <img src="https://cdn-icons-png.flaticon.com/512/716/716784.png" alt="Upload Icon">
        <span>Drag and drop files here (or click)</span>
        <input type="file" name="file" id="fileInput" accept="image/*,video/*,.pdf,.doc,.docx,.txt,.ppt,.pptx,.xls,.xlsx,.csv" required>
      </label>

      <div class="file-name" id="fileName">No file selected</div>

      <button type="submit">Submit</button>
    </form>

    <a class="back" href="dashboard.php">⬅ Back to Dashboard</a>
  </div>

<script>
const dropArea = document.getElementById("dropArea");
const fileInput = document.getElementById("fileInput");
const fileName = document.getElementById("fileName");

function showFileName() {
  fileName.textContent =
    fileInput.files && fileInput.files.length > 0
      ? fileInput.files[0].name
      : "No file selected";
}

fileInput.addEventListener("change", showFileName);

dropArea.addEventListener("dragover", (e) => {
  e.preventDefault();
  dropArea.classList.add("dragover");
});

dropArea.addEventListener("dragleave", () => {
  dropArea.classList.remove("dragover");
});

dropArea.addEventListener("drop", (e) => {
  e.preventDefault();
  dropArea.classList.remove("dragover");
  if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
    fileInput.files = e.dataTransfer.files;
    showFileName();
  }
});
</script>
</body>
</html>
