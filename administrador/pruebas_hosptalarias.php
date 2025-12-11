<?php
// Iniciar la sesión si aún no está iniciada para manejar mensajes

include_once '../componentes/header.php';
?>

<body>

    <div class="d-flex" id="wrapper">
        <?php include_once '../componentes/sidebar.php'; ?>

        <div id="content" class="p-4 bg-gray-100 flex-grow">
            <?php
            include_once '../componentes/barra_nav.php';
            ?>

            <div class="d-flex justify-content-end mb-3">
                <button class="btn btn-primary btn-rounded shadow-sm" data-bs-toggle="modal" data-bs-target="#modalRegistrarPrueba">
                    <i class="bi bi-person-plus-fill me-2"></i> Añadir Nueva Prueba
                </button>
            </div>

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

    <!-- Modal de Registrar Paciente-->
    <div class="modal fade" id="modalRegistrarPrueba" tabindex="-1" aria-labelledby="modalRegistrarPruebaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content shadow-lg border-0 rounded-3">

                <div class="modal-header bg-primary text-white">
                    <i class="bi bi-flask-fill fs-4 me-2"></i>
                    <h5 class="modal-title" id="modalRegistrarPruebaLabel"> Registrar Nueva Prueba Médica</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form id="formRegistrarPrueba" action="../php/registrar_prueba.php" method="POST" autocomplete="off">
                    <div class="modal-body p-4">
                        <div class="row g-3">

                            <!-- Nombre de la prueba -->
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-card-checklist"></i> Nombre de la Prueba
                                </label>
                                <input type="text" class="form-control" name="nombre" required placeholder="Ej: Hemograma, Glucosa, VIH, etc.">
                            </div>

                            <!-- Precio -->
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-cash-coin"></i> Precio (FCFA)
                                </label>
                                <input type="number" step="0.01" class="form-control" name="precio" required placeholder="Ej: 5000">
                            </div>

                            <!-- id_usuario oculto -->
                            <input type="hidden" name="id_usuario" value="<?= $_SESSION['id_usuario']; ?>">

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save-fill"></i> Guardar Prueba
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>


    <!-- Modal de Editar Paciente-->
    <div class="modal fade" id="modalEditarPrueba" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-3">

                <div class="modal-header bg-primary text-dark">
                    <h5 class="modal-title">
                        <i class="bi bi-pencil-square me-2"></i>Editar Prueba Médica
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form id="formEditarPrueba" action="../php/actualizar_prueba.php" method="POST">

                        <!-- ID oculta -->
                        <input type="hidden" id="edit_id_prueba" name="id_prueba">

                        <div class="row g-3">

                            <!-- Nombre de la prueba -->
                            <div class="col-md-8">
                                <label class="form-label">
                                    <i class="bi bi-file-medical"></i> Nombre de la Prueba
                                </label>
                                <input type="text" class="form-control" id="edit_nombre_prueba" name="nombre" required>
                            </div>

                            <!-- Precio -->
                            <div class="col-md-4">
                                <label class="form-label">
                                    <i class="bi bi-cash-coin"></i> Precio (FCFA)
                                </label>
                                <input type="number" step="0.01" min="0" class="form-control" id="edit_precio_prueba" name="precio" required>
                            </div>

                        </div>

                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </button>
                    <button type="submit" form="formEditarPrueba" class="btn btn-primary">
                        <i class="bi bi-save"></i> Guardar Cambios
                    </button>
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