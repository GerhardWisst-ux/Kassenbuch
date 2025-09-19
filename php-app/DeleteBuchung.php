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
require_once 'includes/bestaende_berechnen.php';
// Optional, falls noch nicht global gesetzt:
if (method_exists($pdo, 'setAttribute')) {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

// echo $_SERVER['REQUEST_METHOD'];

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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $userid = $_SESSION['userid'] ?? null;
    try {
        $pdo->beginTransaction();

        // Zuerst die Buchung abrufen, um kassennummer und datum zu bekommen
        $stmt = $pdo->prepare("SELECT kassennummer, datum FROM buchungen WHERE id = :id AND userid = :userid");
        $stmt->execute([':id' => $id, ':userid' => $userid]);
        $buchung = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$buchung) {
            throw new RuntimeException("Buchung nicht gefunden oder Zugriff verweigert.");
        }

        $kassennummer = (int) $buchung['kassennummer'];
        $jahr = (int) (new DateTime($buchung['datum']))->format('Y');

        // Buchung löschen
        $stmtDel = $pdo->prepare("DELETE FROM buchungen WHERE id = :id AND userid = :userid");
        $stmtDel->execute([':id' => $id, ':userid' => $userid]);

        // Bestände neu berechnen
        $result = berechneBestaende($pdo, $userid, $kassennummer, $jahr, true);

        $pdo->commit();

        $_SESSION['success_message'] = "Buchung #{$id} gelöscht und Bestände aktualisiert!";
        header('Location: Buchungen.php');
        exit;

    } catch (Throwable $e) {
        if ($pdo->inTransaction())
            $pdo->rollBack();
        error_log("Fehler beim Löschen der Buchung ID $id: " . $e->getMessage());
        $_SESSION['error_message'] = "Fehler beim Löschen der Buchung. Bitte versuchen Sie es später.";
        header('Location: Buchungen.php');
        exit;
    }
} else {
    echo "Ungültige Anfrage.";
}

?>