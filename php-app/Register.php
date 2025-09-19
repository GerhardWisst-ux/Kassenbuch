<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\Providers\Qr\GoogleChartsQRCodeProvider;
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CashControl Registrierung</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* === Grundlayout === */
        html,
        body {
            height: 100%;
            margin: 0;
            background: linear-gradient(135deg, #f0f4f9, #dce9f7);
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 95vh;
        }

        /* === Karte (Formularbox) === */
        .card {
            border-radius: 20px;
            overflow: hidden;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            background: #fff;
        }

        .custom-header {
            background: linear-gradient(90deg, #1e3c72, #2a5298);
            color: #fff;
            padding: 1rem;
            text-align: center;
        }

        .custom-header h4 {
            margin: 0;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        /* === Formularfelder === */
        .form-control {
            border-radius: 12px;
            padding: 0.65rem 1rem;
            border: 1px solid #d1d9e6;
            transition: border 0.3s ease, box-shadow 0.3s ease;
        }

        .form-control:focus {
            border-color: #1e3c72;
            box-shadow: 0 0 0 0.2rem rgba(30, 60, 114, 0.2);
        }

        /* === Buttons === */
        .btn-custom {
            background: linear-gradient(90deg, #1e3c72, #2a5298);
            border: none;
            border-radius: 30px;
            font-size: 1rem;
            padding: 0.6rem;
            font-weight: 500;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            width: 100%;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(30, 60, 114, 0.3);
        }

        /* === Links & Fehler === */
        .text-center a {
            color: #1e3c72;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .text-center a:hover {
            color: #2a5298;
            text-decoration: underline;
        }

        .alert {
            border-radius: 12px;
        }
    </style>
</head>

<body>
    <?php
    if (headers_sent()) {
        die("Headers wurden bereits gesendet.");
    }
    ob_start();
    session_start();
    require 'db.php';

    // Registrierungsvorgang (gekürzt, wie gehabt)...
    $errorMessage = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {

        $error = false;
        $errorMessage = '';

        // Eingaben bereinigen
        $email = trim($_POST['email'] ?? '');
        $passwort = $_POST['passwort'] ?? '';
        $passwort2 = $_POST['passwort2'] ?? '';
        $vorname = trim($_POST['vorname'] ?? '');
        $nachname = trim($_POST['nachname'] ?? '');
        $strasse = trim($_POST['strasse'] ?? '');
        $plz = trim($_POST['plz'] ?? '');
        $ort = trim($_POST['ort'] ?? '');

        // Validierung
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorMessage = "Bitte eine gültige E-Mail-Adresse eingeben.";
            $error = true;
        } elseif (empty($passwort)) {
            $errorMessage = "Bitte ein Passwort angeben.";
            $error = true;
        } elseif ($passwort !== $passwort2) {
            $errorMessage = "Die Passwörter müssen übereinstimmen.";
            $error = true;
        }

        if (!$error) {
            // Prüfen, ob E-Mail bereits existiert
            $stmtCheck = $pdo->prepare("SELECT id FROM users WHERE email = :email");
            $stmtCheck->execute(['email' => $email]);
            if ($stmtCheck->fetch()) {
                $errorMessage = "Diese E-Mail-Adresse ist bereits registriert.";
                $error = true;
            }
        }

        try {
            $pdo->beginTransaction();

            if (!$error) {
                // Passwort hashen
                $passwort_hash = password_hash($passwort, PASSWORD_DEFAULT);

                // User einfügen
                $stmtInsert = $pdo->prepare("
            INSERT INTO users 
                (email, passwort, is_admin, vorname, nachname, strasse, plz, ort, freigeschaltet) 
            VALUES 
                (:email, :passwort, :is_admin, :vorname, :nachname, :strasse, :plz, :ort, :freigeschaltet)
        ");
                $result = $stmtInsert->execute([
                    'email' => $email,
                    'passwort' => $passwort_hash,
                    'is_admin' => false,
                    'vorname' => $vorname,
                    'nachname' => $nachname,
                    'strasse' => $strasse,
                    'plz' => $plz,
                    'ort' => $ort,
                    'freigeschaltet' => true
                ]);

                if (!$result) {
                    throw new Exception("Fehler beim Anlegen des Users.");
                }

                $newUserId = $pdo->lastInsertId();
                sleep(2);
            

                // 2FA einrichten
                require_once '../vendor/autoload.php';
                $qrProvider = new GoogleChartsQRCodeProvider();
                $tfa = new TwoFactorAuth($qrProvider);
                $secret = $tfa->createSecret();

                $updateStmt = $pdo->prepare("UPDATE users SET twofactor_secret = :secret WHERE id = :id");
                $updateStmt->execute(['secret' => $secret, 'id' => $newUserId]);

                // QR-Code erzeugen und anzeigen
                $qrCodeUrl = $tfa->getQRCodeImageAsDataUri('CashControl', $secret);
                echo "<p>Scanne diesen QR-Code mit deiner Authenticator-App:</p>";
                echo "<img src='$qrCodeUrl' alt='QR-Code'>";
                echo "<p>ODER Code manuell eingeben: <b>$secret</b></p>";

                // Alles committen
                $pdo->commit();

                // Weiterleitung nach erfolgreicher Registrierung
                header("Location: Login.php");
                exit();
            }
        } catch (Exception $e) {
            // Rollback bei Fehler
            $pdo->rollBack();
            $errorMessage = "Fehler bei der Registrierung: " . $e->getMessage();
            echo "<div class='alert alert-danger'>$errorMessage</div>";
        }

        // Fehlermeldung anzeigen, falls vorhanden
        if ($error && !empty($errorMessage)) {
            echo "<div class='alert alert-danger'>{$errorMessage}</div>";
        }
    }
    ?>
    ?>

    <div class="container">
        <div class="col-md-7 col-lg-6">
            <div class="card shadow-lg">
                <div class="custom-header">
                    <h4>CashControl - Registrierung</h4>
                </div>
                <div class="card-body p-4">
                    <form method="post" action="?register=1" novalidate>
                        <div class="mb-3">
                            <label for="email" class="form-label">Benutzer:</label>
                            <input type="email" name="email" id="email" class="form-control" required
                                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" placeholder="E-Mail eingeben">
                        </div>
                        <div class="mb-3">
                            <label for="vorname" class="form-label">Vorname:</label>
                            <input type="text" name="vorname" id="vorname" class="form-control" required
                                value="<?= htmlspecialchars($_POST['vorname'] ?? '') ?>" placeholder="Vorname eingeben">
                        </div>
                        <div class="mb-3">
                            <label for="nachname" class="form-label">Nachname:</label>
                            <input type="text" name="nachname" id="nachname" class="form-control" required
                                value="<?= htmlspecialchars($_POST['nachname'] ?? '') ?>"
                                placeholder="Nachname eingeben">
                        </div>
                        <div class="mb-3">
                            <label for="strasse" class="form-label">Straße:</label>
                            <input type="text" name="strasse" id="strasse" class="form-control" required
                                value="<?= htmlspecialchars($_POST['strasse'] ?? '') ?>" placeholder="Straße eingeben">
                        </div>
                        <div class="mb-3">
                            <label for="plz" class="form-label">PLZ:</label>
                            <input type="number" name="plz" id="plz" class="form-control" required
                                value="<?= htmlspecialchars($_POST['plz'] ?? '') ?>" placeholder="PLZ eingeben">
                        </div>
                        <div class="mb-3">
                            <label for="ort" class="form-label">Ort:</label>
                            <input type="text" name="ort" id="ort" class="form-control" required
                                value="<?= htmlspecialchars($_POST['ort'] ?? '') ?>" placeholder="Ort eingeben">
                        </div>
                        <div class="mb-3">
                            <label for="passwort" class="form-label">Passwort:</label>
                            <input type="password" name="passwort" id="passwort" class="form-control" required
                                value="<?= htmlspecialchars($_POST['passwort'] ?? '') ?>"
                                placeholder="Passwort eingeben">
                        </div>
                        <div class="mb-3">
                            <label for="passwort2" class="form-label">Passwort bestätigen:</label>
                            <input type="password" name="passwort2" id="passwort2" class="form-control" required
                                value="<?= htmlspecialchars($_POST['passwort2'] ?? '') ?>"
                                placeholder="Passwort erneut eingeben">
                        </div>

                        <?php if (!empty($errorMessage)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($errorMessage) ?></div>
                        <?php endif; ?>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-custom text-white" name="register">Speichern</button>
                        </div>

                        <div class="text-center mt-3">
                            <a href="Login.php">Bereits registriert? Zum Login</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php ob_end_flush(); ?>