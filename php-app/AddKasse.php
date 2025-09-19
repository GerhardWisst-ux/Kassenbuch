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


$formData = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']); // nur einmal anzeigen

?>

<!DOCTYPE html>
<html>

<head>
    <title>CashControl - Kasse hinzufügen</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Datensicherung für das Kassenbuch – einfache Verwaltung und sichere Backups.">
    <meta name="author" content="Gerhard Wißt">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
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
    endif;

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
            </div>
            <?php
            require_once 'includes/benutzerversion.php';
            ?>

        </div>
    </header>



    <div id="addkasse" class="container-fluid mt-4">
        <form action="AddKasseEntry.php" method="post" class="p-1 border rounded bg-light shadow-sm">

            <div class="d-flex justify-content-between align-items-center m-2">
                <!-- Linke Buttons -->
                <div>
                    <button class="btn btn-primary btn-sm rounded-circle me-2 circle-btn" type="submit"><i
                            class="fas fa-save"></i></button>

                    <a href="Index.php" title="Zurück zur Kassenübersicht"
                        class="btn btn-primary btn-sm rounded-circle me-2 circle-btn">
                        <i class="fa fa-arrow-left"></i>
                    </a>
                </div>

                <!-- Rechte Buttons -->
                <div>
                    <a href="help/Kasse.php" title="Hilfe" class="btn btn-primary btn-sm rounded-circle circle-btn">
                        <i class="fa fa-question-circle"></i>
                    </a>
                </div>
            </div>

            <input type="hidden" name="csrf_token"
                value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

            <div class="mb-3 row">
                <label for="kasse" class="col-sm-2 col-form-label">Kasse:</label>
                <div class="col-sm-4">
                    <input class="form-control" type="text" id="kasse" name="kasse"
                        value="<?= htmlspecialchars($formData['kasse'] ?? '') ?>">
                </div>
            </div>

            <div class="mb-3 row">
                <label for="kunde_typ" class="col-sm-2 col-form-label">Kundentyp:</label>
                <div class="col-sm-4">
                    <select class="form-select" id="kunde_typ" name="kunde_typ" onchange="toggleKundeFields(this)">
                        <option value="privat" <?= (isset($formData['kunde_typ']) && $formData['kunde_typ'] === 'privat') ? 'selected' : '' ?>>Privat</option>
                        <option value="gewerblich" <?= (isset($formData['kunde_typ']) && $formData['kunde_typ'] === 'gewerblich') ? 'selected' : '' ?>>Gewerblich</option>
                    </select>
                </div>
            </div>

            <!-- Privat: Vorname / Nachname -->
            <div id="privat-fields"
                class="<?= (isset($formData['kunde_typ']) && $formData['kunde_typ'] === 'gewerblich') ? 'd-none' : '' ?>">

                <div class="mb-3 row">
                    <label class="col-sm-2 col-form-label">Vorname:</label>
                    <div class="col-sm-4">
                        <input type="text" name="vorname" class="form-control"
                            value="<?= htmlspecialchars($formData['vorname'] ?? '') ?>">
                    </div>
                </div>

                <div class="mb-3 row">
                    <label class="col-sm-2 col-form-label">Nachname:</label>
                    <div class="col-sm-4">
                        <input type="text" name="nachname" class="form-control"
                            value="<?= htmlspecialchars($formData['nachname'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <!-- Gewerblich: Firmenname -->
            <div id="gewerblich-fields"
                class="mb-3 row <?= (isset($formData['kunde_typ']) && $formData['kunde_typ'] === 'gewerblich') ? '' : 'd-none' ?>">
                <label for="firma" class="col-sm-2 col-form-label">Firmenname:</label>
                <div class="col-sm-10">
                    <input type="text" name="firma" id="firma" class="form-control"
                        value="<?= htmlspecialchars($formData['firma'] ?? '') ?>">
                </div>
            </div>
            <div class="mb-3 row">
                <label for="kontenrahmen" class="col-sm-2 col-form-label">Kontenrahmen:</label>
                <div class="col-sm-4">
                    <select class="form-select" id="kontenrahmen" name="kontenrahmen" onchange="setKontonummer()">
                        <option value="">-- Bitte wählen --</option>
                        <option value="SKR03" <?= (isset($formData['kontenrahmen']) && $formData['kontenrahmen'] === 'SKR03') ? 'selected' : '' ?>>SKR03</option>
                        <option value="SKR04" <?= (isset($formData['kontenrahmen']) && $formData['kontenrahmen'] === 'SKR04') ? 'selected' : '' ?>>SKR04</option>
                    </select>
                </div>
            </div>

            <div class="mb-3 row">
                <label for="kontonummer" class="col-sm-2 col-form-label">Kontonummer:</label>
                <div class="col-sm-4">
                    <input class="form-control bg-light" type="text" id="kontonummer" name="kontonummer"
                        value="<?= htmlspecialchars($formData['kontonummer'] ?? '') ?>" readonly>
                </div>
            </div>

            <div class="mb-3 row">
                <label for="betrag" class="col-sm-2 col-form-label text-dark">Anfangsbestand:</label>
                <div class="col-sm-1">
                    <div class="input-group">
                        <input class="form-control" type="number" id="anfangsbestand" name="anfangsbestand" step="0.01"
                            value="<?= htmlspecialchars($formData['anfangsbestand'] ?? '') ?>">
                        <span class="input-group-text">€</span>
                    </div>
                    <small id="betragWarnung" class="text-danger fw-bold" style="display:none;">
                        Betrag überschreitet den aktuellen Bestand!
                    </small>
                </div>
            </div>

            <div class="mb-3 row">
                <label for="datumab" class="col-sm-2 col-form-label">Anfangsbestand Datum:</label>
                <div class="col-sm-4">
                    <input class="form-control" type="date" id="datumab" name="datumab"
                        value="<?= htmlspecialchars($formData['datumab'] ?? '') ?>">
                </div>
            </div>

            <!-- Checkbox sauber -->
            <div class="mb-3 row">
                <label for="checkminus" class="col-sm-2 col-form-label">Kasse minus:</label>
                <div class="col-sm-10">
                    <input class="form-check-input" id="checkminus" type="checkbox" name="checkminus" value="1"
                        <?= !empty($formData['checkminus']) ? 'checked' : '' ?>>
                </div>
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

        // Ersten Tag des Monats (lokal) setzen
        const firstOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);

        // yyyy-mm-dd im lokalen Kontext formatieren
        const year = firstOfMonth.getFullYear();
        const month = String(firstOfMonth.getMonth() + 1).padStart(2, '0');
        const day = String(firstOfMonth.getDate()).padStart(2, '0');

        const formattedDate = `${year}-${month}-${day}`;

        document.getElementById("datumab").value = formattedDate;

        setKontonummer();
    });

    function setKontonummer() {
        const rahmen = document.getElementById('kontenrahmen').value;
        const kontonummer = document.getElementById('kontonummer');

        if (rahmen === 'SKR03') {
            kontonummer.value = '1000';
        } else if (rahmen === 'SKR04') {
            kontonummer.value = '1600';
        } else {
            kontonummer.value = '';
        }
    }

    function NavBarClick() {
        const topnav = document.getElementById("myTopnav");
        if (topnav.className === "topnav") {
            topnav.className += " responsive";
        } else {
            topnav.className = "topnav";
        }
    }

    function toggleKundeFields(select) {
        const privatFields = document.getElementById('privat-fields');
        const gewerblichFields = document.getElementById('gewerblich-fields');

        if (select.value === 'privat') {
            privatFields.classList.remove('d-none');
            gewerblichFields.classList.add('d-none');
        } else if (select.value === 'gewerblich') {
            privatFields.classList.add('d-none');
            gewerblichFields.classList.remove('d-none');
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