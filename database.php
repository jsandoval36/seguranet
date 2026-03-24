<?php
$host = getenv("DB_HOST");
$user = getenv("DB_USER");
$pass = getenv("DB_PASS");
$db   = getenv("DB_NAME");

try {
    $conn = new PDO("sqlsrv:server = tcp:seguranet.database.windows.net,1433; Database = seguranetDB", "CloudSAf51fec62", "{Apple123!}");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
    print("Error connecting to SQL Server.");
    die(print_r($e));
}
    die("Database connection failed: " . $e->getMessage());
}
?>
