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
    <title>CashControl Buchungsart hinzufügen</title>
    <link rel="icon" type="image/png" href="images/favicon.png" />
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
    $kassennummer = $_SESSION['kassennummer'] ?? null;

    if (!$userid) {
        header('Location: Login.php');
        exit();
    }
    require_once 'includes/header.php';

    // Buchungsarten laden
    $sql = "SELECT DISTINCT ID, buchungsart FROM buchungsarten WHERE userid = :userid ORDER BY Buchungsart";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['userid' => $userid]);
    $buchungsarten = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <header class="custom-header py-2 text-white">
        <div class="container-fluid">
            <?php
            $sql = "SELECT * FROM kasse WHERE userid = :userid AND id = :kassennummer";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'userid' => $userid,
                'kassennummer' => $kassennummer
            ]);

            $kasse = null;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $kasse = $row['kasse'];
            }
            ?>
            <div class="row align-items-center">
                <!-- Titel zentriert -->
                <div class="col-12 text-center mb-2 mb-md-0">
                    <h2 class="h2 mb-0"><?php echo htmlspecialchars($kasse); ?> - Buchung hinzufügen</h2>
                </div>
                <?php
                require_once 'includes/benutzerversion.php';
                ?>
            </div>
    </header>

    <div id="addbuchung" class="container-fluid mt-4">
        <form action="AddBuchungEntry.php" method="post" class="p-3 border rounded bg-light shadow-sm">
            <input type="hidden" name="csrf_token"
                value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

            <?php

            $stmtBestand = $pdo->prepare("
            SELECT bestand 
            FROM bestaende 
            WHERE kassennummer = :kassennummer 
              AND userid = :userid
            ORDER BY datum DESC
            LIMIT 1
        ");
            $stmtBestand->execute([
                ':kassennummer' => $kassennummer,
                ':userid' => $userid
            ]);
            $aktuellerBestand = $stmtBestand->fetchColumn();

            ?>
            <div class="form-group row me-4">
                <label class="col-sm-2 col-form-label text-dark">Aktueller Bestand:</label>
                <div class="col-sm-1">
                    <div class="input-group">
                        <input class="form-control" type="text" name="aktuellerbestand" value="<?= $aktuellerBestand ?>"
                            disabled>
                        <span class="input-group-text">€</span>
                    </div>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">Datum:</label>
                <div class="col-sm-4">
                    <input class="form-control" type="date" id="datum" name="datum" required>
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
                <label for="betrag" class="col-sm-2 col-form-label text-dark">Betrag:</label>
                <div class="col-sm-1">
                    <div class="input-group">
                        <input class="form-control" type="number" step="0.01" id="betrag" name="betrag" required
                            data-bestand="<?= $aktuellerBestand ?>">
                        <span class="input-group-text">€</span>
                    </div>
                    <small id="betragWarnung" class="text-danger fw-bold" style="display:none;">
                        Betrag überschreitet den aktuellen Bestand!
                    </small>
                </div>
            </div>

            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">Buchungsart:</label>
                <div class="col-sm-10">
                    <select class="form-control" id="buchungsarten-dropdown" name="buchungart_id">
                        <?php foreach ($buchungsarten as $row): ?>
                            <option value="<?= htmlspecialchars($row['ID'], ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($row['buchungsart'], ENT_QUOTES, 'UTF-8') ?>
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
                <a href="Buchungen.php" class="btn btn-primary"><i class="fa fa-arrow-left"></i></a>
            </div>
        </form>
    </div>
</body>


<!-- JS -->
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const betragInput = document.getElementById("betrag");
        const warnung = document.getElementById("betragWarnung");
        const bestand = parseFloat(betragInput.getAttribute("data-bestand"));
        const typ = document.getElementById("typ");

        betragInput.addEventListener("input", function () {
            const wert = parseFloat(this.value);
            if (!isNaN(wert) && wert > bestand && typ.value === "Ausgabe") {
                warnung.style.display = "block";
                betragInput.classList.add("is-invalid");
            } else {
                warnung.style.display = "none";
                betragInput.classList.remove("is-invalid");
            }
        });
    });

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

        // // Debug-Ausgabe
        // console.log("Aktueller Wert:", select.value || "Keiner ausgewählt");
    }
</script>

</html>