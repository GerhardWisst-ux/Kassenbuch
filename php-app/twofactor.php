<?php
session_start();
require 'db.php';
require_once '../vendor/autoload.php';

if (!isset($_SESSION['2fa_user'])) {
    header("Location: Login.php");
    exit();
}

use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\Providers\Qr\GoogleChartsQRCodeProvider;

$qrProvider = new GoogleChartsQRCodeProvider();
$tfa = new TwoFactorAuth($qrProvider);

// Secret des Users aus DB laden
$stmt = $pdo->prepare("SELECT twofactor_secret FROM users WHERE id = :id");
$stmt->execute(['id' => $_SESSION['2fa_user']]);
$secret = $stmt->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code']);

    
$secret = '47TJA6WGYSWHIM7TKU2I73U3OXA5TRGF';

echo "Aktueller Code: " . $tfa->getCode($secret);

    if ($tfa->verifyCode($secret, $code)) {
        // 2FA erfolgreich -> User vollständig einloggen
        $_SESSION['userid'] = $_SESSION['2fa_user'];
        unset($_SESSION['2fa_user']);
        header("Location: Index.php");
        exit();
    } else {
        $error = "Ungültiger Code!";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>2FA Bestätigung</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-primary text-white text-center">
                        <h4>2-Faktor-Authentifizierung</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="code" class="form-label">Code aus der App:</label>
                                <input type="text" name="code" id="code" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Bestätigen</button>
                        </form>
                        <?php if (!empty($error)): ?>
                            <div class="text-danger mt-2"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>