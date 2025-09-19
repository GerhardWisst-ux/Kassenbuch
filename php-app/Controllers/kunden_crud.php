<?php
ob_start();            // Puffer starten, um alles zu sammeln
error_reporting(0);    // temporär PHP-Warnungen unterdrücken
session_start();
header('Content-Type: application/json; charset=utf-8');
session_set_cookie_params([
    'httponly' => true,
    'secure' => true,
    'samesite' => 'Strict'
]);

// Security-Header
header('Content-Type: text/html; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: no-referrer-when-downgrade');
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:; form-action 'self'; base-uri 'self';");
header('Strict-Transport-Security: max-age=63072000; includeSubDomains; preload');
header('X-XSS-Protection: 0');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');

require '../DB.php';

// CSRF-Check für POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token'], $_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        http_response_code(403);
        exit('CSRF-Token ungültig.');
    }
}

// JSON-Hilfsfunktion
function jsonResponse(array $data): void
{
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

try {
    $action = $_REQUEST['action'] ?? null;

    switch ($action) {
        case 'get':
            // Einzelnen Kunden holen
            $id = $_GET['id'] ?? null;
            if (!$id)
                jsonResponse(['success' => false, 'message' => 'ID fehlt']);

            $stmt = $pdo->prepare("
                SELECT id, kundennummer, typ, nachname, strasse, plz, ort, created_at, updated_at 
                FROM mandanten 
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            jsonResponse($row ? array_merge(['success' => true], $row) : ['success' => false, 'message' => 'Kunde nicht gefunden']);
            break;

        case 'add':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                exit('Nur POST erlaubt.');
            }
            if (!isset($_POST['csrf_token'], $_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                http_response_code(403);
                exit('Ungültiges CSRF-Token.');
            }

            $typ = trim($_POST['typ'] ?? '');
            $nachname = trim($_POST['nachname'] ?? '');
            $strasse = trim($_POST['strasse'] ?? '');
            $plz = trim($_POST['plz'] ?? '');
            $ort = trim($_POST['ort'] ?? '');
            $kundennummer = trim($_POST['kundennummer'] ?? '');


            $check = $pdo->prepare("
                                SELECT COUNT(*) 
                                FROM mandanten 
                                WHERE kundennummer = :kundennummer 
                                AND id = :id
                            ");

            $check->execute([
                ':kundennummer' => $kundennummer,
                ':id' => (int) $kundenid
            ]);

            $exists = $check->fetchColumn() > 0;

            if ($exists) {
                // Transaktion nur zurückrollen, wenn eine läuft
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }

                $_SESSION['success_message'] = "CashControl Kunde mit der Mandantennummer #" .
                    htmlspecialchars((string) $mandantennummer, ENT_QUOTES, 'UTF-8') .
                    " besteht schon!";

                header('Location: ../Mandanten.php');
                exit;
            }

            $stmt = $pdo->prepare("INSERT INTO mandanten (kundennummer, typ, nachname, strasse, plz, ort, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
            $success = $stmt->execute([$kundennummer, $typ, $nachname, $strasse, $plz, $ort]);

            if ($success) {
                $_SESSION['success_message'] = "Mandant #" . htmlspecialchars($id . "" . $nachname, ENT_QUOTES) . " wurde hinzugefügt!";
            } else {
                $_SESSION['error_message'] = "Fehler beim Update von Mandant #" . $id . "" . $nachname . htmlspecialchars($id, ENT_QUOTES);
            }

            header('Location: ../Mandanten.php');
            exit;

        case 'update':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                jsonResponse(['success' => false, 'message' => 'Nur POST erlaubt']);
            }

           
            $kundennummer = trim($_POST['kundennummer'] ?? '');
            $typ = trim($_POST['typ'] ?? '');
            $nachname = trim($_POST['nachname'] ?? '');
            $strasse = trim($_POST['strasse'] ?? '');
            $plz = trim($_POST['plz'] ?? '');
            $ort = trim($_POST['ort'] ?? '');


            $check = $pdo->prepare("
                                SELECT COUNT(*) 
                                FROM mandanten 
                                WHERE kundennummer = :kundennummer 
                                AND id = :id
                            ");

            $check->execute([
                ':kundennummer' => $kundennummer,
                ':id' => (int) $kundenid
            ]);

            $exists = $check->fetchColumn() > 0;

            if ($exists) {
                // Transaktion nur zurückrollen, wenn eine läuft
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }

                $_SESSION['success_message'] = "CashControl Kunde mit der Mandantennummer #" .
                    htmlspecialchars((string) $mandantennummer, ENT_QUOTES, 'UTF-8') .
                    " besteht schon!";

                header('Location: ../Mandanten.php');
                exit;
            }

            $id = $_POST['id'] ?? null;
            if (!$id) {
                jsonResponse(['success' => false, 'message' => 'Keine ID übergeben']);
            }

            $stmt = $pdo->prepare("
            UPDATE mandanten
            SET kundennummer = ?, 
                typ = ?, 
                nachname = ?, 
                strasse = ?, 
                plz = ?, 
                ort = ?, 
                updated_at = NOW()
            WHERE id = ?
            ");

            $success = $stmt->execute([$kundennummer, $typ, $nachname, $strasse, $plz, $ort, $id]);

            if ($success) {
                $_SESSION['success_message'] = "Mandant #" . htmlspecialchars($id . "" . $nachname, ENT_QUOTES) . " wurde upgedatet!";
            } else {
                $_SESSION['error_message'] = "Fehler beim Update von Mandant #" . htmlspecialchars($id . "" . $nachname, ENT_QUOTES);
            }
            // if ($success) {
            //     // Kein Redirect, sondern sauberes JSON zurückgeben
            //     jsonResponse([
            //         'success' => true,
            //         'message' => "Kunde #" . $pdo->lastInsertId() . " $nachname wurde hinzugefügt!",
            //         'id' => $pdo->lastInsertId(),
            //         'typ' => $typ,
            //         'nachname' => $nachname,
            //         'strasse' => $strasse,
            //         'plz' => $plz,
            //         'ort' => $ort
            //     ]);
            // } else {
            //     jsonResponse(['success' => false, 'message' => 'Fehler beim Hinzufügen']);
            // }
            header('Location: ../Mandanten.php');
            exit;

        case 'delete':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                exit('Nur POST erlaubt.');
            }

            $id = $_POST['id'] ?? null;
            if (!$id) {
                header('Location: ../Mandanten.php');
                ob_end_flush(); // Alles aus dem Puffer ausgeben (nur JSON)
                exit;
            }

            $stmt = $pdo->prepare("DELETE FROM mandanten WHERE id = ?");
            $success = $stmt->execute([$id]);

            if ($success) {
                $_SESSION['success_message'] = "Kunde #" . htmlspecialchars($id . "" . $nachname, ENT_QUOTES) . " wurde gelöscht!";
            } else {
                $_SESSION['error_message'] = "Fehler beim Löschen von Mandant #" . htmlspecialchars($id . "" . $nachname, ENT_QUOTES);
            }

            header('Location: ../Mandanten.php');
            exit;

        default:
            jsonResponse(['success' => false, 'message' => 'Ungültige Aktion']);
    }

} catch (PDOException $e) {
    $_SESSION['error_message'] = "Datenbankfehler: " . $e->getMessage();
    jsonResponse(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
}
