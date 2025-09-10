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

$id = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userid = $_SESSION['userid'];
    $buchungsart = htmlspecialchars($_POST['buchungsart'], ENT_QUOTES, 'UTF-8');
    $mwst = htmlspecialchars($_POST['mwst'], ENT_QUOTES, 'UTF-8');
    $dauerbuchung = filter_var($_POST['dauerbuchung'], FILTER_VALIDATE_BOOLEAN);
    $mwst_ermaessigt = filter_var($_POST['mwst_ermaessigt'], FILTER_VALIDATE_BOOLEAN);
    $updated_at = date('Y-m-d H:i:s');

    try {
        // Update-Statement
        $sql = "UPDATE buchungsarten 
                SET buchungsart = :buchungsart, 
                    mwst = :mwst, 
                    mwst_ermaessigt = :mwst_ermaessigt, 
                    dauerbuchung = :dauerbuchung, 
                    updated_at = :updated_at,                 
                    userid = :userid 
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'buchungsart' => $buchungsart,
            'mwst' => $mwst,
            'mwst_ermaessigt' => $mwst_ermaessigt,
            'dauerbuchung' => $dauerbuchung,
            'updated_at' => $updated_at,
            'userid' => $userid,
        ]);

        //echo "Position mit der ID" . $id . " wurde upgedatet!";
        // Erfolgsmeldung setzen
        $_SESSION['success_message'] = "Die Buchungsart wurde erfolgreich gespeichert.";

        if (isset($_SERVER['HTTP_REFERER'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        } else {
            header('Location: Buchungsarten.php'); // Fallback, falls kein Referrer vorhanden
            exit;
        }
    } catch (PDOException $e) {
        echo "Fehler beim Aktualisieren: " . $e->getMessage();
        $_SESSION['success_message'] = "Fehler beim Aktualisieren: " . $e->getMessage();
        exit();
    }
}
?>