<?php

require 'db.php';
session_start();

$userid = $_SESSION['userid'];
$monatFilter = isset($_GET['monat']) ? $_GET['monat'] : '';
$AB = 0;
$kassen_nummer = $userid;
$kassenbuch_datum = date("d.m.Y");
$lieferdatum = date("d.m.Y");
$pdfAuthor = "EDV Beratung Wißt";

setlocale(LC_TIME, 'de_DE.UTF-8');

if (!file_exists('Images/Cash.png')) {
    echo 'Fehler: Logo-Datei nicht gefunden.';
}


$kassenbuch_header = '
<img src="Images/Cash.png">
';

$kassenbuch_empfaenger = 'Firma: Testfirma GmbH, Im Neuen Berg 32, 70327 Stuttgart';
$kassenbuch_empfaenger = "<b>" . htmlspecialchars($kassenbuch_empfaenger, ENT_QUOTES, 'UTF-8') . "</b>";

// $kassenbuch_footer = "Dieses PDF Dokument wurde mit dem Kassenbuch erstellt";

// Wenn kein Monat ausgewählt wurde, alle Buchungen anzeigen
$startDatum = $monatFilter . "-01";
$endDatum = date("Y-m-t", strtotime($startDatum)); // Letzter Tag des Monats
$sql = "SELECT * FROM buchungen 
     WHERE datum BETWEEN :startDatum AND :endDatum 
     AND userid = :userid 
     AND barkasse = 1 
     ORDER BY datum";
$stmt = $pdo->prepare($sql);
$stmt->execute(['startDatum' => $startDatum, 'endDatum' => $endDatum, 'userid' => $userid]);

$umsatzsteuer = 0.0; 

$pdfName = "Kassenbuch_Auszug_".$kassen_nummer.".pdf";

// TCPDF Library laden
if (!file_exists('tcpdf/tcpdf.php')) {
    die('Fehler: TCPDF-Bibliothek nicht gefunden.');
}
require_once('tcpdf/tcpdf.php');

class MYPDF extends TCPDF {
    // Fußzeile überschreiben
    public function Footer() {
        // Position 15 mm vom unteren Rand
        $this->SetY(-15);
        // Schriftart für die Fußzeile
        $this->SetFont('helvetica', 'N', 8);
        
        
        $this->SetX(15); // Verschiebung nach rechts

        // Text für die Fußzeile
        $footerText = 'Dieses PDF Dokument wurde mit dem Kassenbuch erstellt';        

        // Text rechtsbündig ausgeben
        $this->Cell(0, 10, $footerText, 0, false, 'L', 0, '', 0, false, 'T', 'M');
    }
}

// Erstellung des PDF Dokuments
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Neues PDF-Dokument erstellen
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Dokumenteninformationen setzen

$pdf->Cell($pdf->SetCreator(PDF_CREATOR));

$pdf->SetAuthor($pdfAuthor);
$pdf->SetTitle('Kassenbuch Kassennummer' . $kassen_nummer);
$pdf->SetSubject('Kassenbuch ' . $kassen_nummer);

// Header-Daten setzen
$pdf->SetX(-15); // Verschiebung nach rechts
$pdf->SetHeaderData('', 0, '', 'Kassenbuch', array(0, 0, 0), array(0, 0, 0));

// Logo linksbündig setzen
$pdf->Image('', 0, PDF_MARGIN_TOP);

$pdf->SetXY(110, 200);
$pdf->Image('Images/Cash.png', '0', '200', 40, 40, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

// Header- und Footer-Schriftarten setzen
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Standard-Schriftart setzen
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Ränder setzen
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Automatischen Seitenumbruch aktivieren
$pdf->SetMargins(15, 20, 15); // Links, oben, rechts
$pdf->SetAutoPageBreak(TRUE, 25); // Untere Margin

// Bildskalierungsfaktor setzen
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Neue Seite hinzufügen
$pdf->AddPage();
// Header
$pdf->writeHTML("<table style='width: 100%;'><tr>
    <td>".nl2br(trim($kassenbuch_header))."</td>
    <td style='text-align: right'>Kassennummer: $kassen_nummer<br>Datum: $kassenbuch_datum<br></td>
</tr></table>", true, false, true, false, 'R');

$monat = isset($_GET['monat']) ? $_GET['monat'] : date('Y-m'); // Fallback zu aktuellem Monat
list($jahr, $monatNummer) = explode('-', $monat);

// Monat als Text bekommen
$monatName = strftime('%B', mktime(0, 0, 0, $monatNummer, 10)); // Übersetzung des Monatsnamens



// SQL-Abfrage zur Bestandsabfrage
$sql = "SELECT * FROM bestaende 
WHERE MONTH(datum) = :monat AND YEAR(datum) = :year AND userid = :userid 
ORDER BY datum DESC";
$stmtAB = $pdo->prepare($sql);
$stmtAB->execute(['year' => $jahr, 'monat' => $monatNummer, 'userid' => $userid]);

while ($row = $stmtAB->fetch(PDO::FETCH_ASSOC)) {
    // Anfangsbestand auslesen und formatieren
    $Anfangsbestand = $row['bestand'];
    $Anfangsbestand = "Anfangsbestand: " . number_format($Anfangsbestand, 2, '.', '.') . " €";
    $AB = $row['bestand'];
    
    // Anfangsbestand mit CSS für engeren Abstand
    $htmlAnfangsbestand = $Anfangsbestand;    
}

$html = <<<EOF
<div>
<b><span style='font-size:1.0em; font-weight: bold;'>Kassenbuch (Barkasse) Monat $monatNummer/$jahr</span></b><br>
<b>$kassenbuch_empfaenger</b><br><br>
$htmlAnfangsbestand
EOF;

// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');


// Tabelle mit TCPDF erstellen
$pdf->SetFont('helvetica', '', 10);
$pdf->Ln(); // Zeilenumbruch

// Kopfzeile erstellen
$html = "
<style>
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        border: 1px solid #000;
        padding: 5px;
        text-align: left;
        border: none; 
    }
    th {
        font-weight: bold;
        background-color: #f2f2f2;
    }
    td {
        vertical-align: top;
    }
    .right-align {
        text-align: right;
    }
</style>
<table>
    <thead>
        <tr>
            <th style='width: 10%;'><b>Datum</b></th>
            <th style='width: 10%;'><b>Typ</b></th>
            <th style='width: 50%;'><b>Beschreibung</b></th>
            <th style='width: 30%;'><b>Verwendungszweck</b></th>            
            <td style=\"text-align: right;\"><b>Betrag</b></td>
        </tr>
    </thead>
    <tbody>
";

// Detailzeilen hinzufügen
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $formattedDate = (new DateTime($row['datum']))->format('d.m.Y');
    $betragFormatted = number_format(abs($row['betrag']), 2, ',', '.') . " €";
    $html .= "
        <tr>
            <td>{$formattedDate}</td>
            <td>{$row['typ']}</td>            
            <td>{$row['vonan']}</td>
            <td>{$row['beschreibung']}</td>            
            <td style=\"text-align: right;\">{$betragFormatted}</td>
        </tr>
    ";  
}

// Tabelle abschließen
$html .= "</tbody></table>";

// Tabelle in das PDF schreiben
$pdf->writeHTML($html, true, false, true, false, '');

$sql = "SELECT SUM(CASE WHEN typ = 'Einlage' THEN betrag ELSE 0 END) AS einlagen,
SUM(CASE WHEN typ = 'Ausgabe' THEN betrag ELSE 0 END) AS ausgaben
FROM buchungen
WHERE Year(datum) = :year AND MONTH(datum) = :monat and userid = :userid and barkasse =1 ";
$stmt = $pdo->prepare($sql);
$stmt->execute(['year' => $jahr, 'monat' => intval($monatNummer), 'userid' => $userid]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

// Anfangsbestand berechnen und formatieren
$einlagen = number_format($result['einlagen'], 2, ',', '.') . " €";
$ausgaben = number_format($result['ausgaben'], 2, ',', '.') . " €";
$saldo = "<b>". number_format($AB + $result['einlagen'] - $result['ausgaben'], 2, ',', '.') . " € </b>";

// HTML-Tabellenstruktur
$html = "
<table border=\"0\" style=\"width: 100%; border-collapse: collapse;\">    
    <tr>
        <td style=\"font-weight: normal;\">Einlagen</td>
        <td style=\"text-align: right;\">$einlagen</td>
    </tr>
    <tr>
        <td style=\"font-weight: normal;\">Ausgaben</td>
        <td style=\"text-align: right;\">$ausgaben</td>
    </tr>
    <tr>
        <td style=\"font-weight: bold;\">Saldo</td>
        <td style=\"text-align: right;\">$saldo</td>
    </tr>
</table>
";

// Tabelle in das PDF schreiben
$pdf->writeHTML($html, true, false, true, false, '');



// PDF ausgeben
$pdf->Output($pdfName, 'I');

?>
