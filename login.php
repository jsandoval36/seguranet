<?php
session_start();

$email = $_POST["email"] ?? "";
$password = $_POST["password"] ?? "";

try {
   $conn = new PDO("sqlsrv:server = tcp:seguranet.database.windows.net,1433; Database = seguranetDB", "ApplicationUser", "Apple123!");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Fetch user by email
    $stmt = $conn->prepare("SELECT id, email, password_hash FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify hashed password
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION["user_id"] = $user['id'];
        $_SESSION["email"] = $user['email'];

        header("Location: dashboard.php");
        exit();
    } else {
        echo "Invalid login. <a href='index.html'>Try again</a>";
    }

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// CAPTCHA
$secretKey = "6LcJnZksAAAAALXrCPLprdLCvGwNi48Qwx0-T92A";

$response = $_POST['g-recaptcha-response'];

$verify = file_get_contents(
  "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$response"
);

$responseData = json_decode($verify);

if ($responseData->success) {
    echo "CAPTCHA passed!";
} else {
    echo "CAPTCHA failed!";
}
?>
