<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once 'db_connect.php';
require_once 'vendor/autoload.php';

use OTPHP\TOTP;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\Result\PngResult;

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

$username = $_SESSION['username'];

// Fetch user from database
$stmt = $pdo->prepare("SELECT secret_key FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Generate a new TOTP secret if not set
if (!isset($user['secret_key']) || strlen($user['secret_key']) < 16) {
    $totp = TOTP::create();
    $totp->setIssuer('UniversalLogin');
    $totp->setLabel($username);

    // Reduce secret key length while keeping it valid
    $secret = substr($totp->getSecret(), 0, 32);

    // Save secret in database
    $stmt = $pdo->prepare("UPDATE users SET secret_key = ? WHERE username = ?");
    $stmt->execute([$secret, $username]);
} else {
    $secret = $user['secret_key'];
}

// Initialize TOTP with the correct secret key
$totp = TOTP::create($secret);
$totp->setIssuer('UniversalLogin');
$totp->setLabel($username);

// Generate the QR code
$qrCode = new QrCode($totp->getProvisioningUri());
$writer = new PngWriter();
$result = $writer->write($qrCode);

/** @var PngResult $result */
$qrCodeBase64 = base64_encode($result->getString());

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan QR Code</title>
</head>
<body>
    <h2>Scan this QR Code with Google Authenticator</h2>
    <img src="data:image/png;base64,<?php echo $qrCodeBase64; ?>" alt="QR Code">
    <p>After scanning, click below to proceed.</p>
    <form action="verify_otp.php" method="GET">
        <button type="submit">Continue</button>
    </form>
</body>
</html>
