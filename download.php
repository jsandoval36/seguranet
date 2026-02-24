<?php
$file = $_GET["file"];
$path = "uploads/" . basename($file);

if (file_exists($path)) {
    header("Content-Disposition: attachment; filename=" . basename($path));
    readfile($path);
}
?>
