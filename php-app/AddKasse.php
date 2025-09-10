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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Datensicherung für das Kassenbuch – einfache Verwaltung und sichere Backups.">
    <meta name="author" content="Dein Name oder Firma">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>CashControl Kasse hinzufügen</title>
    <link rel="icon" type="image/png" href="images/favicon.png" />
    <!-- CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="css/style.css" rel="stylesheet">

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
    $sql = "SELECT DISTINCT id, buchungsart FROM buchungsarten WHERE userid = :userid ORDER BY Buchungsart";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['userid' => $userid]);
    $buchungsarten = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (isset($_SESSION['success_message'])) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">'
            . htmlspecialchars($_SESSION['success_message']) .
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
         </div>';
        // Meldung nach einmaligem Anzeigen löschen        
        unset($_SESSION['success_message']);
    }
    if (!empty($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['error_message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Schließen"></button>
        </div>
        <?php
        // Meldung nach Anzeige löschen        
        unset($_SESSION['error_message']);
    endif; ?>

    <header class="custom-header py-2 text-white">
        <div class="container-fluid">
            <div class="row align-items-center">
                <!-- Titel zentriert -->
                <div class="col-12 text-center mb-2 mb-md-0">
                    <h2 class="h4 mb-0">CashControl - Kasse hinzufügen</h2>
                </div>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <title>CashControl Buchungsart bearbeiten</title>

                <link href="css/bootstrap.min.css" rel="stylesheet">
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
                <link href="css/style.css" rel="stylesheet">
            </div>
        </div>
    </header>

    <div id="addbuchung" class="container-fluid mt-4">
        <form action="AddKasseEntry.php" method="post" class="p-3 border rounded bg-light shadow-sm">
            <input type="hidden" name="csrf_token"
                value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

            <div class="mb-3 row">
                <label for="kasse" class="col-sm-2 col-form-label">Kasse:</label>
                <div class="col-sm-4">
                    <input class="form-control" type="text" id="kasse" name="kasse" required>
                </div>
            </div>

            <div class="mb-3 row">
                <label for="kontonummer" class="col-sm-2 col-form-label">Kontonummer:</label>
                <div class="col-sm-4">
                    <input class="form-control" type="text" id="kontonummer" name="kontonummer" required
                        pattern="\d{1,8}" title="Maximal 8 Ziffern" maxlength="8">
                </div>
            </div>

            <div class="mb-3 row">
                <label for="anfangsbestand" class="col-sm-2 col-form-label">Anfangsbestand:</label>
                <div class="col-sm-4">
                    <input class="form-control" type="number" id="anfangsbesatnd" name="anfangsbestand" step="0.01"
                        required>
                </div>
            </div>

            <div class="mb-3 row">
                <label for="datumab" class="col-sm-2 col-form-label">Anfangsbestand Datum:</label>
                <div class="col-sm-4">
                    <input class="form-control" type="date" id="datumab" name="datumab" required>
                </div>
            </div>

            <div class="mb-3 row">
                <label for="checkminus" class="col-sm-2 col-form-label">Kasse minus:</label>
                <div class="col-sm-10">
                    <input class="form-check-input" id="checkminus" type="checkbox" name="checkminus">
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

<script>
    // Heutiges Datum automatisch setzen
    document.addEventListener("DOMContentLoaded", function () {
        const today = new Date();
        const formattedDate = today.toISOString().split('T')[0];
        document.getElementById("datumab").value = formattedDate;
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

        // // Debug-Ausgabe
        // console.log("Aktueller Wert:", select.value || "Keiner ausgewählt");
    }
</script>

</html>