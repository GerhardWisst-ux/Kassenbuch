<?php
session_start();

if(!isset($_SESSION['backup_file']) || !file_exists($_SESSION['backup_file'])){
    die("Backup-Datei nicht gefunden.");
}

$backupFile = $_SESSION['backup_file'];

// Download starten
header('Content-Description: File Transfer');
header('Content-Type: application/sql');
header('Content-Disposition: attachment; filename=' . basename($backupFile));
header('Content-Length: ' . filesize($backupFile));
readfile($backupFile);

// Optional löschen
// unlink($backupFile);
exit;
