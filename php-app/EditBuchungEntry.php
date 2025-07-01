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
   // $vonan = htmlspecialchars($_POST['vonan'], ENT_QUOTES, 'UTF-8');
    $beschreibung = htmlspecialchars($_POST['beschreibung'], ENT_QUOTES, 'UTF-8');
    $typ = htmlspecialchars($_POST['typ'], ENT_QUOTES, 'UTF-8');
    $datum = $_POST['datum'];
    $barkasse = filter_var(1, FILTER_VALIDATE_BOOLEAN);
    $betrag = $_POST['betrag'];
    $buchungart_id = $_POST['buchungart_id'];

    try {

         $sql = "select Buchungsart from Buchungsarten where id = :buchungsart";

        echo $sql;
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'buchungsart' => $buchungart_id,
        ]);

      
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $vonan = $row['Buchungsart'];
        }


        // Update-Statement
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
            'buchungart' => $buchungart_id,
            'betrag' => $betrag,
            'userid' => $userid,
        ]);

        echo "Position mit der ID" . $id . " wurde upgedatet!";
        header('Location: Index.php');
        exit();
    } catch (PDOException $e) {
        echo "Fehler beim Aktualisieren: " . $e->getMessage();
        exit();
    }
}
?>
