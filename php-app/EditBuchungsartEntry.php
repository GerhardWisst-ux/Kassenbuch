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
    $buchungsart = htmlspecialchars($_POST['buchungsart'], ENT_QUOTES, 'UTF-8');
    $dauerbuchung = filter_var($_POST['dauerbuchung'], FILTER_VALIDATE_BOOLEAN);
    $updated_at = date('Y-m-d H:i:s');

    try {
        // Update-Statement
        $sql = "UPDATE buchungsarten 
                SET buchungsart = :buchungsart, 
                    dauerbuchung = :dauerbuchung, 
                    updated_at = :updated_at,                 
                    userid = :userid 
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'buchungsart' => $buchungsart,
            'dauerbuchung' => $dauerbuchung,
            'updated_at' => $updated_at,
            'userid' => $userid,
        ]);

        echo "Position mit der ID" . $id . " wurde upgedatet!";

        header('Location: Buchungsarten.php');
        exit();
    } catch (PDOException $e) {
        echo "Fehler beim Aktualisieren: " . $e->getMessage();
        exit();
    }
}
?>
