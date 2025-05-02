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
    $einnahmen = $_POST['einnahmen'];
    $bestand = $_POST['bestand'];

    try {
        // Update-Statement
        $sql = "UPDATE bestaende 
                SET ausgaben = :ausgaben, 
                    einnahmen = :einnahmen,
                    bestand = :bestand,
                    datum = :datum,                     
                    userid = :userid 
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'ausgaben' => $ausgaben,
            'bestand' => $bestand,
            'einnahmen' => $einnahmen,
            'datum' => $datum,            
            'userid' => $userid,
        ]);

        header('Location: Bestaende.php');
        exit();
    } catch (PDOException $e) {
        echo "Fehler beim Aktualisieren: " . $e->getMessage();
        exit();
    }
}
?>
