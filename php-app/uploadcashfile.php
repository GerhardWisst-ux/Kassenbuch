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

$userid = $_SESSION['userid'] ?? null;
if (empty($userid) || !ctype_digit((string) $userid)) {
    http_response_code(401);
    exit('Nicht angemeldet.');
}

require 'db.php';

$errors = [];
$kassennummer = (int) ($_POST['kassennummer'] ?? 0);
$buchungsid = (int) ($_POST['buchungsid'] ?? 0);
$mandantennummer = $_SESSION['mandantennummer'];
$uploadDir = __DIR__ . '/uploads/cashfiles/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['ticketfile']) && $_FILES['ticketfile']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['ticketfile']['tmp_name'];
        $fileName = basename($_FILES['ticketfile']['name']);
        $fileSize = $_FILES['ticketfile']['size'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExtensions = ["pdf"];
        if (!in_array($fileExt, $allowedExtensions, true)) {
            $_SESSION['error_message'] = "Nur PDF-Dateien erlaubt.";
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        if ($fileSize > 2 * 1024 * 1024) {
            $_SESSION['error_message'] = "Dateigröße muss unter 2 MB liegen.";
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        $newFileName = 'CashFile_' . $kassennummer . '_' . $fileName;
        $destPath = $uploadDir . '/' . $newFileName; // ✅ Slash hinzugefügt

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $sql = "INSERT INTO cash_files (kassennummer, mandantennummer, buchungsid, userid, FilePath, UploadedAt) 
                    VALUES (:kassennummer, :mandantennummer, :buchungsid, :userid, :path, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'kassennummer' => $kassennummer,
                'mandantennummer' => $mandantennummer,
                'buchungsid' => $buchungsid,
                'userid' => $userid,
                'path' => 'uploads/cashfiles/' . $newFileName
            ]);

            $_SESSION['error_message'] = ['type'=>'success','text'=>'Datei erfolgreich hochgeladen.'];
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        } else {
            $_SESSION['error_message'] = ['type'=>'danger','text'=>'Fehler beim Verschieben der Datei.'];
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

    } else {
        $_SESSION['error_message'] = ['type'=>'danger','text'=>'Fehler beim Upload. Bitte erneut versuchen.'];
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
}
