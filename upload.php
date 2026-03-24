<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: index.html");
    exit();
}

$userId = (int)$_SESSION["user_id"];
$folder = trim($_POST["folder"] ?? "");
$folder = str_replace("\\", "/", $folder);
$folder = trim($folder, "/");

$baseDir = __DIR__ . "/uploads/" . $userId . "/";
if (!is_dir($baseDir)) {
    mkdir($baseDir, 0755, true);
}

$baseReal = realpath($baseDir);
if ($baseReal === false) {
    die("Upload base folder not found.");
}

$targetDir = $baseReal;

if ($folder !== "") {
    $candidate = realpath($baseReal . "/" . $folder);
    if ($candidate === false || strpos($candidate, $baseReal) !== 0 || !is_dir($candidate)) {
        die("Invalid upload folder.");
    }
    $targetDir = $candidate;
}

if (!isset($_FILES["file"]) || $_FILES["file"]["error"] !== UPLOAD_ERR_OK) {
    die("No file uploaded or upload failed.");
}

$originalName = basename($_FILES["file"]["name"]);
$originalName = preg_replace('/[^A-Za-z0-9_\.\- ]/', '', $originalName);

if ($originalName === "") {
    die("Invalid file name.");
}

$ext = pathinfo($originalName, PATHINFO_EXTENSION);
$nameOnly = pathinfo($originalName, PATHINFO_FILENAME);

$finalName = $originalName;
$counter = 1;

while (file_exists($targetDir . "/" . $finalName)) {
    $finalName = $nameOnly . "_" . $counter;
    if ($ext !== "") {
        $finalName .= "." . $ext;
    }
    $counter++;
}

$destination = $targetDir . "/" . $finalName;

if (!move_uploaded_file($_FILES["file"]["tmp_name"], $destination)) {
    die("Failed to save uploaded file.");
}

header("Location: dashboard.php" . ($folder !== "" ? "?folder=" . urlencode($folder) : ""));
exit();
?>
