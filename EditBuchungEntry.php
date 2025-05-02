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
    $vonan = htmlspecialchars($_POST['vonan'], ENT_QUOTES, 'UTF-8');
    $beschreibung = htmlspecialchars($_POST['beschreibung'], ENT_QUOTES, 'UTF-8');
    $typ = htmlspecialchars($_POST['typ'], ENT_QUOTES, 'UTF-8');
    $datum = $_POST['datum'];
    $barkasse = filter_var(1, FILTER_VALIDATE_BOOLEAN);
    $betrag = $_POST['betrag'];

    try {
        // Update-Statement
        $sql = "UPDATE buchungen 
                SET vonan = :vonan, 
                    beschreibung = :beschreibung, 
                    typ = :typ, 
                    datum = :datum,   
                    barkasse = :barkasse,
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
            'betrag' => $betrag,
            'userid' => $userid,
        ]);

        header('Location: Index.php');
        exit();
    } catch (PDOException $e) {
        echo "Fehler beim Aktualisieren: " . $e->getMessage();
        exit();
    }
}
?>
