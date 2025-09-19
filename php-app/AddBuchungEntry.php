<?php
session_set_cookie_params([
    'httponly' => true,
    'secure' => true,
    'samesite' => 'Strict'
]);
session_start();

header('Content-Type: text/html; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: no-referrer-when-downgrade');
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:; form-action 'self'; base-uri 'self';");
header('Strict-Transport-Security: max-age=63072000; includeSubDomains; preload');
header('X-XSS-Protection: 0');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');

require 'db.php';
require_once 'includes/bestaende_berechnen.php';
// Nur POST zulassen
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Nur POST erlaubt.');
}

// CSRF prüfen
if (
    !isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    http_response_code(403);
    exit('CSRF-Token ungültig.');
}

// Session-Werte
$userid = $_SESSION['userid'] ?? null;
$mandantennummer = $_SESSION['mandantennummer'] ?? null;
$kassennummer = $_SESSION['kassennummer'] ?? 1;

// POST-Werte
$datum = $_POST['datum'] ?? '';
$typ = $_POST['typ'] ?? '';
$betragRaw = $_POST['betrag'] ?? '';
$buchungart_id = $_POST['buchungart_id'] ?? '';
$beschreibung = $_POST['beschreibung'] ?? '';
$customInput = $_POST['custom_buchungsart'] ?? '';

// Eingaben zwischenspeichern
$_SESSION['form_data'] = [
    'beschreibung' => $beschreibung,
    'betrag' => $betragRaw
];

// Validierung
if (empty($userid) || !ctype_digit((string) $userid)) {
    $_SESSION['error_message'] = "Nicht angemeldet!";
    redirectBack();
}

if (empty($mandantennummer) || !ctype_digit((string) $mandantennummer)) {
    $_SESSION['error_message'] = "Mandant nicht gesetzt!";
    redirectBack();
}

$d = DateTime::createFromFormat('Y-m-d', $datum);
if (!$d || $d->format('Y-m-d') !== $datum) {
    $_SESSION['error_message'] = "Ungültiges Datum!";
    redirectBack();
}

// Betrag normalisieren
$betragNorm = str_replace(',', '.', trim($betragRaw));
if (!is_numeric($betragNorm)) {
    $_SESSION['error_message'] = "Ungültiger Betrag!";
    redirectBack();
}
$betrag = (float) $betragNorm;

$allowedTyp = ['Einlage', 'Ausgabe'];
if (!in_array($typ, $allowedTyp, true)) {
    $_SESSION['error_message'] = "Ungültiger Typ!";
    redirectBack();
}

if (strlen($beschreibung) < 4) {
    $_SESSION['error_message'] = "Verwendungszweck zu kurz!";
    redirectBack();
}

try {
    $jahr = $d->format('Y');

    $pdo->beginTransaction();

    // Aktueller Bestand
    $stmtBestand = $pdo->prepare("
        SELECT bestand 
        FROM bestaende 
        WHERE kassennummer = :kassennummer 
          AND mandantennummer = :mandantennummer
          AND userid = :userid
        ORDER BY datum DESC
        LIMIT 1
    ");
    $stmtBestand->execute([
        ':kassennummer' => $kassennummer,
        ':mandantennummer' => $mandantennummer,
        ':userid' => $userid
    ]);
    $aktuellerBestand = (float) ($stmtBestand->fetchColumn() ?? 0);

    // Checkminus abrufen
    $checkStmt = $pdo->prepare("
        SELECT Checkminus 
        FROM kasse 
        WHERE id = :kassennummer 
          AND mandantennummer = :mandantennummer 
          AND userid = :userid
        LIMIT 1
    ");
    $checkStmt->execute([
        ':kassennummer' => $kassennummer,
        ':mandantennummer' => $mandantennummer,
        ':userid' => $userid
    ]);
    $checkminus = (int) ($checkStmt->fetchColumn() ?? 0);

    // Prüfen, ob Buchung möglich ist
    if ($typ === "Ausgabe" && $checkminus === 0 && $betrag > $aktuellerBestand) {
        $_SESSION['error_message'] = "Die Buchung ist nicht möglich: Betrag "
            . number_format($betrag, 2, ',', ' ') . " überschreitet den aktuellen Bestand von € "
            . number_format($aktuellerBestand, 2, ',', ' ');
        redirectBack();
    }

    // Buchungsart bestimmen
    if ($buchungart_id === 'custom' && $customInput !== '') {
        $vonan = $customInput;
        $buchungsart = null;
    } else {
        if (!ctype_digit($buchungart_id)) {
            throw new RuntimeException('Ungültige Buchungsart-ID.');
        }
        $stmt = $pdo->prepare("
            SELECT buchungsart 
            FROM buchungsarten 
            WHERE id = :id 
              AND userid = :userid
        ");
        $stmt->execute([':id' => $buchungart_id, ':userid' => $userid]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row)
            throw new RuntimeException('Buchungsart nicht gefunden.');
        $vonan = $row['buchungsart'];
        $buchungsart = (int) $buchungart_id;
    }

    // Buchung einfügen
    $stmt = $pdo->prepare("
        INSERT INTO buchungen 
        (datum, vonan, beschreibung, betrag, typ, userid, barkasse, buchungsart, kassennummer, mandantennummer)
        VALUES 
        (:datum, :vonan, :beschreibung, :betrag, :typ, :userid, 1, :buchungsart, :kassennummer, :mandantennummer)
    ");
    $stmt->execute([
        ':datum' => $datum,
        ':vonan' => $vonan,
        ':beschreibung' => $beschreibung,
        ':betrag' => $betrag,
        ':typ' => $typ,
        ':userid' => $userid,
        ':buchungsart' => $vonan,
        ':kassennummer' => $kassennummer,
        ':mandantennummer' => $mandantennummer
    ]);

    $stmtRe = $pdo->prepare("        
        UPDATE buchungen
                SET belegnr = CONCAT('RE', YEAR(CURDATE()), LPAD(:userid, 4, '0'), :numStr)
                WHERE id = :id        
    ");
    $stmtRe->execute([':numStr' => $numStr, 'userid' => $userid, ':id' => $lastIdInt]);

    $lastId = (int) $pdo->lastInsertId();
    $numStr = str_pad((string) $lastId, 4, '0', STR_PAD_LEFT);

    var_dump($userid, $kassennummer, $jahr, true);

    $pdo->commit();
    $result = berechneBestaende($pdo, $userid, $kassennummer, $jahr, true);
    $_SESSION['success_message'] = "Buchung wurde gespeichert!";
    redirectBack();

} catch (Throwable $e) {
    if ($pdo->inTransaction())
        $pdo->rollBack();
    error_log('Buchung-Fehler: ' . $e->getMessage());
    var_dump($e->getMessage());
    http_response_code(500);
    exit('Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.');
}

// Funktion für Redirect
function redirectBack()
{
    if (!empty($_SERVER['HTTP_REFERER'])) {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    } else {
        header('Location: AddBuchung.php');
    }
    exit;
}
