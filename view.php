<?php
$uploadDir = __DIR__ . "/uploads";

if (!isset($_GET["file"])) {
  exit("No file specified.");
}

$file = basename($_GET["file"]);
$path = $uploadDir . "/" . $file;

if (!file_exists($path)) {
  exit("File not found.");
}

$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

// file URL
$fileUrl = "uploads/" . urlencode($file);
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
}

img, video{
  max-width:90%;
  max-height:80vh;
  border-radius:10px;
}

iframe{
  width:90%;
  height:80vh;
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
// show preview based on file type

if (in_array($ext, ["jpg","jpeg","png","gif","webp"])) {
  echo "<img src='$fileUrl'>";
}
elseif (in_array($ext, ["mp4","mov","webm"])) {
  echo "<video controls src='$fileUrl'></video>";
}
elseif ($ext === "pdf") {
  echo "<iframe src='$fileUrl'></iframe>";
}
else {
  echo "<p>Preview not available.</p>";
  echo "<p><a href='$fileUrl' download>Download File</a></p>";
}
?>

</div>

</body>
</html>
