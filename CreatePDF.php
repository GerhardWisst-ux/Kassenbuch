<?php
 
require 'db.php';
session_start();

$userid = $_SESSION['userid'];

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
 
$kassenbuch_empfaenger = 'Firma: Barkasse GmbH, Im Neuen Berg 32, 70327 Stuttgart';

$kassenbuch_empfaenger = "<b>" . htmlspecialchars($kassenbuch_empfaenger, ENT_QUOTES, 'UTF-8') . "</b>";
 
$kassenbuch_footer = "Dieses PDF Dokument wurde mit dem Kassenbuch erstellt:
 
";
 
 // Wenn kein Monat ausgewählt wurde, alle Buchungen anzeigen
 $sql = "SELECT * FROM buchungen WHERE userid = :userid and barkasse =1 ORDER BY datum DESC";      
 $stmt = $pdo->prepare($sql);
 $stmt->execute(['userid' => $userid]);   

//Höhe eurer Umsatzsteuer. 0.19 für 19% Umsatzsteuer
$umsatzsteuer = 0.0; 
 
$pdfName = "Kassenbuch_Auszug_".$kassen_nummer.".pdf";
  
//////////////////////////// Inhalt des PDFs als HTML-Code \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 
 
// Erstellung des HTML-Codes. Dieser HTML-Code definiert das Aussehen eures PDFs.
// tcpdf unterstützt recht viele HTML-Befehle. Die Nutzung von CSS ist allerdings
// stark eingeschränkt.
 
$html = '
<table cellpadding="5" cellspacing="0" style="width: 100%; ">
	<tr>
	   <td>'.nl2br(trim($kassenbuch_header)).'</td>
	   <td style="text-align: right">
			Kassennummer: '.$kassen_nummer.'<br>
			Datum: '.$kassenbuch_datum.'<br>
			Auszugsdatum: '.$lieferdatum.'<br>
		</td>
	</tr>
 
	<tr>
		 <td style="font-size:1.3em; font-weight: bold;">
<br><br>
Kassenbuch(Barkasse)
<br>
		 </td>
	</tr>
 
 
	<tr>
		<td colspan="2">'.nl2br(trim($kassenbuch_empfaenger)).'</td>
	</tr>
</table>
<br><br><br>
 
<table cellpadding="5" cellspacing="0" style="width: 100%;" border="0">
    <tr style="background-color: #cccccc; padding:5px;">
        <td><b>Datum</b></td>
        <td style="text-align: left; width: 100px;"><b>VonAn</b></td>
        <td style="text-align: left; width: 300px;"><b>Beschreibung</b></td>
        <td style="text-align: right; width: 75px;"><b>Betrag in €</b></td>
    </tr>';

$gesamtpreis = 0;

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Formatierung des Datums
    $formattedDate = (new DateTime($row['datum']))->format('d.m.Y');
    
    // Formatierung des Betrags (mit Tausendertrennzeichen, 2 Dezimalstellen)
    $betragFormatted = number_format($row['betrag'], 2, ',', '.');

    // Erstelle die Zeile mit Tabelleninhalten
    $html .= "
    <tr>
        <td style='vertical-align: top; width: 100px;'>{$formattedDate}</td>
        <td style='vertical-align: top; width: 150px;'>{$row['vonan']}</td>
        <td style='vertical-align: top; width: 300px;'>{$row['beschreibung']}</td>
        <td style='vertical-align: top; text-align: right; width: 75px;white-space: nowrap;'>{$betragFormatted}</td>
    </tr>";
}

$html .= "</table>";
 
$html .= '
<hr>
<table cellpadding="5" cellspacing="0" style="width: 100%;" border="0"><br><br><br><br>';

$html .= nl2br($kassenbuch_footer); 
 
//////////////////////////// Erzeugung eures PDF Dokuments \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 
// TCPDF Library laden
if (!file_exists('tcpdf/tcpdf.php')) {
    die('Fehler: TCPDF-Bibliothek nicht gefunden.');
}
require_once('tcpdf/tcpdf.php');
 
// Erstellung des PDF Dokuments
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
 
// Dokumenteninformationen
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor($pdfAuthor);
$pdf->SetTitle('Kassenbuch '.$kassen_nummer);
$pdf->SetSubject('Kassenbuch '.$kassen_nummer); 

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "", "Kassenbuch", array(0,64,255), array(0,64,128));
$pdf->setFooterData(array(0,64,0), array(0,64,128));
// Header und Footer Informationen
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
 
// Auswahl des Font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
 
// Auswahl der MArgins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
 
// Automatisches Autobreak der Seiten
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
 
// Image Scale 
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
 
// Schriftart
$pdf->SetFont('helvetica', '', 10);  // Legt die Schriftart für den Text fest
 
// Neue Seite
$pdf->AddPage();


// Fügt den HTML Code in das PDF Dokument ein
try {
    $pdf->writeHTML($html, true, true, true, false, align: '');
} catch (Exception $e) {
    die('Fehler bei der PDF-Erstellung: ' . $e->getMessage());
}
 
//Ausgabe der PDF
 
//Variante 1: PDF direkt an den Benutzer senden:
$pdf->Output($pdfName, 'I');
 
//PDF-Dokument per E-Mail versenden
$pdfPfad = dirname(__FILE__).'/'.$pdfName;
$pdf->Output($pdfPfad, 'F');
 
$dateien = array($pdfPfad);
//mail_att("g.wisst@web.de", "Betreff", "Euer Nachrichtentext", "Absendername", "g.wisst@web.de", "g.wisst@web.de", $dateien);

//Variante 2: PDF im Verzeichnis abspeichern:
// $pdf->Output(dirname(__FILE__).'\\PDF\\'.$pdfName, 'F');
// echo 'PDF herunterladen: <a href="\\PDF\\'.$pdfName.'">'.$pdfName.'</a>';
 
?>


