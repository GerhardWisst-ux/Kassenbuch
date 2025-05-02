<?php

require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userid = $_SESSION['userid'];
    $datum = $_POST['datum'];
    $vonan = isset($_POST['custom_vonan']) ? $_POST['custom_vonan'] : $_POST['vonan'];
    $beschreibung = htmlspecialchars($_POST['beschreibung'], ENT_QUOTES, 'UTF-8');
    $betrag = $_POST['betrag'];
    $typ = $_POST['typ'];

    try {
        $pdo->beginTransaction();

        // Einfügen in die Tabelle
        $sql = "INSERT INTO buchungen (datum, vonan, beschreibung, betrag, typ, userid) 
                VALUES (:datum, :vonan, :beschreibung, :betrag, :typ, :userid)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'datum' => $datum,
            'vonan' => $vonan,
            'beschreibung' => $beschreibung,
            'betrag' => $betrag,
            'typ' => $typ,
            'userid' => $userid,
        ]);

        $last_id = str_pad($pdo->lastInsertId(), 4, 0, STR_PAD_LEFT); 

        // Aktualisieren der Tabelle
        $sql = "UPDATE buchungen SET barkasse = 1 WHERE id = :last_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['last_id' => $last_id]);

        $sql = "UPDATE buchungen SET belegnr = CONCAT('RE', YEAR(CURDATE()), '21-', :last_id) WHERE id = :last_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['last_id' => $last_id]);

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
