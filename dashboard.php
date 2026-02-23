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
          <button class="btn" onclick="window.location.href='Upload.html'">Upload</button>
          <button class="btn ghost">New folder</button>
        </div>
      </header>

      <!-- TITLE -->
      <div class="title-row">
        <h1>All files</h1>
        <div class="meta">Signed in as: <?php echo htmlspecialchars($_SESSION["email"] ?? "user"); ?></div>
      </div>

      <!-- FILE TABLE -->
      <section class="table">
        <div class="row head">
          <div>Name</div>
          <div>Last modified</div>
          <div class="right">Size</div>
        </div>

        <div class="row">
          <div class="namecell">📁 Camera Uploads</div>
          <div>—</div>
          <div class="right">406.66 MB</div>
        </div>

        <div class="row">
          <div class="namecell">📄 Resume.pdf</div>
          <div>10/12/2024</div>
          <div class="right">320 KB</div>
        </div>

        <div class="row">
          <div class="namecell">🖼️ Photo.jpg</div>
          <div>12/28/2023</div>
          <div class="right">3.36 MB</div>
        </div>

        <div class="row">
          <div class="namecell">🎞️ Video.mov</div>
          <div>12/28/2023</div>
          <div class="right">39.52 MB</div>
        </div>
      </section>

    </main>
  </div>
</body>
</html>
