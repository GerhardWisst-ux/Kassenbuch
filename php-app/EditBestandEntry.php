<?php
require 'db.php';
session_start();

if (!isset($_SESSION['id'])) {
    echo "Keine ID angegeben.";
    exit();
}

$id = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userid = $_SESSION['userid'];
    $datum = $_POST['datum'];
    $ausgaben = $_POST['ausgaben'];
    $einlagen = $_POST['einlagen'];
    $bestand = $_POST['einlagen'] - $_POST['ausgaben'];

    try {
        // Update-Statement
        $sql = "UPDATE bestaende 
                SET ausgaben = :ausgaben, 
                    einlagen = :einlagen,
                    bestand = :bestand,
                    datum = :datum,                     
                    userid = :userid 
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'ausgaben' => $ausgaben,
            'bestand' => $bestand,
            'einlagen' => $einlagen,
            'datum' => $datum,
            'userid' => $userid,
        ]);

        // Erfolgsmeldung setzen
        $_SESSION['success_message'] = "Der Bestand wurde erfolgreich gespeichert.";


        if (isset($_SERVER['HTTP_REFERER'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        } else {
            header('Location: Bestaende.php'); // Fallback, falls kein Referrer vorhanden
            exit;
        }
    } catch (PDOException $e) {
        echo "Fehler beim Aktualisieren: " . $e->getMessage();
        exit();
    }
}
?>