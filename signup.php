<?php

session_start();

require_once 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    if ($password !== $confirmPassword) {
        die("Passwords do not match. Please go back and try again.");
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    
    if ($stmt) {
        $stmt->bind_param("sss", $username, $email, $hashedPassword);
        
        if ($stmt->execute()) {
            $newUserId = $stmt->insert_id;
            
            $_SESSION['user_id'] = $newUserId;
            $_SESSION['email'] = $email;
            
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Error: Could not create account. " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Database error: " . $conn->error;
    }
} else {
    header("Location: CreateAccount.html");
    exit();
}
?>
