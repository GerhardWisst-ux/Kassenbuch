<?php
session_start();
$backupDir = __DIR__ . '/exports';

if (!is_dir($backupDir)) {
    echo "<p>Keine Backups gefunden.</p>";
    exit;
}

$files = glob($backupDir.'/Backup_Kassenbuch_*.sql');
if(!$files){ echo "<p>Keine Backups gefunden.</p>"; exit; }

usort($files, fn($a,$b)=>filemtime($b)-filemtime($a));
$latest = array_slice($files,0,10);

echo "<h5>Letzte Backups:</h5><ul>";
foreach($latest as $file){
    $name = basename($file);
    $url  = "exports/$name";
    echo "<li><a href='$url' download>$name</a> 
          (".date("d.m.Y H:i:s", filemtime($file)).", ".round(filesize($file)/1024,2)." KB)</li>";
}
echo "</ul>";
