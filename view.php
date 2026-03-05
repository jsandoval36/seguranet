```php
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

/* Top bar */
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

/* Viewer container */
.viewer{
  padding:20px;
  display:flex;
  justify-content:center;
  align-items:center;
  min-height: calc(100vh - 60px);
}

/* Images & videos */
img, video{
  width:auto;
  height:auto;
  max-width:92vw;
  max-height:82vh;
  border-radius:10px;
  display:block;
  margin:auto;
}

/* PDFs */
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

elseif (in_array($ext, ["mp4","mov","webm"])) {

  echo "<video controls src='$fileUrl'></video>";

}

elseif ($ext === "pdf") {

  e
```
