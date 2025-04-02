<?php
session_start();
require_once 'db_connect.php';
require_once 'vendor/autoload.php';

use OTPHP\TOTP;

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = implode('', $_POST['otp']); // Combine OTP digits

    // Fetch user's secret
    $stmt = $pdo->prepare("SELECT secret_key FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $totp = TOTP::create($user['secret_key']);
    if ($totp->verify($otp)) {
        header("Location: success.php");
        exit;
    } else {
        $error = "Invalid OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="style.css">

</head>
<body>
    <div class="otp-container <?php echo !empty($error) ? 'shake' : ''; ?>" id="otp-box">
        <div class="otp-box">
            <div class="otp-icon">
                <img src="Group 1 (1).png" alt="OTP Icon">
            </div>
            <h2>Enter Authenticator Code</h2>
            <p class="error-message <?php echo !empty($error) ? 'show' : ''; ?>">
                <?php echo isset($error) ? $error : ''; ?>
            </p>
            <form method="POST">
                <div class="otp-input-group">
                    <input type="text" maxlength="1" class="otp-input" name="otp[]" required>
                    <input type="text" maxlength="1" class="otp-input" name="otp[]" required>
                    <input type="text" maxlength="1" class="otp-input" name="otp[]" required>
                    <input type="text" maxlength="1" class="otp-input" name="otp[]" required>
                    <input type="text" maxlength="1" class="otp-input" name="otp[]" required>
                    <input type="text" maxlength="1" class="otp-input" name="otp[]" required>
                </div>
                <button type="submit" class="otp-btn">Verify OTP</button>
            </form>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>
