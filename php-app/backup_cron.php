<?php
// backup_cron.php
$backupDir = __DIR__ . '/exports';
if (!is_dir($backupDir)) mkdir($backupDir, 0777, true);

// DB-Zugang
$host = '127.0.0.1';
$db   = 'kassenbuch';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    file_put_contents(__DIR__.'/backup_error.log', date("Y-m-d H:i:s")." Fehler: ".$e->getMessage()."\n", FILE_APPEND);
    exit;
}

// Tabellen auslesen
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
$sqlDump = "";
foreach($tables as $table){
    $create = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC);
    $sqlDump .= $create['Create Table'].";\n\n";

    $rows = $pdo->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $row){
        $cols = array_map(fn($c)=>"`$c`", array_keys($row));
        $vals = array_map(fn($v)=> $v===null ? "NULL" : $pdo->quote($v), array_values($row));
        $sqlDump .= "INSERT INTO `$table` (".implode(",",$cols).") VALUES (".implode(",",$vals).");\n";
    }
    $sqlDump .= "\n\n";
}

// Backup-Datei speichern
$backupFile = $backupDir.'/Backup_Kassenbuch_'.date("Ymd_His").'.sql';
file_put_contents($backupFile, $sqlDump);

// Alte Backups löschen, nur 5 behalten
$files = glob($backupDir.'/Backup_Kassenbuch_*.sql');
usort($files, fn($a,$b)=>filemtime($b)-filemtime($a));
foreach(array_slice($files,10) as $old){ unlink($old); }
