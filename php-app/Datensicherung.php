<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'includes/header.php';

// Backup-Verzeichnis
$backupDir = __DIR__ . '/exports';
if (!is_dir($backupDir))
    mkdir($backupDir, 0777, true);

// Funktion: Letzte 5 Backups holen
function getLatestBackups($dir)
{
    $files = glob($dir . '/Backup_Kassenbuch_*.sql');
    if (!$files)
        return [];
    usort($files, fn($a, $b) => filemtime($b) - filemtime($a));
    return array_slice($files, 0, 10);
}

$latestBackups = getLatestBackups($backupDir);
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>Datensicherung Kassenbuch</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Datensicherung für das Kassenbuch – einfache Verwaltung und sichere Backups.">
    <meta name="author" content="Gerhard Wißt">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" type="image/png" href="images/favicon.png" />
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="js/jquery.min.js"></script>
    <link href="css/style.css" rel="stylesheet">

    <style>
        .circle-btn {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            font-size: 14px;
            /* Icon-Größe */
        }
    </style>
</head>

<body>
    <header class="custom-header py-2 text-white">
        <div class="container-fluid">
            <div class="row align-items-center">
                <!-- Titel zentriert -->
                <div class="col-12 text-center mb-2 mb-md-0">
                    <h2 class="h4 mb-0">CashContol - Datensicherung</h2>
                </div>

                <?php
                require_once 'includes/benutzerversion.php';
                ?>
            </div>
    </header>
    <div class="container-fluid mt-3">

        <!-- Toolbar -->
        <div class="btn-toolbar mb-3" role="toolbar" aria-label="Toolbar">
            <!-- Backup starten -->
            <button id="startBackup" title="Backup starten" class="btn btn-primary rounded-circle me-2 circle-btn"><i class="fa fa-hdd"
                    aria-hidden="true"></i></button>
            <div class="ms-auto">
                <a href="help/Datensicherung.php" class="btn btn-outline-secondary btn-sm" title="Hilfe"><i
                        class="fa fa-question-circle"></i></a>
            </div>
        </div>

        <!-- Fortschrittsbalken -->
        <div class="progress mb-3" style="height: 30px; display:none;">
            <div class="progress-bar" role="progressbar" style="width:0%">0%</div>
        </div>

        <!-- Statusanzeige -->
        <div id="status" class="mb-3"></div>

        <!-- Letzte 5 Backups -->
        <div id="backupList">
            <h4>Letzte Backups:</h4>
            <?php if (empty($latestBackups)): ?>
                <p>Keine Backups vorhanden.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($latestBackups as $file): ?>
                        <li>
                            <a href="exports/<?= basename($file) ?>" download><?= basename($file) ?></a>
                            (<?= date("d.m.Y H:i:s", filemtime($file)) ?>, <?= round(filesize($file) / 1024, 2) ?> KB)
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <script>
        $(function () {
            $("#startBackup").click(function () {
                $(".progress").show();
                $(".progress-bar").css("width", "0%").text("0%");
                $("#status").text("Backup läuft...");

                // Backup starten
                $.post("backup_process.php");

                // Fortschritt überwachen per SSE
                var es = new EventSource("backup_progress.php");
                es.onmessage = function (event) {
                    var data = JSON.parse(event.data);
                    $(".progress-bar").css("width", data.percent + "%").text(data.percent + "%");
                    $("#status").text(data.message);

                    if (data.percent >= 100) {
                        es.close();
                        $("#status").text("Backup abgeschlossen!");

                        // Letzte Backups aktualisieren
                        $.get("list_backups.php", function (html) {
                            $("#backupList").html(html);
                        });
                    }
                };

                es.onerror = function () {
                    es.close();
                    $("#status").text("Fehler beim Abrufen des Fortschritts.");
                }
            });
        });
    </script>
</body>

</html>