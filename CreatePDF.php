<?php

require 'db.php';
session_start();

$userid = $_SESSION['userid'];
$monatFilter = isset($_GET['monat']) ? $_GET['monat'] : '';

$kassen_nummer = $userid;
$kassenbuch_datum = date("d.m.Y");
$lieferdatum = date("d.m.Y");
$pdfAuthor = "EDV Beratung Wißt";

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
     ORDER BY datum DESC";
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
$pdf->SetTitle('Kassenbuch ' . $kassen_nummer);
$pdf->SetSubject('Kassenbuch ' . $kassen_nummer);

// Header-Daten setzen

$pdf->SetX(-15); // Verschiebung nach rechts
$pdf->SetHeaderData('', 0, '', 'Kassenbuch', array(0, 0, 0), array(0, 0, 0));

// Logo rechtsbündig setzen
$logoX = $pdf->getPageWidth() - PDF_HEADER_LOGO_WIDTH - PDF_MARGIN_RIGHT;
$pdf->Image('Images/Cash.png', $logoX, PDF_MARGIN_TOP, PDF_HEADER_LOGO_WIDTH);

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
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Bildskalierungsfaktor setzen
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Neue Seite hinzufügen
$pdf->AddPage();
// Header
$pdf->writeHTML("<table style='width: 100%;'><tr>
    <td>".nl2br(trim($kassenbuch_header))."</td>
    <td style='text-align: right'>Kassennummer: $kassen_nummer<br>Datum: $kassenbuch_datum<br>Auszugsdatum: $lieferdatum<br></td>
</tr></table>", true, false, true, false, 'R');

// Empfänger
$pdf->writeHTML("<b>".nl2br(trim($kassenbuch_empfaenger))."</b><br><br>", true, false, true, false, '');

// Titel
$pdf->writeHTML("<p style='font-size:1.3em; font-weight: bold;'>Kassenbuch (Barkasse)</p><br>", true, false, true, false, '');

// Tabelle mit TCPDF erstellen
$pdf->SetFont('helvetica', '', 10);
$pdf->Ln(); // Zeilenumbruch

// Kopfzeile
$pdf->SetFont('helvetica', 'B', 10); // Fett für Überschrift
$pdf->Cell(20, 8, 'Datum', 0, 0, 'L');
$pdf->Cell(65, 8, 'Beschreibung', 0, 0, 'L');
$pdf->Cell(50, 8, 'Verwendungszweck', 0, 0, 'L');
$pdf->Cell(20, 8, 'Typ', 0, 0, 'L');
$pdf->Cell(20, 8, 'Betrag in €', 0, 1, 'R');

$pdf->SetFont('helvetica', '', 10);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $formattedDate = (new DateTime($row['datum']))->format('d.m.Y');
    $betragNegativ = $row['betrag'] > 0 ? $row['betrag'] : -$row['betrag'];
    $betragFormatted = number_format($betragNegativ, 2, ',', '.');

    // Aktuelle Position speichern
    $x = $pdf->GetX();
    $y = $pdf->GetY();

    // Datum
    $pdf->Cell(20, 8, $formattedDate, 0, 0, 'L');

    // VonAn
    $pdf->Cell(65, 8, $row['beschreibung'], 0, 0, 'L');

    // Beschreibung mit MultiCell
    $pdf->SetXY($x + 85, $y); // X-Position entsprechend verschieben
    $pdf->MultiCell(50, 8, $row['vonan'], 0, 'L');

    // Typ (zur ursprünglichen Zeile zurückkehren)
    $pdf->SetXY($x + 135, $y); // Position für "Typ"
    $pdf->Cell(20, 8, $row['typ'], 0, 0, 'L');

    // Betrag
    $pdf->Cell(20, 8, $betragFormatted, 0, 1, 'R'); // 1 für Zeilenumbruch
}

// PDF ausgeben
$pdf->Output($pdfName, 'I');

?>
