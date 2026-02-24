<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    move_uploaded_file(
        $_FILES["file"]["tmp_name"],
        "uploads/" . $_FILES["file"]["name"]
    );
    echo "Upload successful!";
}
?>

<form method="POST" enctype="multipaart/form-data">
    <input type="file" name="file">
    <button type="submit">Upload</button>

</form>


/*<?php
include "db.php";
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $file = $_FILES["file"];
    $filename = basename($file["name"]);
    $target = "uploads/" . $filename;

    move_uploaded_file($file["tmp_name"], $target);

    $stmt = $conn->prepare("INSERT INTO files (user_id, filename) VALUES (?, ?)");
    $stmt->bind_param("is", $_SESSION["user_id"], $filename);
    $stmt->execute();

    header("Location: files.php");
}
?>

<form method="POST" enctype="multipart/form-data">
  <input type="file" name="file" required>
  <button>Upload</button>
</form>
<a href="files.php">My Files</a>
*/
