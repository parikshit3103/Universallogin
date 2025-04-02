<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $whatsapp = trim($_POST['whatsapp']);

    // Check if the user exists with the given WhatsApp number
    $stmt = $pdo->prepare("SELECT secret_key FROM users WHERE username = ? AND whatsapp_number = ?");
    $stmt->execute([$username, $whatsapp]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['username'] = $username; // Store username in session
        header("Location: verify_otp.php"); // Redirect to OTP verification
        exit;
    } else {
        $error = "User not found or incorrect WhatsApp number.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="otp-container">
        <div class="otp-icon">
        <img src="Group 1 (1).png" alt="OTP Icon">
        </div>
        <h2>Login</h2>

        <?php if (isset($error)) echo "<p class='error-message show'>$error</p>"; ?>

        <form method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="whatsapp">Phone Number:</label>
                <input type="text" id="whatsapp" name="whatsapp" required>
            </div>

            <button type="submit" class="otp-btn">Login</button>
        </form>
    </div>
</body>
</html>
