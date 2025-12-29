<?php
session_start();
require_once "../config/conexion.php"; 

$page_title = 'Gestión de Pacientes';
$page_name = 'Mis Pacientes'; 

if (!isset($_SESSION['id_personal'])) {
    $doctor_id = 1; 
} else {
    $doctor_id = $_SESSION['id_personal'];
}

$resultados = [];
$error_message = null;

try {
    $resultados = [
        ['id_paciente' => 101, 'paciente_nombre' => 'Juan', 'paciente_apellido' => 'Pérez', 'ultima_consulta' => '2025-11-20'],
        ['id_paciente' => 102, 'paciente_nombre' => 'María', 'paciente_apellido' => 'Gómez', 'ultima_consulta' => '2025-12-10'],
        ['id_paciente' => 103, 'paciente_nombre' => 'Luis', 'paciente_apellido' => 'Rodríguez', 'ultima_consulta' => '2025-10-01'],
    ];
} catch (Exception $e) {
    $error_message = "Error al cargar pacientes: " . $e->getMessage();
}

include 'header_doctores.php'; 
?>

<style>
    .card { border: none; border-radius: 12px; }
    .table thead th { font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; }
    .btn-action { transition: all 0.2s; border-radius: 8px; }
    .btn-action:hover { transform: translateY(-2px); }
    .search-container .input-group-text { border-radius: 10px 0 0 10px; }
    .search-container .form-control { border-radius: 0 10px 10px 0; }
    .badge-date { font-size: 0.9rem; padding: 6px 12px; border-radius: 6px; }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary fw-bold"><i class="bi bi-people-fill me-2"></i> Mis Pacientes</h1>
            <p class="text-muted small mb-0">Gestión y seguimiento de pacientes asignados</p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-dark">Listado General</h6>
            
            <div class="search-container" style="min-width: 300px;">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" id="inputBuscador" class="form-control border-start-0" placeholder="Buscar paciente o ID...">
                </div>
            </div>
        </div>
        
        <div class="card-body p-0"> <?php if ($error_message): ?>
                <div class="p-3">
                    <div class="alert alert-danger m-0"><?= htmlspecialchars($error_message) ?></div>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tablaPacientes">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Paciente</th>
                            <th>Última Consulta</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($resultados)): ?>
                            <tr id="filaVacia">
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="bi bi-folder-x display-4 d-block mb-2"></i>
                                    No se encontraron pacientes asignados.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($resultados as $paciente): ?>
                                <tr>
                                    <td class="ps-4"><span class="text-muted small">#</span><?= htmlspecialchars($paciente['id_paciente']) ?></td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($paciente['paciente_nombre'] . ' ' . $paciente['paciente_apellido']) ?></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-secondary border badge-date">
                                            <i class="bi bi-calendar3 me-1"></i> <?= htmlspecialchars($paciente['ultima_consulta'] ?? 'N/A') ?>
                                        </span>
                                    </td>
                                    <td class="text-center pe-4">
                                        <div class="btn-group gap-2">
                                            <a href="historial_clinico.php?id=<?= $paciente['id_paciente'] ?>" class="btn btn-sm btn-outline-info btn-action" title="Ver Historial">
                                                <i class="bi bi-person-lines-fill me-1"></i> Historial
                                            </a>
                                            <a href="ver_tratamientos.php?id=<?= $paciente['id_paciente'] ?>" class="btn btn-sm btn-outline-primary btn-action" title="Tratamientos">
                                                <i class="bi bi-receipt-cutoff me-1"></i> Tratamientos
                                            </a>
                                            <button type="button" class="btn btn-sm btn-warning text-dark btn-action btn-crear-cita fw-bold" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalCrearCita"
                                                data-paciente-id="<?= $paciente['id_paciente'] ?>"
                                                data-paciente-nombre="<?= htmlspecialchars($paciente['paciente_nombre'] . ' ' . $paciente['paciente_apellido']) ?>">
                                                <i class="bi bi-calendar-plus me-1"></i> Cita
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr id="noResultados" style="display: none;">
                                <td colspan="4" class="text-center py-4 text-danger">
                                    <i class="bi bi-exclamation-circle me-2"></i> No hay coincidencias con la búsqueda.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCrearCita" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <form id="formCrearCita" action="procesar_cita.php" method="POST">
                <div class="modal-header border-0 bg-warning">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="bi bi-calendar-plus me-2"></i>Agendar Cita
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <span class="text-muted d-block small">Paciente seleccionado:</span>
                        <h5 id="pacienteNombreModal" class="text-primary fw-bold mb-0"></h5>
                    </div>
                    
                    <input type="hidden" name="paciente_id" id="inputPacienteId">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Fecha</label>
                            <input type="date" class="form-control rounded-3" name="fecha_cita" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Hora</label>
                            <input type="time" class="form-control rounded-3" name="hora_cita" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Motivo de Consulta</label>
                            <textarea class="form-control rounded-3" name="motivo_cita" rows="3" placeholder="Escriba aquí..." required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light p-3">
                    <button type="button" class="btn btn-link text-muted text-decoration-none" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning px-4 fw-bold">Agendar Ahora</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Buscador
    const inputBuscador = document.getElementById('inputBuscador');
    const filas = document.querySelectorAll('#tablaPacientes tbody tr:not(#noResultados):not(#filaVacia)');
    const filaNoResultados = document.getElementById('noResultados');

    inputBuscador.addEventListener('keyup', function() {
        const filtro = inputBuscador.value.toLowerCase();
        let coincidencias = 0;

        filas.forEach(fila => {
            const texto = fila.innerText.toLowerCase();
            if (texto.includes(filtro)) {
                fila.style.display = "";
                coincidencias++;
            } else {
                fila.style.display = "none";
            }
        });

        if (filaNoResultados) {
            filaNoResultados.style.display = (coincidencias === 0 && filtro !== "") ? "" : "none";
        }
    });

    // Modal
    const modalCrearCita = document.getElementById('modalCrearCita');
    modalCrearCita.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget; 
        document.getElementById('pacienteNombreModal').textContent = button.getAttribute('data-paciente-nombre');
        document.getElementById('inputPacienteId').value = button.getAttribute('data-paciente-id');
        document.getElementById('formCrearCita').reset();
    });
});
</script>

<?php include 'footer_doctores.php'; ?>