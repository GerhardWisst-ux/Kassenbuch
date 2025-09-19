<?php
declare(strict_types=1);

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
header('X-XSS-Protection: 0'); // Moderne Browser nutzen CSP
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');


/* DB laden (PDO im Exception-Modus empfohlen) */
require 'db.php';
require_once 'includes/bestaende_berechnen.php';
// Optional, falls noch nicht global gesetzt:
if (method_exists($pdo, 'setAttribute')) {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

/* Nur POST zulassen */
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
if (empty($userid) || !ctype_digit((string) $userid)) {
    http_response_code(401);
    exit('Nicht angemeldet.');
}


if (!isset($_SESSION['id'])) {
    echo "Keine ID angegeben.";
    exit();
}

// Validierung

$id = $_SESSION['id'];
$kassennummer = $_SESSION['kassennummer'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $userid = $_SESSION['userid'];
    $kassennummer = $_SESSION['kassennummer'] ?? 1;
    $beschreibung = htmlspecialchars($_POST['beschreibung'], ENT_QUOTES, 'UTF-8');
    $typ = htmlspecialchars($_POST['typ'], ENT_QUOTES, 'UTF-8');
    $datum = $_POST['datum'];
    $barkasse = 1; // 0 oder 1 in DB
    $betrag = $_POST['betrag'];
    $buchungart_id = $_POST['buchungart_id'];
    $mandantennummer = $_SESSION['mandantennummer'] ?? 1;

    // Eingaben sichern, damit sie nach Redirect (POST-Redirect-GET) wieder da sind
    $_SESSION['form_data'] = [
        'beschreibung' => $beschreibung,
        'betrag' => $betrag
    ];

    $d = DateTime::createFromFormat('Y-m-d', $datum);
    if (!$d || $d->format('Y-m-d') !== $datum) {
        $_SESSION['error_message'] = "Ungültiges Datum!";
        RedirectToEditBuchung();
        exit;
    }

    $betragNorm = str_replace([','], ['.'], trim($betrag));
    if (!is_numeric($betragNorm)) {
        $_SESSION['error_message'] = "Ungültiger Betrag!";
        RedirectToEditBuchung();
        exit;
    }
    $betrag = (float) $betragNorm;

    $allowedTyp = ['Einlage', 'Ausgabe'];
    if (!in_array($typ, $allowedTyp, true)) {
        $_SESSION['error_message'] = "Ungültiger Typ!";
        RedirectToEditBuchung();
        exit;
    }

    if (strlen($beschreibung) == 0) {
        $_SESSION['error_message'] = "Verwendungszweck muß angegeben werden!";
        RedirectToEditBuchung();
        exit;
    } elseif (strlen($beschreibung) < 4) {
        $_SESSION['error_message'] = "Verwendungszweck zu kurz!";
        RedirectToEditBuchung();
        exit;
    }

  try {
    $jahr = $d->format('Y');
    $pdo->beginTransaction();

    // Aktuellen Bestand abrufen
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
        ':mandantennummer' => $_SESSION['mandantennummer'],
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
        ':mandantennummer' => $_SESSION['mandantennummer'],
        ':userid' => $userid
    ]);
    $checkminus = (int) ($checkStmt->fetchColumn() ?? 0);

    // Prüfen, ob Buchung möglich ist
    $betrag = (float) $_POST['betrag'];
    if ($typ === "Ausgabe" && $checkminus === 0 && $betrag > $aktuellerBestand) {
        $_SESSION['error_message'] = "Die Buchung ist nicht möglich: Betrag "
            . number_format($betrag, 2, ',', ' ')
            . " überschreitet den aktuellen Bestand von € "
            . number_format($aktuellerBestand, 2, ',', ' ');
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    // Buchungsart abrufen
    $stmt = $pdo->prepare("SELECT buchungsart FROM buchungsarten WHERE id = :buchungart_id");
    $stmt->execute(['buchungart_id' => $buchungart_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $vonan = $row['buchungsart'] ?? '';

    // Buchung aktualisieren
    $stmt = $pdo->prepare("
        UPDATE buchungen 
        SET vonan = :vonan, 
            beschreibung = :beschreibung, 
            kassennummer = :kassennummer, 
            mandantennummer = :mandantennummer,
            typ = :typ, 
            datum = :datum,   
            barkasse = :barkasse,
            buchungsart = :buchungsart,
            betrag = :betrag,
            userid = :userid 
        WHERE id = :id
    ");
    $stmt->execute([
        'id' => $id,
        'vonan' => $vonan,
        'beschreibung' => $beschreibung,
        'kassennummer' => $kassennummer,
        'mandantennummer' => $_SESSION['mandantennummer'],
        'typ' => $typ,
        'datum' => $datum,
        'barkasse' => $barkasse,
        'buchungsart' => $vonan,
        'betrag' => $betrag,
        'userid' => $userid,
    ]);

    $pdo->commit(); // Transaktion erfolgreich abschließen

    // Bestände erst nach Commit berechnen
    $result = berechneBestaende($pdo, $userid, $kassennummer, $jahr, true);

    $_SESSION['success_message'] = "Buchung wurde gespeichert!";
    
    RedirectToEditBuchung();

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Fehler beim Aktualisieren der Buchung: " . $e->getMessage());
    $_SESSION['error_message'] = "Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.";
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

}
function RedirectToEditBuchung()
{
    if (isset($_SERVER['HTTP_REFERER'])) {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    } else {
        header('Location: EditBuchungBuchung.php'); // Fallback, falls kein Referrer vorhanden
    }
    exit;
}
?>