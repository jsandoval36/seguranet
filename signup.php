<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {

        $conn = new PDO("sqlsrv:server = tcp:seguranet.database.windows.net,1433; Database = seguranetDB", "ApplicationUser", "Apple123!");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if (!$email || !$password) {
            die("All fields are required.");
        }

        // Check if email already exists
        $check = $conn->prepare("SELECT id FROM users WHERE email = :email");
        $check->execute(['email' => $email]);

        if ($check->fetch()) {
            echo "Email already registered. <a href='index.html'>Login</a>";
            exit();
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert user
        $sql = "INSERT INTO users (email, password_hash)
                VALUES (:email, :password_hash)";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':email' => $email,
            ':password' => $hashedPassword
        ]);

        echo "Account created successfully!";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
