<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: index.html");
    exit();
}

$userId = (int)$_SESSION["user_id"];
$file = trim($_GET["file"] ?? "");
$file = str_replace("\\", "/", $file);
$file = trim($file, "/");

$baseDir = __DIR__ . "/uploads/" . $userId . "/";
$baseReal = realpath($baseDir);

if ($baseReal === false || $file === "") {
    header("Location: dashboard.php");
    exit();
}

$targetPath = realpath($baseReal . "/" . $file);

if ($targetPath === false || strpos($targetPath, $baseReal) !== 0) {
    die("File not found.");
}

function deleteRecursively($path) {
    if (is_dir($path)) {
        $items = array_diff(scandir($path), array(".", ".."));
        foreach ($items as $item) {
            deleteRecursively($path . "/" . $item);
        }
        return rmdir($path);
    }
    return unlink($path);
}

deleteRecursively($targetPath);

$parentFolder = dirname($file);
if ($parentFolder === "." || $parentFolder === "/") {
    $parentFolder = "";
}

header("Location: dashboard.php" . ($parentFolder !== "" ? "?folder=" . urlencode($parentFolder) : ""));
exit();
?>
