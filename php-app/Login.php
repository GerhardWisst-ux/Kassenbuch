<?php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'db.php';
session_start();
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CashControl Login</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* === Grundlayout === */
        html,
        body {
            height: 100%;
            margin: 0;
            /* Hellerer, wärmerer Blauverlauf */
            background: linear-gradient(135deg, #4da8da, #a0d8f1);
            font-family: 'Segoe UI', Tahoma, sans-serif;
            color: #000;
            /* optional für Text auf hellblau */
        }

        /* Zentrierung */
        .container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 90vh;
        }

        /* === Karte (Login-Box) === */
        .card {
            border-radius: 20px;
            overflow: hidden;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            background: #fff;
            color: #000;
        }

        /* Header */
        .custom-header {
            background: linear-gradient(90deg, #1e3c72, #2a5298);
            color: #fff;
            padding: 1rem;
        }

        .custom-header h4 {
            margin: 0;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        /* Formularfelder */
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

        /* Buttons */
        .btn-custom {
            background: linear-gradient(90deg, #1e3c72, #2a5298);
            border: none;
            border-radius: 30px;
            font-size: 1rem;
            padding: 0.6rem;
            font-weight: 500;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(30, 60, 114, 0.3);
        }

        /* Links */
        .text-center a {
            color: #1e3c72;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .text-center a:hover {
            color: #2a5298;
            text-decoration: underline;
        }

        /* Fehlermeldung */
        .alert {
            border-radius: 12px;
        }
    </style>


</head>

<body>
    <?php
    $_SESSION['userid'] = "";
    $errorMessage = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $password = trim($_POST['passwort']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorMessage = "Ungültige E-Mail-Adresse.";
        } else {
            try {

                $stmt = $pdo->prepare("SELECT id, email, vorname, nachname, passwort, freigeschaltet, is_admin, gesperrt, mandantennummer FROM users WHERE email = :email");
                $stmt->execute(['email' => $email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$user) {
                    $errorMessage = "Benutzer existiert nicht.";
                } elseif (!password_verify($password, $user['passwort'])) {
                    $errorMessage = "Passwort ist falsch.";
                } elseif ((int) $user['gesperrt'] === 1) {
                    $errorMessage = "Es liegt eine Sperre vor. Bitte kontaktieren Sie den Administrator.";
                } else {
                    if ((int) $user['freigeschaltet'] === 1) {
                        $_SESSION['userid'] = $user['id'];
                        $_SESSION['email'] = $user['email'];
                        $_SESSION['vorname'] = $user['vorname'];
                        $_SESSION['nachname'] = $user['nachname'];
                        $_SESSION['is_admin'] = $user['is_admin'];
                        $_SESSION['mandantennummer'] = $user['mandantennummer'];
                        header("Location: Index.php");
                        exit;
                    }
                }
            } catch (PDOException $e) {
                $errorMessage = "Datenbankfehler: " . $e->getMessage();
            }
        }
    }
    ?>

    <div class="container">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg">
                <div class="custom-header text-center">
                    <h4>CashControl Login</h4>
                </div>
                <div class="card-body p-4">
                    <form method="post" action="" novalidate>
                        <div class="mb-3">
                            <label for="email" class="form-label">Benutzer:</label>
                            <input type="email" name="email" id="email" class="form-control" required
                                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" placeholder="Benutzer eingeben">
                        </div>
                        <div class="mb-3">
                            <label for="passwort" class="form-label">Passwort:</label>
                            <input type="password" name="passwort" id="passwort" class="form-control" required
                                value="<?= htmlspecialchars($_POST['passwort'] ?? '') ?>"
                                placeholder="Passwort eingeben">
                        </div>

                        <?php if (!empty($errorMessage)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($errorMessage) ?></div>
                        <?php endif; ?>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-custom text-white">Anmelden</button>
                        </div>

                        <div class="text-center mt-3">
                            <a href="register.php">Noch kein Konto? Jetzt registrieren</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php ob_end_flush(); ?>