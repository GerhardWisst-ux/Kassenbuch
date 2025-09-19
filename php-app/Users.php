<?php
ob_start();
if (session_status() === PHP_SESSION_NONE)
    session_start();


// Login prüfen
if (empty($_SESSION['email'])) {
    header("Location: Login.php");
    exit;
}

// CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once 'DB.php';

// User abrufen
$stmt = $pdo->prepare("
    SELECT id, mandantennummer, vorname, nachname, strasse, plz, ort, freigeschaltet, is_admin, gesperrt, created_at 
    FROM users 
    ORDER BY id ASC
");
$stmt->execute();
$User = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <title>CashControl - User</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="images/favicon.png">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="css/responsive.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        #UserTable {
            width: 100%;
            font-size: 0.9rem;
        }

        #UserTable tbody tr:hover {
            background-color: #f1f5ff;
        }

        .circle-btn {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            font-size: 14px;
            /* Icon-Größe */
        }
    </style>
</head>

<body>
    <?php include 'includes/header.php';

    if (isset($_SESSION['success_message'])) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">'
            . htmlspecialchars($_SESSION['success_message']) .
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
         </div>';
        // Meldung nach einmaligem Anzeigen löschen        
        unset($_SESSION['success_message']);
    }
    if (!empty($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['error_message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Schließen"></button>
        </div>
        <?php
        // Meldung nach Anzeige löschen        
        unset($_SESSION['error_message']);
    endif; ?>

    <div class="wrapper">
        <header class="custom-header py-2 text-white">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-12 text-center mb-2 mb-md-0">
                        <h2 class="h4 mb-0">CashControl - User</h2>
                    </div>
                    <?php require_once 'includes/benutzerversion.php'; ?>
                </div>
            </div>
        </header>

        <!-- Toolbar -->
        <div class="btn-toolbar mb-3 d-flex justify-content-between mt-2" role="toolbar">
            <div class="btn-group"></div>
            <div class="btn-group">
                <a href="help/User.php" class="btn btn-primary btn-sm" title="Hilfe">
                    <i class="fa fa-question-circle"></i>
                </a>
            </div>
        </div>

        <div class="container-fluid mt-5 mb-5">
            <div class="mb-4">
                <div class="table-responsive">
                    <table id="UserTable" class="table table-striped w-100">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Mandantennummer</th>
                                <th>Vorname</th>
                                <th>Nachname</th>
                                <th>Straße</th>
                                <th>PLZ Ort</th>
                                <th>Freigeschaltet</th>
                                <th>Admin</th>
                                <th>Gesperrt</th>
                                <th>Angelegt am</th>
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($User as $user): ?>
                                <tr data-id="<?= $user['id'] ?>">
                                    <td><?= htmlspecialchars($user['id']) ?></td>
                                    <td><?= htmlspecialchars($user['mandantennummer']) ?></td>
                                    <td><?= htmlspecialchars($user['vorname']) ?></td>
                                    <td><?= htmlspecialchars($user['nachname']) ?></td>
                                    <td><?= htmlspecialchars($user['strasse']) ?></td>
                                    <td><?= htmlspecialchars($user['plz'] . ' ' . $user['ort']) ?></td>
                                    <td><?= $user['freigeschaltet'] ?></td>
                                    <td><?= $user['is_admin'] ?></td>
                                    <td><?= $user['gesperrt'] ?></td>
                                    <td><?= date('d.m.Y H:i', strtotime($user['created_at'])) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary edit-btn" data-id="<?= $user['id'] ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="post" action="Controllers/users_crud.php?action=delete"
                                            class="d-inline" onsubmit="return confirm('User wirklich löschen?');">
                                            <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger"><i
                                                    class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <a class="btn btn-sm btn-outline-secondary mt-3" href="Index.php">
                    <i class="fa-solid fa-arrow-left"></i> Zurück
                </a>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="editUserForm">
                    <div class="modal-header">
                        <h5 class="modal-title">User bearbeiten</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="id" id="edit-id">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Mandantennummer</label>
                                <input type="text" class="form-control" id="edit-mandantennummer" name="mandantennummer"
                                    readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Vorname</label>
                                <input type="text" class="form-control" id="edit-vorname" name="vorname" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nachname</label>
                                <input type="text" class="form-control" id="edit-nachname" name="nachname" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Straße</label>
                                <input type="text" class="form-control" id="edit-strasse" name="strasse">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">PLZ</label>
                                <input type="text" class="form-control" id="edit-plz" name="plz">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Ort</label>
                                <input type="text" class="form-control" id="edit-ort" name="ort">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Admin</label>
                                <select class="form-select" id="edit-is_admin" name="is_admin">
                                    <option value="0">User</option>
                                    <option value="1">Admin</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Freigeschaltet</label>
                                <select class="form-select" id="edit-freigeschaltet" name="freigeschaltet">
                                    <option value="0">Nein</option>
                                    <option value="1">Ja</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Gesperrt</label>
                                <select class="form-select" id="edit-gesperrt" name="gesperrt">
                                    <option value="0">Nein</option>
                                    <option value="1">Ja</option>
                                </select>
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

    <?php include 'includes/footer.php'; ?>
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.responsive.min.js"></script>

    <script>
        $(document).ready(function () {
            var editModal = new bootstrap.Modal(document.getElementById('editUserModal'));

            // DataTable initialisieren
            var userTable = $('#UserTable').DataTable({
                responsive: true,
                language: { url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/de-DE.json" },
                columns: Array(11).fill({}) // 11 Spalten
            });

            // Users laden und Tabelle aktualisieren
            function loadUsers() {
                $.getJSON('Controllers/users_crud.php', { action: 'list' }, function (data) {
                    if (!data.success) { alert(data.message); return; }

                    data.users.forEach(u => {
                        var row = userTable.row('tr[data-id="' + u.id + '"]');
                        var rowData = [
                            u.id,
                            u.mandantennummer,
                            u.vorname,
                            u.nachname,
                            u.strasse,
                            u.plz + ' ' + u.ort,
                            parseInt(u.freigeschaltet),
                            parseInt(u.is_admin),
                            parseInt(u.gesperrt),
                            u.created_at_formatted || u.created_at,
                            `<button class="btn btn-sm btn-primary edit-btn" data-id="${u.id}"><i class="fas fa-edit"></i></button>
                     <button class="btn btn-sm btn-danger delete-btn" data-id="${u.id}"><i class="fas fa-trash"></i></button>`
                        ];

                        if (row.node()) {
                            // Zeile existiert: aktualisieren
                            row.data(rowData).draw(false);
                        } else {
                            // Zeile existiert nicht: hinzufügen
                            userTable.row.add(rowData).draw(false);
                        }
                    });
                });
            }

            loadUsers();

            // Edit Button
            $('#UserTable tbody').on('click', '.edit-btn', function () {
                var id = $(this).data('id');
                $.getJSON('Controllers/users_crud.php', { action: 'get', id: id }, function (data) {
                    if (data.success) {
                        $('#edit-id').val(data.id);
                        $('#edit-mandantennummer').val(data.mandantennummer);
                        $('#edit-vorname').val(data.vorname);
                        $('#edit-nachname').val(data.nachname);
                        $('#edit-strasse').val(data.strasse);
                        $('#edit-plz').val(data.plz);
                        $('#edit-ort').val(data.ort);
                        $('#edit-is_admin').val(data.is_admin);
                        $('#edit-freigeschaltet').val(data.freigeschaltet);
                        $('#edit-gesperrt').val(data.gesperrt);
                        editModal.show();
                    } else {
                        alert(data.message);
                    }
                });
            });

            // Delete Button
            $('#UserTable tbody').on('click', '.delete-btn', function () {
                if (!confirm('User wirklich löschen?')) return;
                var id = $(this).data('id');
                $.post('Controllers/users_crud.php', { action: 'delete', id: id, csrf_token: '<?= $_SESSION['csrf_token'] ?>' }, function (data) {
                    if (data.success) { loadUsers(); }
                    else { alert('Fehler beim Löschen: ' + (data.message || '')); }
                }, 'json');
            });

            // Submit Edit Form            
            $('#editUserForm').submit(function (e) {
                e.preventDefault();
                var formData = $(this).serialize() + '&action=update';
                $.post('Controllers/users_crud.php', formData, function (data) {
                    if (data.success) {
                        editModal.hide();

                        // Die DataTable-Zeile für diesen User aktualisieren
                        var row = $('#UserTable tbody').find('tr[data-id="' + data.user.id + '"]');
                        if (row.length) {
                            userTable.row(row).data([
                                data.user.id,
                                data.user.mandantennummer,
                                data.user.vorname,
                                data.user.nachname,
                                data.user.strasse,
                                data.user.plz + ' ' + data.user.ort,
                                data.user.freigeschaltet,
                                data.user.is_admin,
                                data.user.gesperrt,
                                row.find('td').eq(9).text(), // created_at unverändert
                                `<button class="btn btn-sm btn-primary edit-btn" data-id="${data.user.id}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-btn" data-id="${data.user.id}">
                                        <i class="fas fa-trash"></i>
                                    </button>`
                            ]).draw(false);
                        }
                        reload();
                        // Optional: Erfolgsnachricht kurz anzeigen
                        // $('<div class="alert alert-success alert-dismissible fade show mt-3" role="alert">' +
                        //     'User #' + data.user.id + ' wurde erfolgreich aktualisiert.' +
                        //     '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                        //     '</div>').prependTo('.container-fluid').delay(4000).fadeOut();
                    } else {
                        alert('Fehler beim Update: ' + (data.message || 'Unbekannt'));
                    }
                }, 'json');
            });

        });


    </script>


</body>

</html>