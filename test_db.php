<?php
require_once "database.php";

echo "DB_HOST: " . getenv("DB_HOST") . "<br>";
echo "DB_USER: " . getenv("DB_USER") . "<br>";
echo "DB_NAME: " . getenv("DB_NAME") . "<br><br>";

if ($conn) {
    echo "✅ Database connected successfully!";
}
?>
