<?php
declare(strict_types=1);

header('Content-Type: text/html; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header("Referrer-Policy: no-referrer-when-downgrade");
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:; form-action 'self'; base-uri 'self';");

session_set_cookie_params([
    'httponly' => true,
    'secure'   => true, // nur unter HTTPS aktivieren!
    'samesite' => 'Strict'
]);
session_start();

require 'db.php';
if (method_exists($pdo, 'setAttribute')) {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Nur POST erlaubt.');
}

/* CSRF prüfen */
if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    http_response_code(403);
    exit('CSRF-Token ungültig.');
}

/* Nutzerprüfung */
$userid = $_SESSION['userid'] ?? null;
if (empty($userid) || !ctype_digit((string)$userid)) {
    http_response_code(401);
    exit('Nicht angemeldet.');
}

$id = $_SESSION['id'] ?? null;
if (empty($id) || !ctype_digit((string)$id)) {
    http_response_code(400);
    exit('Ungültige ID.');
}

// Hilfsfunktion für sichere Filterung
function cleanString(?string $val): ?string {
    $val = trim((string)$val);
    return $val === '' ? null : $val;
}

try {
    $kasse          = cleanString($_POST['kasse'] ?? '');
    $kontonummer    = cleanString($_POST['kontonummer'] ?? '');
    $anfangsbestand = (float)($_POST['anfangsbestand'] ?? 0);
    $checkminus     = !empty($_POST['checkminus']) ? 1 : 0;
    $datumab        = cleanString($_POST['datumab'] ?? '');

    $kunde_typ = in_array($_POST['kunde_typ'] ?? '', ['privat', 'gewerblich'], true)
        ? $_POST['kunde_typ']
        : 'privat';

    $vorname = $kunde_typ === 'privat' ? cleanString($_POST['vorname'] ?? '') : null;
    $nachname = $kunde_typ === 'privat' ? cleanString($_POST['nachname'] ?? '') : null;
    $firma = $kunde_typ === 'gewerblich' ? cleanString($_POST['firma'] ?? '') : null;

    $sql = "UPDATE kasse
            SET kasse = :kasse,
                anfangsbestand = :anfangsbestand,
                checkminus = :checkminus,
                kontonummer = :kontonummer,
                datumab = :datumab,
                typ = :kunde_typ,
                vorname = :vorname,
                nachname = :nachname,
                firma = :firma
            WHERE id = :id
              AND userid = :userid";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'id'              => $id,
        'userid'          => $userid,
        'kasse'           => $kasse,
        'anfangsbestand'  => $anfangsbestand,
        'checkminus'      => $checkminus,
        'kontonummer'     => $kontonummer,
        'datumab'         => $datumab,
        'kunde_typ'       => $kunde_typ,
        'vorname'         => $vorname,
        'nachname'        => $nachname,
        'firma'           => $firma
    ]);

    $_SESSION['success_message'] = "Die Kasse wurde erfolgreich gespeichert.";

    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'EditKasse.php'));
    exit;
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Fehler beim Bearbeiten der Kasse: " . $e->getMessage();
    exit("Fehler beim Aktualisieren: " . $e->getMessage());
}
