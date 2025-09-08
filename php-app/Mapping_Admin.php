<?php
session_start();

require 'db.php';
require_once 'includes/header.php';


// Sicherheit: nur Admin
if (empty($_SESSION['userid'])) {
    header("Location: Login.php");
    exit;
}

// Falls Eintrag gespeichert/aktualisiert/gelöscht wird
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $stmt = $pdo->prepare("INSERT INTO buchungsart_mapping (buchungsart, kontenrahmen, konto, gegenkonto, bu_schluessel) 
                               VALUES (:buchungsart, :kontenrahmen, :konto, :gegenkonto, :bu)");
        $stmt->execute([
            'buchungsart' => $_POST['buchungsart'],
            'kontenrahmen' => $_POST['kontenrahmen'],
            'konto' => $_POST['konto'],
            'gegenkonto' => $_POST['gegenkonto'],
            'bu' => $_POST['bu_schluessel'] ?: null
        ]);
    }

    if (isset($_POST['edit'])) {
        $stmt = $pdo->prepare("UPDATE buchungsart_mapping 
                               SET buchungsart=:buchungsart, kontenrahmen=:kontenrahmen, konto=:konto, gegenkonto=:gegenkonto, bu_schluessel=:bu
                               WHERE id=:id");
        $stmt->execute([
            'id' => $_POST['id'],
            'buchungsart' => $_POST['buchungsart'],
            'kontenrahmen' => $_POST['kontenrahmen'],
            'konto' => $_POST['konto'],
            'gegenkonto' => $_POST['gegenkonto'],
            'bu' => $_POST['bu_schluessel'] ?: null
        ]);
    }

    if (isset($_POST['delete'])) {
        $stmt = $pdo->prepare("DELETE FROM buchungsart_mapping WHERE id=:id");
        $stmt->execute(['id' => $_POST['id']]);
    }

    header("Location: mapping_admin.php");
    exit;
}

// Alle Mappings holen
$filterRahmen = $_GET['kontenrahmen'] ?? '';

// SQL vorbereiten
$sql = "SELECT * FROM buchungsart_mapping";
$params = [];

if ($filterRahmen !== '') {
    $sql .= " WHERE kontenrahmen = :rahmen";
    $params['rahmen'] = $filterRahmen;
}

$sql .= " ORDER BY buchungsart ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$mappings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>Buchungsarten-Mapping</title>
    <!-- CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="css/responsive.dataTables.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <header class="custom-header py-2 text-white">
        <div class="container-fluid">
            <div class="row align-items-center">
                <!-- Titel zentriert -->
                <div class="col-12 text-center mb-2 mb-md-0">
                    <h2 class="h4 mb-0">CashControl - Buchungsarten-Mapping (DATEV)</h2>
                </div>
                <!-- Benutzerinfo + Logout -->
                <link href="css/style.css" rel="stylesheet">
                <?php
                require_once 'includes/benutzerversion.php';
                ?>
            </div>
    </header>
    <div class="container-fluid mb-3 mt-3">

        <a href="Index.php" class="btn btn-primary btn-sm"><i class="fa fa-arrow-left"></i></a>
        <!-- Add Button -->
        <br>
        <button class="btn btn-success mt-3" data-bs-toggle="modal" data-bs-target="#addModal">➕ Neue
            Zuordnung</button>
        <!-- Tabelle -->
        <form method="GET" class="d-flex gap-3 mb-3 align-items-center flex-wrap">
            <label for="filterKontenrahmen" class="form-label mb-0">Kontenrahmen:</label>
            <select id="filterKontenrahmen" name="kontenrahmen" class="form-select" style="width: 150px;"
                onchange="this.form.submit()">
                <option value="">Alle</option>
                <option value="SKR03" <?= (isset($_GET['kontenrahmen']) && $_GET['kontenrahmen'] == 'SKR03') ? 'selected' : '' ?>>SKR03</option>
                <option value="SKR04" <?= (isset($_GET['kontenrahmen']) && $_GET['kontenrahmen'] == 'SKR04') ? 'selected' : '' ?>>SKR04</option>
            </select>
        </form>
        <table id="mappingTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Buchungsart</th>
                    <th>Kontenrahmen</th>
                    <th>Konto</th>
                    <th>Gegenkonto</th>
                    <th>BU-Schlüssel</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mappings as $m): ?>
                    <tr>
                        <td><?= htmlspecialchars($m['id']) ?></td>
                        <td><?= htmlspecialchars($m['buchungsart']) ?></td>
                        <td><?= htmlspecialchars($m['kontenrahmen']) ?></td>
                        <td><?= htmlspecialchars($m['konto']) ?></td>
                        <td><?= htmlspecialchars($m['gegenkonto']) ?></td>
                        <td><?= htmlspecialchars($m['bu_schluessel']) ?></td>
                        <td>
                            <!-- Bearbeiten -->
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                data-bs-target="#editModal<?= $m['id'] ?>">✏️</button>
                            <!-- Löschen -->
                            <form method="post" action="" class="d-inline">
                                <input type="hidden" name="id" value="<?= $m['id'] ?>">
                                <button type="submit" name="delete" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Wirklich löschen?')">🗑️</button>
                            </form>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal<?= $m['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="post">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Buchungsart bearbeiten</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id" value="<?= $m['id'] ?>">
                                        <div class="mb-3">
                                            <label class="form-label">Buchungsart</label>
                                            <input type="text" name="buchungsart" class="form-control"
                                                value="<?= htmlspecialchars($m['buchungsart']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Kontenrahmen</label>
                                            <select name="kontenrahmen" class="form-select">
                                                <option <?= $m['kontenrahmen'] == "SKR03" ? "selected" : "" ?>>SKR03
                                                </option>
                                                <option <?= $m['kontenrahmen'] == "SKR04" ? "selected" : "" ?>>SKR04
                                                </option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Konto</label>
                                            <input type="text" name="konto" class="form-control"
                                                value="<?= htmlspecialchars($m['konto']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Gegenkonto</label>
                                            <input type="text" name="gegenkonto" class="form-control"
                                                value="<?= htmlspecialchars($m['gegenkonto']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">BU-Schlüssel</label>
                                            <input type="text" name="bu_schluessel" class="form-control"
                                                value="<?= htmlspecialchars($m['bu_schluessel']) ?>">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" name="edit" class="btn btn-success">Speichern</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </tbody>
        </table>


        <!-- Add Modal -->
        <div class="modal fade" id="addModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post">
                        <div class="modal-header">
                            <h5 class="modal-title">Neue Buchungsart anlegen</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Buchungsart</label>
                                <input type="text" name="buchungsart" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kontenrahmen</label>
                                <select name="kontenrahmen" class="form-select">
                                    <option>SKR03</option>
                                    <option>SKR04</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Konto</label>
                                <input type="text" name="konto" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Gegenkonto</label>
                                <input type="text" name="gegenkonto" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">BU-Schlüssel</label>
                                <input type="text" name="bu_schluessel" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="add" class="btn btn-primary">Speichern</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <!-- JS -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.min.js"></script>
    <script src="js/dataTables.responsive.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#mappingTable').DataTable({
                language: { url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/de-DE.json" },
                responsive: {
                    details: {
                        display: $.fn.dataTable.Responsive.display.modal({
                            header: function (row) {
                                var data = row.data();
                                return 'Details zu ' + data[1];
                            }
                        }),
                        renderer: $.fn.dataTable.Responsive.renderer.tableAll({
                            tableClass: 'table'
                        })
                    }
                },
                scrollX: false,
                pageLength: 50,
                autoWidth: false
            });
        });
    </script>
</body>

</html>