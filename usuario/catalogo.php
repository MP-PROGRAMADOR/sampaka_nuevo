<?php

include_once '../componentes/header_usuario.php';
?>

<body>

    <div class="d-flex" id="wrapper">
        <?php include_once '../componentes/sidebar.php'; ?>

        <div id="content" class="p-4 bg-gray-100 flex-grow">
          


            <div class="card shadow-sm rounded-xl">
                <div class="card-body">
                    <h5 class="card-title mb-3 fw-bold"> Lista de Pruebas Médicas</h5>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success fade-msg"><?= htmlspecialchars($_SESSION['success']); ?></div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger fade-msg"><?= htmlspecialchars($_SESSION['error']); ?></div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <div class="table-responsive">

                        <?php
                        require_once "../config/conexion.php";

                        try {
                            // Traemos pruebas médicas + nombre del usuario que la registró
                            $stmt = $pdo->query("
                    SELECT pm.id_prueba, pm.nombre, pm.precio,
                           u.username AS usuario
                    FROM pruebas_medicas pm
                    LEFT JOIN usuarios u ON pm.id_usuario = u.id_usuario
                    ORDER BY pm.id_prueba DESC
                ");
                            $pruebas = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        } catch (PDOException $e) {
                            echo "<div class='alert alert-danger'>Error al cargar pruebas: " . $e->getMessage() . "</div>";
                            $pruebas = [];
                        }
                        ?>

                        <table id="tablaPacientes" class="table table-striped table-hover align-middle mb-0 nowrap" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre de la Prueba</th>
                                    <th>Precio (FCFA)</th>
                                    <th>Registrado por</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (count($pruebas) > 0): ?>
                                    <?php foreach ($pruebas as $p): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($p['id_prueba']) ?></td>
                                            <td><?= htmlspecialchars($p['nombre']) ?></td>
                                            <td><?= htmlspecialchars($p['precio']) ?></td>
                                            <td><?= htmlspecialchars($p['usuario'] ?? '---') ?></td>

                                            <td>
                                                <button
                                                    class="btn btn-sm btn-primary me-2"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalEditarPrueba"
                                                    data-id="<?= htmlspecialchars($p['id_prueba']) ?>"
                                                    data-nombre="<?= htmlspecialchars($p['nombre']) ?>"
                                                    data-precio="<?= htmlspecialchars($p['precio']) ?>">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>

                                                <button
                                                    class="btn btn-sm btn-danger me-2"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="##modalEliminarPrueba"
                                                    data-id="<?= htmlspecialchars($p['id_prueba']) ?>"
                                                    data-nombre="<?= htmlspecialchars($p['nombre']) ?>">
                                                    <i class="bi bi-trash"></i>
                                                </button>




                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No hay pruebas registradas</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>

        </div>
    </div>

  





    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var modal = document.getElementById('modalEditarPrueba');

            modal.addEventListener('show.bs.modal', function(event) {
                let button = event.relatedTarget;

                let id = button.getAttribute('data-id');
                let nombre = button.getAttribute('data-nombre');
                let precio = button.getAttribute('data-precio');

                document.getElementById('edit_id_prueba').value = id;
                document.getElementById('edit_nombre_prueba').value = nombre;
                document.getElementById('edit_precio_prueba').value = precio;
            });
        });
    </script>










    <?php
    include_once '../componentes/footer.php';
    ?>