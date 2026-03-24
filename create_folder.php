<?php
session_start();
if (!isset($_SESSION["user_id"])) {
  header("Location: login.html");
  exit();
}

$userId = $_SESSION["user_id"];
$filter = $_GET["filter"] ?? "all";
$currentFolder = $_GET["folder"] ?? "";
$currentFolder = trim($currentFolder, "/");

function active($name, $filter) {
  return ($name === $filter) ? "active" : "";
}

function pageTitle($filter) {
  if ($filter === "photos") return "Photos";
  if ($filter === "videos") return "Videos";
  if ($filter === "docs") return "Documents";
  if ($filter === "shared") return "Shared";
  if ($filter === "deleted") return "Deleted";
  return "All files";
}

function isPhotoExt($ext) {
  return in_array($ext, ["jpg","jpeg","png","gif","webp"]);
}

function isVideoExt($ext) {
  return in_array($ext, ["mp4","mov","avi","mkv","webm"]);
}

function isDocExt($ext) {
  return in_array($ext, ["pdf","doc","docx","txt","ppt","pptx","xls","xlsx","csv"]);
}

$baseUserDir = __DIR__ . "/uploads/" . $userId;
$targetDir = $baseUserDir . ($currentFolder !== "" ? "/" . $currentFolder : "");
$targetDirReal = realpath($targetDir);
$baseUserDirReal = realpath($baseUserDir);

if (!is_dir($baseUserDir)) {
  mkdir($baseUserDir, 0755, true);
  $baseUserDirReal = realpath($baseUserDir);
}

if ($targetDirReal === false || strpos($targetDirReal, $baseUserDirReal) !== 0) {
  $currentFolder = "";
  $targetDir = $baseUserDir;
}

function buildFolderLink($folder, $filter) {
  $url = "dashboard.php?filter=" . urlencode($filter);
  if ($folder !== "") {
    $url .= "&folder=" . urlencode($folder);
  }
  return $url;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SeguraNet | Dashboard</title>
<link href="seguraNet-icon.png" rel="icon">
<link rel="stylesheet" href="dashboard.css">
<style>
.modal-overlay{
  display:none;
  position:fixed;
  inset:0;
  background:rgba(0,0,0,0.45);
  z-index:999;
  align-items:center;
  justify-content:center;
}
.modal{
  background:#111827;
  color:white;
  width:400px;
  max-width:90%;
  border-radius:16px;
  padding:24px;
  box-shadow:0 10px 30px rgba(0,0,0,0.35);
}
.modal h3{
  margin:0 0 14px 0;
}
.modal input{
  width:100%;
  padding:12px;
  border-radius:10px;
  border:1px solid #374151;
  background:#0b1220;
  color:white;
  margin-bottom:16px;
}
.modal-actions{
  display:flex;
  justify-content:flex-end;
  gap:10px;
}
.modal-actions button{
  border:none;
  border-radius:10px;
  padding:10px 16px;
  cursor:pointer;
}
.modal-actions .cancel-btn{
  background:#374151;
  color:white;
}
.modal-actions .create-btn{
  background:#2563eb;
  color:white;
}
.breadcrumb{
  margin:10px 0 18px 0;
  color:#cbd5e1;
  font-size:14px;
}
.breadcrumb a{
  color:#60a5fa;
  text-decoration:none;
}
.breadcrumb a:hover{
  text-decoration:underline;
}
.namecell a.folder-link{
  color:inherit;
  text-decoration:none;
}
.namecell a.folder-link:hover{
  text-decoration:underline;
}
</style>
</head>
<body>

<div class="app">

  <aside class="sidebar">
    <div class="brand">
      <div class="mark">
        <img src="seguranet-icon-small.png" alt="SeguraNet" style="width:24px;height:24px;object-fit:contain;display:block;">
      </div>
      <div>
        <div class="name">SeguraNet</div>
        <div class="sub">Secure files</div>
      </div>
    </div>

    <nav class="nav">
      <a class="<?= active('all',$filter) ?>" href="<?= buildFolderLink($currentFolder, 'all') ?>">📁 All files</a>
      <a class="<?= active('photos',$filter) ?>" href="<?= buildFolderLink($currentFolder, 'photos') ?>">🖼 Photos</a>
      <a class="<?= active('videos',$filter) ?>" href="<?= buildFolderLink($currentFolder, 'videos') ?>">🎞 Videos</a>
      <a class="<?= active('docs',$filter) ?>" href="<?= buildFolderLink($currentFolder, 'docs') ?>">📄 Docs</a>
      <a class="<?= active('shared',$filter) ?>" href="<?= buildFolderLink($currentFolder, 'shared') ?>">🤝 Shared</a>
      <a class="<?= active('deleted',$filter) ?>" href="<?= buildFolderLink($currentFolder, 'deleted') ?>">🗑 Deleted</a>
    </nav>

    <div class="sidebar-footer">
      <a class="logout" href="logout.php">🚪 Log out</a>
    </div>
  </aside>

  <main class="main">
    <header class="topbar">
      <div class="search">
        <input id="searchBox" type="text" placeholder="Search files...">
      </div>

      <div class="top-actions">
        <button class="btn" onclick="window.location.href='upload.php?folder=<?= urlencode($currentFolder) ?>'">Upload</button>
        <button class="btn ghost" onclick="openFolderModal()">New folder</button>
      </div>
    </header>

    <div class="title-row">
      <h1><?= htmlspecialchars(pageTitle($filter)) ?></h1>
      <div class="meta">
        Signed in as: <?= htmlspecialchars($_SESSION["email"] ?? "user"); ?>
      </div>
    </div>

    <div class="breadcrumb">
      <?php
      echo '<a href="dashboard.php?filter=' . urlencode($filter) . '">Home</a>';

      if ($currentFolder !== "") {
        $parts = explode("/", $currentFolder);
        $pathSoFar = "";
        foreach ($parts as $part) {
          $pathSoFar .= ($pathSoFar === "" ? "" : "/") . $part;
          echo ' / <a href="' . htmlspecialchars(buildFolderLink($pathSoFar, $filter)) . '">' . htmlspecialchars($part) . '</a>';
        }
      }
      ?>
    </div>

    <section class="table">
      <div class="row head">
        <div>Name</div>
        <div>Last modified</div>
        <div class="right">Size</div>
        <div class="right">Action</div>
      </div>

<?php
if (!is_dir($targetDir)) {
  echo '<div class="row">
    <div class="namecell">Folder not found</div>
    <div>—</div>
    <div class="right">—</div>
    <div class="right">—</div>
  </div>';
} else {
  $items = array_diff(scandir($targetDir), ['.','..','.gitkeep']);
  rsort($items);

  $filteredItems = [];

  foreach ($items as $item) {
    $path = $targetDir . "/" . $item;

    if (is_dir($path)) {
      if ($filter === "all") {
        $filteredItems[] = $item;
      }
      continue;
    }

    if (!is_file($path)) continue;

    $ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));

    if ($filter === "photos") {
      if (!isPhotoExt($ext)) continue;
    } elseif ($filter === "videos") {
      if (!isVideoExt($ext)) continue;
    } elseif ($filter === "docs") {
      if (!isDocExt($ext)) continue;
    } elseif ($filter === "shared") {
      if (stripos($item, "shared_") !== 0) continue;
    } elseif ($filter === "deleted") {
      if (stripos($item, "deleted_") !== 0) continue;
    } else {
      if (stripos($item, "deleted_") === 0) continue;
    }

    $filteredItems[] = $item;
  }

  if (count($filteredItems) === 0) {
    echo '<div class="row">
      <div class="namecell">No files or folders found</div>
      <div>—</div>
      <div class="right">—</div>
      <div class="right">—</div>
    </div>';
  } else {
    foreach ($filteredItems as $item) {
      $path = $targetDir . "/" . $item;
      $date = date("m/d/Y", filemtime($path));

      if (is_dir($path)) {
        $childFolder = ($currentFolder === "") ? $item : $currentFolder . "/" . $item;
        $folderLink = buildFolderLink($childFolder, "all");

        echo '
        <div class="row fileRow" data-name="'.htmlspecialchars(strtolower($item)).'">
          <div class="namecell">
            <a class="folder-link" href="'.htmlspecialchars($folderLink).'">📁 '.htmlspecialchars($item).'</a>
          </div>
          <div>'.$date.'</div>
          <div class="right">Folder</div>
          <div class="right">—</div>
        </div>';
        continue;
      }

      $bytes = filesize($path);

      if ($bytes >= 1024 * 1024) {
        $sizeText = round($bytes / (1024 * 1024), 2) . " MB";
      } else {
        $sizeText = round($bytes / 1024, 2) . " KB";
      }

      $ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));

      $icon = "📄";
      if (isPhotoExt($ext)) $icon = "🖼️";
      if (isVideoExt($ext)) $icon = "🎞️";
      if ($ext === "pdf") $icon = "📕";
      if (in_array($ext, ["zip","rar","7z"])) $icon = "🗜️";

      $badge = "";
      if (stripos($item, "shared_") === 0) $badge = ' <span class="badge shared">Shared</span>';
      if (stripos($item, "deleted_") === 0) $badge = ' <span class="badge deleted">Deleted</span>';

      $safeItem = htmlspecialchars($item);
      $viewLink = "view.php?file=" . urlencode($item) . "&folder=" . urlencode($currentFolder);

      if ($filter === "deleted") {
        $recoverLink = "recover.php?file=" . urlencode($item) . "&folder=" . urlencode($currentFolder);
        $actionButton = '<a class="recover-btn" href="'.$recoverLink.'" onclick="return confirm(\'Recover this file?\')">↩ Recover</a>';
      } else {
        $deleteLink = "delete.php?file=" . urlencode($item) . "&folder=" . urlencode($currentFolder);
        $actionButton = '<a class="delete-btn" href="'.$deleteLink.'" onclick="return confirm(\'Move file to deleted?\')">🗑 Delete</a>';
      }

      echo '
      <div class="row fileRow" data-name="'.htmlspecialchars(strtolower($item)).'">
        <div class="namecell">
          <a href="'.$viewLink.'" style="color:inherit;text-decoration:none;">
            '.$icon.' '.$safeItem.'
          </a>'.$badge.'
        </div>
        <div>'.$date.'</div>
        <div class="right">'.$sizeText.'</div>
        <div class="right">'.$actionButton.'</div>
      </div>';
    }
  }
}
?>

    </section>
  </main>
</div>

<div class="modal-overlay" id="folderModal">
  <div class="modal">
    <h3>Create New Folder</h3>
    <form action="create_folder.php" method="GET">
      <input type="hidden" name="parent" value="<?= htmlspecialchars($currentFolder) ?>">
      <input type="text" name="foldername" placeholder="Enter folder name" required>
      <div class="modal-actions">
        <button type="button" class="cancel-btn" onclick="closeFolderModal()">Cancel</button>
        <button type="submit" class="create-btn">Create</button>
      </div>
    </form>
  </div>
</div>

<script>
const searchBox = document.getElementById("searchBox");
const folderModal = document.getElementById("folderModal");

if (searchBox) {
  searchBox.addEventListener("input", () => {
    const q = searchBox.value.toLowerCase().trim();
    document.querySelectorAll(".fileRow").forEach(row => {
      const name = row.getAttribute("data-name") || "";
      row.style.display = name.includes(q) ? "" : "none";
    });
  });
}

function openFolderModal() {
  folderModal.style.display = "flex";
}

function closeFolderModal() {
  folderModal.style.display = "none";
}
</script>

</body>
</html>
