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
