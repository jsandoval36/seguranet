<?php
session_start();
if (!isset($_SESSION["user_id"])) {
  header("Location: index.html");
  exit();
}

$filter = $_GET["filter"] ?? "all";

function active($name, $filter) {
  return ($name === $filter) ? "active" : "";
}

function pageTitle($filter) {
  if ($filter === "photos") return "Photos";
  if ($filter === "shared") return "Shared";
  if ($filter === "deleted") return "Deleted";
  return "All files";
}

// file extension rules
function isPhotoExt($ext) {
  return in_array($ext, ["jpg","jpeg","png","gif","webp"]);
}

function isVideoExt($ext) {
  return in_array($ext, ["mp4","mov","avi","mkv"]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SeguraNet | Dashboard</title>
  <link href="seguraNet-icon.png" rel="icon" type="image/x-icon">
  <link rel="stylesheet" href="dashboard.css" />
</head>

<body>
  <div class="app">

    <!-- LEFT SIDEBAR -->
    <aside class="sidebar">
      <div class="brand">
        <!-- LOGO (add this file in your root if you want) -->
        <img src="seguranet-logo.png" alt="SeguraNet" class="brand-logo" onerror="this.style.display='none'">
        <div>
          <div class="name">SeguraNet</div>
          <div class="sub">Secure files</div>
        </div>
      </div>

      <nav class="nav">
        <a class="<?= active('all', $filter) ?>" href="dashboard.php?filter=all">All files</a>
        <a class="<?= active('photos', $filter) ?>" href="dashboard.php?filter=photos">Photos</a>
        <a class="<?= active('shared', $filter) ?>" href="dashboard.php?filter=shared">Shared</a>
        <a class="<?= active('deleted', $filter) ?>" href="dashboard.php?filter=deleted">Deleted</a>
      </nav>

      <div class="sidebar-footer">
        <a class="logout" href="logout.php">Log out</a>
      </div>
    </aside>

    <!-- MAIN -->
    <main class="main">

      <!-- TOP BAR -->
      <header class="topbar">
        <div class="search">
          <input id="searchBox" type="text" placeholder="Search files..." />
        </div>

        <div class="top-actions">
          <button class="btn" onclick="window.location.href='upload.html'">Upload</button>
          <button class="btn ghost" onclick="alert('New folder needs a database or folder system. I can add it if you want.')">New folder</button>
        </div>
      </header>

      <!-- TITLE -->
      <div class="title-row">
        <h1><?= htmlspecialchars(pageTitle($filter)) ?></h1>
        <div class="meta">Signed in as: <?php echo htmlspecialchars($_SESSION["email"] ?? "user"); ?></div>
      </div>

      <!-- FILE TABLE (REAL UPLOADS) -->
      <section class="table">
        <div class="row head">
          <div>Name</div>
          <div>Last modified</div>
          <div class="right">Size</div>
        </div>

        <?php
          $uploadDir = __DIR__ . "/uploads";

          if (!is_dir($uploadDir)) {
            echo '<div class="row"><div class="namecell">⚠ uploads folder not found</div><div>—</div><div class="right">—</div></div>';
          } else {
            $files = array_diff(scandir($uploadDir), ['.', '..', '.gitkeep']);
            rsort($files);

            // FILTERING
            $filteredFiles = [];
            foreach ($files as $file) {
              $path = $uploadDir . "/" . $file;
              if (!is_file($path)) continue;

              $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

              if ($filter === "photos") {
                if (!isPhotoExt($ext)) continue;
              } elseif ($filter === "shared") {
                // No DB yet: you can treat files starting with "shared_" as shared
                // Example: shared_report.pdf
                if (stripos($file, "shared_") !== 0) continue;
              } elseif ($filter === "deleted") {
                // No DB yet: treat files starting with "deleted_" as deleted
                // Example: deleted_old.png
                if (stripos($file, "deleted_") !== 0) continue;
              }

              $filteredFiles[] = $file;
            }

            if (count($filteredFiles) === 0) {
              echo '<div class="row"><div class="namecell">No files found for this section</div><div>—</div><div class="right">—</div></div>';
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
                if ($ext === "pdf") $icon = "📄";
                if (in_array($ext, ["zip","rar","7z"])) $icon = "🗜️";

                $safeFile = htmlspecialchars($file);
                $viewLink = "view.php?file=" . urlencode($file);

                echo '
                  <div class="row fileRow" data-name="'.htmlspecialchars(strtolower($file)).'">
                    <div class="namecell">
                      <a href="'.$viewLink.'" target="_blank" style="color:inherit; text-decoration:none;">
                        '.$icon.' '.$safeFile.'
                      </a>
                    </div>
                    <div>'.$date.'</div>
                    <div class="right">'.$sizeText.'</div>
                  </div>
                ';
              }
            }
          }
        ?>
      </section>

    </main>
  </div>

  <!-- SIMPLE SEARCH (client-side) -->
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
