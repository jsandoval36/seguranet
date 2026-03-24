<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: index.html");
    exit();
}

$userId = (int)$_SESSION["user_id"];
$folderName = trim($_GET["foldername"] ?? "");
$parent = trim($_GET["parent"] ?? "");

if ($folderName === "") {
    header("Location: dashboard.php");
    exit();
}

$folderName = preg_replace('/[^A-Za-z0-9_\- ]/', '', $folderName);
$parent = str_replace("\\", "/", $parent);
$parent = trim($parent, "/");

$baseDir = __DIR__ . "/uploads/" . $userId . "/";
if (!is_dir($baseDir)) {
    mkdir($baseDir, 0755, true);
}

$baseReal = realpath($baseDir);
if ($baseReal === false) {
    die("Base upload folder not found.");
}

$targetParent = $baseReal;

if ($parent !== "") {
    $candidateParent = realpath($baseReal . "/" . $parent);
    if ($candidateParent === false || strpos($candidateParent, $baseReal) !== 0 || !is_dir($candidateParent)) {
        die("Invalid parent folder.");
    }
    $targetParent = $candidateParent;
}

$newFolderPath = $targetParent . "/" . $folderName;

if (!file_exists($newFolderPath)) {
    if (!mkdir($newFolderPath, 0755, true)) {
        die("Failed to create folder.");
    }
}

header("Location: dashboard.php" . ($parent !== "" ? "?folder=" . urlencode($parent) : ""));
exit();
?>
