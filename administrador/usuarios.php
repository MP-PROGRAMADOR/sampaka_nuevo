<?php
include_once '../componentes/header.php';
?>

<body>

    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->

        <?php include_once '../componentes/sidebar.php'; ?>

        <!-- Page Content -->
        <div id="content" class="p-4 bg-gray-100 flex-grow">
            <!-- Navbar -->
            <?php
            include_once '../componentes/barra_nav.php';
            ?>

            <!-- Botón añadir paciente -->
            <div class="d-flex justify-content-end mb-3">
                <button class="btn btn-primary btn-rounded shadow-sm" data-bs-toggle="modal" data-bs-target="#modalRegistrarUsuario">
                    <i class="bi bi-person-plus-fill me-2"></i> Añadir Nuevo Usuario
                </button>
            </div>

            <!-- Tabla de pacientes -->
            <div class="card shadow-sm rounded-xl">
                <div class="card-body">
                    <h5 class="card-title mb-3">Lista de Usuarios</h5>



                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success fade-msg"><?= $_SESSION['success']; ?></div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger fade-msg"><?= $_SESSION['error']; ?></div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>



                    <div class="table-responsive">
                        <?php
                        require_once "../config/conexion.php";

                        try {
                            $stmt = $pdo->query("
            SELECT u.id_usuario, u.username, u.rol, u.estado, 
                   p.nombre, p.apellido, p.codigo, p.cargo, p.id_personal, h.nombre AS hospital
            FROM usuarios u
            INNER JOIN personal p ON u.id_personal = p.id_personal
            INNER JOIN hospitales h ON p.id_hospital = h.id_hospital
            ORDER BY u.id_usuario DESC
        ");
                            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='10' class='text-danger'>Error al cargar usuarios: " . $e->getMessage() . "</td></tr>";
                            exit;
                        }
                        ?>

                        <table id="tablaPacientes" class="table table-striped table-hover align-middle mb-0 nowrap" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Usuario</th>
                                    <th>Rol</th>
                                    <th>Estado</th>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Apellido</th>
                                    <th>Cargo</th>
                                    <th>Hospital</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($usuarios) > 0): ?>
                                    <?php foreach ($usuarios as $u): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($u['id_usuario']) ?></td>
                                            <td><span class="fw-bold text-primary"><?= htmlspecialchars($u['username']) ?></span></td>
                                            <td><span class="badge bg-info"><?= htmlspecialchars($u['rol']) ?></span></td>
                                            <td>
                                                <?php if ($u['estado'] == "Activo"): ?>
                                                    <span class="badge bg-success">Activo</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Inactivo</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($u['codigo']) ?></td>
                                            <td><?= htmlspecialchars($u['nombre']) ?></td>
                                            <td><?= htmlspecialchars($u['apellido']) ?></td>
                                            <td><?= htmlspecialchars($u['cargo']) ?></td>
                                            <td><?= htmlspecialchars($u['hospital']) ?></td>
                                            <td>
                                                <button
                                                    class="btn btn-sm btn-outline-primary me-2"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalEditarUsuario"
                                                    data-id="<?= $u['id_usuario'] ?>"
                                                    data-username="<?= htmlspecialchars($u['username']) ?>"
                                                    data-id_personal="<?= htmlspecialchars($u['id_personal']) ?>"
                                                    data-rol="<?= htmlspecialchars($u['rol']) ?>"
                                                    data-estado="<?= htmlspecialchars($u['estado']) ?>"
                                                    data-nombre="<?= htmlspecialchars($u['nombre']) ?>"
                                                    data-apellido="<?= htmlspecialchars($u['apellido']) ?>">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>

                                                <button
                                                    class="btn btn-sm btn-outline-toggle btnEstadoUsuario"
                                                    data-id="<?= $u['id_usuario'] ?>"
                                                    data-nombre="<?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido']) ?>"
                                                    data-estado="<?= $u['estado'] ?>">
                                                    <i class="bi bi-person-dash"></i> <?= $u['estado'] === 'Activo' ? 'Desactivar' : 'Activar' ?>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="10" class="text-center text-muted">No hay usuarios registrados</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>


                </div>
            </div>
        </div>



    </div>



    <!-- Modal Registrar Paciente -->
    <div class="modal fade" id="modalRegistrarUsuario" tabindex="-1" aria-labelledby="modalRegistrarUsuarioLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content shadow-lg border-0 rounded-3">

                <!-- Header -->
                <div class="modal-header bg-primary text-white">
                    <i class="bi bi-person-badge-fill fs-4 me-2"></i>
                    <h5 class="modal-title" id="modalRegistrarUsuarioLabel">Registrar Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <!-- Formulario -->
                <form id="formRegistrarUsuario" action="../php/registrar_usuario.php" method="POST" autocomplete="off">
                    <div class="modal-body p-4">

                        <!-- 🔍 Buscador de Personal -->
                        <div class="mb-3">
                            <label for="buscarPersonal" class="form-label">
                                <i class="bi bi-search"></i> Buscar Personal
                            </label>
                            <input type="text" class="form-control" id="buscarPersonal" placeholder="Escribe nombre o apellido...">
                        </div>

                        <!-- Resultados del buscador -->
                        <div id="resultadosPersonal" class="list-group mb-3" style="max-height:200px; overflow-y:auto;">
                            <!-- Resultados en tiempo real con AJAX -->
                        </div>

                        <!-- Personal Seleccionado -->
                        <div id="personalSeleccionado" class="alert alert-info d-none">
                            <i class="bi bi-person-check-fill"></i>
                            <span id="datosPersonal"></span>
                            <input type="hidden" name="id_personal" id="id_personal">
                        </div>

                        <hr>

                        <!-- Username -->
                        <div class="mb-3">
                            <label for="username" class="form-label">
                                <i class="bi bi-person-fill"></i> Nombre de Usuario
                            </label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="bi bi-lock-fill"></i> Contraseña
                            </label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <!-- Rol -->
                        <div class="mb-3">
                            <label for="rol" class="form-label">
                                <i class="bi bi-shield-lock-fill"></i> Rol
                            </label>
                            <select class="form-select" id="rol" name="rol" required>
                                <option value="">Selecciona...</option>
                                <option value="Administrador">Administrador</option>
                                <option value="General">General</option>
                                <option value="Urgencias">Urgencias</option>
                                <option value="Farmaceutico">Farmacéutico</option>
                                <option value="Laboratorio">Laboratorio</option>
                                <option value="Finanzas">Finanzas</option>
                            </select>
                        </div>

                        <!-- Estado -->
                        <div class="mb-3">
                            <label for="estado" class="form-label">
                                <i class="bi bi-toggle-on"></i> Estado
                            </label>
                            <select class="form-select" id="estado" name="estado" required>
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                            </select>
                        </div>
                    </div>

                    <!-- Footer -->
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





    <!-- Modal para editar paciente -->
    <div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-labelledby="modalEditarUsuarioLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content shadow-lg border-0 rounded-3">

                <!-- Header -->
                <div class="modal-header bg-primary text-dark">
                    <i class="bi bi-person-badge-fill fs-4 me-2"></i>
                    <h5 class="modal-title" id="modalEditarUsuarioLabel">Editar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <!-- Formulario -->
                <form id="formEditarUsuario" action="../php/actualizar_usuario.php" method="POST" autocomplete="off">
                    <div class="modal-body p-4">

                        <input type="hidden" name="id_usuario" id="edit_id_usuario">
                        <input type="hidden" name="id_personal" id="edit_id_personal">

                        <!-- Personal Asociado (no editable) -->
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-person-fill"></i> Personal
                            </label>
                            <input type="text" class="form-control" id="edit_personal" readonly>
                        </div>

                        <!-- Username -->
                        <div class="mb-3">
                            <label for="edit_username" class="form-label">
                                <i class="bi bi-person-fill"></i> Nombre de Usuario
                            </label>
                            <input type="text" class="form-control" id="edit_username" name="username" required>
                        </div>

                        <!-- Password (opcional) -->
                        <div class="mb-3">
                            <label for="edit_password" class="form-label">
                                <i class="bi bi-lock-fill"></i> Nueva Contraseña (opcional)
                            </label>
                            <input type="password" class="form-control" id="edit_password" name="password" placeholder="Dejar en blanco para no cambiar">
                        </div>

                        <!-- Rol -->
                        <div class="mb-3">
                            <label for="edit_rol" class="form-label">
                                <i class="bi bi-shield-lock-fill"></i> Rol
                            </label>
                            <select class="form-select" id="edit_rol" name="rol" required>
                                <option value="">Selecciona...</option>
                                <option value="Administrador">Administrador</option>
                                <option value="General">General</option>
                                <option value="Urgencias">Urgencias</option>
                                <option value="Farmaceutico">Farmacéutico</option>
                                <option value="Laboratorio">Laboratorio</option>
                                <option value="Finanzas">Finanzas</option>
                            </select>
                        </div>

                        <!-- Estado -->
                        <div class="mb-3">
                            <label for="edit_estado" class="form-label">
                                <i class="bi bi-toggle-on"></i> Estado
                            </label>
                            <select class="form-select" id="edit_estado" name="estado" required>
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                            </select>
                        </div>

                    </div>

                    <!-- Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save-fill"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <!-- Modal de Confirmación -->
  <!-- Modal Profesional de Confirmación -->
<div class="modal fade" id="modalConfirmarEstado" tabindex="-1" aria-labelledby="modalConfirmarEstadoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4">

            <!-- Header Dinámico -->
            <div class="modal-header text-white" id="modalHeaderEstado">
                <h5 class="modal-title" id="modalConfirmarEstadoLabel">
                    <i id="iconoEstado" class="bi me-2"></i> Confirmar Acción
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- Body -->
            <div class="modal-body text-center">
                <p class="fs-6">¿Está seguro que desea <span id="accionUsuario"></span> al usuario <strong id="nombreUsuario"></strong>?</p>
                <p class="text-muted small">Esta acción cambiará el estado del usuario en el sistema.</p>
            </div>

            <!-- Footer -->
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancelar
                </button>

                <form id="formCambiarEstado" method="POST" action="../php/cambiar_estado_usuario.php">
                    <input type="hidden" name="id_usuario" id="modal_id_usuario">
                    <input type="hidden" name="nuevo_estado" id="modal_nuevo_estado">
                    <button type="submit" class="btn" id="btnConfirmarEstado">
                        <i class="bi me-1" id="iconoBoton"></i> Confirmar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>



    <script>
        // Cuando se abre el modal, llenamos los campos con los datos del botón
        var modalEditarUsuario = document.getElementById('modalEditarUsuario');
        modalEditarUsuario.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;

            var id_usuario = button.getAttribute('data-id');
            var username = button.getAttribute('data-username');
            var rol = button.getAttribute('data-rol');
            var estado = button.getAttribute('data-estado');
            var nombre = button.getAttribute('data-nombre');
            var apellido = button.getAttribute('data-apellido');
            var id_personal = button.getAttribute('data-id_personal');

            modalEditarUsuario.querySelector('#edit_id_usuario').value = id_usuario;
            modalEditarUsuario.querySelector('#edit_id_personal').value = id_personal;
            modalEditarUsuario.querySelector('#edit_personal').value = nombre + ' ' + apellido;
            modalEditarUsuario.querySelector('#edit_username').value = username;
            modalEditarUsuario.querySelector('#edit_rol').value = rol;
            modalEditarUsuario.querySelector('#edit_estado').value = estado;
        });
    </script>





    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const inputBuscar = document.getElementById("buscarPersonal");
            const resultadosDiv = document.getElementById("resultadosPersonal");
            const personalSeleccionado = document.getElementById("personalSeleccionado");
            const datosPersonal = document.getElementById("datosPersonal");
            const idPersonalInput = document.getElementById("id_personal");

            // Buscar en tiempo real
            inputBuscar.addEventListener("keyup", async () => {
                const query = inputBuscar.value.trim();

                if (query.length < 2) {
                    resultadosDiv.innerHTML = "";
                    return;
                }

                try {
                    const response = await fetch("../php/buscar_personal.php?q=" + encodeURIComponent(query));
                    const data = await response.json();

                    resultadosDiv.innerHTML = "";

                    if (data.length > 0) {
                        data.forEach(persona => {
                            const item = document.createElement("button");
                            item.classList.add("list-group-item", "list-group-item-action");
                            item.innerHTML = `<i class="bi bi-person"></i> ${persona.nombre} ${persona.apellido} 
                                      <small class="text-muted">(${persona.cargo})</small>`;

                            item.addEventListener("click", () => {
                                idPersonalInput.value = persona.id_personal;
                                datosPersonal.innerHTML = `<strong>${persona.nombre} ${persona.apellido}</strong> - ${persona.cargo}`;
                                personalSeleccionado.classList.remove("d-none");
                                resultadosDiv.innerHTML = "";
                                inputBuscar.value = "";
                            });

                            resultadosDiv.appendChild(item);
                        });
                    } else {
                        resultadosDiv.innerHTML = `<div class="list-group-item text-muted">No se encontraron resultados</div>`;
                    }

                } catch (error) {
                    console.error("Error en la búsqueda:", error);
                }
            });
        });
    </script>


<script>
document.querySelectorAll('.btnEstadoUsuario').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        const nombre = this.dataset.nombre;
        const estadoActual = this.dataset.estado;

        const nuevoEstado = estadoActual === 'Activo' ? 'Inactivo' : 'Activo';
        const accion = estadoActual === 'Activo' ? 'desactivar' : 'activar';

        // Textos dinámicos
        document.getElementById('nombreUsuario').textContent = nombre;
        document.getElementById('accionUsuario').textContent = accion;
        document.getElementById('modal_id_usuario').value = id;
        document.getElementById('modal_nuevo_estado').value = nuevoEstado;

        // Cambiar colores e iconos según la acción
        const header = document.getElementById('modalHeaderEstado');
        const iconoHeader = document.getElementById('iconoEstado');
        const btnConfirm = document.getElementById('btnConfirmarEstado');
        const iconoBtn = document.getElementById('iconoBoton');

        if (nuevoEstado === 'Inactivo') {
            header.className = 'modal-header bg-danger text-white';
            iconoHeader.className = 'bi bi-person-dash me-2';
            btnConfirm.className = 'btn btn-danger';
            iconoBtn.className = 'bi bi-person-dash me-1';
        } else {
            header.className = 'modal-header bg-success text-white';
            iconoHeader.className = 'bi bi-person-check me-2';
            btnConfirm.className = 'btn btn-success';
            iconoBtn.className = 'bi bi-person-check me-1';
        }

        // Mostrar el modal
        const modal = new bootstrap.Modal(document.getElementById('modalConfirmarEstado'));
        modal.show();
    });
});
</script>



    <?php
    include_once '../componentes/footer.php';
    ?>