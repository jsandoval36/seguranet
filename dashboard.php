<?php
session_start();
if (!isset($_SESSION["user_id"])) {
  header("Location: index.html");
  exit();
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
        <div class="mark"></div>
        <div>
          <div class="name">SeguraNet</div>
          <div class="sub">Secure files</div>
        </div>
      </div>

      <nav class="nav">
        <a class="active" href="#">All files</a>
        <a href="#">Photos</a>
        <a href="#">Shared</a>
        <a href="#">Deleted</a>
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
          <input type="text" placeholder="Search" />
        </div>

        <div class="top-actions">
          <!-- IMPORTANT: upload.html is lowercase -->
          <button class="btn" onclick="window.location.href='upload.html'">Upload</button>
          <button class="btn ghost">New folder</button>
        </div>
      </header>

      <!-- TITLE -->
      <div class="title-row">
        <h1>All files</h1>
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

            // show newest first (your uploaded names are unique, so this works well)
            rsort($files);

            if (count($files) === 0) {
              echo '<div class="row"><div class="namecell">No uploads yet</div><div>—</div><div class="right">—</div></div>';
            } else {
              foreach ($files as $file) {
                $path = $uploadDir . "/" . $file;
                if (!is_file($path)) continue;

                $date = date("m/d/Y", filemtime($path));

                $bytes = filesize($path);
                if ($bytes >= 1024 * 1024) {
                  $sizeText = round($bytes / (1024 * 1024), 2) . " MB";
                } else {
                  $sizeText = round($bytes / 1024, 2) . " KB";
                }

                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                $icon = "📄";
                if (in_array($ext, ["jpg","jpeg","png","gif","webp"])) $icon = "🖼️";
                if (in_array($ext, ["mp4","mov","avi","mkv"])) $icon = "🎞️";
                if ($ext === "pdf") $icon = "📄";
                if (in_array($ext, ["zip","rar","7z"])) $icon = "🗜️";

                $safeFile = htmlspecialchars($file);
                $viewLink = "view.php?file=" . urlencode($file);

                echo '
                  <div class="row">
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
</body>
</html>
