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
                <button class="btn btn-primary btn-rounded shadow-sm" data-bs-toggle="modal" data-bs-target="#modalRegistrarPaciente">
                    <i class="bi bi-person-plus-fill me-2"></i> Añadir Nuevo Paciente
                </button>
            </div>

            <div class="card shadow-sm rounded-xl">
                <div class="card-body">
                    <h5 class="card-title mb-3 fw-bold"> Lista de Pacientes</h5>

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
                            // Uso de prepared statements (aunque aquí es una consulta simple sin parámetros)
                            $stmt = $pdo->query("SELECT id_paciente, nombre, apellido, sexo, nacionalidad, telefono, ocupacion, fecha_registro, codigo 
                                FROM pacientes 
                                ORDER BY fecha_registro DESC");
                            $pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        } catch (PDOException $e) {
                            // Mejorar manejo de errores para evitar exponer detalles sensibles al usuario
                            echo "<div class='alert alert-danger'>Error al cargar pacientes: " . $e->getMessage() . "</div>";
                            $pacientes = [];
                        }
                        ?>

                        <table id="tablaPacientes" class="table table-striped table-hover align-middle mb-0 nowrap" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Apellido</th>
                                    <th>Código</th>
                                    <th>Sexo</th>
                                    <th>Nacionalidad</th>
                                    <th>Teléfono</th>
                                    <th>Ocupación</th>
                                    <th>Fecha Registro</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($pacientes) > 0): ?>
                                    <?php foreach ($pacientes as $p): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($p['id_paciente']) ?></td>
                                            <td><?= htmlspecialchars($p['nombre']) ?></td>
                                            <td><?= htmlspecialchars($p['apellido']) ?></td>
                                            <td><?= htmlspecialchars($p['codigo']) ?></td>
                                            <td><?= htmlspecialchars($p['sexo']) ?></td>
                                            <td><?= htmlspecialchars($p['nacionalidad']) ?></td>
                                            <td><?= htmlspecialchars($p['telefono']) ?></td>
                                            <td><?= htmlspecialchars($p['ocupacion']) ?></td>
                                            <td><?= htmlspecialchars($p['fecha_registro']) ?></td>
                                            <td>
                                                <!-- Boton de Editar Paciente-->
                                                <button
                                                    class="btn btn-sm btn-primary me-2"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalEditarPaciente"
                                                    data-id="<?= htmlspecialchars($p['id_paciente']) ?>"
                                                    data-nombre="<?= htmlspecialchars($p['nombre']) ?>"
                                                    data-apellido="<?= htmlspecialchars($p['apellido']) ?>"
                                                    data-sexo="<?= htmlspecialchars($p['sexo']) ?>"
                                                    data-nacionalidad="<?= htmlspecialchars($p['nacionalidad']) ?>"
                                                    data-telefono="<?= htmlspecialchars($p['telefono']) ?>"
                                                    data-ocupacion="<?= htmlspecialchars($p['ocupacion']) ?>">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>

                                                <!-- Boton de Eliminar Paciente-->
                                                <button
                                                    class="btn btn-sm btn-outline-danger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalConfirmarEliminacion"
                                                    data-id="<?= htmlspecialchars($p['id_paciente']) ?>"
                                                    data-nombre-completo="<?= htmlspecialchars($p['nombre'] . ' ' . $p['apellido']) ?>">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="10" class="text-center text-muted">No hay pacientes registrados</td>
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
    <div class="modal fade" id="modalRegistrarPaciente" tabindex="-1" aria-labelledby="modalRegistrarPacienteLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content shadow-lg border-0 rounded-3">
                <div class="modal-header bg-primary text-white">
                    <i class="bi bi-person-plus-fill fs-4 me-2"></i>
                    <h5 class="modal-title" id="modalRegistrarPacienteLabel"> Registrar Nuevo Paciente</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form id="formRegistrarPaciente" action="../php/registrar_paciente.php" method="POST" autocomplete="off">
                    <div class="modal-body p-4">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label for="nombre" class="form-label"><i class="bi bi-person-fill"></i> Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>

                            <div class="col-md-6">
                                <label for="apellido" class="form-label"><i class="bi bi-person-lines-fill"></i> Apellido</label>
                                <input type="text" class="form-control" id="apellido" name="apellido" required>
                            </div>

                            <div class="col-md-6">
                                <label for="sexo" class="form-label"><i class="bi bi-gender-ambiguous"></i> Sexo</label>
                                <select class="form-select" id="sexo" name="sexo" required>
                                    <option value="">Selecciona...</option>
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="fecha_nacimiento" class="form-label"><i class="bi bi-calendar-date"></i> Fecha de Nacimiento</label>
                                <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
                            </div>

                            <div class="col-md-6">
                                <label for="correo" class="form-label"><i class="bi bi-envelope-fill"></i> Correo</label>
                                <input type="email" class="form-control" id="correo" name="correo" placeholder="ejemplo@email.com">
                            </div>

                            <div class="col-md-6">
                                <label for="telefono" class="form-label"><i class="bi bi-telephone-fill"></i> Teléfono</label>
                                <input type="text" class="form-control" id="telefono" name="telefono" placeholder="+240 555 123456">
                            </div>

                            <div class="col-md-12">
                                <label for="direccion" class="form-label"><i class="bi bi-geo-alt-fill"></i> Dirección</label>
                                <input type="text" class="form-control" id="direccion" name="direccion">
                            </div>

                            <div class="col-md-6">
                                <label for="nacionalidad" class="form-label"><i class="bi bi-flag-fill"></i> Nacionalidad</label>
                                <select class="form-select" id="nacionalidad" name="nacionalidad" required>
                                    <option value="">Selecciona...</option>
                                    <option value="Guinea Ecuatorial">Guinea Ecuatorial</option>
                                    <option value="España">España</option>
                                    <option value="Camerún">Camerún</option>
                                    <option value="Nigeria">Nigeria</option>
                                    <option value="Gabón">Gabón</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="ocupacion" class="form-label"><i class="bi bi-briefcase-fill"></i> Ocupación</label>
                                <input type="text" class="form-control" id="ocupacion" name="ocupacion">
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save-fill"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Modal de Editar Paciente-->
    <div class="modal fade" id="modalEditarPaciente" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Editar Paciente</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarPaciente" action="../php/actualizar_paciente.php" method="POST">
                        <input type="hidden" id="edit_id_paciente" name="id_paciente">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="edit_nombre" name="nombre">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Apellido</label>
                                <input type="text" class="form-control" id="edit_apellido" name="apellido">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sexo</label>
                                <select class="form-select" id="edit_sexo" name="sexo">
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nacionalidad</label>
                                <input type="text" class="form-control" id="edit_nacionalidad" name="nacionalidad">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Teléfono</label>
                                <input type="text" class="form-control" id="edit_telefono" name="telefono">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ocupación</label>
                                <input type="text" class="form-control" id="edit_ocupacion" name="ocupacion">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" form="formEditarPaciente" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>



    <script>
        // Lógica para llenar los campos del modal de edición
        document.getElementById('modalEditarPaciente').addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;

            if (!button) return;
            // Extraer atributos y asignarlos a los campos con IDs únicos (edit)
            document.getElementById('edit_id_paciente').value = button.getAttribute('data-id') || "";
            document.getElementById('edit_nombre').value = button.getAttribute('data-nombre') || "";
            document.getElementById('edit_apellido').value = button.getAttribute('data-apellido') || "";
            document.getElementById('edit_sexo').value = button.getAttribute('data-sexo') || "";
            document.getElementById('edit_nacionalidad').value = button.getAttribute('data-nacionalidad') || "";
            document.getElementById('edit_telefono').value = button.getAttribute('data-telefono') || "";
            document.getElementById('edit_ocupacion').value = button.getAttribute('data-ocupacion') || "";
        });

        // LÓGICA DEL NUEVO MODAL DE CONFIRMACIÓN DE ELIMINACIÓN
        const modalEliminar = document.getElementById('modalConfirmarEliminacion');
        const inputId = document.getElementById('idPacienteEliminar');
        const h6Nombre = document.getElementById('nombrePacienteEliminar');
        const btnConfirmar = document.getElementById('btnEliminarConfirmado');

        // 1. Al abrir el modal, llenamos los datos del paciente
        modalEliminar.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const pacienteId = button.getAttribute('data-id');
            const pacienteNombre = button.getAttribute('data-nombre-completo');

            // Mostrar el nombre del paciente y guardar el ID para la eliminación
            inputId.value = pacienteId;
            h6Nombre.textContent = pacienteNombre;
        });

        // 2. Al hacer clic en el botón de confirmación dentro del modal
        btnConfirmar.addEventListener('click', function() {
            const id = inputId.value;
            if (id) {
                const url = '../php/eliminar_paciente.php?id=' + id;
                window.location.href = url;
            }
        });

        // Lógica para el mensaje de fade-out (si es necesario para fade-msg)
        document.addEventListener('DOMContentLoaded', function() {
            const fadeMessages = document.querySelectorAll('.fade-msg');
            fadeMessages.forEach(function(msg) {
                setTimeout(function() {
                    msg.style.transition = 'opacity 1s ease-out';
                    msg.style.opacity = '0';
                    setTimeout(function() {
                        msg.remove();
                    }, 1000);
                }, 5000);
            });
        });
    </script>


    <!-- Modal Confirmar Eliminacion de Paciente-->
    <div class="modal fade" id="modalConfirmarEliminacion" tabindex="-1" aria-labelledby="modalConfirmarEliminacionLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="modalConfirmarEliminacionLabel"><i class="bi bi-exclamation-triangle-fill me-2"></i> Confirmar Eliminación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body text-center">
                    <p>¿Está seguro que desea eliminar al paciente:</p>
                    <h6 class="fw-bold text-danger mb-3" id="nombrePacienteEliminar"></h6>
                    <p class="text-muted small">Esta acción no se puede deshacer.</p>
                    <input type="hidden" id="idPacienteEliminar">
                </div>
                <div class="modal-footer justify-content-center border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-danger" id="btnEliminarConfirmado">
                        <i class="bi bi-trash"></i> Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>


    <?php
    include_once '../componentes/footer.php';
    ?>