<?php
session_start();
require_once 'DB.php';
require_once 'includes/bestaende_berechnen.php';

if (!isset($_SESSION['userid']) || !isset($_SESSION['mandantennummer'])) {
    die("Kein Zugriff!");
}

$userid = $_SESSION['userid'];
$mandantennummer = $_SESSION['mandantennummer'];
$jahr = date("Y");

// Alle Kassen holen
$stmt = $pdo->prepare("
    SELECT id, kasse 
    FROM kasse 
    WHERE mandantennummer = :mandantennummer
");
$stmt->execute(['mandantennummer' => $mandantennummer]);
$kassen = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php

require_once 'includes/header.php';
$email = $_SESSION['email'];
$userid = $_SESSION['userid'];
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Datensicherung für das Kassenbuch – einfache Verwaltung und sichere Backups.">
    <meta name="author" content="Gerhard Wißt">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" type="image/png" href="images/favicon.png" />
    <title>CashControl - Impressum</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <div class="wrapper">
        <header class="custom-header py-2 text-white">
            <div class="container-fluid">
                <div class="row align-items-center">

                    <!-- Titel zentriert -->
                    <div class="col-12 text-center mb-2 mb-md-0">
                        <h2 class="h4 mb-0">CashControl - Neu berechnen</h2>
                    </div>

                    <?php
                    require_once 'includes/benutzerversion.php';
                    ?>
                </div>
        </header>
        <div class="container-fluid mt-4">
            <div class="custom-container">
                <div class="row">
                    <?php
                    if (!$kassen) {
                        echo "<div class='col-12'><div class='alert alert-warning'>Keine Kassen gefunden.</div></div>";
                    } else {
                        foreach ($kassen as $kasse):
                            ?>
                            <div class="col-sm-6 col-md-4 col-lg-3 mb-3" id="kasse-card-<?= $kasse['id'] ?>">
                                <div class="card shadow-sm">
                                    <div class="card-header bg-primary text-white">
                                        <?= htmlspecialchars($kasse['kasse']) ?>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-1"><strong>Eingefügt:</strong> <span class="eingefuegt">-</span></p>
                                        <p class="mb-1"><strong>Aktualisiert:</strong> <span class="aktualisiert">-</span></p>
                                        <p class="mb-0"><strong>Endsaldo:</strong> <span class="saldo">-</span> €</p>
                                    </div>
                                    <div class="card-footer text-end">
                                        <button class="btn btn-success btn-sm btn-berechnen" data-kasseid="<?= $kasse['id'] ?>">
                                            <i class="fa-solid fa-sync"></i> Neuberechnen
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php
                        endforeach;
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>

<script>

    document.querySelectorAll('.btn-berechnen').forEach(button => {
        button.addEventListener('click', function () {
            const kassenId = this.dataset.kasseid;
            const card = document.getElementById('kasse-card-' + kassenId);
            const eingefuegtEl = card.querySelector('.eingefuegt');
            const aktualisiertEl = card.querySelector('.aktualisiert');
            const saldoEl = card.querySelector('.saldo');

            const btn = this; // Button sichern

            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Berechne...';

            fetch('includes/berechneBestaendeAjax.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'kassennummer=' + kassenId + '&jahr=' + new Date().getFullYear()
            })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    if (data.success) {
                        eingefuegtEl.textContent = data.eingefuegt;
                        aktualisiertEl.textContent = data.aktualisiert;
                        saldoEl.textContent = parseFloat(data.saldo).toFixed(2).replace('.', ',') + ' €';
                    } else {
                        alert('Fehler: ' + data.message);
                    }
                })
                .catch(err => alert('AJAX Fehler: ' + err))
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-sync"></i> Neuberechnen';
                });
        });
    });

</script>