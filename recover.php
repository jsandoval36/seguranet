<?php
session_start();
if (!isset($_SESSION["user_id"])) {
  header("Location: index.html");
  exit();
}

if (!isset($_GET["file"]) || empty($_GET["file"])) {
  header("Location: dashboard.php?filter=deleted");
  exit();
}

$file = basename($_GET["file"]);
$uploadDir = __DIR__ . "/uploads/";
$oldPath = $uploadDir . $file;

if (!is_file($oldPath)) {
  header("Location: dashboard.php?filter=deleted&error=notfound");
  exit();
}

if (stripos($file, "deleted_") !== 0) {
  header("Location: dashboard.php?filter=deleted");
  exit();
}

$restoredName = preg_replace('/^deleted_(\d+_)?/', '', $file);

if (!$restoredName) {
  header("Location: dashboard.php?filter=deleted");
  exit();
}

$newPath = $uploadDir . $restoredName;

if (file_exists($newPath)) {
  $restoredName = time() . "_" . $restoredName;
  $newPath = $uploadDir . $restoredName;
}

if (rename($oldPath, $newPath)) {
  header("Location: dashboard.php");
  exit();
} else {
  header("Location: dashboard.php?filter=deleted&error=recoverfailed");
  exit();
}
