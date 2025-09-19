<?php
session_start();
require_once 'DB.php';

// Kunden abrufen
$stmt = $pdo->prepare("SELECT id, kundennummer, typ, nachname, strasse, plz, ort, created_at, updated_at FROM mandanten");
$stmt->execute();
$Kunden = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <title>CashControl - Kunden</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="css/responsive.dataTables.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        table.dataTable {
            width: 100% !important;
        }

        .dataTables_wrapper .dataTables_scroll {
            overflow-x: hidden !important;
        }
    </style>
</head>

<body>
    <?php include 'includes/header.php';

    // Alerts
    function showAlert($type, $message)
    {
        echo '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">'
            . htmlspecialchars($message) .
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    }

    if (isset($_SESSION['success_message'])) {
        showAlert('success', $_SESSION['success_message']);
        unset($_SESSION['success_message']);
    }
    if (!empty($_SESSION['error_message'])) {
        showAlert('danger', $_SESSION['error_message']);
        unset($_SESSION['error_message']);
    }
    ?>

    <header class="custom-header py-2 text-white">
        <div class="container-fluid">
            <div class="row align-items-center">
                <!-- Titel zentriert -->
                <div class="col-12 text-center mb-2 mb-md-0">
                    <h2 class="h4 mb-0">CashControl - Mandanten</h2>
                </div>

                <?php
                require_once 'includes/benutzerversion.php';
                ?>
            </div>
    </header>

    <div class="container-fluid mt-2">
        <div class="d-flex justify-content-between align-items-center mb-3">

            <!-- Linke Buttons -->
            <div>
                <!-- Neuer Kunde -->
                <button type="button" class="btn btn-primary btn-sm rounded-circle me-2 circle-btn"
                    data-bs-toggle="modal" data-bs-target="#addKundenModal">
                    <i class="fas fa-plus"></i>
                </button>

                <!-- Zurück -->
                <a href="Index.php" title="Zurück zur Kassenübersicht"
                    class="btn btn-primary btn-sm rounded-circle me-2 circle-btn">
                    <i class="fa fa-arrow-left" aria-hidden="true"></i>
                </a>
            </div>

            <!-- Hilfe rechts -->
            <div>
                <a href="help/Mandanten.php" class="btn btn-primary btn-sm rounded-circle circle-btn" title="Hilfe">
                    <i class="fa fa-question-circle"></i>
                </a>
            </div>

        </div>


        <table id="KundenTable" class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kundennummer</th>
                    <th>Typ</th>
                    <th>Nachname</th>
                    <th>Straße</th>
                    <th>PLZ Ort</th>
                    <th>Erstellt</th>
                    <th>Aktualisiert</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($Kunden as $kunde): ?>
                    <tr>
                        <td><?= htmlspecialchars($kunde['id']) ?></td>
                        <td><?= htmlspecialchars($kunde['kundennummer']) ?></td>
                        <td><?= htmlspecialchars($kunde['typ']) ?></td>
                        <td><?= htmlspecialchars($kunde['nachname']) ?></td>
                        <td><?= htmlspecialchars($kunde['strasse']) ?></td>
                        <td><?= htmlspecialchars($kunde['plz']) . ' ' . htmlspecialchars($kunde['ort']) ?></td>
                        <td><?= date('d.m.Y H:i', strtotime($kunde['created_at'])) ?></td>
                        <td><?= date('d.m.Y H:i', strtotime($kunde['updated_at'])) ?></td>
                        <td>
                            <button class="btn btn-sm btn-primary edit-btn" data-id="<?= $kunde['id'] ?>">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="post" action="Controllers/kunden_crud.php?action=delete" class="d-inline"
                                onsubmit="return confirm('Kunde wirklich löschen?');">
                                <input type="hidden" name="id" value="<?= $kunde['id'] ?>">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    </div>

    <!-- Add-Kunden Modal -->
    <div class="modal fade" id="addKundenModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="addKundenForm" method="post" action="Controllers/kunden_crud.php?action=add">
                    <div class="modal-header">
                        <h5 class="modal-title">Neuen Mandanten hinzufügen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="add-typ" class="form-label">Kundennummer</label>
                                <input type="text" class="form-control" id="add-kundennummer" name="kundennummer"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label for="add-typ" class="form-label">Typ</label>
                                <input type="text" class="form-control" id="add-typ" name="typ" required>
                            </div>
                            <div class="col-md-4">
                                <label for="add-nachname" class="form-label">Nachname</label>
                                <input type="text" class="form-control" id="add-nachname" name="nachname" required>
                            </div>
                            <div class="col-md-4">
                                <label for="add-strasse" class="form-label">Straße</label>
                                <input type="text" class="form-control" id="add-strasse" name="strasse">
                            </div>
                            <div class="col-md-2">
                                <label for="add-plz" class="form-label">PLZ</label>
                                <input type="text" class="form-control" id="add-plz" name="plz">
                            </div>
                            <div class="col-md-4">
                                <label for="add-ort" class="form-label">Ort</label>
                                <input type="text" class="form-control" id="add-ort" name="ort">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                        <button type="submit" class="btn btn-success">Hinzufügen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Edit-Kunden Modal -->
    <div class="modal fade" id="editKundeModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form method="post" action="Controllers/kunden_crud.php?action=update">
                    <div class="modal-header">
                        <h5 class="modal-title">Mandant bearbeiten</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="id" id="edit-id">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Kundennummer</label>
                                <input type="text" class="form-control" name="kundennummer" id="edit-kundennummer"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Typ</label>
                                <input type="text" class="form-control" name="typ" id="edit-typ" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nachname</label>
                                <input type="text" class="form-control" name="nachname" id="edit-nachname" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Straße</label>
                                <input type="text" class="form-control" name="strasse" id="edit-strasse">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">PLZ</label>
                                <input type="text" class="form-control" name="plz" id="edit-plz">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Ort</label>
                                <input type="text" class="form-control" name="ort" id="edit-ort">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                        <button type="submit" class="btn btn-primary">Speichern</button>
                    </div>
                </form>
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
            $('#KundenTable').DataTable({
                responsive: true,
                autoWidth: false,
                scrollX: false, // horizontalen Scroll verhindern
                pageLength: 10,
                columnDefs: [{ targets: 0, visible: false }],
                order: [[0, 'desc']],
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/de-DE.json"
                }
            });

            $('.edit-btn').on('click', function () {
                var id = $(this).data('id');

                $.ajax({
                    url: 'Controllers/kunden_crud.php',
                    language: { url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/de-DE.json" },
                    type: 'GET',
                    data: { action: 'get', id: id },
                    dataType: 'json',
                    success: function (data) {
                        if (data.success) {
                            $('#edit-id').val(data.id);
                            $('#edit-kundennummer').val(data.kundennummer);
                            $('#edit-typ').val(data.typ);
                            $('#edit-nachname').val(data.nachname);
                            $('#edit-strasse').val(data.strasse);
                            $('#edit-plz').val(data.plz);
                            $('#edit-ort').val(data.ort);

                            new bootstrap.Modal(document.getElementById('editKundeModal')).show();
                        } else {
                            alert('Fehler: ' + data.message);
                        }
                    },
                    error: function () {
                        alert('Fehler beim Laden der Mandantendaten');
                    }
                });
            });
        });

        $('#addKundeForm').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: 'Controllers/kunden_crud.php?action=add',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (res) {
                    if (res.success) {
                        $('#addKundeModal').modal('hide');
                        // Hier kann die Tabelle per DataTable.reload oder append aktualisiert werden
                        alert(res.message); // oder schöner Toast anzeigen
                    } else {
                        alert('Fehler: ' + res.message);
                    }
                },
                error: function (xhr, status, err) {
                    alert('Serverfehler: ' + err);
                }
            });
        });

    </script>
</body>

</html>