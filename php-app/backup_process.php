<?php
session_start();

$host = '127.0.0.1';
$dbname = 'kassenbuch';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Datenbankverbindung fehlgeschlagen: " . $e->getMessage());
}

// Alle Tabellen holen
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

// Reihung: users, kasse, dann Rest alphabetisch
$priorityTables = ['users', 'kasse'];
$otherTables = array_diff($tables, $priorityTables);
sort($otherTables);
$tablesOrdered = array_merge($priorityTables, $otherTables);

$sqlDump = "-- Datenbank Kassenbuch --\n";
$sqlDump .= "-- Backup vom " . date("Y-m-d H:i:s") . "\n\n";
$sqlDump .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

// Gesamtzahl Zeilen
$totalRows = 0;
foreach ($tablesOrdered as $table) {
    $totalRows += $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
}
$processedRows = 0;

// Tabellen durchlaufen
foreach ($tablesOrdered as $table) {
    // Drop + Create
    $createTableStmt = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC);
    $sqlDump .= "DROP TABLE IF EXISTS `$table`;\n";
    $sqlDump .= $createTableStmt['Create Table'] . ";\n\n";

    // Daten exportieren
    $rowCount = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
    $chunkSize = 100;
    for ($offset = 0; $offset < $rowCount; $offset += $chunkSize) {
        $rows = $pdo->query("SELECT * FROM `$table` LIMIT $offset,$chunkSize")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $columns = array_map(fn($col) => "`$col`", array_keys($row));
            $values = array_map(fn($val) => $val === null ? "NULL" : $pdo->quote($val), array_values($row));
            $sqlDump .= "INSERT INTO `$table` (" . implode(",", $columns) . ") VALUES (" . implode(",", $values) . ");\n";
            $processedRows++;
        }
        $_SESSION['backup_progress'] = [
            'percent' => round(($processedRows / max(1,$totalRows)) * 100),
            'message' => "Backup läuft... Tabelle $table ($processedRows/$totalRows Datensätze)"
        ];
        flush();
    }
    $sqlDump .= "\n\n";
}

$sqlDump .= "SET FOREIGN_KEY_CHECKS=1;\n";

// Backup-Datei erstellen
$backupDir = __DIR__ . '/exports';
if (!is_dir($backupDir)) mkdir($backupDir, 0777, true);
$backupFile = $backupDir . "/Backup_Kassenbuch_" . date("Ymd_His") . ".sql";
file_put_contents($backupFile, $sqlDump);
$_SESSION['backup_file'] = $backupFile;
$_SESSION['backup_progress'] = ['percent'=>100,'message'=>'Backup abgeschlossen!'];

// Alte Backups auf 10 begrenzen
$files = glob($backupDir . '/Backup_Kassenbuch_*.sql');
if (count($files) > 10) {
    usort($files, fn($a, $b) => filemtime($a) - filemtime($b));
    $toDelete = array_slice($files, 0, count($files) - 10);
    foreach ($toDelete as $f) { if (file_exists($f)) unlink($f); }
}
