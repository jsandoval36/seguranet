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
$path = $baseUserDir . ($currentFolder !== "" ? "/" . $currentFolder : "") . "/" . $file;

if (!is_file($path)) {
    die("File not found.");
}

$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
$mime = mime_content_type($path);

$isImage = in_array($ext, ["jpg", "jpeg", "png", "gif", "webp"]);
$isVideo = in_array($ext, ["mp4", "mov", "avi", "mkv", "webm"]);
$isPdf = ($ext === "pdf");

$fileUrl = "uploads/" . rawurlencode($userId)
         . ($currentFolder !== "" ? "/" . str_replace("%2F", "/", rawurlencode($currentFolder)) : "")
         . "/" . rawurlencode($file);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View File</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 30px;
            background: #f5f7fb;
            text-align: center;
        }
        .topbar { margin-bottom: 20px; }
        .back {
            text-decoration: none;
            color: #1f4ed8;
            font-weight: bold;
        }
        .viewer {
            max-width: 1100px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 14px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }
        img, video, iframe {
            max-width: 100%;
            border-radius: 10px;
        }
        iframe {
            width: 100%;
            height: 80vh;
            border: none;
        }
        .download {
            display: inline-block;
            margin-top: 16px;
            padding: 10px 16px;
            background: #2563eb;
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="topbar">
        <a class="back" href="dashboard.php<?= $currentFolder !== '' ? '?folder=' . urlencode($currentFolder) : '' ?>">⬅ Back to Dashboard</a>
    </div>

    <div class="viewer">
        <h2><?php echo htmlspecialchars($file); ?></h2>

        <?php if ($isImage): ?>
            <img src="<?php echo $fileUrl; ?>" alt="Image">
        <?php elseif ($isVideo): ?>
            <video controls>
                <source src="<?php echo $fileUrl; ?>" type="<?php echo htmlspecialchars($mime); ?>">
                Your browser does not support video playback.
            </video>
        <?php elseif ($isPdf): ?>
            <iframe src="<?php echo $fileUrl; ?>"></iframe>
        <?php else: ?>
            <p>This file type cannot be previewed here.</p>
        <?php endif; ?>

        <br>
        <a class="download" href="download.php?file=<?php echo urlencode($file); ?>&folder=<?php echo urlencode($currentFolder); ?>">⬇ Download File</a>
    </div>
</body>
</html>
