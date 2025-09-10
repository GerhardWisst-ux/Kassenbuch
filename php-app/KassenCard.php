<?php
$kassenId = $row['id'];
$formattedDate = (new DateTime($row['datumab']))->format('d.m.Y');
$anfangsbestand = (float) $row['anfangsbestand'];
$anfangsbestandFormatted = number_format($anfangsbestand, 2, ',', '.') . ' €';

// Letzten Bestand ermitteln
$stmtBestand = $pdo->prepare("
    SELECT bestand 
    FROM bestaende 
    WHERE kassennummer = :kassennummer 
    AND userid = :userid
    ORDER BY datum DESC
    LIMIT 1
");
$stmtBestand->execute([
    ':kassennummer' => $kassenId,
    ':userid' => $userid
]);
$aktuellerBestand = $stmtBestand->fetchColumn();
$aktuellerBestandFormatted = $aktuellerBestand !== false
    ? number_format((float) trim($aktuellerBestand), 2, ',', '.') . ' €'
    : '-';

// Badge für Kasse minus
$checkminusBadge = $row['checkminus'] == 1
    ? '<span class="badge bg-danger" title="Kasse darf ins Minus">Ja</span>'
    : '<span class="badge bg-success" title="Kasse darf nicht ins Minus">Nein</span>';

// Header-Farbe
if ($aktuellerBestand >= 200) {
    $headerClass = 'bg-success text-white';
} elseif ($aktuellerBestand >= 100) {
    $headerClass = 'bg-warning text-dark';
} else {
    $headerClass = 'bg-danger text-white';
}

// Label kritisch
$kritischLabel = $aktuellerBestand < 100
    ? '<span class="position-absolute top-0 end-0 m-2 px-2 py-1 bg-danger text-white rounded-pill small">KRITISCH</span>'
    : '';

// Archiv-Button
// Archiv-Button mit Action-Parameter
$archiveButton = $row['archiviert'] == 0
    ? "<a href='ArchiveKasse.php?id={$row['id']}&action=archivieren' class='btn btn-warning btn-sm me-2' title='Kasse archivieren'><i class='fa-solid fa-box-archive'></i></a>"
    : "<a href='ArchiveKasse.php?id={$row['id']}&action=aktivieren' class='btn btn-success btn-sm me-2' title='Kasse reaktivieren'><i class='fa-solid fa-rotate-left'></i></a>";


echo "
<div class='col-sm-6 col-md-4 col-lg-3 position-relative'>
    <div class='card shadow-sm d-flex h-100 flex-column card-hover'>
        {$kritischLabel}
        <div class='card-header {$headerClass} py-2 px-3'>
            <h6 class='mb-0'>{$row['kasse']}</h6>
        </div>
        <div class='card-body py-2 px-3 flex-grow-1'>
            <p class='card-text mb-1'><strong>Kontonummer:</strong> {$row['kontonummer']}</p>
            <p class='card-text mb-1'><strong>Datum ab:</strong> {$formattedDate}</p>
            <p class='card-text mb-1'><strong>Anfangsbestand:</strong> {$anfangsbestandFormatted}</p>
            <p class='card-text mb-1'><strong>Aktueller Bestand:</strong> {$aktuellerBestandFormatted}</p>
            <p class='card-text mb-0'><strong>Kasse minus:</strong> {$checkminusBadge}</p>
        </div>
        <div class='card-footer bg-light py-2 px-3 d-flex justify-content-end'>
             <a href='Buchungen.php?kassennummer={$row['id']}' class='btn btn-secondary btn-sm me-2' title='Buchungen ansehen'>
                <i class='fa-solid fa-receipt'></i>
            </a>
            <a href='Editkasse.php?id={$row['id']}' class='btn btn-primary btn-sm me-2' title='Kasse bearbeiten'>
                <i class='fa-solid fa-pen'></i>
            </a>            
            {$archiveButton}
            <a href='DeleteKasse.php?id={$row['id']}' class='btn btn-danger btn-sm delete-button' title='Kasse löschen'>
                <i class='fa-solid fa-trash-can'></i>
            </a>
        </div>
    </div>
</div>";
