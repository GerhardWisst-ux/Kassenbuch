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
header('X-XSS-Protection: 0');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');

/* Sichere Session-Cookies */
session_set_cookie_params([
    'httponly' => true,
    'secure' => true,
    'samesite' => 'Strict'
]);
session_start();

/* DB laden */
require 'db.php';
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    /* Nur POST zulassen */
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new RuntimeException('Ungültiger Aufruf – nur POST erlaubt.');
    }

    /* CSRF-Token prüfen */
    if (
        empty($_POST['csrf_token']) ||
        empty($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        throw new RuntimeException('CSRF-Token ungültig.');
    }

    /* Nutzerprüfung */
    $userid = $_SESSION['userid'] ?? null;
    if (empty($userid) || !ctype_digit((string) $userid)) {
        throw new RuntimeException('Nicht angemeldet.');
    }

    /* ID prüfen */
    if (empty($_POST['id']) || !ctype_digit((string) $_POST['id'])) {
        throw new RuntimeException('Ungültige ID.');
    }
    $id = (int) $_POST['id'];

    /* Löschaktion */
    $stmt = $pdo->prepare("DELETE FROM kasse WHERE id = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    /* Logging */
    $log = sprintf("[%s] User %d gelöscht id %d\n", date('c'), $userid, $id);
    file_put_contents(__DIR__ . '/delete.log', $log, FILE_APPEND);

    /* Erfolgsmeldung in Session */
    $_SESSION['success_message'] = "CashControl Kasse $id wurde gelöscht.";

    /* CSRF-Token nach Verwendung ungültig machen */
    unset($_SESSION['csrf_token']);

} catch (Throwable $e) {
    // Alle Fehler als Session-Error ablegen
    $_SESSION['error_message'] = $e->getMessage();
}

// Immer zurück zur Übersicht
header('Location: Index.php');
exit;
