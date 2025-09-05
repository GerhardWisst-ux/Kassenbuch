<?php
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:; form-action 'self'; base-uri 'self';");

session_set_cookie_params([
    'httponly' => true,
    'secure'   => true, // auf localhost ggf. false
    'samesite' => 'Strict'
]);
session_start();

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Nur POST erlaubt.');
}

// CSRF prüfen
if (!isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    http_response_code(403);
    exit('CSRF-Token ungültig.');
}

// Eingaben
$userid         = $_SESSION['userid'] ?? null;
$datumab        = $_POST['datumab'] ?? '';
$kontonummer    = $_POST['kontonummer'] ?? '';
$kasse          = $_POST['kasse'] ?? '';
$anfangsbestandRaw  = $_POST['anfangsbestand'] ?? '';
$checkminusRaw  = $_POST['checkminus'] ?? '';

// Validierung
if (empty($userid) || !ctype_digit((string)$userid)) {
    exit('Nicht angemeldet.');
}

// Datum validieren

$d = DateTime::createFromFormat('Y-m-d', $datumab);
if (!$d || $d->format('Y-m-d') !== $datumab) {
    exit('Ungültiges Datum-Ab.');
}

$datumDb = $d->format('Y-m-d');

// Anfängsbestand validieren
$anfangsbestand = is_numeric($anfangsbestandRaw) ? (float)$anfangsbestandRaw : 0;

// checkminus auf 0 oder 1
$checkminus = !empty($checkminusRaw) ? 1 : 0;

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        INSERT INTO kasse (datumab, checkminus, anfangsbestand, kasse, userid, kontonummer)
        VALUES (:datumab, :checkminus, :anfangsbestand, :kasse, :userid, :kontonummer)
    ");
    $stmt->execute([
        ':datumab'       => $datumDb,
        ':checkminus'    => $checkminus,
        ':anfangsbestand'=> $anfangsbestand,
        ':kasse'         => $kasse,
        ':kontonummer'   => $kontonummer,
        ':userid'        => (int)$userid
    ]);

    $pdo->commit();

    header('Location: Index.php', true, 303);
    exit();
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Buchung-Fehler: ' . $e->getMessage());
    http_response_code(500);
    exit('Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.');
}
