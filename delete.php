<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit();
}

if (!isset($_GET["file"]) || $_GET["file"] === "") {
    die("File not found.");
}

$userId = $_SESSION["user_id"];
$currentFolder = $_GET["folder"] ?? "";
$currentFolder = trim($currentFolder, "/");
$file = basename($_GET["file"]);

$baseUserDir = __DIR__ . "/uploads/" . $userId;
$userDir = $baseUserDir . ($currentFolder !== "" ? "/" . $currentFolder : "");
$oldPath = $userDir . "/" . $file;

if (!is_file($oldPath)) {
    die("File not found.");
}

if (stripos($file, "deleted_") === 0) {
    $redirect = "dashboard.php?filter=deleted";
    if ($currentFolder !== "") {
        $redirect .= "&folder=" . urlencode($currentFolder);
    }
    header("Location: " . $redirect);
    exit();
}

$newPath = $userDir . "/deleted_" . $file;

if (rename($oldPath, $newPath)) {
    $redirect = "dashboard.php";
    if ($currentFolder !== "") {
        $redirect .= "?folder=" . urlencode($currentFolder);
    }
    header("Location: " . $redirect);
    exit();
} else {
    die("Could not delete file.");
}
?>
