<?php
require_once "database.php";

echo "<h3>Checking Environment Variables...</h3>";

echo "DB_HOST: " . getenv("DB_HOST") . "<br>";
echo "DB_USER: " . getenv("DB_USER") . "<br>";
echo "DB_NAME: " . getenv("DB_NAME") . "<br><br>";

echo "<h3>Connection Test:</h3>";

try {
    if ($conn) {
        echo "✅ Database connected successfully!";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
