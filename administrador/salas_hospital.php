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
                <button class="btn btn-primary btn-rounded shadow-sm" data-bs-toggle="modal" data-bs-target="#modalRegistrarSala">
                    <i class="bi bi-person-plus-fill me-2"></i> Añadir Nueva Sala
                </button>
            </div>

            <div class="card shadow-sm rounded-xl">
                <div class="card-body">
                    <h5 class="card-title mb-3 fw-bold"> Lista de las Salas</h5>

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
                            // Traemos salas + nombre del usuario que la registró
                            $stmt = $pdo->query("
                    SELECT s.id_sala, s.nombre, s.num_cama,
                           u.username AS usuario
                    FROM salas s
                    LEFT JOIN usuarios u ON s.id_usuario = u.id_usuario
                    ORDER BY s.id_sala DESC
                ");
                            $salas = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        } catch (PDOException $e) {
                            echo "<div class='alert alert-danger'>Error al cargar salas: " . $e->getMessage() . "</div>";
                            $salas = [];
                        }
                        ?>

                        <table id="tablaPacientes" class="table table-striped table-hover align-middle mb-0 nowrap" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre de la Sala</th>
                                     <th>Numero de Camas</th>
                                    <th>Registrado por</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (count($salas) > 0): ?>
                                    <?php foreach ($salas as $s): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($s['id_sala']) ?></td>
                                            <td><?= htmlspecialchars($s['nombre']) ?></td>
                                             <td><?= htmlspecialchars($s['num_cama']) ?></td>
                                            <td><?= htmlspecialchars($s['usuario'] ?? '---') ?></td>

                                            <td>
                                                <button
                                                    class="btn btn-sm btn-primary me-2"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalEditarSala"
                                                    data-id="<?= $s['id_sala'] ?>"
                                                    data-nombre="<?= $s['nombre'] ?>"
                                                    data-numcama="<?= $s['num_cama'] ?>">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>


                                                <!-- Botón Eliminar Sala -->
                                                <button
                                                    class="btn btn-sm btn-danger me-2"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalEliminarSala"
                                                    data-id="<?= htmlspecialchars($s['id_sala']) ?>"
                                                    data-nombre="<?= htmlspecialchars($s['nombre']) ?>">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No hay salas registradas</td>
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
    <!-- Modal Registrar Sala -->
    <!-- Modal Registrar Sala -->
    <div class="modal fade" id="modalRegistrarSala" tabindex="-1" aria-labelledby="modalRegistrarSalaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content shadow-lg border-0 rounded-3">

                <div class="modal-header bg-primary text-white">
                    <i class="bi bi-house-fill fs-4 me-2"></i>
                    <h5 class="modal-title" id="modalRegistrarSalaLabel">Registrar Nueva Sala</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form action="../php/registrar_sala.php" method="POST" autocomplete="off">
                    <div class="modal-body p-4">
                        <div class="row g-3">

                            <!-- Nombre de la Sala -->
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-card-checklist"></i> Nombre de la Sala
                                </label>
                                <input type="text" class="form-control" name="nombre" required placeholder="Ej: Urgencias, Pediatría, Maternidad">
                            </div>

                            <!-- Número de Camas -->
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-hospital"></i> Nº de Camas
                                </label>
                                <input type="number" class="form-control" name="num_cama" required min="0" placeholder="Ej: 10">
                            </div>

                            <!-- id_usuario -->
                            <input type="hidden" name="id_usuario" value="<?= $_SESSION['id_usuario']; ?>">

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save-fill"></i> Guardar Sala
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>



    <!-- Modal Editar Sala -->
    <div class="modal fade" id="modalEditarSala" tabindex="-1" aria-labelledby="modalEditarSalaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content shadow-lg border-0 rounded-3">

                <div class="modal-header bg-primary text-white">
                    <i class="bi bi-pencil-square fs-4 me-2"></i>
                    <h5 class="modal-title" id="modalEditarSalaLabel">Editar Sala Hospitalaria</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form id="formEditarSala" action="../php/actualizar_sala.php" method="POST" autocomplete="off">
                    <div class="modal-body p-4">
                        <div class="row g-3">

                            <!-- ID oculto -->
                            <input type="hidden" name="id_sala" id="edit_id_sala">

                            <!-- Nombre de la sala -->
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-hospital"></i> Nombre de la Sala
                                </label>
                                <input type="text" class="form-control" name="nombre" id="edit_nombre" required>
                            </div>

                            <!-- Número de camas -->
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-hash"></i> Número de Camas
                                </label>
                                <input type="number" class="form-control" name="num_cama" id="edit_num_cama" min="0" required>
                            </div>

                            <!-- Usuario -->
                            <input type="hidden" name="id_usuario" value="<?= $_SESSION['id_usuario']; ?>">

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary text-white">
                            <i class="bi bi-save"></i> Guardar Cambios
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>





    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // Abrir modal editar y cargar datos
            const modalEditar = document.getElementById("modalEditarSala");

            modalEditar.addEventListener("show.bs.modal", function(event) {
                const button = event.relatedTarget;

                const id = button.getAttribute("data-id");
                const nombre = button.getAttribute("data-nombre");
                const numCama = button.getAttribute("data-numcama"); // si lo traes en la tabla

                // Rellenar los campos del modal
                document.getElementById("edit_id_sala").value = id;
                document.getElementById("edit_nombre").value = nombre;
                document.getElementById("edit_num_cama").value = numCama;
            });

        });
    </script>










    <?php
    include_once '../componentes/footer.php';
    ?>