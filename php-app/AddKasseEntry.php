<?php
session_set_cookie_params([
    'httponly' => true,
    'secure' => true, // auf localhost ggf. false
    'samesite' => 'strict'
]);

session_start();

// Sicherheits-Header
header('Content-Type: text/html; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: no-referrer-when-downgrade');
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:; form-action 'self'; base-uri 'self';");
header('Strict-Transport-Security: max-age=63072000; includeSubDomains; preload');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');

require 'db.php';
require_once 'includes/bestaende_berechnen.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Nur POST erlaubt.');
}

// CSRF prüfen
if (
    !isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    http_response_code(403);
    exit('CSRF-Token ungültig.');
}

// Eingaben
$userid = $_SESSION['userid'] ?? null;
$datumab = $_POST['datumab'] ?? '';
$kontonummer = $_POST['kontonummer'] ?? '';
$kasse = $_POST['kasse'] ?? '';
$anfangsbestandRaw = $_POST['anfangsbestand'] ?? '0';
$checkminus = isset($_POST['checkminus']) ? 1 : 0;
$typ = $_POST['kunde_typ'] ?? 'privat'; // Standard: privat
$vorname = $_POST['vorname'] ?? '';
$nachname = $_POST['nachname'] ?? '';
$firma = $_POST['firma'] ?? '';

// Formulardaten sichern (für Redirect)
$_SESSION['form_data'] = [
    'kasse' => $kasse,
    'kontonummer' => $kontonummer,
    'anfangsbestand' => $anfangsbestandRaw,
    'datumab' => $datumab,
    'checkminus' => $checkminus,
    'typ' => $typ,
    'vorname' => $vorname,
    'nachname' => $nachname,
    'firma' => $firma
];

// Validierung User
if (empty($userid) || !ctype_digit((string) $userid)) {
    exit('Nicht angemeldet.');
}

// Validierung Kasse/Kontonummer
if (strlen($kasse) === 0) {
    $_SESSION['error_message'] = "Kasse muß angegeben werden!";
    header('Location: AddKasse.php', true, 303);
    exit;
} elseif (strlen($kasse) < 4) {
    $_SESSION['error_message'] = "Kasse zu kurz!";
    header('Location: AddKasse.php', true, 303);
    exit;
} elseif (strlen($kontonummer) < 4) {
    $_SESSION['error_message'] = "Kontonummer zu kurz!";
    header('Location: AddKasse.php', true, 303);
    exit;
} elseif (!preg_match('/^\d{1,8}$/', $kontonummer)) {
    $_SESSION['error_message'] = "Kontonummer ungültig!";
    header('Location: AddKasse.php', true, 303);
    exit;
}

// Validierung Kundentyp
if (!in_array($typ, ['privat', 'gewerblich'], true)) {
    $_SESSION['error_message'] = "Ungültiger Kundentyp!";
    header('Location: AddKasse.php', true, 303);
    exit;
}

// Pflichtfelder je nach Typ
if ($typ === 'privat' && (empty($vorname) || empty($nachname))) {
    $_SESSION['error_message'] = "Vor- und Nachname müssen für Privatkunden angegeben werden!";
    header('Location: AddKasse.php', true, 303);
    exit;
} elseif ($typ === 'gewerblich' && empty($firma)) {
    $_SESSION['error_message'] = "Firmenname muss für gewerbliche Kunden angegeben werden!";
    header('Location: AddKasse.php', true, 303);
    exit;
}

// Datum validieren
$d = DateTime::createFromFormat('Y-m-d', $datumab);

if (!$d || $d->format('Y-m-d') !== $datumab) {
    $_SESSION['error_message'] = "Ungültiges Datum!";
    header('Location: AddKasse.php', true, 303);
    exit;
}

// prüfen ob es der 1. des Monats ist
if ($d->format('d') !== '01') {
    $_SESSION['error_message'] = "Das Datum muss auf den 1. des Monats fallen!";
    header('Location: AddKasse.php', true, 303);
    exit;
}

$datumDb = $d->format('Y-m-d');
$jahr = $d->format('Y');

// Anfängsbestand validieren
$anfangsbestand = is_numeric($anfangsbestandRaw) ? (float) $anfangsbestandRaw : 0;

// checkminus auf 0/1
$checkminus = !empty($checkminusRaw) ? 1 : 0;

if ($buchungsarten) {
    $insert = $pdo->prepare("
        INSERT INTO buchungsarten 
            (kassennummer, Dauerbuchung, created_at, updated_at, userid, mandantennummer, buchungsart, mwst, mwst_ermaessigt, standard)
        VALUES 
            (:kassennummer, :Dauerbuchung, NOW(), NOW(), :userid, :mandantennummer, :buchungsart, :mwst, :mwst_ermaessigt, :standard)
    ");

    foreach ($buchungsarten as $b) {
        $insert->execute([
            'kassennummer' => $lastkasseid,
            'Dauerbuchung' => $b['Dauerbuchung'],
            'userid' => $_SESSION['userid'],
            'mandantennummer' => (int) $_SESSION['mandantennummer'],
            'buchungsart' => $b['buchungsart'],
            'mwst' => $b['mwst'],
            'mwst_ermaessigt' => $b['mwst_ermaessigt'],
            'standard' => $b['standard']
        ]);
    }

    // **Hier UPDATE ausführen**
    $stmtUpdateBA = $pdo->prepare("
        UPDATE buchungsarten
        SET mandantennummer = :mandantennummer,
            kassennummer = :kassennummer
        WHERE userid = :userid
          AND mandantennummer = 0
          AND kassennummer = 0
    ");

    $stmtUpdateBA->execute([
        ':mandantennummer' => (int) $_SESSION['mandantennummer'],
        ':kassennummer' => $lastkasseid,
        ':userid' => $_SESSION['userid']
    ]);
}

try {
    $pdo->beginTransaction();

    // Neue Kasse einfügen
    $stmt = $pdo->prepare("
        INSERT INTO kasse (
            kasse, typ, anfangsbestand, kontonummer, datumab, checkminus,
            userid, mandantennummer, archiviert, vorname, nachname, firma
        ) VALUES (
            :kasse, :typ, :anfangsbestand, :kontonummer, :datumab, :checkminus,
            :userid, :mandantennummer, :archiviert, :vorname, :nachname, :firma
        )
    ");
    $stmt->execute([
        ':kasse' => $kasse,
        ':typ' => $typ,
        ':anfangsbestand' => $anfangsbestand,
        ':kontonummer' => $kontonummer,
        ':datumab' => $datumDb,
        ':checkminus' => $checkminus,
        ':userid' => (int) $_SESSION['userid'],
        ':mandantennummer' => (int) $_SESSION['mandantennummer'],
        ':archiviert' => false,
        ':vorname' => $typ === 'privat' ? $vorname : null,
        ':nachname' => $typ === 'privat' ? $nachname : null,
        ':firma' => $typ === 'gewerblich' ? $firma : null
    ]);

    $lastkasseid = (int) $pdo->lastInsertId();
    if ($lastkasseid <= 0) {
        $pdo->rollBack();
        throw new Exception("Kassen-ID konnte nicht ermittelt werden!");
    }

    // Anfangsbestand in 'bestaende'
    $anfangsbestand = (float) $_POST['anfangsbestand'];
    $stmt = $pdo->prepare("
        INSERT INTO bestaende (kassennummer, datum, bestand)
        VALUES (:kassennummer, :datum, :bestand)
    ");
    $stmt->execute([
        'kassennummer' => $lastkasseid,
        'datum' => $_POST['datumab'],
        'bestand' => $anfangsbestand
    ]);

    // --- Standard-Buchungsarten kopieren ---
    $check = $pdo->prepare("SELECT COUNT(*) FROM buchungsarten WHERE userid = :userid");
    $check->execute(['userid' => $_SESSION['userid']]);
    $exists = (int) $check->fetchColumn();

    if ($exists === 0) {
        $stmtB = $pdo->prepare("
            SELECT Dauerbuchung, buchungsart, mwst, mwst_ermaessigt, standard
            FROM buchungsarten 
            WHERE userid = 1 AND standard = 1
        ");
        $stmtB->execute();
        $buchungsarten = $stmtB->fetchAll(PDO::FETCH_ASSOC);

        if ($buchungsarten) {
            $insert = $pdo->prepare("
                INSERT INTO buchungsarten 
                    (kassennummer, Dauerbuchung, created_at, updated_at, userid, mandantennummer, buchungsart, mwst, mwst_ermaessigt, standard)
                VALUES 
                    (:kassennummer, :Dauerbuchung, NOW(), NOW(), :userid, :mandantennummer, :buchungsart, :mwst, :mwst_ermaessigt, :standard)
            ");

            foreach ($buchungsarten as $b) {
                $insert->execute([
                    'kassennummer' => $lastkasseid,
                    'Dauerbuchung' => $b['Dauerbuchung'],
                    'userid' => $_SESSION['userid'],
                    'mandantennummer' => (int) $_SESSION['mandantennummer'],
                    'buchungsart' => $b['buchungsart'],
                    'mwst' => $b['mwst'],
                    'mwst_ermaessigt' => $b['mwst_ermaessigt'],
                    'standard' => $b['standard']
                ]);
            }
        }
    }

    // --- Sicherstellen, dass alle Buchungsarten jetzt Kassennummer & Mandantennummer haben ---
    $stmtUpdateBA = $pdo->prepare("
        UPDATE buchungsarten
        SET mandantennummer = :mandantennummer,
            kassennummer = :kassennummer
        WHERE userid = :userid
          AND (mandantennummer = 0 OR kassennummer = 0)
    ");
    $stmtUpdateBA->execute([
        ':mandantennummer' => (int) $_SESSION['mandantennummer'],
        ':kassennummer' => $lastkasseid,
        ':userid' => $_SESSION['userid']
    ]);

    // --- Bestände berechnen ---
    $jahr = (new DateTime($_POST['datumab']))->format('Y');
    $result = berechneBestaende($pdo, $_SESSION['userid'], $lastkasseid, $jahr, true);

    $pdo->commit();
    $_SESSION['success_message'] = "Kasse wurde angelegt!";
    header('Location: AddKasse.php');
    exit;

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['error_message'] = "Fehler beim Anlegen der Kasse: " . $e->getMessage();
    error_log('Kassen-Fehler: ' . $e->getMessage());
    http_response_code(500);
    exit('Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.');
}
