<?php
$host = getenv("DB_HOST");
$user = getenv("DB_USER");
$pass = getenv("DB_PASS");
$db   = getenv("DB_NAME");

try {
    // This is the specific connection string format for Azure SQL (Microsoft)
    $conn = new PDO("odbc:Driver={ODBC Driver 17 for SQL Server};Server=tcp:$host,1433;Database=$db", $user, $pass);
    
    // Tell PDO to throw an exception if it encounters an error
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
