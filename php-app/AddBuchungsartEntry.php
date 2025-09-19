<?php
declare(strict_types=1);

/* Sichere Session-Cookies (vor session_start) */
session_set_cookie_params([
    'httponly' => true,
    'secure' => true,     // nur unter HTTPS aktivieren
    'samesite' => 'Strict'
]);
session_start();

/*
 * Sicherheits-Header (früh senden)
 * Hinweis: Passe die CSP an, falls du externe Skripte/Styles brauchst.
 */
header('Content-Type: text/html; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header("Referrer-Policy: no-referrer-when-downgrade");
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:; form-action 'self'; base-uri 'self';");


/* DB laden (PDO im Exception-Modus empfohlen) */
require 'db.php';
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

/* Eingaben einlesen & normalisieren */
$buchungsart = isset($_POST['buchungsart']) ? trim((string) $_POST['buchungsart']) : '';
$mwst = isset($_POST['mwst']) ? trim((string) $_POST['mwst']) : '';
$dauerbuchung = !empty($_POST['dauerbuchung']) ? 1 : 0;
$mwst_ermaessigt = !empty($_POST['mwst_ermaessigt']) ? 1 : 0;
$kassennummer = $_SESSION['kassennummer'] ?? 1;

// Eingaben sichern, damit sie nach Redirect (POST-Redirect-GET) wieder da sind
$_SESSION['form_data'] = [
    'buchungsart' => $buchungsart,
    'dauerbuchung' => $dauerbuchung,
    'mwst' => $mwst,
    'mwst_ermaessigt' => $mwst_ermaessigt
];

/* Validierung: Buchungsart
   - Länge 1..64
   - Erlaubt: Buchstaben (inkl. Umlaute), Ziffern, Leerzeichen, - _ . /
   - Unicode-fähig
*/
if (strlen($buchungsart) == 0) {
    $_SESSION['error_message'] = "Buchungsart muß angegeben werden!";
    RedirectToAddBuchungsArt();
    exit;
} elseif (strlen($buchungsart) < 4) {
    $_SESSION['error_message'] = "Buchungsart zu kurz!";
    RedirectToAddBuchungsArt();
    exit;
} elseif (strlen($buchungsart) > 64) {
    $_SESSION['error_message'] = "Buchungsart zu lang (max. 64 Zeichen)!";
    RedirectToAddBuchungsArt();
    exit;
}

if (!preg_match('/^[\p{L}\p{N}\s\-\._\/]{1,64}$/u', $buchungsart)) {
    http_response_code(422);
    exit('Buchungsart enthält unzulässige Zeichen.');
}

print_r($_POST);

/* Datum für created/updated besser aus der DB (UTC) beziehen */
try {
    // Optional: Transaktion, falls du später mehr Logik ergänzen willst
    $pdo->beginTransaction();

    // Mandantenbezogene Duplikatsprüfung (pro userid)
    $check = $pdo->prepare(
        "SELECT COUNT(*) 
           FROM buchungsarten 
          WHERE buchungsart = :ba AND userid = :uid"
    );
    $check->execute([
        ':ba' => $buchungsart,
        ':uid' => (int) $userid
    ]);
    $exists = (int) $check->fetchColumn() > 0;

    if ($exists) {
        // Kein Echo mit Details – saubere UX per Redirect mit Status
        $pdo->rollBack();
        header('Location: Buchungsarten.php?exists=1', true, 303);
        exit;
    }

    $mwst_ermaessigt = $mwst_ermaessigt ? 1 : 0;   // Boolean als 1/0
    $mwst = (float) $mwst;                          // sicherstellen, dass DECIMAL/Float

    $stmt = $pdo->prepare("
    INSERT INTO buchungsarten 
    (buchungsart, Dauerbuchung, mwst, mwst_ermaessigt, created_at, updated_at, userid, kassennummer, mandantennummer)
    VALUES (:ba, :dauer, :mwst, :mwst_ermaessigt, UTC_DATE(), UTC_DATE(), :uid, :kassennummer, :mandantennummer)");
    $stmt->execute([
        ':ba' => $buchungsart,
        ':dauer' => $dauerbuchung,
        ':mwst' => $mwst,
        ':mwst_ermaessigt' => $mwst_ermaessigt,
        ':uid' => (int) $userid,
        ':kassennummer' => (int) $kassennummer,
        ':mandantennummer' => (int) $_SESSION['mandantennummer']

    ]);

    $pdo->commit();

    // CSRF-Token nach erfolgreichem POST optional rotieren
    unset($_SESSION['csrf_token']);

    // Post/Redirect/Get – Erfolg
    header('Location: Buchungsarten.php?success=1', true, 303);
    exit;
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    // Deduplizierung per Unique-Constraint abfangen (siehe Empfehlung unten)
    if ($e instanceof PDOException && $e->getCode() === '23000') {
        // Eindeutiger Konflikt (Duplicate Key)
        error_log('Duplicate buchungsart: ' . $buchungsart . ' user ' . $userid);
        header('Location: Buchungsarten.php?exists=1', true, 303);
        exit;
    }

    // Generisch loggen & generische Fehlermeldung
    error_log('Buchungsart-Insert-Fehler: ' . $e->getMessage());
    http_response_code(500);
    exit('Ein Fehler ist aufgetreten. Bitte später erneut versuchen.');
}
function RedirectToAddBuchungsArt()
{
    if (isset($_SERVER['HTTP_REFERER'])) {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    } else {
        header('Location: AddBuchungsart.php'); // Fallback, falls kein Referrer vorhanden
    }
    exit;
}