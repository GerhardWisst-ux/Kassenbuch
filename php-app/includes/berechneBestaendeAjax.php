<?php
session_start();
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . '/Cash/php-app/DB.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Cash/php-app/includes/bestaende_berechnen.php';

if (!isset($_SESSION['userid']) || !isset($_POST['kassennummer'])) {
    echo json_encode(['success' => false, 'message' => 'Kein Zugriff oder Kassennummer fehlt']);
    exit;
}

$userid = $_SESSION['userid'];
$mandantennummer = $_SESSION['mandantennummer'];
$kassennummer = (int) $_POST['kassennummer'];
$jahr = (int) ($_POST['jahr'] ?? date('Y'));

try {
    $result = berechneBestaende($pdo, $userid, $kassennummer, $jahr, false);

    echo json_encode([
        'success' => true,
        'eingefuegt' => $result['eingefuegt'],
        'aktualisiert' => $result['aktualisiert'],
        'saldo' => $result['saldo']
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
