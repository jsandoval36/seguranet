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

$items = array_diff(scandir($targetDir), array(".", ".."));

function formatSize($bytes) {
    if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . " GB";
    if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . " MB";
    if ($bytes >= 1024) return number_format($bytes / 1024, 2) . " KB";
    return $bytes . " B";
}

function joinFolderPath($folder, $item) {
    return trim($folder . "/" . $item, "/");
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
            color: #fff;
        }
        .layout {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 260px;
            background: #0b1220;
            padding: 20px;
            border-right: 1px solid rgba(255,255,255,0.06);
        }
        .brand {
            background: #111827;
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .brand small {
            display: block;
            font-weight: normal;
            opacity: .8;
            margin-top: 4px;
        }
        .nav a {
            display: block;
            color: #d1d5db;
            text-decoration: none;
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 8px;
        }
        .nav a.active,
        .nav a:hover {
            background: #102647;
            color: #fff;
        }
        .main {
            flex: 1;
            padding: 26px;
        }
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
        }
        .search {
            flex: 1;
        }
        .search input {
            width: 100%;
            max-width: 540px;
            background: #08101d;
            border: 1px solid rgba(255,255,255,0.08);
            color: white;
            padding: 14px 18px;
            border-radius: 14px;
            outline: none;
        }
        .actions {
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
            color: white;
            text-decoration: none;
            display: inline-block;
        }
        .btn-upload {
            background: linear-gradient(90deg, #37c8ff, #5cf2c5);
            color: #fff;
        }
        .btn-folder {
            background: #1f2937;
        }
        .user {
            font-size: 15px;
            color: #d1d5db;
            margin-bottom: 18px;
            text-align: right;
        }
        h1 {
            margin-bottom: 8px;
        }
        .crumbs {
            margin-bottom: 20px;
            color: #93c5fd;
        }
        .crumbs a {
            color: #60a5fa;
            text-decoration: none;
        }
        .card {
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
        tr:hover {
            background: rgba(255,255,255,0.02);
        }
        .file-link {
            color: #fff;
            text-decoration: none;
            font-weight: 600;
        }
        .file-link:hover {
            text-decoration: underline;
        }
        .folder-link {
            color: #facc15;
            text-decoration: none;
            font-weight: 700;
        }
        .folder-link:hover {
            text-decoration: underline;
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
            SeguraNet
            <small>Secure files</small>
        </div>

        <div class="nav">
            <a href="dashboard.php" class="active">📁 All files</a>
            <a href="#">🖼 Photos</a>
            <a href="#">🎬 Videos</a>
            <a href="#">📄 Docs</a>
            <a href="#">🤝 Shared</a>
            <a href="#">🗑 Deleted</a>
            <a href="logout.php">🚪 Logout</a>
        </div>
    </aside>

    <main class="main">
        <div class="topbar">
            <div class="search">
                <input type="text" placeholder="Search files..." />
            </div>

            <div class="actions">
                <form id="uploadForm" class="upload-form" action="upload.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="folder" value="<?= htmlspecialchars($currentFolder) ?>">
                    <input type="file" name="file" id="fileInput" onchange="document.getElementById('uploadForm').submit();">
                </form>

                <button class="btn btn-upload" onclick="document.getElementById('fileInput').click()">Upload</button>
                <button class="btn btn-folder" onclick="createFolder()">New folder</button>
            </div>
        </div>

        <div class="user">Signed in as: <?= htmlspecialchars($userEmail) ?></div>

        <h1>All files</h1>

        <div class="crumbs">
            <a href="dashboard.php">Home</a>
            <?php foreach ($breadcrumbs as $crumb): ?>
                / <a href="dashboard.php?folder=<?= urlencode($crumb["path"]) ?>"><?= htmlspecialchars($crumb["name"]) ?></a>
            <?php endforeach; ?>
        </div>

        <div class="card">
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
                        <td colspan="4" class="empty">This folder is empty.</td>
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
                                    <a class="folder-link" href="dashboard.php?folder=<?= urlencode($relativePath) ?>">
                                        📁 <?= htmlspecialchars($item) ?>
                                    </a>
                                <?php else: ?>
                                    <a class="file-link" href="view.php?file=<?= urlencode($relativePath) ?>">
                                        <?= htmlspecialchars($item) ?>
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td><?= date("m/d/Y", filemtime($itemPath)) ?></td>
                            <td>
                                <?= is_dir($itemPath) ? "Folder" : formatSize(filesize($itemPath)) ?>
                            </td>
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
