<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $whatsapp = $_POST['whatsapp'];

    if (empty($username) || empty($whatsapp)) {
        $error = "All fields are required.";
    } elseif (!preg_match('/^\d{10}$/', $whatsapp)) {
        $error = "Phone number must be exactly 10 digits."; // To ensure that only the 10 number and the number is written in this input
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = "Username already exists.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (username, whatsapp_number) VALUES (?, ?)");
            $stmt->execute([$username, $whatsapp]);
            $_SESSION['username'] = $username;
            header("Location: qrpage.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="otp-container">
        <div class="otp-icon">
        <img src="Group 1 (1).png" alt="OTP Icon">
        </div>
        <h2>Register</h2>

        <?php if (isset($error)) echo "<p class='error-message show'>$error</p>"; ?>

        <form method="POST">
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" class="input-field" required>
            </div>
            <div class="form-group">
                <label>Phone Number:</label>
                <input type="text" name="whatsapp" class="input-field" required 
                       maxlength="10" pattern="[0-9]{10}" 
                       oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            </div>
            <button type="submit" class="otp-btn">Continue</button>
        </form>

        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>
