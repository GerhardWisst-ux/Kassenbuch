<?php
declare(strict_types=1);

/*
 * Sicherheits-Header
 */
header('Content-Type: text/html; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: no-referrer-when-downgrade');
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:; form-action 'self'; base-uri 'self';");
header('Strict-Transport-Security: max-age=63072000; includeSubDomains; preload');
header('X-XSS-Protection: 0'); // Moderne Browser nutzen CSP
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');

/* HTTPS erzwingen */
// if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
//     http_response_code(403);
//     exit('Verbindung muss über HTTPS erfolgen.');
// }

/* Sichere Session-Cookies */
session_set_cookie_params([
    'httponly' => true,
    'secure' => true,
    'samesite' => 'Strict'
]);
session_start();

$userid = $_SESSION['userid'];
if (empty($userid) || !ctype_digit((string) $userid)) {
    http_response_code(401);
    exit('Nicht angemeldet.');
}

require 'db.php';

if (isset($_POST['delete'])) {
    $kassennummer = intval($_POST['kassennummer']); // Ticket-ID absichern
    $filePath = $_POST['FilePath'];

    // Basis-Upload-Ordner
    $uploadDir = getcwd() . '/uploads/cashfiles';

    // Datei-Name extrahieren und finalen Pfad bestimmen
    $fileName = basename($filePath);
    $fullPath = $uploadDir . '/' . $fileName;

    // DB und Pfad prüfen
    if ($kassennummer > 0 && !empty($fileName)) {

        // DB-Eintrag löschen mit TicketID UND FilePath (Sicherheitsfilter)
        $deletepath = 'uploads/cashfiles/' . $fileName; // Pfad muss zum Upload-Skript passen!

        $stmt = $pdo->prepare("
                    DELETE FROM cash_files 
                    WHERE filepath = :FilePath 
                    AND userid = :userid 
                    AND kassennummer = :kassennummer
                ");
        $stmt->bindValue(':FilePath', $deletepath, PDO::PARAM_STR);
        $stmt->bindValue(':kassennummer', $kassennummer, PDO::PARAM_INT);
        $stmt->bindValue(':userid', $userid, PDO::PARAM_INT);

        $stmt->execute();
        $rowsDeleted = $stmt->rowCount(); // wichtig!

        if ($rowsDeleted > 0 && file_exists($fullPath) && strpos(realpath($fullPath), realpath($uploadDir)) === 0) {
            if (unlink($fullPath)) {
                $_SESSION['error_message'] = ['type' => 'success', 'text' => 'Datei und Datenbankeintrag wurden gelöscht.'];
            } else {
                $_SESSION['error_message'] = ['type' => 'warning', 'text' => 'Datenbankeintrag gelöscht, aber Datei konnte nicht entfernt werden.'];
            }
        } elseif ($rowsDeleted > 0) {
            $_SESSION['error_message'] = ['type' => 'success', 'text' => 'Datenbankeintrag gelöscht. Datei war nicht vorhanden.'];
        } else {
            $_SESSION['error_message'] = ['type' => 'danger', 'text' => 'Datei oder Datenbankeintrag nicht gefunden.'];
        }
    }

    // Weiterleitung zurück zur Ticketseite
    if (!empty($_SERVER['HTTP_REFERER'])) {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    } else {
        header("Location: Buchungen.php");
    }
    exit;
}
?>