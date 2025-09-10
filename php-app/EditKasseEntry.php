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

print_r($_POST);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userid = $_SESSION['userid'];
    $kasse = htmlspecialchars($_POST['kasse'], ENT_QUOTES, 'UTF-8');
    $kontonummer = htmlspecialchars($_POST['kontonummer'], ENT_QUOTES, 'UTF-8');
    $anfangsbestand = htmlspecialchars($_POST['anfangsbestand'], ENT_QUOTES, 'UTF-8');    
    $checkminus = filter_var($_POST['checkminus'], FILTER_VALIDATE_BOOLEAN);
    $datumab = htmlspecialchars($_POST['datumab'], ENT_QUOTES, 'UTF-8');

    try {
        // Update-Statement
        $sql = "UPDATE kasse 
                SET kasse = :kasse, 
                    anfangsbestand = :anfangsbestand, 
                    checkminus = :checkminus, 
                    kontonummer = :kontonummer, 
                    datumab = :datumab,                 
                    userid = :userid 
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'kasse' => $kasse,
            'anfangsbestand' => $anfangsbestand,
            'checkminus' => $checkminus,
            'kontonummer' => $kontonummer,
            'datumab' => $datumab,
            'userid' => $userid,
        ]);
        
        // Erfolgsmeldung setzen
        $_SESSION['success_message'] = "Die Kasse wurde erfolgreich gespeichert.";

        if (isset($_SERVER['HTTP_REFERER'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        } else {
            header('Location: Index.php'); // Fallback, falls kein Referrer vorhanden
            exit;
        }
    } catch (PDOException $e) {
        echo "Fehler beim Aktualisieren: " . $e->getMessage();
        exit();
    }
}
?>