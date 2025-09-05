<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';
require 'db.php';

use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\Providers\Qr\GoogleChartsQrCodeProvider;

// --- TwoFactorAuth initialisieren ---
$qrcodeProvider = new GoogleChartsQrCodeProvider();
$tfa = new TwoFactorAuth($qrcodeProvider, 'CashControl');

// --- Prüfen, ob 2FA-Login läuft ---
$userId = $_SESSION['2fa_user'] ?? null;
if (!$userId) {
    header("Location: Login.php");
    exit;
}

// --- Benutzer aus DB laden ---
$stmt = $pdo->prepare("SELECT twofactor_secret, freigeschaltet FROM users WHERE id = :id");
$stmt->execute(['id' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: Login.php");
    exit;
} else {
    $email = trim($_POST['email']);
    $stmt = $pdo->prepare("UPDATE users SET freigeschaltet = 1 WHERE id = :id");
    $stmt->execute(['id' => $userId]);

    header("Location: Index.php");
}

// --- Bereits freigeschaltete Benutzer weiterleiten ---
if (!empty($user['freigeschaltet'])) {
    $_SESSION['userid'] = $userId;
    unset($_SESSION['2fa_user']);
    header("Location: Index.php");
    exit;
}

// --- Secret vorbereiten oder neu erstellen ---
if (!empty($user['twofactor_secret'])) {
    $secret = $user['twofactor_secret'];
} else {
    $secret = $tfa->createSecret();
    $stmt = $pdo->prepare("UPDATE users SET twofactor_secret = :secret WHERE id = :id");
    $stmt->execute(['secret' => $secret, 'id' => $userId]);
}

// --- Codeprüfung ---
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $userCode = trim($_POST['code']);
    if (preg_match('/^\d{6}$/', $userCode)) { // nur 6-stellige Zahl
        if ($tfa->verifyCode($secret, $userCode)) {
            // Benutzer freischalten
            $stmt = $pdo->prepare("UPDATE users SET freigeschaltet = 1 WHERE id = :id");
            $stmt->execute(['id' => $userId]);

            $_SESSION['userid'] = $userId;
            unset($_SESSION['2fa_user']);
            header("Location: Index.php");
            exit;
        } else {
            $message = "<div class='alert alert-danger'>❌ Falscher Code!</div>";
        }
    } else {
        $message = "<div class='alert alert-warning'>Bitte gib eine gültige 6-stellige Zahl ein!</div>";
    }
}

?>


<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>2FA Bestätigung</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">2-Faktor-Authentifizierung</h4>
            </div>
            <div class="card-body text-center">
                <p>Trage den folgenden Secret Key in deine Authenticator-App ein:</p>
                <p><strong><?= htmlspecialchars($secret) ?></strong></p>
                <p>Öffne deine App (Google Authenticator, Authy, etc.) und erstelle einen neuen Eintrag mit diesem Key.
                </p>

                <form method="post" class="mt-4" onsubmit="return validateCode();">
                    <div class="mb-3">
                        <label for="code" class="form-label">6-stelliger Code:</label>
                        <input type="text" class="form-control text-center" id="code" name="code" maxlength="6"
                            pattern="\d{6}" title="Bitte 6-stellige Zahl eingeben" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Bestätigen</button>
                </form>

                <?= $message ?>
            </div>
        </div>
    </div>

    <script>
        function validateCode() {
            const code = document.getElementById('code').value.trim();
            if (!/^\d{6}$/.test(code)) {
                alert("Bitte gib eine gültige 6-stellige Zahl ein!");
                return false;
            }
            return true;
        }
    </script>
</body>

</html>