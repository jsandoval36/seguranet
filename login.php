<?php
session_start();

$email = $_POST["email"] ?? "";
$password = $_POST["password"] ?? "";

// TEMP test login (replace with DB later)
if ($email === "test@test.com" && $password === "password123") {
    $_SESSION["user_id"] = 1;
    $_SESSION["email"] = $email;

    header("Location: dashboard.php");
    exit();
} else {
    echo "Invalid login. <a href='index.html'>Try again</a>";
}

/*
<?php
include "db.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user["password_hash"])) {
        $_SESSION["user_id"] = $user["id"];
        header("Location: upload.php");
    } else {
        echo "Invalid login";
    }
}
?>

<form method="POST">
  <input type="email" name="email" required>
  <input type="password" name="password" required>
  <button>Login</button>
</form>
*/
