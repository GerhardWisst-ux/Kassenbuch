<?php
session_start();
require 'db.php';

if (empty($_SESSION['userid'])) {
    header('Location: Login.php');
    exit;
}

$userid = $_SESSION['userid'];

// Prüfen, ob ID und action gesetzt sind
$kassenId = $_GET['id'] ?? null;
$action = $_GET['action'] ?? null;

if (!$kassenId || !in_array($action, ['archivieren', 'aktivieren'])) {
    die('Ungültige Anfrage.');
}

// Aktion ausführen
if ($action === 'archivieren') {
    $stmt = $pdo->prepare("UPDATE kasse SET archiviert = 1 WHERE id = :id AND userid = :userid");
    $stmt->execute([':id' => $kassenId, ':userid' => $userid]);
    $message = "Kasse erfolgreich archiviert.";
} elseif ($action === 'aktivieren') {
    $stmt = $pdo->prepare("UPDATE kasse SET archiviert = 0 WHERE id = :id AND userid = :userid");
    $stmt->execute([':id' => $kassenId, ':userid' => $userid]);
    $message = "Kasse erfolgreich reaktiviert.";
}

// Zurück zur Übersicht mit Erfolgsmeldung
header("Location: Index.php?message=" . urlencode($message));
exit;
