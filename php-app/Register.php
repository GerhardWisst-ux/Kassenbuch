<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\Providers\Qr\GoogleChartsQRCodeProvider;
?>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CashControl Registrierung </title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* === Grundlayout === */
        html,
        body {
            height: 100%;
            margin: 0;
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        /* Wrapper für Flex */
        .wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* === Navbar & Header === */
        .custom-header {
            background: linear-gradient(90deg, #1e3c72, #2a5298);
            color: #fff;
            height: 40px;
            border-bottom: 2px solid #1b3a6d;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            border-radius: 1px 1px 1px 1px;
        }

        .custom-header h2 {
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        /* === Buttons === */
        .btn-custom {
            background-color: #1e3c72 !important;
            border-radius: 30px;
            font-size: 0.85rem;
            padding: 0.45rem 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary-custom {
            color: linear-gradient(90deg, #1e3c72, #2a5298) !important;
            background-color: #1e3c72 !important;
            border-color: #1e3c72 !important;
        }

        .btn-primary-custom:hover {
            background-color: #1e3c72 !important;
        }


        /* === Karten & Tabellen === */
        .custom-container {
            background-color: #fff;
            border-radius: 12px;
            /* padding: 20px; */
            margin-top: 0px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }


        /* === Navbar Design === */
        .navbar-custom {
            background: linear-gradient(to right, #cce5f6, #e6f2fb);
            border-bottom: 1px solid #b3d7f2;
        }

        .navbar-custom .navbar-brand,
        .navbar-custom .nav-link {
            color: #0c2c4a;
            font-weight: 500;
        }

        .navbar-custom .nav-link:hover,
        .navbar-custom .nav-link:focus {
            color: #04588c;
            text-decoration: underline;
        }

        /* === Modal === */
        .modal-content {
            border-radius: 12px;
        }

        .modal-header {
            background-color: #0946c9ff;
            color: #fff;
            border-radius: 12px 12px 0 0;
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


    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
        // foreach ($_POST as $key => $value) {
        //     echo htmlspecialchars($key) . " = " . htmlspecialchars($value) . "<br>";
        // }
    
        $error = false;
        $email = trim($_POST['email']);
        $passwort = $_POST['passwort'];
        $passwort2 = $_POST['passwort2'];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorMessage = "Bitte eine gültige E-Mail-Adresse eingeben.";
            $error = true;
        }
        if (strlen($passwort) == 0) {
            $errorMessage = "Bitte ein Passwort angeben.";
            $error = true;
        }
        if ($passwort !== $passwort2) {
            $errorMessage = "Die Passwörter müssen übereinstimmen.";
            $error = true;
        }

        if (!$error) {
            $statement = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $statement->execute(['email' => $email]);
            $user = $statement->fetch();

            if ($user) {
                $errorMessage = "Diese E-Mail-Adresse ist bereits registriert.";
            } else {
                $passwort_hash = password_hash($passwort, PASSWORD_DEFAULT);
                $statement = $pdo->prepare("INSERT INTO users (email, passwort) VALUES (:email, :passwort)");
                $result = $statement->execute([
                    'email' => $email,
                    'passwort' => $passwort_hash
                ]);

                if ($result) {
                    // ID des gerade eingefügten Datensatzes
                    $newUserId = $pdo->lastInsertId();
                    echo "Neue User-ID: " . $newUserId;
                } else {
                    echo "Fehler beim Einfügen!";
                }

                // Standardbuchungsarten anlegen für neuen Benutzer
                $buchungsarten = ["_Diveres", "ALDI SUED", "Essen", "Einlage", "LIDL"];
                $stmt = $pdo->prepare("
                        INSERT INTO buchungsarten (buchungsart, Dauerbuchung, created_at, updated_at, userid)
                        VALUES (?, ?, ?, ?, ?)
                    ");

                foreach ($buchungsarten as $art) {
                    $stmt->execute([$art, false, date('Y-m-d'), null, $newUserId]);
                }

                require_once '../vendor/autoload.php';

                $qrProvider = new GoogleChartsQRCodeProvider();
                $tfa = new TwoFactorAuth($qrProvider);

                // Secret generieren
                $secret = $tfa->createSecret();

                // QR-Code als Data URI
                $qrCodeUrl = $tfa->getQRCodeImageAsDataUri('CashControl', $secret);

                // Secret speichern
                $updateStmt = $pdo->prepare("UPDATE users SET twofactor_secret = :secret WHERE id = :id");
                $updateStmt->execute(['secret' => $secret, 'id' => $newUserId]);

                // QR-Code für Google Authenticator anzeigen
                $qrCodeUrl = $tfa->getQRCodeImageAsDataUri('CashControl', $secret);
                echo "<p>Scanne diesen QR-Code mit deiner Authenticator-App:</p>";
                echo "<img src='$qrCodeUrl' alt='QR-Code'>";
                echo "<p>ODER Code manuell eingeben: <b>$secret</b></p>";

                if ($result) {
                    header("Location: Login.php");
                    exit();
                } else {
                    $errorMessage = "Beim Abspeichern ist ein Fehler aufgetreten.";
                }
            }
        }
    }
    ?>


    <!-- Inhalt mit Login-Form -->
    <div id="register">
        <form id="loginform" method="post" action="?register=1" class="needs-validation" novalidate>
            <div class="container mt-5">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card shadow-lg border-0">
                            <div class="custom-header bg-primary text-white text-center">
                                <h4 class="mb-0">CashControl Registrierung</h4>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="email" class="text-dark">Benutzer:</label>
                                    <input type="email" name="email" id="email" class="form-control" required
                                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                        placeholder="Benutzer eingeben">
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
                                <br>
                                <div class="mb-3">
                                    <button type="submit" style="width: 100%;"
                                        class="btn-custom text-white rounded-pill" name=" register" id="register">
                                        Speichern
                                    </button>
                                </div>

                                <div id="error-link" class="text-danger">
                                    <?php if (isset($errorMessage))
                                        echo $errorMessage; ?>
                                </div>
                                <div id="login-link" class="text-center">
                                    <a href="Login.php">Login</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>

</body>

</html>