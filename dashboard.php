<?php
session_start();
if (!isset($_SESSION["user_id"])) {
  header("Location: login.html");
  exit();
}

$filter = $_GET["filter"] ?? "all";
$userId = $_SESSION["user_id"];

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SeguraNet | Dashboard</title>
<link href="seguraNet-icon.png" rel="icon">
<link rel="stylesheet" href="dashboard.css">
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
      <a class="<?= active('all',$filter) ?>" href="dashboard.php?filter=all">📁 All files</a>
      <a class="<?= active('photos',$filter) ?>" href="dashboard.php?filter=photos">🖼 Photos</a>
      <a class="<?= active('videos',$filter) ?>" href="dashboard.php?filter=videos">🎞 Videos</a>
      <a class="<?= active('docs',$filter) ?>" href="dashboard.php?filter=docs">📄 Docs</a>
      <a class="<?= active('shared',$filter) ?>" href="dashboard.php?filter=shared">🤝 Shared</a>
      <a class="<?= active('deleted',$filter) ?>" href="dashboard.php?filter=deleted">🗑 Deleted</a>
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
        <button class="btn" onclick="window.location.href='upload.php'">Upload</button>
        <button class="btn ghost">New folder</button>
      </div>
    </header>

    <div class="title-row">
      <h1><?= htmlspecialchars(pageTitle($filter)) ?></h1>
      <div class="meta">
        Signed in as: <?= htmlspecialchars($_SESSION["email"] ?? "user"); ?>
      </div>
    </div>

    <section class="table">
      <div class="row head">
        <div>Name</div>
        <div>Last modified</div>
        <div class="right">Size</div>
        <div class="right">Action</div>
      </div>

<?php
$uploadDir = __DIR__ . "/uploads/" . $userId;

if (!is_dir($uploadDir)) {

  echo '<div class="row">
    <div class="namecell">No files uploaded yet</div>
    <div>—</div>
    <div class="right">—</div>
    <div class="right">—</div>
  </div>';

} else {

  $files = array_diff(scandir($uploadDir), ['.','..','.gitkeep']);
  rsort($files);

  $filteredFiles = [];

  foreach ($files as $file) {
    $path = $uploadDir . "/" . $file;

    if (!is_file($path)) continue;

    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

    if ($filter === "photos") {
      if (!isPhotoExt($ext)) continue;
    } elseif ($filter === "videos") {
      if (!isVideoExt($ext)) continue;
    } elseif ($filter === "docs") {
      if (!isDocExt($ext)) continue;
    } elseif ($filter === "shared") {
      if (stripos($file, "shared_") !== 0) continue;
    } elseif ($filter === "deleted") {
      if (stripos($file, "deleted_") !== 0) continue;
    } else {
      if (stripos($file, "deleted_") === 0) continue;
    }

    $filteredFiles[] = $file;
  }

  if (count($filteredFiles) === 0) {

    echo '<div class="row">
      <div class="namecell">No files found</div>
      <div>—</div>
      <div class="right">—</div>
      <div class="right">—</div>
    </div>';

  } else {

    foreach ($filteredFiles as $file) {
      $path = $uploadDir . "/" . $file;
      $date = date("m/d/Y", filemtime($path));
      $bytes = filesize($path);

      if ($bytes >= 1024 * 1024) {
        $sizeText = round($bytes / (1024 * 1024), 2) . " MB";
      } else {
        $sizeText = round($bytes / 1024, 2) . " KB";
      }

      $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

      $icon = "📄";
      if (isPhotoExt($ext)) $icon = "🖼️";
      if (isVideoExt($ext)) $icon = "🎞️";
      if ($ext === "pdf") $icon = "📕";
      if (in_array($ext, ["zip","rar","7z"])) $icon = "🗜️";

      $badge = "";
      if (stripos($file, "shared_") === 0) $badge = ' <span class="badge shared">Shared</span>';
      if (stripos($file, "deleted_") === 0) $badge = ' <span class="badge deleted">Deleted</span>';

      $safeFile = htmlspecialchars($file);
      $viewLink = "view.php?file=" . urlencode($file);

      if ($filter === "deleted") {
        $recoverLink = "recover.php?file=" . urlencode($file);
        $actionButton = '<a class="recover-btn" href="'.$recoverLink.'" onclick="return confirm(\'Recover this file?\')">↩ Recover</a>';
      } else {
        $deleteLink = "delete.php?file=" . urlencode($file);
        $actionButton = '<a class="delete-btn" href="'.$deleteLink.'" onclick="return confirm(\'Move file to deleted?\')">🗑 Delete</a>';
      }

      echo '
      <div class="row fileRow" data-name="'.htmlspecialchars(strtolower($file)).'">
        <div class="namecell">
          <a href="'.$viewLink.'" style="color:inherit;text-decoration:none;">
            '.$icon.' '.$safeFile.'
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

<script>
const searchBox = document.getElementById("searchBox");

if (searchBox) {
  searchBox.addEventListener("input", () => {
    const q = searchBox.value.toLowerCase().trim();
    document.querySelectorAll(".fileRow").forEach(row => {
      const name = row.getAttribute("data-name") || "";
      row.style.display = name.includes(q) ? "" : "none";
    });
  });
}
</script>

</body>
</html>
