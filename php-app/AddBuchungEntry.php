<?php

require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // print_r($_POST);  

    $userid = $_SESSION['userid'];
    $datum = $_POST['datum'];
    $vonan = ""; //isset($_POST['custom_vonan']) ? $_POST['custom_vonan'] : $_POST['vonan'];
    $beschreibung = htmlspecialchars($_POST['beschreibung'], ENT_QUOTES, 'UTF-8');
    $betrag = $_POST['betrag'];
    $buchungart_id = $_POST['buchungart_id'];
    $typ = $_POST['typ'];


    try {
        $pdo->beginTransaction();
        
        $sql = "select Buchungsart from Buchungsarten where id = :buchungsart";

        echo $sql;
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'buchungsart' => $buchungart_id,
        ]);

      
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $vonan = $row['Buchungsart'];
        }
      
        // Einfügen in die Tabelle
        $sql = "INSERT INTO buchungen (datum, vonan, beschreibung, betrag, typ, userid,barkasse, buchungsart) 
                VALUES (:datum, :vonan, :beschreibung, :betrag, :typ, :userid, :barkasse, :buchungsart)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'datum' => $datum,
            'vonan' => $vonan,
            'beschreibung' => $beschreibung,
            'betrag' => $betrag,
            'typ' => $typ,
            'buchungsart' => $buchungart_id,
            'barkasse' => 1,
            'userid' => $userid,
        ]);

        $last_id = str_pad($pdo->lastInsertId(), 4, 0, STR_PAD_LEFT);

        $sql = "UPDATE buchungen SET belegnr = CONCAT('RE', YEAR(CURDATE()), '21-', :last_id) WHERE id = :last_id";
        $stmtReNr = $pdo->prepare($sql);
        $stmtReNr->execute(['last_id' => $last_id]);

        $pdo->commit();

        echo "Position hinzugefügt!";
        header('Location: Index.php');
        exit();
    } catch (PDOException $e) {
        $pdo->rollback();
        echo "Error!: " . $e->getMessage() . "</br>";
    }
}
?>