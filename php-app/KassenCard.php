<?php
$kassenId = $row['id'];
$formattedDate = (new DateTime($row['datumab']))->format('d.m.Y');

// Fester Anfangsbestand direkt aus Kasse
$anfangsbestand = (float) $row['anfangsbestand'];
$anfangsbestandFormatted = number_format($anfangsbestand, 2, ',', '.') . ' €';

// Aktueller Bestand aus bestaende
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
    ? number_format((float) $aktuellerBestand, 2, ',', '.') . ' €'
    : '-';

// Letzte Buchung
$stmtLastBuchung = $pdo->prepare("
    SELECT datum, betrag, typ, beschreibung
    FROM buchungen
    WHERE kassennummer = :kassennummer
      AND userid = :userid
    ORDER BY datum DESC, id DESC
    LIMIT 1
");
$stmtLastBuchung->execute([
    ':kassennummer' => $kassenId,
    ':userid' => $userid
]);
$lastBuchung = $stmtLastBuchung->fetch(PDO::FETCH_ASSOC);
if ($lastBuchung) {
    $lastBuchungDate = (new DateTime($lastBuchung['datum']))->format('d.m.Y');
    $lastBuchungInfo = htmlspecialchars($lastBuchung['beschreibung'] ?? '—');
    $lastBuchungBetrag = number_format((float) $lastBuchung['betrag'], 2, ',', '.') . ' €';
    $lastBuchungDisplay = "<strong>{$lastBuchungDate}</strong>: {$lastBuchungInfo} ({$lastBuchungBetrag})";
} else {
    $lastBuchungDisplay = 'Keine Buchungen vorhanden';
}

// Kunde
$stmtKunde = $pdo->prepare("
    SELECT vorname, nachname, typ 
    FROM mandanten 
    WHERE kundennummer = :mandantennummer 
    LIMIT 1
");
$stmtKunde->execute([':mandantennummer' => $row['mandantennummer']]);
$kunde = $stmtKunde->fetch(PDO::FETCH_ASSOC);
$kundenName = $kunde
    ? htmlspecialchars(trim($kunde['vorname'] . ' ' . $kunde['nachname'] . ' ' . $kunde['typ']))
    : '-';

// Badge Minus
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

// Kritisch-Label
$kritischLabel = $aktuellerBestand < 100
    ? '<span class="position-absolute top-0 end-0 m-2 px-2 py-1 bg-danger text-white rounded-pill small">KRITISCH</span>'
    : '';

// Archiv-Button
$archiveButton = $row['archiviert'] == 0
    ? "<a href='ArchiveKasse.php?id={$row['id']}&action=archivieren' class='btn btn-warning btn-sm rounded-circle me-2 circle-btn' title='Kasse archivieren'><i class='fa-solid fa-box-archive'></i></a>"
    : "<a href='ArchiveKasse.php?id={$row['id']}&action=aktivieren' class='btn btn-success btn-sm rounded-circle me-2 circle-btn' title='Kasse reaktivieren'><i class='fa-solid fa-rotate-left'></i></a>";
?>

<div class='col-sm-6 col-md-4 col-lg-3 position-relative'>
    <div class='card shadow-sm d-flex h-100 flex-column card-hover'>
        <?= $kritischLabel ?>
        <div class='card-header <?= $headerClass ?> py-2 px-3'>
            <h6 class='mb-0'><?= htmlspecialchars($row['kasse']) ?></h6>
        </div>
        <div class='card-body py-2 px-3 flex-grow-1'>
            <p class='card-text mb-1'><strong>Mandant/Kunde:</strong> <?= $kundenName ?></p>
            <p class='card-text mb-1'><strong>Kontonummer:</strong> <?= htmlspecialchars($row['kontonummer']) ?></p>
            <p class='card-text mb-1'><strong>Datum ab:</strong> <?= $formattedDate ?></p>
            <p class='card-text mb-1'><strong>Anfangsbestand:</strong> <?= $anfangsbestandFormatted ?></p>
            <p class='card-text mb-1'><strong>Aktueller Bestand:</strong>
                <span class="saldo-text"><?= $aktuellerBestandFormatted ?></span>
            </p>
            <p class='card-text mb-1'><strong>Letzte Buchung:</strong> <?= $lastBuchungDisplay ?></p>
            <p class='card-text mb-0'><strong>Kasse minus:</strong> <?= $checkminusBadge ?></p>
        </div>
        <div class='card-footer bg-light py-2 px-3 d-flex justify-content-end'>
            <button class="btn btn-success btn-sm btn-berechnen me-2" data-kasseid="<?= $row['id'] ?>"
                title="Bestände neuberechnen">
                <i class="fa-solid fa-sync"></i>
            </button>
            <a href='Buchungen.php?kassennummer=<?= $row['id'] ?>' class='btn btn-secondary btn-sm me-2 circle-btn'
                title='Buchungen ansehen'><i class='fa-solid fa-receipt'></i></a>
            <a href='EditKasse.php?id=<?= $row['id'] ?>' class='btn btn-primary btn-sm me-2 circle-btn'
                title='Kasse bearbeiten'><i class='fa-solid fa-pen'></i></a>
            <?= $archiveButton ?>
            <a href='#' class='btn btn-danger btn-sm delete-button' data-id='<?= $row['id'] ?>' title='Kasse löschen'><i
                    class='fa-solid fa-trash'></i></a>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.btn-berechnen').forEach(button => {
        button.addEventListener('click', function () {
            const kassenId = this.dataset.kasseid;
            const card = this.closest('.card');
            const saldoEl = card.querySelector('.saldo-text');

            this.disabled = true;
            this.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

            fetch('includes/berechneBestaendeAjax.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'kassennummer=' + kassenId + '&jahr=' + new Date().getFullYear()
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const saldo = parseFloat(data.saldo);
                        saldoEl.textContent = saldo.toFixed(2).replace('.', ',') + ' €';

                        // Farbcodierung
                        saldoEl.classList.remove('text-success', 'text-warning', 'text-danger');
                        if (saldo >= 200) saldoEl.classList.add('text-success');
                        else if (saldo >= 100) saldoEl.classList.add('text-warning');
                        else saldoEl.classList.add('text-danger');

                    } else {
                        alert('Fehler: ' + data.message);
                    }
                })
                .catch(err => alert('AJAX Fehler: ' + err))
                .finally(() => {
                    this.disabled = false;
                    this.innerHTML = '<i class="fa-solid fa-sync"></i>';
                });
        });
    });
</script>