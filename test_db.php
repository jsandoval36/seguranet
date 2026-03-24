
<?php
require_once "database.php";

if ($conn) {
    echo "✅ Database connected successfully!";
} else {
    echo "❌ Connection failed";
}
?>
