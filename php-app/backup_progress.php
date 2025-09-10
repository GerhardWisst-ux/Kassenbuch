<?php
session_start();
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

$progress = $_SESSION['backup_progress'] ?? ['percent'=>0,'message'=>'Backup startet...'];
echo "data: " . json_encode($progress) . "\n\n";
flush();
