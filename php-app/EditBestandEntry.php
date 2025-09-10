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
    'secure'   => true,     // nur unter HTTPS aktivieren
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
if (empty($userid) || !ctype_digit((string)$userid)) {
    http_response_code(401);
    exit('Nicht angemeldet.');
}

if (!isset($_SESSION['id'])) {
    echo "Keine ID angegeben.";
    exit();
}

$id = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userid = $_SESSION['userid'];
    $datum = $_POST['datum'];
    $ausgaben = $_POST['ausgaben'];
    $einlagen = $_POST['einlagen'];
    $bestand = $_POST['einlagen'] - $_POST['ausgaben'];

    try {
        // Update-Statement
        $sql = "UPDATE bestaende 
                SET ausgaben = :ausgaben, 
                    einlagen = :einlagen,
                    bestand = :bestand,
                    datum = :datum,                     
                    userid = :userid 
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'ausgaben' => $ausgaben,
            'bestand' => $bestand,
            'einlagen' => $einlagen,
            'datum' => $datum,
            'userid' => $userid,
        ]);

        // Erfolgsmeldung setzen
        $_SESSION['success_message'] = "Der Bestand wurde erfolgreich gespeichert.";

        if (isset($_SERVER['HTTP_REFERER'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        } else {
            header('Location: Bestaende.php'); // Fallback, falls kein Referrer vorhanden
            exit;
        }
    } catch (PDOException $e) {
        echo "Fehler beim Aktualisieren: " . $e->getMessage();
        exit();
    }
}
?>