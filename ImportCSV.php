<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file']['tmp_name'];

    if (($handle = fopen($file, 'r')) !== false) {
        // Überspringe die Kopfzeile
        fgetcsv($handle);

        while (($data = fgetcsv($handle, 1000, ';')) !== false) {
            $datum = $data[0];
            $vonan = $data[1];
            $beschreibung = $data[2];
            $betrag = $data[3];
            $typ = $data[4];
            $userid = $data[5];

            // Daten in die Tabelle einfügen
            $sql = "INSERT INTO buchungen (datum, vonan, beschreibung, betrag, typ, userid) VALUES (:datum, :vonan, :beschreibung, :betrag, :typ, :userid)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'datum' => $datum,
                'vonan' => $vonan,
                'beschreibung' => $beschreibung,
                'betrag' => $betrag,
                'typ' => $typ,
                'userid' => $userid
            ]);
        }

        fclose($handle);
        echo "Daten erfolgreich importiert!";
    } else {
        echo "Fehler beim Lesen der Datei.";
    }
}
?>
