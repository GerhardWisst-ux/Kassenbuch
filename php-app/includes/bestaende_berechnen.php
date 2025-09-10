<?php
/**
 * berechneBestaende.php
 * Berechnet die Monatsbestände für eine Kasse und trägt sie in die Tabelle 'bestaende' ein.
 * 
 * @param PDO $pdo
 * @param int $userid
 * @param int $kassennummer
 * @param int $jahr
 * @return array ['eingefuegt'=>int, 'aktualisiert'=>int, 'saldo'=>float]
 */
function berechneBestaende(PDO $pdo, int $userid, int $kassennummer, int $jahr, bool $kasseneu): array
{
    $eingefuegt = 0;
    $aktualisiert = 0;
    $gesamtSaldo = 0;

    // 1️⃣ Daten der Kasse abrufen
    $stmtKasse = $pdo->prepare("
        SELECT anfangsbestand, datumab 
        FROM kasse 
        WHERE id = :kassennummer
    ");
    $stmtKasse->execute([':kassennummer' => $kassennummer]);
    $kasseData = $stmtKasse->fetch(PDO::FETCH_ASSOC);

    if (!$kasseData) {
        throw new Exception("Kasse mit ID $kassennummer nicht gefunden!");
    }

    $anfangsbestand = (float) $kasseData['anfangsbestand'];

    if ($kasseneu  == true) 
        $startmonat = (int) date('m', strtotime($kasseData['datumab']));
    else
        $startmonat = 1;    
    

    // 2️⃣ Anfangsbestand aus Vorjahr ermitteln
    $erstesDatum = "$jahr-01-01";
    $stmtVor = $pdo->prepare("
        SELECT bestand 
        FROM bestaende
        WHERE userid = :userid 
          AND datum < :erstesDatum
          AND kassennummer = :kassennummer
        ORDER BY datum DESC
        LIMIT 1
    ");
    $stmtVor->execute([
        'userid' => $userid,
        'erstesDatum' => $erstesDatum,
        'kassennummer' => $kassennummer
    ]);
    $saldoVormonat = (float) $stmtVor->fetchColumn() ?: $anfangsbestand;

    // 3️⃣ Monatsweise Berechnung ab Startmonat
    for ($monat = 1; $monat <= 12; $monat++) {
        // Einlagen/Ausgaben für den Monat berechnen
        $stmt = $pdo->prepare("
            SELECT 
                SUM(CASE WHEN typ='Einlage' THEN betrag ELSE 0 END) AS einlagen,
                SUM(CASE WHEN typ='Ausgabe' THEN betrag ELSE 0 END) AS ausgaben
            FROM buchungen
            WHERE userid = :userid 
              AND kassennummer = :kassennummer 
              AND barkasse = 1
              AND YEAR(datum) = :jahr 
              AND MONTH(datum) = :monat
        ");
        $stmt->execute([
            'userid' => $userid,
            'kassennummer' => $kassennummer,
            'jahr' => $jahr,
            'monat' => $monat
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $einlagen = (float) ($result['einlagen'] ?? 0);
        $ausgaben = (float) ($result['ausgaben'] ?? 0);

        $saldo = $saldoVormonat + $einlagen - $ausgaben;
        $datum = date("$jahr-$monat-01");

        // Prüfen ob Eintrag für den Monat existiert
        $checkStmt = $pdo->prepare("
            SELECT id FROM bestaende
            WHERE userid = :userid 
              AND kassennummer = :kassennummer 
              AND YEAR(datum) = :jahr 
              AND MONTH(datum) = :monat
        ");
        $checkStmt->execute([
            'userid' => $userid,
            'kassennummer' => $kassennummer,
            'jahr' => $jahr,
            'monat' => $monat
        ]);
        $id = $checkStmt->fetchColumn();

        if ($id) {
            // Update
            $stmtUpdate = $pdo->prepare("
                UPDATE bestaende 
                SET ausgaben = ?, einlagen = ?, bestand = ?, monat = ?, kassennummer = ?
                WHERE id = ?
            ");
            $stmtUpdate->execute([$ausgaben, $einlagen, $saldo, $monat, $kassennummer, $id]);
            $aktualisiert++;
        } else {
            // Insert
            $stmtInsert = $pdo->prepare("
                INSERT INTO bestaende (datum, ausgaben, einlagen, bestand, monat, userid, kassennummer)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmtInsert->execute([$datum, $ausgaben, $einlagen, $saldo, $monat, $userid, $kassennummer]);
            $eingefuegt++;
        }

        $saldoVormonat = $saldo;
        $gesamtSaldo = $saldo;
    }

    return [
        'eingefuegt' => $eingefuegt,
        'aktualisiert' => $aktualisiert,
        'saldo' => $gesamtSaldo
    ];
}
