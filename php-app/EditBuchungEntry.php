<?php
require 'db.php';
session_start();

// Prüfen, ob die ID gesetzt ist
if (!isset($_SESSION['id'])) {
    echo "Keine ID angegeben.";
    exit();
}

$id = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userid = $_SESSION['userid'];
    $beschreibung = htmlspecialchars($_POST['beschreibung'], ENT_QUOTES, 'UTF-8');
    $typ = htmlspecialchars($_POST['typ'], ENT_QUOTES, 'UTF-8');
    $datum = $_POST['datum'];
    $barkasse = 1; // 0 oder 1 in DB
    $betrag = $_POST['betrag'];
    $buchungart_id = $_POST['buchungart_id'];

    try {
        // 1️⃣ Buchungsart abrufen
        $sql = "SELECT Buchungsart FROM Buchungsarten WHERE id = :buchungart_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['buchungart_id' => $buchungart_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $vonan = $row['Buchungsart'];
        } else {
            $vonan = ''; // Falls keine Buchungsart gefunden
        }

        // 2️⃣ Update in die Buchungen
        $sql = "UPDATE buchungen 
                SET vonan = :vonan, 
                    beschreibung = :beschreibung, 
                    typ = :typ, 
                    datum = :datum,   
                    barkasse = :barkasse,
                    buchungsart = :buchungsart,
                    betrag = :betrag,
                    userid = :userid 
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'vonan' => $vonan,
            'beschreibung' => $beschreibung,
            'typ' => $typ,
            'datum' => $datum,
            'barkasse' => $barkasse,
            'buchungsart' => $buchungart_id,
            'betrag' => $betrag,
            'userid' => $userid,
        ]);

        // 3️⃣ Erfolgsmeldung setzen
        $_SESSION['success_message'] = "Die Buchung wurde erfolgreich gespeichert.";

        // 4️⃣ Auf Referrer oder Fallback weiterleiten
        if (isset($_SERVER['HTTP_REFERER'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        } else {
            header('Location: Buchungsarten.php'); // Fallback
            exit;
        }

    } catch (PDOException $e) {
        echo "Fehler beim Aktualisieren: " . $e->getMessage();
        exit();
    }
}
?>
