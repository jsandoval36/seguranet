<?php
session_start();

if (!isset($_SESSION["user_id"])) {
  header("Location: index.html");
  exit();
}

if (isset($_GET["file"])) {

  $file = basename($_GET["file"]);
  $path = __DIR__ . "/uploads/" . $file;

  if (file_exists($path)) {
      unlink($path); // deletes only this file
  }

}

header("Location: dashboard.php");
exit();
?>


