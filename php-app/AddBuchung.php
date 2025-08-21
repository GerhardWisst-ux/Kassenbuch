<?php
ob_start();
session_set_cookie_params([
    'httponly' => true,
    'secure' => true,  // Nur bei HTTPS
    'samesite' => 'Strict'
]);
session_start();
if ($_SESSION['userid'] == "") {
    header('Location: Login.php'); // zum Loginformular
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kassenbuch Position hinzufügen</title>

    <!-- CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="css/responsive.dataTables.min" rel="stylesheet">

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
            border-bottom: 2px solid #1b3a6d;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            border-radius: 0 0 12px 12px;
        }

        .custom-header h2 {
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        /* === Buttons === */
        .btn {
            border-radius: 30px;
            font-size: 0.85rem;
            padding: 0.45rem 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #2a5298;
            border-color: #1e3c72;
        }

        .btn-primary:hover {
            background-color: #1e3c72;
        }

        .btn-darkgreen {
            background-color: #198754;
            border-color: #146c43;
        }

        .btn-darkgreen:hover {
            background-color: #146c43;
        }

        /* === Karten & Tabellen === */
        .custom-container {
            background-color: #fff;
            border-radius: 12px;
            /* padding: 20px; */
            margin-top: 0px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }

        #TableBestaende {
            width: 100%;
            font-size: 0.9rem;
        }

        #TableBestaende tbody tr:hover {
            background-color: #f1f5ff;
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

        /* === Toast === */
        .toast-green {
            background-color: #198754;
            color: #fff;
        }
    </style>
</head>

<body>
    <?php

    // CSRF-Token erzeugen
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    require 'db.php';
    $userid = $_SESSION['userid'] ?? null;

    if (!$userid) {
        header('Location: Login.php');
        exit();
    }
    require_once 'includes/header.php';

    // Buchungsarten laden
    $sql = "SELECT DISTINCT ID, Buchungsart FROM Buchungsarten WHERE userid = :userid ORDER BY Buchungsart";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['userid' => $userid]);
    $buchungsarten = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <header class="custom-header py-2 text-white">
        <div class="container-fluid">
            <div class="row align-items-center">
                <!-- Titel zentriert -->
                <div class="col-12 text-center mb-2 mb-md-0">
                    <h2 class="h4 mb-0">Kassenbuch Position hinzufügen</h2>
                </div>
                <!-- Benutzerinfo + Logout -->
                <div class="col-12 col-md-auto ms-md-auto text-center text-md-end">
                    <!-- Auf kleinen Bildschirmen: eigene Zeile für E-Mail -->
                    <div class="d-block d-md-inline mb-1 mb-md-0">
                        <span class="me-2">Angemeldet als:
                            <?= htmlspecialchars($_SESSION['email']) ?></span>
                    </div>
                    <!-- Logout-Button -->
                    <a class="btn btn-darkgreen btn-sm" title="Abmelden vom Webshop" href="logout.php">
                        <i class="fa fa-sign-out" aria-hidden="true"></i> Ausloggen
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div id="addbuchung" class="container-fluid mt-4">
        <form action="AddBuchungEntry.php" method="post" class="p-3 border rounded bg-light shadow-sm">
            <input type="hidden" name="csrf_token"
                value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">Datum:</label>
                <div class="col-sm-4">
                    <input class="form-control" type="date" name="datum" required>
                </div>
            </div>

            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">Typ:</label>
                <div class="col-sm-4">
                    <select class="form-control" name="typ">
                        <option value="Einlage">Einlage</option>
                        <option value="Ausgabe" selected>Ausgabe</option>
                    </select>
                </div>
            </div>

            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">Betrag:</label>
                <div class="col-sm-4">
                    <input class="form-control" type="number" name="betrag" step="0.01" required>
                </div>
            </div>

            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">Buchungsart:</label>
                <div class="col-sm-10">
                    <select class="form-control" id="buchungsarten-dropdown" name="buchungart_id">
                        <?php foreach ($buchungsarten as $row): ?>
                            <option value="<?= htmlspecialchars($row['ID'], ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($row['Buchungsart'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                        <option value="custom">Wert eingeben</option>
                    </select>

                    <input class="form-control mt-2 d-none" type="text" id="custom-input" name="custom_buchungsart"
                        placeholder="Wert eingeben">
                </div>
            </div>

            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">Verwendungszweck:</label>
                <div class="col-sm-10">
                    <input class="form-control" type="text" name="beschreibung" required>
                </div>
            </div>

            <div class="mb-3">
                <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i></button>
                <a href="Index.php" class="btn btn-primary"><i class="fa fa-arrow-left"></i></a>
            </div>
        </form>
    </div>
</body>

<!-- JS -->
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>

<script>
    // Heutiges Datum automatisch setzen
    document.addEventListener("DOMContentLoaded", function () {
        const today = new Date();
        const formattedDate = today.toISOString().split('T')[0];
        document.getElementById("datum").value = formattedDate;
    });

    function NavBarClick() {
        const topnav = document.getElementById("myTopnav");
        if (topnav.className === "topnav") {
            topnav.className += " responsive";
        } else {
            topnav.className = "topnav";
        }
    }

    function toggleCustomInput(select) {

        const customInput = document.getElementById('custom-input');
        if (select.value === 'custom') {
            customInput.classList.remove('d-none');
            customInput.removeAttribute('disabled');
            customInput.setAttribute('required', 'required');
        } else {
            customInput.classList.add('d-none');
            customInput.setAttribute('disabled', 'disabled');
            customInput.removeAttribute('required');
            customInput.value = '';
        }

        const customLabel = document.getElementById('custom-label');
        if (select.value === 'custom') {
            customLabel.classList.remove('d-none');
        } else {
            customLabel.classList.add('d-none');
        }

        const Label = document.getElementById('label');
        if (select.value === 'custom') {
            Label.classList.add('d-none');
        } else {
            Label.classList.remove('d-none');
        }

        // Debug-Ausgabe
        console.log("Aktueller Wert:", select.value || "Keiner ausgewählt");
    }
</script>

</html>