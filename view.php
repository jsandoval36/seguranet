<?php
session_start();

if (!isset($_SESSION["user_id"])) {
  header("Location: index.html");
  exit();
}

$uploadDir = __DIR__ . "/uploads/";

if (!isset($_GET["file"]) || $_GET["file"] === "") {
  exit("No file specified.");
}

$file = basename($_GET["file"]);
$path = $uploadDir . $file;

if (!file_exists($path)) {
  exit("File not found.");
}

$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
$fileUrl = "uploads/" . rawurlencode($file);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View File</title>

<style>

body{
  margin:0;
  background:#0b0d10;
  color:white;
  font-family:Segoe UI, sans-serif;
  text-align:center;
}

.topbar{
  background:#111;
  padding:12px;
}

.topbar a{
  color:white;
  text-decoration:none;
  margin:0 10px;
  font-weight:600;
}

.viewer{
  padding:20px;
  display:flex;
  justify-content:center;
  align-items:center;
  min-height: calc(100vh - 60px);
}

img, video{
  width:auto;
  height:auto;
  max-width:92vw;
  max-height:82vh;
  border-radius:10px;
  display:block;
  margin:auto;
}

iframe{
  width:92vw;
  height:82vh;
  border:none;
  border-radius:10px;
}

</style>
</head>

<body>

<div class="topbar">
  <a href="dashboard.php">⬅ Back to Dashboard</a>
  |
  <a href="<?php echo $fileUrl; ?>" download>⬇ Download</a>
</div>

<div class="viewer">

<?php

if (in_array($ext, ["jpg","jpeg","png","gif","webp"])) {

  echo "<img src='$fileUrl'>";

}

elseif (in_array($ext, ["mp4","mov","webm","mkv","avi"])) {

  echo "<video controls src='$fileUrl'></video>";

}

elseif ($ext === "pdf") {

  echo "<iframe src='$fileUrl'></iframe>";

}

else {

  echo "<p>Preview not available for this file type.</p>";

}

?>

</div>

</body>
</html>
