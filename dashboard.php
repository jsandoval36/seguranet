<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: index.html");
    exit();
}

$userId = (int)$_SESSION["user_id"];
$userEmail = $_SESSION["email"] ?? "User";

$baseDir = __DIR__ . "/uploads/" . $userId . "/";
if (!is_dir($baseDir)) {
    mkdir($baseDir, 0755, true);
}

$currentFolder = trim($_GET["folder"] ?? "");
$currentFolder = str_replace("\\", "/", $currentFolder);
$currentFolder = trim($currentFolder, "/");

$view = $_GET["view"] ?? "all";
$allowedViews = ["all", "photos", "videos", "docs"];
if (!in_array($view, $allowedViews, true)) {
    $view = "all";
}

$baseReal = realpath($baseDir);
$targetDir = $baseReal;

if ($currentFolder !== "") {
    $candidate = realpath($baseReal . "/" . $currentFolder);
    if ($candidate !== false && strpos($candidate, $baseReal) === 0 && is_dir($candidate)) {
        $targetDir = $candidate;
    } else {
        $currentFolder = "";
        $targetDir = $baseReal;
    }
}

function formatSize($bytes) {
    if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . " GB";
    if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . " MB";
    if ($bytes >= 1024) return number_format($bytes / 1024, 2) . " KB";
    return $bytes . " B";
}

function joinFolderPath($folder, $item) {
    return trim($folder . "/" . $item, "/");
}

function isPhotoExt($ext) {
    return in_array(strtolower($ext), ["jpg", "jpeg", "png", "gif", "webp", "bmp"], true);
}

function isVideoExt($ext) {
    return in_array(strtolower($ext), ["mp4", "mov", "avi", "mkv", "webm", "m4v"], true);
}

function isDocExt($ext) {
    return in_array(strtolower($ext), ["pdf", "doc", "docx", "txt", "ppt", "pptx", "xls", "xlsx", "csv"], true);
}

function itemMatchesView($itemPath, $view) {
    if (is_dir($itemPath)) {
        return $view === "all";
    }

    $ext = strtolower(pathinfo($itemPath, PATHINFO_EXTENSION));

    if ($view === "photos") return isPhotoExt($ext);
    if ($view === "videos") return isVideoExt($ext);
    if ($view === "docs") return isDocExt($ext);

    return true;
}

function navClass($name, $view) {
    return $name === $view ? "active" : "";
}

$items = [];
$allItems = array_diff(scandir($targetDir), [".", ".."]);

foreach ($allItems as $item) {
    $itemPath = $targetDir . "/" . $item;
    if (itemMatchesView($itemPath, $view)) {
        $items[] = $item;
    }
}

$breadcrumbs = [];
if ($currentFolder !== "") {
    $parts = explode("/", $currentFolder);
    $build = "";
    foreach ($parts as $part) {
        $build = trim($build . "/" . $part, "/");
        $breadcrumbs[] = [
            "name" => $part,
            "path" => $build
        ];
    }
}

$pageTitle = "All files";
if ($view === "photos") $pageTitle = "Photos";
if ($view === "videos") $pageTitle = "Videos";
if ($view === "docs") $pageTitle = "Docs";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SeguraNet | Dashboard</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, sans-serif;
            background: #050b16;
            color: white;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: #0b1220;
            padding: 20px;
            border-right: 1px solid rgba(255,255,255,0.08);
        }

        .brand {
            background: #111827;
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 20px;
        }

        .brand strong {
            display: block;
            font-size: 22px;
        }

        .brand span {
            color: #cbd5e1;
            font-size: 14px;
        }

        .nav a {
            display: block;
            color: #d1d5db;
            text-decoration: none;
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 8px;
            font-size: 16px;
        }

        .nav a:hover,
        .nav a.active {
            background: #102647;
            color: white;
        }

        .main {
            flex: 1;
            padding: 28px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .search-bar input {
            width: 500px;
            max-width: 100%;
            padding: 14px 18px;
            border-radius: 14px;
            border: 1px solid rgba(255,255,255,0.08);
            background: #08101d;
            color: white;
        }

        .top-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .btn {
            border: none;
            border-radius: 12px;
            padding: 12px 18px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            color: white;
        }

        .btn-upload {
            background: linear-gradient(90deg, #37c8ff, #5cf2c5);
        }

        .btn-folder {
            background: #1f2937;
        }

        .user {
            text-align: right;
            color: #d1d5db;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 52px;
            margin-bottom: 8px;
        }

        .breadcrumbs {
            margin-bottom: 20px;
        }

        .breadcrumbs a {
            color: #60a5fa;
            text-decoration: none;
        }

        .table-card {
            background: #0a101a;
            border-radius: 18px;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 18px;
            text-align: left;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        th {
            background: #151c28;
        }

        .folder-link {
            color: #facc15;
            text-decoration: none;
            font-weight: bold;
        }

        .file-link {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        .download-btn {
            background: #2563eb;
            color: white;
            text-decoration: none;
            padding: 10px 14px;
            border-radius: 10px;
            font-weight: bold;
            display: inline-block;
            margin-right: 8px;
        }

        .delete-btn {
            background: #ff5b5b;
            color: white;
            text-decoration: none;
            padding: 10px 14px;
            border-radius: 10px;
            font-weight: bold;
            display: inline-block;
        }

        .empty {
            padding: 24px;
            color: #9ca3af;
        }

        .upload-form {
            display: none;
        }
    </style>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="brand">
            <strong>SeguraNet</strong>
            <span>Secure files</span>
        </div>

        <div class="nav">
            <a href="dashboard.php?view=all" class="<?= navClass('all', $view) ?>">📁 All files</a>
            <a href="dashboard.php?view=photos" class="<?= navClass('photos', $view) ?>">🖼️ Photos</a>
            <a href="dashboard.php?view=videos" class="<?= navClass('videos', $view) ?>">🎬 Videos</a>
            <a href="dashboard.php?view=docs" class="<?= navClass('docs', $view) ?>">📄 Docs</a>
            <a href="logout.php">🚪 Logout</a>
        </div>
    </aside>

    <main class="main">
        <div class="topbar">
            <div class="search-bar">
                <input type="text" placeholder="Search files...">
            </div>

            <div class="top-actions">
                <form id="uploadForm" class="upload-form" action="upload.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="folder" value="<?= htmlspecialchars($currentFolder) ?>">
                    <input type="file" name="file" id="fileInput" onchange="document.getElementById('uploadForm').submit();">
                </form>

                <button class="btn btn-upload" onclick="document.getElementById('fileInput').click()">Upload</button>
                <button class="btn btn-folder" onclick="createFolder()">New folder</button>
            </div>
        </div>

        <div class="user">Signed in as: <?= htmlspecialchars($userEmail) ?></div>

        <h1><?= htmlspecialchars($pageTitle) ?></h1>

        <div class="breadcrumbs">
            <a href="dashboard.php?view=<?= urlencode($view) ?>">Home</a>
            <?php foreach ($breadcrumbs as $crumb): ?>
                / <a href="dashboard.php?view=<?= urlencode($view) ?>&folder=<?= urlencode($crumb["path"]) ?>"><?= htmlspecialchars($crumb["name"]) ?></a>
            <?php endforeach; ?>
        </div>

        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Last modified</th>
                        <th>Size</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="4" class="empty">No items found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($items as $item): ?>
                        <?php
                        $itemPath = $targetDir . "/" . $item;
                        $relativePath = joinFolderPath($currentFolder, $item);
                        ?>
                        <tr>
                            <td>
                                <?php if (is_dir($itemPath)): ?>
                                    <a class="folder-link" href="dashboard.php?view=<?= urlencode($view) ?>&folder=<?= urlencode($relativePath) ?>">
                                        📁 <?= htmlspecialchars($item) ?>
                                    </a>
                                <?php else: ?>
                                    <a class="file-link" href="view.php?file=<?= urlencode($relativePath) ?>&view=<?= urlencode($view) ?>">
                                        <?= htmlspecialchars($item) ?>
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td><?= date("m/d/Y", filemtime($itemPath)) ?></td>
                            <td><?= is_dir($itemPath) ? "Folder" : formatSize(filesize($itemPath)) ?></td>
                            <td>
                                <?php if (!is_dir($itemPath)): ?>
                                    <a class="download-btn" href="download.php?file=<?= urlencode($relativePath) ?>">Download</a>
                                <?php endif; ?>
                                <a class="delete-btn" href="delete.php?file=<?= urlencode($relativePath) ?>" onclick="return confirm('Are you sure you want to delete this item?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<script>
function createFolder() {
    let folderName = prompt("Enter folder name:");
    if (!folderName || folderName.trim() === "") return;

    let currentFolder = "<?= urlencode($currentFolder) ?>";
    window.location.href = "create_folder.php?parent=" + currentFolder + "&foldername=" + encodeURIComponent(folderName.trim());
}
</script>
</body>
</html>
