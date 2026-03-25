<?php
// Iniciar sesión y componentes de cabecera
include_once '../componentes/header.php';
require_once "../config/conexion.php";

// Consulta para obtener hospitalizaciones activas e históricas
try {
    $sql = "SELECT h.*, p.nombre, p.apellido, s.nombre as sala_nombre, hosp.nombre as hospital_nombre 
            FROM hospitalizaciones h
            JOIN pacientes p ON h.id_paciente = p.id_paciente
            JOIN salas s ON h.id_sala = s.id_sala
            JOIN hospitales hosp ON h.id_hospital = hosp.id_hospital
            ORDER BY h.fecha_ingreso DESC";
    $stmt = $pdo->query($sql);
    $hospitalizaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener datos auxiliares para los selects de los modales
    $pacientes = $pdo->query("SELECT id_paciente, nombre, apellido FROM pacientes")->fetchAll(PDO::FETCH_ASSOC);
    $salas = $pdo->query("SELECT id_sala, nombre FROM salas")->fetchAll(PDO::FETCH_ASSOC);
    $centros = $pdo->query("SELECT id_hospital, nombre FROM hospitales")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_db = $e->getMessage();
    $hospitalizaciones = [];
}
?>

<body>
    <div class="d-flex" id="wrapper">
        <?php include_once '../componentes/sidebar.php'; ?>

        <div id="content" class="p-4 bg-gray-100 flex-grow">

            <div class="table-responsive">

                <div class="card shadow-sm rounded-xl">
                    <div class="card-body">

                        <?php include_once '../componentes/barra_nav.php'; ?>
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success fade-msg"><?= htmlspecialchars($_SESSION['success']); ?></div>
                            <?php unset($_SESSION['success']); ?>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger fade-msg"><?= htmlspecialchars($_SESSION['error']); ?></div>
                            <?php unset($_SESSION['error']); ?>
                        <?php endif; ?>

                        <div class="table-responsive">
                            <table id="tablaPacientes" class="table table-striped table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Paciente</th>
                                        <th>Centro</th>
                                        <th>Sala/Cama</th>
                                        <th>Ingreso</th>
                                        <th>Estado/Alta</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($hospitalizaciones) > 0): ?>
                                        <?php foreach ($hospitalizaciones as $h): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($h['nombre'] . ' ' . $h['apellido']) ?></td>
                                                <td><?= htmlspecialchars($h['hospital_nombre']) ?></td>
                                                <td> <span class="badge bg-info text-dark"><?= htmlspecialchars($h['sala_nombre']) ?></span>
                                                    <small class="d-block text-muted">Cama: <?= htmlspecialchars($h['numero_cama']) ?></small>
                                                </td>
                                                <td><?= date('d/m/Y', strtotime($h['fecha_ingreso'])) ?></td>
                                                <td> <?php if ($h['fecha_alta']): ?>
                                                        <span class="badge bg-success">Alta: <?= htmlspecialchars($h['estado_alta']) ?></span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning text-dark">En Espera</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!$h['fecha_alta']): ?>
                                                        <button class="btn btn-sm btn-primary me-1"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalEditarHospitalizacion"
                                                            data-id="<?= $h['id_hospitalizacion'] ?>"
                                                            data-paciente-id="<?= $h['id_paciente'] ?>"
                                                            data-hospital-id="<?= $h['id_hospital'] ?>"
                                                            data-sala-id="<?= $h['id_sala'] ?>"
                                                            data-cama="<?= htmlspecialchars($h['numero_cama']) ?>"
                                                            data-fecha="<?= $h['fecha_ingreso'] ?>"
                                                            data-causa="<?= htmlspecialchars($h['causa']) ?>">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </button>

                                                        <button class="btn btn-sm btn-success"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalDarAlta"
                                                            data-id="<?= $h['id_hospitalizacion'] ?>"
                                                            data-paciente="<?= htmlspecialchars($h['nombre'] . ' ' . $h['apellido']) ?>">
                                                            <i class="bi bi-check-circle"></i> Alta
                                                        </button>
                                                    <?php else: ?>
                                                        <span class="text-muted small"><i class="bi bi-lock-fill"></i> Registro cerrado</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No hay registros de hospitalización.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalEditarHospitalizacion" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Editar Datos de Ingreso</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="../php/guardar_hospitalizaciones.php" method="POST">
                        <input type="hidden" name="id_hospitalizacion" id="edit_id">

                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">Paciente</label>
                                    <select class="form-select" name="id_paciente" id="edit_paciente" required>
                                        <?php foreach ($pacientes as $p): ?>
                                            <option value="<?= $p['id_paciente'] ?>"><?= $p['nombre'] ?> <?= $p['apellido'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Centro</label>
                                    <select class="form-select" name="id_hospital" id="edit_hospital">
                                        <?php foreach ($centros as $c): ?>
                                            <option value="<?= $c['id_hospital'] ?>"><?= $c['nombre'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Sala</label>
                                    <select class="form-select" name="id_sala" id="edit_sala">
                                        <?php foreach ($salas as $s): ?>
                                            <option value="<?= $s['id_sala'] ?>"><?= $s['nombre'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Número de Cama</label>
                                    <input type="text" class="form-control" name="numero_cama" id="edit_cama" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Fecha de Ingreso</label>
                                    <input type="date" class="form-control" name="fecha_ingreso" id="edit_fecha" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Causa del Ingreso</label>
                                    <textarea class="form-control" name="causa" id="edit_causa" rows="3" required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-warning fw-bold">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalDarAlta" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">Finalizar Hospitalización</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="../php/alta_hospitalizacion.php" method="POST">
                        <div class="modal-body text-center">
                            <input type="hidden" name="id_hospitalizacion" id="alta_id">
                            <p>Está por registrar el alta médica para:</p>
                            <h5 id="alta_nombre_paciente" class="fw-bold text-success"></h5>
                            <hr>
                            <div class="text-start">
                                <label class="form-label">Estado al Salir</label>
                                <select class="form-select mb-3" name="estado_alta" required>
                                    <option value="Curado">Curado</option>
                                    <option value="Mejorado">Mejorado</option>
                                    <option value="Fallecido">Fallecido</option>
                                </select>
                                <label class="form-label">Fecha de Alta</label>
                                <input type="date" class="form-control" name="fecha_alta" value="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success">Confirmar Alta</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            // Lógica para el modal de Alta
            document.getElementById('modalDarAlta').addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                document.getElementById('alta_id').value = button.getAttribute('data-id');
                document.getElementById('alta_nombre_paciente').textContent = button.getAttribute('data-paciente');
            });

            // Lógica para el modal de Edición (Opcional, similar al anterior)
            document.getElementById('modalEditarHospitalizacion').addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
            });
        </script>



        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Lógica para el modal de Edición
                const modalEditar = document.getElementById('modalEditarHospitalizacion');
                modalEditar.addEventListener('show.bs.modal', function(event) {
                    const btn = event.relatedTarget; // El botón pulsado

                    // Asignar valores a los campos del modal
                    document.getElementById('edit_id').value = btn.getAttribute('data-id');
                    document.getElementById('edit_paciente').value = btn.getAttribute('data-paciente-id');
                    document.getElementById('edit_hospital').value = btn.getAttribute('data-hospital-id');
                    document.getElementById('edit_sala').value = btn.getAttribute('data-sala-id');
                    document.getElementById('edit_cama').value = btn.getAttribute('data-cama');
                    document.getElementById('edit_fecha').value = btn.getAttribute('data-fecha');
                    document.getElementById('edit_causa').value = btn.getAttribute('data-causa');
                });

                // Lógica para el modal de Alta (Ya lo tenías, asegúrate que esté así)
                const modalAlta = document.getElementById('modalDarAlta');
                modalAlta.addEventListener('show.bs.modal', function(event) {
                    const btn = event.relatedTarget;
                    document.getElementById('alta_id').value = btn.getAttribute('data-id');
                    document.getElementById('alta_nombre_paciente').textContent = btn.getAttribute('data-paciente');
                });
            });
        </script>

        <?php include_once '../componentes/footer.php'; ?>
</body>