<?php
session_start();
if (!isset($_SESSION["user_id"])) {
  header("Location: index.html");
  exit();
}

if (!isset($_GET["file"]) || empty($_GET["file"])) {
  header("Location: dashboard.php");
  exit();
}

$file = basename($_GET["file"]);
$uploadDir = __DIR__ . "/uploads/";
$oldPath = $uploadDir . $file;

if (!is_file($oldPath)) {
  header("Location: dashboard.php");
  exit();
}

if (stripos($file, "deleted_") === 0) {
  header("Location: dashboard.php?filter=deleted");
  exit();
}

$newName = "deleted_" . $file;
$newPath = $uploadDir . $newName;

if (file_exists($newPath)) {
  $newName = "deleted_" . time() . "_" . $file;
  $newPath = $uploadDir . $newName;
}

if (rename($oldPath, $newPath)) {
  header("Location: dashboard.php?filter=deleted");
  exit();
} else {
  header("Location: dashboard.php");
  exit();
}
