<?php
session_start();
require_once '../DB.php';

header('Content-Type: application/json; charset=utf-8');

function jsonResponse(array $data): void
{
    echo json_encode($data);
    exit;
}

// Login prüfen
if (empty($_SESSION['userid'])) {
    jsonResponse(['success' => false, 'message' => 'Nicht angemeldet!']);
}

// POST nur bei schreibenden Aktionen
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token'], $_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        jsonResponse(['success' => false, 'message' => 'Ungültiges CSRF-Token']);
    }
}

$action = $_REQUEST['action'] ?? null;

try {
    switch ($action) {
        case 'list':
            $stmt = $pdo->prepare("SELECT id, vorname, nachname, strasse, plz, ort, freigeschaltet, is_admin, gesperrt, created_at FROM users WHERE mandantennummer = ? ORDER BY id ASC");
            $stmt->execute([$mandantennummer]);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            jsonResponse(['success' => true, 'users' => $users]);

        case 'get':
            $id = (int) ($_GET['id'] ?? 0);
            // Mandantennummer aus der DB holen
            $stmt = $pdo->prepare("SELECT mandantennummer FROM users WHERE id=?");
            $stmt->execute([$id]);
            $mandantennummer = $stmt->fetchColumn();

            if (!$mandantennummer) {
                jsonResponse(['success' => false, 'message' => 'User nicht gefunden']);
            }

            // print($mandantennummer);
            // print($id);
            if (!$id)
                jsonResponse(['success' => false, 'message' => 'ID fehlt']);
            $stmt = $pdo->prepare("SELECT id, vorname, nachname, strasse, plz, ort, freigeschaltet, is_admin, gesperrt,mandantennummer FROM users WHERE id=? AND mandantennummer=?");
            $stmt->execute([$id, $mandantennummer]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            jsonResponse($user ? ['success' => true] + $user : ['success' => false, 'message' => 'User nicht gefunden']);

        case 'add':
            $mandantennummer = (int) ($_POST['mandantennummer'] ?? 0);
            if ($_SERVER['REQUEST_METHOD'] !== 'POST')
                jsonResponse(['success' => false, 'message' => 'Nur POST erlaubt']);

            $fields = ['vorname', 'nachname', 'strasse', 'plz', 'ort'];
            $data = [];
            foreach ($fields as $f)
                $data[$f] = trim($_POST[$f] ?? '');
            $data['freigeschaltet'] = (int) ($_POST['freigeschaltet'] ?? 0);
            $data['is_admin'] = (int) ($_POST['is_admin'] ?? 0);
            $data['gesperrt'] = (int) ($_POST['gesperrt'] ?? 0);

            $stmt = $pdo->prepare("INSERT INTO users (vorname, nachname, strasse, plz, ort, freigeschaltet, is_admin, gesperrt, mandantennummer, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $success = $stmt->execute(array_merge(array_values($data), [$mandantennummer]));

            if ($success) {
                $data['id'] = $pdo->lastInsertId();
                jsonResponse(['success' => true, 'user' => $data]);
            } else {
                jsonResponse(['success' => false, 'message' => 'Fehler beim Anlegen']);
            }


        case 'update':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(['success' => false, 'message' => 'Nur POST erlaubt']);
            }

            $id = (int) ($_POST['id'] ?? 0);
            if (!$id) {
                jsonResponse(['success' => false, 'message' => 'Keine ID übergeben']);
            }

            // Mandantennummer aus DB holen
            $stmt = $pdo->prepare("SELECT mandantennummer FROM users WHERE id=?");
            $stmt->execute([$id]);
            $mandantennummer = $stmt->fetchColumn();
            if (!$mandantennummer) {
                jsonResponse(['success' => false, 'message' => 'User nicht gefunden']);
            }

            // Normale Felder
            $vorname = trim($_POST['vorname'] ?? '');
            $nachname = trim($_POST['nachname'] ?? '');
            $strasse = trim($_POST['strasse'] ?? '');
            $plz = trim($_POST['plz'] ?? '');
            $ort = trim($_POST['ort'] ?? '');

            // BIT(1) Felder
            $freigeschaltet = (int) ($_POST['freigeschaltet'] ?? 0);
            $is_admin = (int) ($_POST['is_admin'] ?? 0);
            $gesperrt = (int) ($_POST['gesperrt'] ?? 0);

            // Update vorbereiten
            $stmt = $pdo->prepare("
                    UPDATE users 
                    SET vorname=?, nachname=?, strasse=?, plz=?, ort=?, freigeschaltet=?, is_admin=?, gesperrt=? 
                    WHERE id=? AND mandantennummer=?
                ");

            // Werte binden
            $stmt->bindValue(1, $vorname);
            $stmt->bindValue(2, $nachname);
            $stmt->bindValue(3, $strasse);
            $stmt->bindValue(4, $plz);
            $stmt->bindValue(5, $ort);
            $stmt->bindValue(6, $freigeschaltet, PDO::PARAM_INT);
            $stmt->bindValue(7, $is_admin, PDO::PARAM_INT);
            $stmt->bindValue(8, $gesperrt, PDO::PARAM_INT);
            $stmt->bindValue(9, $id, PDO::PARAM_INT);
            $stmt->bindValue(10, $mandantennummer, PDO::PARAM_INT);

            $success = $stmt->execute();
            $rowsAffected = $stmt->rowCount();

            if ($success && $rowsAffected > 0) {
                $_SESSION['success_message'] = "User #$id wurde erfolgreich upgedatet!";
                jsonResponse([
                    'success' => true,
                    'user' => [
                        'id' => $id,
                        'vorname' => $vorname,
                        'nachname' => $nachname,
                        'strasse' => $strasse,
                        'plz' => $plz,
                        'ort' => $ort,
                        'freigeschaltet' => $freigeschaltet,
                        'is_admin' => $is_admin,
                        'gesperrt' => $gesperrt,
                        'mandantennummer' => $mandantennummer
                    ]
                ]);
            } elseif ($success) {
                // Update erfolgreich, aber keine Änderung nötig
                $_SESSION['success_message'] = "User #$id wurde erfolgreich upgedatet!Keine Änderungen notwendig";
                header('Location: ../Users.php');
                exit;

            } else {
                $_SESSION['error_message'] = "Fehler beim Update von User #$id";
                header('Location: ../Users.php');
                exit;
            }


        case 'delete':
            $id = (int) ($_POST['id'] ?? 0);
            // Mandantennummer aus der DB holen
            $stmt = $pdo->prepare("SELECT mandantennummer FROM users WHERE id=?");
            $stmt->execute([$id]);
            $mandantennummer = $stmt->fetchColumn();

            if (!$mandantennummer) {
                jsonResponse(['success' => false, 'message' => 'User nicht gefunden']);
            }

            // Jetzt User löschen
            $stmt = $pdo->prepare("DELETE FROM users WHERE id=? AND mandantennummer=?");
            $stmt->execute([$id, $mandantennummer]);

            $rowsAffected = $stmt->rowCount();
            if ($rowsAffected > 0) {
                $_SESSION['success_message'] = "User #$id wurde gelöscht!";
                header('Location: ../Users.php');
                exit;
            } else {
                $_SESSION['error_message'] = "User #$id konnte nicht gelöscht werden!";
                header('Location: ../Users.php');
                exit;
            }

        default:
            jsonResponse(['success' => false, 'message' => 'Ungültige Aktion']);
    }
} catch (PDOException $e) {
    jsonResponse(['success' => false, 'message' => 'DB-Fehler: ' . $e->getMessage()]);
}
