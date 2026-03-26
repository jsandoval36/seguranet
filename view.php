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

$view = $_GET["view"] ?? "all";
$allowedViews = ["all", "photos", "videos", "docs"];
if (!in_array($view, $allowedViews, true)) {
    $view = "all";
}

$baseDir = __DIR__ . "/uploads/" . $userId . "/";
$baseReal = realpath($baseDir);

if ($baseReal === false || $file === "") {
    die("Invalid request.");
}

$targetPath = realpath($baseReal . "/" . $file);

if ($targetPath === false || strpos($targetPath, $baseReal) !== 0 || is_dir($targetPath)) {
    die("File not found.");
}

$mime = mime_content_type($targetPath);
$relativeUrl = "uploads/" . $userId . "/" . str_replace("%2F", "/", rawurlencode($file));

$parentFolder = dirname($file);
if ($parentFolder === "." || $parentFolder === "/") {
    $parentFolder = "";
}

$backUrl = "dashboard.php?view=" . urlencode($view);
if ($parentFolder !== "") {
    $backUrl .= "&folder=" . urlencode($parentFolder);
}

$downloadUrl = "download.php?file=" . urlencode($file);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View File</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #050b16;
            color: white;
        }

        .topbar {
            background: #0b1220;
            padding: 18px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        .topbar-right {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .btn {
            color: white;
            text-decoration: none;
            padding: 10px 16px;
            border-radius: 10px;
            font-weight: bold;
            display: inline-block;
        }

        .btn-download {
            background: #2563eb;
        }

        .btn-back {
            background: #2563eb;
        }

        .container {
            padding: 30px;
            text-align: center;
        }

        .preview-actions {
            margin-bottom: 20px;
        }

        img, video, iframe {
            max-width: 90%;
            max-height: 80vh;
            border-radius: 12px;
            background: white;
        }

        .text-box {
            max-width: 900px;
            margin: 0 auto;
            text-align: left;
            background: #0f172a;
            padding: 20px;
            border-radius: 14px;
            white-space: pre-wrap;
            word-break: break-word;
        }
    </style>
</head>
<body>
    <div class="topbar">
        <div><?= htmlspecialchars(basename($file)) ?></div>

        <div class="topbar-right">
            <a class="btn btn-download" href="<?= htmlspecialchars($downloadUrl) ?>">Download</a>
            <a class="btn btn-back" href="<?= htmlspecialchars($backUrl) ?>">Back to Dashboard</a>
        </div>
    </div>

    <div class="container">
        <div class="preview-actions">
            <a class="btn btn-download" href="<?= htmlspecialchars($downloadUrl) ?>">Download</a>
        </div>

        <?php if (strpos($mime, "image/") === 0): ?>
            <img src="<?= htmlspecialchars($relativeUrl) ?>" alt="Image Preview">
        <?php elseif (strpos($mime, "video/") === 0): ?>
            <video controls>
                <source src="<?= htmlspecialchars($relativeUrl) ?>" type="<?= htmlspecialchars($mime) ?>">
                Your browser does not support video playback.
            </video>
        <?php elseif ($mime === "application/pdf"): ?>
            <iframe src="<?= htmlspecialchars($relativeUrl) ?>" width="100%" height="800"></iframe>
        <?php elseif (strpos($mime, "text/") === 0): ?>
            <div class="text-box"><?= htmlspecialchars(file_get_contents($targetPath)) ?></div>
        <?php else: ?>
            <p>Preview not available for this file type.</p>
            <p><a class="btn btn-download" href="<?= htmlspecialchars($downloadUrl) ?>">Download File</a></p>
        <?php endif; ?>
    </div>
</body>
</html>
