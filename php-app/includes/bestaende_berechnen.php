<?php
/**
 * berechneBestaende.php
 * Berechnet die Monatsbestände für eine Kasse und trägt sie in die Tabelle 'bestaende' ein.
 * 
 * @param PDO $pdo
 * @param int $userid
 * @param int $kassennummer
 * @param int $jahr
 * @param bool $kasseneu
 * @return array ['eingefuegt'=>int, 'aktualisiert'=>int, 'saldo'=>float]
 */
function berechneBestaende(PDO $pdo, int $userid, int $kassennummer, int $jahr, bool $kasseneu): array
{
    $mandantennummer = $_SESSION['mandantennummer'];
    $eingefuegt = 0;
    $aktualisiert = 0;
    $gesamtSaldo = 0;

    // 1️⃣ Kasse laden
    $stmtKasse = $pdo->prepare("
        SELECT anfangsbestand, datumab 
        FROM kasse 
        WHERE id = :kassennummer
          AND mandantennummer = :mandantennummer
    ");
    $stmtKasse->execute([':kassennummer' => $kassennummer, 'mandantennummer' => $mandantennummer]);
    $kasseData = $stmtKasse->fetch(PDO::FETCH_ASSOC);

    if (!$kasseData) {
        throw new Exception("Kasse mit ID $kassennummer nicht gefunden!");
    }

    $anfangsbestand = (float) $kasseData['anfangsbestand'];
    $datumAb = new DateTime($kasseData['datumab']);

    // Startjahr/monat aus datumab
    $startmonat = (int) $datumAb->format('m');
    $startjahr  = (int) $datumAb->format('Y');

    // Falls Berechnung in einem späteren Jahr startet → ab Januar
    if ($jahr > $startjahr) {
        $startmonat = 1;
    }

    $festerAnfangsbestand = $anfangsbestand;

    // 2️⃣ Startsaldo bestimmen (Anfangsbestand wenn keine Vorgänger-Einträge existieren)
    $stmtVor = $pdo->prepare("
        SELECT bestand 
        FROM bestaende
        WHERE userid = :userid 
          AND mandantennummer = :mandantennummer
          AND datum < :erstesDatum
          AND kassennummer = :kassennummer
        ORDER BY datum DESC
        LIMIT 1
    ");
    $erstesDatum = "$jahr-$startmonat-01";
    $stmtVor->execute([
        'userid' => $userid,
        'erstesDatum' => $erstesDatum,
        'kassennummer' => $kassennummer,
        'mandantennummer' => $mandantennummer
    ]);
    $saldoVormonat = $stmtVor->fetchColumn();
    if ($saldoVormonat === false) {
        $saldoVormonat = $anfangsbestand;
    }

    // 3️⃣ Durch alle Monate iterieren
    for ($monat = $startmonat; $monat <= 12; $monat++) {
        $monatStart = "$jahr-" . str_pad($monat, 2, "0", STR_PAD_LEFT) . "-01";
        $monatEnde = date("Y-m-t", strtotime($monatStart));

        // Summen ermitteln
        $stmt = $pdo->prepare("
            SELECT
                SUM(CASE WHEN LOWER(typ) = 'einlage' THEN betrag ELSE 0 END) AS einlagen,
                SUM(CASE WHEN LOWER(typ) = 'ausgabe' THEN betrag ELSE 0 END) AS ausgaben
            FROM buchungen
            WHERE userid = :userid
              AND kassennummer = :kassennummer
              AND mandantennummer = :mandantennummer
              AND datum BETWEEN :monatStart AND :monatEnde
        ");
        $stmt->execute([
            'userid' => $userid,
            'kassennummer' => $kassennummer,
            'mandantennummer' => $mandantennummer,
            'monatStart' => $monatStart,
            'monatEnde' => $monatEnde
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $einlagen = (float) ($result['einlagen'] ?? 0);
        $ausgaben = (float) ($result['ausgaben'] ?? 0);

        $saldo = $saldoVormonat + $einlagen - $ausgaben;

        // Prüfen ob Eintrag existiert
        $checkStmt = $pdo->prepare("
            SELECT id FROM bestaende
            WHERE userid = :userid 
              AND kassennummer = :kassennummer
              AND mandantennummer = :mandantennummer
              AND YEAR(datum) = :jahr
              AND MONTH(datum) = :monat
        ");
        $checkStmt->execute([
            'userid' => $userid,
            'kassennummer' => $kassennummer,
            'mandantennummer' => $mandantennummer,
            'jahr' => $jahr,
            'monat' => $monat
        ]);
        $id = $checkStmt->fetchColumn();

        if ($id) {
            // Update
            $stmtUpdate = $pdo->prepare("
                UPDATE bestaende
                SET ausgaben = ?, anfangsbestand = ?, einlagen = ?, bestand = ?, monat = ?
                WHERE id = ?
            ");
            $stmtUpdate->execute([$ausgaben, $saldoVormonat, $einlagen, $saldo, $monat, $id]);
            $aktualisiert++;
        } else {
            // Insert
            $datum = "$jahr-" . str_pad($monat, 2, "0", STR_PAD_LEFT) . "-01";
            $stmtInsert = $pdo->prepare("
                INSERT INTO bestaende (datum, anfangsbestand, ausgaben, einlagen, bestand, monat, userid, kassennummer, mandantennummer)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmtInsert->execute([$datum, $saldoVormonat, $ausgaben, $einlagen, $saldo, $monat, $userid, $kassennummer, $mandantennummer]);
            $eingefuegt++;
        }

        $saldoVormonat = $saldo;
        $gesamtSaldo = $saldo;
    }

    return [
        'eingefuegt' => $eingefuegt,
        'aktualisiert' => $aktualisiert,
        'saldo' => $gesamtSaldo,
        'anfangsbestand' => $festerAnfangsbestand
    ];
}
