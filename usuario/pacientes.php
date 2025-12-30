<?php

session_start();
// Asegúrate de que esta ruta sea correcta para tu conexión
require_once "../config/conexion.php"; 

// 1. Configuración de la página para el header
$page_title = 'Gestión de Pacientes';
$page_name = 'Mis Pacientes'; 

// 2. Obtener el ID del doctor (ASUMIDO: usa 'id_personal' de la sesión)
if (!isset($_SESSION['id_personal'])) {
    $doctor_id = 1; 
} else {
    $doctor_id = $_SESSION['id_personal'];
}

// Inicializar la variable de resultados
$resultados = [];
$error_message = null;


// Ejemplo de datos simulados (reemplazar con la lógica de tu conexión a DB)
try {
    $resultados = [
        ['id_paciente' => 101, 'paciente_nombre' => 'Juan', 'paciente_apellido' => 'Pérez', 'ultima_consulta' => '2025-11-20'],
        ['id_paciente' => 102, 'paciente_nombre' => 'María', 'paciente_apellido' => 'Gómez', 'ultima_consulta' => '2025-12-10'],
        ['id_paciente' => 103, 'paciente_nombre' => 'Luis', 'paciente_apellido' => 'Rodríguez', 'ultima_consulta' => '2025-10-01'],
    ];
 

} catch (Exception $e) {
    $error_message = "Error al cargar pacientes: " . $e->getMessage();
}

// 4. Incluir el encabezado (abre HTML, Sidebar y .main-content)
include 'header_doctores.php'; 
?>

<h1 class="mb-4 fw-light text-primary"><i class="bi bi-people-fill me-2"></i> Mis Pacientes</h1>

<div class="card p-4">
    <div class="card-header bg-white border-0 ps-0 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 mt-5">Listado de Pacientes Asignados</h5>
        <div class="d-flex">
               <input type="text" class="form-control me-2 mb-5" placeholder="Buscar paciente...">
            <!-- <button class="btn btn-success"><i class="bi bi-plus-circle me-1"></i> Nuevo Paciente</button> -->
        </div>
    </div>
    
    <?php if ($error_message): ?>
        <div class="alert alert-danger mt-3"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>

    <div class="table-responsive mt-3">
        <table class="table table-striped table-hover align-middle">
            <thead>
                <tr class="table-primary">
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Última Consulta</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($resultados)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted">No se encontraron pacientes asignados.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($resultados as $paciente): ?>
                        <tr>
                            <td><?= htmlspecialchars($paciente['id_paciente']) ?></td>
                            <td><?= htmlspecialchars($paciente['paciente_nombre']) ?></td>
                            <td><?= htmlspecialchars($paciente['paciente_apellido']) ?></td>
                            <td><?= htmlspecialchars($paciente['ultima_consulta'] ?? 'N/A') ?></td>
                            <td class="d-flex">
                                <a href="historial_clinico.php?id=<?= htmlspecialchars($paciente['id_paciente']) ?>" class="btn btn-sm btn-info me-2" title="Ver Historial">
                                    <i class="bi bi-person-lines-fill"></i> Historial
                                </a>
                                <a href="ver_tratamientos.php" class="btn btn-sm btn-primary me-2" title="Tratamientos">
                                    <i class="bi bi-receipt-cutoff"></i> Tratamientos
                                </a>
                                <button type="button" class="btn btn-sm btn-warning text-dark" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalCrearCita"
                                    data-paciente-id="<?= htmlspecialchars($paciente['id_paciente']) ?>"
                                    data-paciente-nombre="<?= htmlspecialchars($paciente['paciente_nombre'] . ' ' . $paciente['paciente_apellido']) ?>"
                                    title="Crear Cita">
                                    <i class="bi bi-calendar-plus"></i> Cita
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div> 
<div class="mt-3">
    
<div class="modal fade" id="modalCrearCita" tabindex="-1" aria-labelledby="modalCrearCitaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formCrearCita" action="procesar_cita.php" method="POST">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="modalCrearCitaLabel"><i class="bi bi-calendar-plus me-2"></i>Nueva Cita para: <span id="pacienteNombreModal" class="fw-bold"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="paciente_id" id="inputPacienteId">
                    
                    <div class="mb-3">
                        <label for="fechaCita" class="form-label">Fecha</label>
                        <input type="date" class="form-control" id="fechaCita" name="fecha_cita" required>
                    </div>
                    <div class="mb-3">
                        <label for="horaCita" class="form-label">Hora</label>
                        <input type="time" class="form-control" id="horaCita" name="hora_cita" required>
                    </div>
                    <div class="mb-3">
                        <label for="motivoCita" class="form-label">Motivo de la Cita</label>
                        <textarea class="form-control" id="motivoCita" name="motivo_cita" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning text-dark"><i class="bi bi-check-circle-fill me-1"></i> Guardar Cita</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modalCrearCita = document.getElementById('modalCrearCita');
        modalCrearCita.addEventListener('show.bs.modal', function (event) {
            // Botón que disparó el modal
            var button = event.relatedTarget; 
            
            // Extraer información del paciente de los atributos data-*
            var pacienteId = button.getAttribute('data-paciente-id');
            var pacienteNombre = button.getAttribute('data-paciente-nombre');

            // Actualizar el título y los campos ocultos del modal
            var modalTitle = modalCrearCita.querySelector('#pacienteNombreModal');
            var inputId = modalCrearCita.querySelector('#inputPacienteId');

            modalTitle.textContent = pacienteNombre;
            inputId.value = pacienteId;
            
            // Opcional: Limpiar fecha y motivo cada vez que se abre el modal
            modalCrearCita.querySelector('#fechaCita').value = '';
            modalCrearCita.querySelector('#horaCita').value = '';
            modalCrearCita.querySelector('#motivoCita').value = '';
        });
    });
</script>

<?php
// 5. Incluir el pie de página (cierra .main-content, body y HTML)
include 'footer_doctores.php'; 
?>
</div>