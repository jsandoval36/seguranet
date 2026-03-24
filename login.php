<?php
session_start();
try {
        $conn = new PDO("sqlsrv:server = tcp:seguranet.database.windows.net,1433; Database = seguranetDB", "ApplicationUser", "Apple123!");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        // Get user by email
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':email' => $email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {

            // 🔐 If using hashed passwords (recommended)
            if (password_verify($password, $user['password'])) {

                // Store session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                header("Location: dashboard.php");
                exit();

            } else {
                echo "Invalid password.";
            }

        } else {
            echo "User not found.";
        }

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
