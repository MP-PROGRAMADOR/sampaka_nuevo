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
                <button class="btn btn-primary btn-rounded shadow-sm" data-bs-toggle="modal" data-bs-target="#modalRegistrarPersonal">
                    <i class="bi bi-person-plus-fill me-2"></i> Añadir Nuevo Personal
                </button>
            </div>

            <!-- Tabla de pacientes -->
            <div class="card shadow-sm rounded-xl">
                <div class="card-body">
                    <h5 class="card-title mb-3">Lista de Personal</h5>



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
        $stmt = $pdo->query("SELECT p.id_personal, p.nombre, p.apellido, p.codigo, p.especialidad, p.cargo, 
                                    p.telefono, p.correo, p.direccion, p.nivel_estudios, p.nacionalidad, h.nombre AS hospital
                             FROM personal p
                             INNER JOIN hospitales h ON p.id_hospital = h.id_hospital
                             ORDER BY p.id_personal DESC");
        $personal = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "<tr><td colspan='12' class='text-danger'>Error al cargar personal: " . $e->getMessage() . "</td></tr>";
        exit;
    }
    ?>

    <table id="tablaPacientes" class="table table-striped table-hover align-middle mb-0 nowrap" style="width:100%">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Código</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Especialidad</th>
                <th>Cargo</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>Dirección</th>
                <th>Nivel de Estudios</th>
                <th>Nacionalidad</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($personal) > 0): ?>
                <?php foreach ($personal as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['id_personal']) ?></td>
                        <td><span class="badge bg-primary"><?= htmlspecialchars($p['codigo']) ?></span></td>
                        <td><?= htmlspecialchars($p['nombre']) ?></td>
                        <td><?= htmlspecialchars($p['apellido']) ?></td>
                        <td><?= htmlspecialchars($p['especialidad']) ?></td>
                        <td><?= htmlspecialchars($p['cargo']) ?></td>
                        <td><?= htmlspecialchars($p['telefono']) ?></td>
                        <td><?= htmlspecialchars($p['correo']) ?></td>
                        <td><?= htmlspecialchars($p['direccion']) ?></td>
                        <td><?= htmlspecialchars($p['nivel_estudios']) ?></td>
                        <td><?= htmlspecialchars($p['nacionalidad']) ?></td>
                        <td>
                            <button
                                class="btn btn-sm btn-outline-primary me-2"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEditarPersonal"
                                data-id="<?= $p['id_personal'] ?>"
                                data-nombre="<?= htmlspecialchars($p['nombre']) ?>"
                                data-apellido="<?= htmlspecialchars($p['apellido']) ?>"
                                data-especialidad="<?= htmlspecialchars($p['especialidad']) ?>"
                                data-cargo="<?= htmlspecialchars($p['cargo']) ?>"
                                data-telefono="<?= htmlspecialchars($p['telefono']) ?>"
                                data-correo="<?= htmlspecialchars($p['correo']) ?>"
                                data-direccion="<?= htmlspecialchars($p['direccion']) ?>"
                                data-nivel_estudios="<?= htmlspecialchars($p['nivel_estudios']) ?>"
                                data-nacionalidad="<?= htmlspecialchars($p['nacionalidad']) ?>">
                                <i class="bi bi-pencil-square"></i>
                            </button>

                            <button class="btn btn-sm btn-outline-danger" onclick="eliminarPersonal(<?= $p['id_personal'] ?>)">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="13" class="text-center text-muted">No hay personal registrado</td>
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
   <div class="modal fade" id="modalRegistrarPersonal" tabindex="-1" aria-labelledby="modalRegistrarPersonalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow-lg border-0 rounded-3">
            
            <!-- Header -->
            <div class="modal-header bg-primary text-white">
                <i class="bi bi-person-badge-fill fs-4 me-2"></i>
                <h5 class="modal-title" id="modalRegistrarPersonalLabel">Registrar Personal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- Formulario -->
            <form id="formRegistrarPersonal" action="../php/registrar_personal.php" method="POST" autocomplete="off">
                <div class="modal-body p-4">
                    <div class="row g-3">

                        <!-- Nombre -->
                        <div class="col-md-6">
                            <label for="nombre" class="form-label">
                                <i class="bi bi-person-fill"></i> Nombre
                            </label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>

                        <!-- Apellido -->
                        <div class="col-md-6">
                            <label for="apellido" class="form-label">
                                <i class="bi bi-person-lines-fill"></i> Apellido
                            </label>
                            <input type="text" class="form-control" id="apellido" name="apellido" required>
                        </div>

                        <!-- Especialidad -->
                        <div class="col-md-6">
                            <label for="especialidad" class="form-label">
                                <i class="bi bi-clipboard2-pulse"></i> Especialidad
                            </label>
                            <input type="text" class="form-control" id="especialidad" name="especialidad" required>
                        </div>

                        <!-- Cargo -->
                        <div class="col-md-6">
                            <label for="cargo" class="form-label">
                                <i class="bi bi-briefcase-fill"></i> Cargo
                            </label>
                            <input type="text" class="form-control" id="cargo" name="cargo" required>
                        </div>

                        <!-- Teléfono -->
                        <div class="col-md-6">
                            <label for="telefono" class="form-label">
                                <i class="bi bi-telephone-fill"></i> Teléfono
                            </label>
                            <input type="text" class="form-control" id="telefono" name="telefono" placeholder="+240 555 123456" required>
                        </div>

                        <!-- Correo -->
                        <div class="col-md-6">
                            <label for="correo" class="form-label">
                                <i class="bi bi-envelope-fill"></i> Correo
                            </label>
                            <input type="email" class="form-control" id="correo" name="correo" placeholder="ejemplo@email.com" required>
                        </div>

                        <!-- Dirección -->
                        <div class="col-md-12">
                            <label for="direccion" class="form-label">
                                <i class="bi bi-geo-alt-fill"></i> Dirección
                            </label>
                            <input type="text" class="form-control" id="direccion" name="direccion" required>
                        </div>

                        <!-- Nivel de Estudios -->
                        <div class="col-md-6">
                            <label for="nivel_estudios" class="form-label">
                                <i class="bi bi-mortarboard-fill"></i> Nivel de Estudios
                            </label>
                            <input type="text" class="form-control" id="nivel_estudios" name="nivel_estudios" required>
                        </div>

                        <!-- Nacionalidad -->
                        <div class="col-md-6">
                            <label for="nacionalidad" class="form-label">
                                <i class="bi bi-flag-fill"></i> Nacionalidad
                            </label>
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

                        <!-- Código (automático, no editable) -->
                        <div class="col-md-12">
                            <label for="codigo" class="form-label">
                                <i class="bi bi-upc-scan"></i> Código
                            </label>
                            <input type="text" class="form-control" id="codigo" name="codigo" readonly placeholder="Generado automáticamente">
                        </div>

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
 <!-- Modal Editar Personal -->
<div class="modal fade" id="modalEditarPersonal" tabindex="-1" aria-labelledby="modalEditarPersonalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow-lg border-0 rounded-3">
            <div class="modal-header bg-primary text-white">
                <i class="bi bi-pencil-square fs-4 me-2"></i>
                <h5 class="modal-title" id="modalEditarPersonalLabel">Editar Personal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form id="formEditarPersonal" action="../php/actualizar_personal.php" method="POST" autocomplete="off">
                <div class="modal-body p-4">
                    <input type="hidden" id="id_personal" name="id_personal">

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label for="nombre_edit" class="form-label"><i class="bi bi-person-fill"></i> Nombre</label>
                            <input type="text" class="form-control" id="nombre_edit" name="nombre" required>
                        </div>

                        <div class="col-md-6">
                            <label for="apellido_edit" class="form-label"><i class="bi bi-person-lines-fill"></i> Apellido</label>
                            <input type="text" class="form-control" id="apellido_edit" name="apellido" required>
                        </div>

                        <div class="col-md-6">
                            <label for="especialidad_edit" class="form-label"><i class="bi bi-hospital"></i> Especialidad</label>
                            <input type="text" class="form-control" id="especialidad_edit" name="especialidad" required>
                        </div>

                        <div class="col-md-6">
                            <label for="cargo_edit" class="form-label"><i class="bi bi-briefcase-fill"></i> Cargo</label>
                            <input type="text" class="form-control" id="cargo_edit" name="cargo" required>
                        </div>

                        <div class="col-md-6">
                            <label for="telefono_edit" class="form-label"><i class="bi bi-telephone-fill"></i> Teléfono</label>
                            <input type="text" class="form-control" id="telefono_edit" name="telefono" required>
                        </div>

                        <div class="col-md-6">
                            <label for="correo_edit" class="form-label"><i class="bi bi-envelope-fill"></i> Correo</label>
                            <input type="email" class="form-control" id="correo_edit" name="correo" required>
                        </div>

                        <div class="col-md-12">
                            <label for="direccion_edit" class="form-label"><i class="bi bi-geo-alt-fill"></i> Dirección</label>
                            <input type="text" class="form-control" id="direccion_edit" name="direccion" required>
                        </div>

                        <div class="col-md-6">
                            <label for="nivel_estudios_edit" class="form-label"><i class="bi bi-mortarboard-fill"></i> Nivel de Estudios</label>
                            <input type="text" class="form-control" id="nivel_estudios_edit" name="nivel_estudios" required>
                        </div>

                        <div class="col-md-6">
                            <label for="nacionalidad_edit" class="form-label"><i class="bi bi-flag-fill"></i> Nacionalidad</label>
                            <input type="text" class="form-control" id="nacionalidad_edit" name="nacionalidad" required>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary text-white">
                        <i class="bi bi-save-fill"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>




<script>
document.addEventListener("DOMContentLoaded", () => {
    const modalEditar = document.getElementById("modalEditarPersonal");

    modalEditar.addEventListener("show.bs.modal", function (event) {
        const button = event.relatedTarget;

        document.getElementById("id_personal").value = button.getAttribute("data-id");
        document.getElementById("nombre_edit").value = button.getAttribute("data-nombre");
        document.getElementById("apellido_edit").value = button.getAttribute("data-apellido");
        document.getElementById("especialidad_edit").value = button.getAttribute("data-especialidad");
        document.getElementById("cargo_edit").value = button.getAttribute("data-cargo");
        document.getElementById("telefono_edit").value = button.getAttribute("data-telefono");
        document.getElementById("correo_edit").value = button.getAttribute("data-correo");
        document.getElementById("direccion_edit").value = button.getAttribute("data-direccion");
        document.getElementById("nivel_estudios_edit").value = button.getAttribute("data-nivel_estudios");
        document.getElementById("nacionalidad_edit").value = button.getAttribute("data-nacionalidad");
    });
});
</script>





    <?php
    include_once '../componentes/footer.php';
    ?>