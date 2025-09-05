<?php
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:; form-action 'self'; base-uri 'self';");

session_set_cookie_params([
    'httponly' => true,
    'secure' => true,
    'samesite' => 'Strict'
]);
session_start();

require 'db.php';

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

// Eingaben
$userid = $_SESSION['userid'] ?? null;
$kassennummer = $_SESSION['kassennummer'] ?? 1;
$datum = $_POST['datum'] ?? '';
$typ = $_POST['typ'] ?? '';
$betragRaw = $_POST['betrag'] ?? '';
$buchungart_id = $_POST['buchungart_id'] ?? '';
$beschreibung = $_POST['beschreibung'] ?? '';
$customInput = $_POST['custom_buchungsart'] ?? '';

// Validierung
if (empty($userid) || !ctype_digit((string) $userid)) {
    exit('Nicht angemeldet.');
}

$d = DateTime::createFromFormat('Y-m-d', $datum);
if (!$d || $d->format('Y-m-d') !== $datum) {
    exit('Ungültiges Datum.');
}

$betragNorm = str_replace([','], ['.'], trim($betragRaw));
if (!is_numeric($betragNorm)) {
    exit('Ungültiger Betrag.');
}
$betrag = (float) $betragNorm;

$allowedTyp = ['Einlage', 'Ausgabe'];
if (!in_array($typ, $allowedTyp, true)) {
    exit('Ungültiger Typ.');
}

try {
    $pdo->beginTransaction();

    $stmtBestand = $pdo->prepare("
                                        SELECT bestand 
                                        FROM bestaende 
                                        WHERE kassennummer = :kassennummer 
                                        AND userid = :userid
                                        ORDER BY datum DESC
                                        LIMIT 1
                                    ");
    $stmtBestand->execute([
        ':kassennummer' => $kassennummer,
        ':userid' => $userid
    ]);

    $aktuellerBestand = (float) $stmtBestand->fetchColumn(); // <-- hier in float umwandeln

    // Betrag in float umwandeln
    $betrag = (float) $_POST['betrag'];

    // Checkminus abrufen
    $checkStmt = $pdo->prepare("SELECT Checkminus FROM kasse WHERE id = :kassennummer AND userid = :userid LIMIT 1");
    $checkStmt->execute([
        ':kassennummer' => $kassennummer,
        ':userid' => $userid
    ]);

    $checkminus = (int) ($checkStmt->fetchColumn() ?? 0);

    // Prüfen, ob Buchung möglich ist
    if ($typ === "Ausgabe" && $checkminus === 0 && $betrag > $aktuellerBestand) {
        $_SESSION['error_message'] = "Die Buchung ist nicht möglich: Betrag " . number_format($betrag, 2, ',', ' ') . " überschreitet den aktuellen Bestand von € "
            . number_format($aktuellerBestand, 2, ',', ' ');
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    if ($buchungart_id === 'custom' && $customInput !== '') {
        $vonan = $customInput;
    } else {
        if (!ctype_digit($buchungart_id)) {
            throw new RuntimeException('Ungültige Buchungsart-ID.');
        }
        $stmt = $pdo->prepare("SELECT Buchungsart FROM Buchungsarten WHERE id = :id AND userid = :userid");
        $stmt->execute([':id' => $buchungart_id, ':userid' => $userid]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            throw new RuntimeException('Buchungsart nicht gefunden.');
        }
        $vonan = $row['Buchungsart'];
    }

    $stmt = $pdo->prepare("
        INSERT INTO buchungen (datum, vonan, beschreibung, betrag, typ, userid, barkasse, buchungsart, kassennummer)
        VALUES (:datum, :vonan, :beschreibung, :betrag, :typ, :userid, 1, :buchungsart, :kassennummer)
    ");
    $stmt->execute([
        ':datum' => $datum,
        ':kassennummer' => $kassennummer,
        ':vonan' => $vonan,
        ':beschreibung' => $beschreibung,
        ':betrag' => $betrag,
        ':typ' => $typ,
        ':userid' => (int) $userid,
        ':buchungsart' => $vonan
    ]);

    $lastIdInt = (int) $pdo->lastInsertId();
    $numStr = str_pad((string) $lastIdInt, 4, '0', STR_PAD_LEFT);

    $stmtRe = $pdo->prepare("
        UPDATE buchungen
        SET belegnr = CONCAT('RE', YEAR(CURDATE()), '21-', :numStr)
        WHERE id = :id
    ");
    $stmtRe->execute([':numStr' => $numStr, ':id' => $lastIdInt]);

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
