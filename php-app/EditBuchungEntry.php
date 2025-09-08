<?php
declare(strict_types=1);

/*
 * Sicherheits-Header (früh senden)
 * Hinweis: Passe die CSP an, falls du externe Skripte/Styles brauchst.
 */
header('Content-Type: text/html; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header("Referrer-Policy: no-referrer-when-downgrade");
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:; form-action 'self'; base-uri 'self';");

/* Sichere Session-Cookies (vor session_start) */
session_set_cookie_params([
    'httponly' => true,
    'secure' => true,     // nur unter HTTPS aktivieren
    'samesite' => 'Strict'
]);
session_start();

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

// Prüfen, ob die ID gesetzt ist
if (!isset($_SESSION['id'])) {
    echo "Keine ID angegeben.";
    exit();
}

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

    try {

        // Checkminus aus DB holen
        $checkStmt = $pdo->prepare("SELECT Checkminus FROM kasse WHERE id = :kassennummer AND userid = :userid LIMIT 1");
        $checkStmt->execute(['kassennummer' => $kassennummer, 'userid' => $userid]);
        $checkminus = (int) ($checkStmt->fetchColumn() ?? 0); // Standard 0

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
        
        // Buchungsart abrufen
        $sql = "SELECT buchungsart FROM buchungsarten WHERE id = :buchungart_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['buchungart_id' => $buchungart_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $vonan = $row['Buchungsart'];
        } else {
            $vonan = ''; // Falls keine Buchungsart gefunden
        }

        // Update in die Buchungen
        $sql = "UPDATE buchungen 
                SET vonan = :vonan, 
                    beschreibung = :beschreibung, 
                    kassennummer = :kassennummer, 
                    typ = :typ, 
                    datum = :datum,   
                    barkasse = :barkasse,
                    buchungsart = :buchungsart,
                    betrag = :betrag,
                    userid = :userid 
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'vonan' => $vonan,
            'beschreibung' => $beschreibung,
            'kassennummer' => $kassennummer,
            'typ' => $typ,
            'datum' => $datum,
            'barkasse' => $barkasse,
            'buchungsart' => $vonan,
            'betrag' => $betrag,
            'userid' => $userid,
        ]);

        // Erfolgsmeldung setzen
        $_SESSION['success_message'] = "Die Buchung wurde erfolgreich gespeichert.";

        // Auf Referrer oder Fallback weiterleiten
        if (isset($_SERVER['HTTP_REFERER'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        } else {
            header('Location: Buchungsarten.php'); // Fallback
            exit;
        }

    } catch (PDOException $e) {
        echo "Fehler beim Aktualisieren: " . $e->getMessage();
        exit();
    }
}
?>