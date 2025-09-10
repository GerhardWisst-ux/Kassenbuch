<?php
session_start();

// Ordner sichern
$backupDir = __DIR__ . '/exports';
if (!is_dir($backupDir))
    mkdir($backupDir, 0777, true);

// Backups auflisten
function listBackups($dir)
{
    $files = glob($dir . '/Backup_Kassenbuch_*.sql');
    usort($files, function ($a, $b) {
        return filemtime($b) - filemtime($a);
    });
    $list = [];
    foreach ($files as $file) {
        $list[] = [
            'serverPath' => $file,
            'urlPath' => 'exports/' . basename($file),
            'name' => basename($file),
            'time' => filemtime($file),
            'size' => filesize($file)
        ];
    }
    return $list;
}

$backups = listBackups($backupDir);
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>Datensicherung Kassenbuch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
</head>

<body class="p-4">
    <div class="container">
        <h1 class="mb-4">Datensicherung Kassenbuch</h1>

        <!-- Backup-Button -->
        <button id="startBackup" class="btn btn-primary mb-3">Backup starten</button>

        <!-- Fortschrittsleiste -->
        <div class="progress mb-3" style="height: 30px; display:none;">
            <div class="progress-bar" role="progressbar" style="width:0%">0%</div>
        </div>
        <div id="status" class="mb-4"></div>
        <div id="backupLinks" class="mb-4"></div>

        <!-- Liste vorhandener Backups -->
        <h4>Vorhandene Backups:</h4>
        <?php if (count($backups) === 0): ?>
            <p>Keine Backups vorhanden.</p>
        <?php else: ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Dateiname</th>
                        <th>Datum / Uhrzeit</th>
                        <th>Größe</th>
                        <th>Aktion</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($backups as $backup): ?>
                        <tr>
                            <td><?= $backup['name'] ?></td>
                            <td><?= date("d.m.Y H:i:s", $backup['time']) ?></td>
                            <td><?= round($backup['size'] / 1024, 2) ?> KB</td>
                            <td><a href="<?= $backup['urlPath'] ?>" class="btn btn-sm btn-success" download>Download</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>


    <!-- JS -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>

    < <script>
        $(document).ready(function () {
        $("#startBackup").click(function () {
        $(".progress").show();
        $(".progress-bar").css("width", "0%").text("0%");
        $("#status").text("Backup läuft...");

        // Backup starten via AJAX
        $.post('backup_process.php', {}, function () { });

        // Fortschritt via SSE überwachen
        var es = new EventSource("backup_progress.php");
        es.onmessage = function (event) {
        var data = JSON.parse(event.data);
        $(".progress-bar").css("width", data.percent + "%").text(data.percent + "%");
        $("#status").text(data.message);

        if (data.percent >= 100) {
        es.close();
        $("#status").text("Backup abgeschlossen!");

        // Backup-Links laden und anzeigen
        $.get("list_backups.php", function (html) {
        $("#backupLinks").html(html);
        });
        }
        };
        });
        });
        </script>
</body>

</html>