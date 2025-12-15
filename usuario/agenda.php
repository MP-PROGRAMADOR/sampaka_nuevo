<?php
session_start();
// Asegúrate de que esta ruta sea correcta para tu conexión
require_once "../config/conexion.php"; 

// 1. Configuración para el header (para marcar el enlace activo)
$page_title = 'Mi Agenda';
$page_name = 'Mi Agenda'; 

// 2. Obtener el ID del doctor
if (!isset($_SESSION['id_personal'])) {
    $doctor_id = 1; // ID de ejemplo, ¡ADAPTAR A LA LÓGICA DE SESIÓN REAL!
} else {
    $doctor_id = $_SESSION['id_personal'];
}

// =========================================================================
// 3. LÓGICA PARA CARGAR CITAS (Simulación con datos estáticos)
// =========================================================================

// Citas para hoy
$citas_hoy = [
    ['id' => 101, 'hora' => '09:00', 'paciente' => 'Juan Pérez López', 'motivo' => 'Consulta de rutina y control', 'estado' => 'Confirmada', 'color' => 'success'],
    ['id' => 102, 'hora' => '11:30', 'paciente' => 'Ana Torres García', 'motivo' => 'Entrega de resultados de laboratorio', 'estado' => 'Confirmada', 'color' => 'info'],
    ['id' => 103, 'hora' => '14:00', 'paciente' => 'Miguel Solís Mora', 'motivo' => 'Urgencia menor por fiebre', 'estado' => 'Pendiente', 'color' => 'danger'],
    ['id' => 104, 'hora' => '16:45', 'paciente' => 'Laura Mena Díaz', 'motivo' => 'Seguimiento post-operatorio', 'estado' => 'Confirmada', 'color' => 'warning'],
];

// Citas para la próxima semana (ejemplo de datos)
$citas_proxima_semana = [
    ['id' => 201, 'dia' => 'Martes, 16', 'hora' => '10:00', 'paciente' => 'Roberto Sánchez', 'motivo' => 'Consulta de rutina'],
    ['id' => 202, 'dia' => 'Miércoles, 17', 'hora' => '15:30', 'paciente' => 'Elena Funes', 'motivo' => 'Control de tensión'],
    ['id' => 203, 'dia' => 'Jueves, 18', 'hora' => '08:45', 'paciente' => 'Pedro Riquelme', 'motivo' => 'Examen preventivo'],
];

// Incluir el encabezado (abre HTML, Sidebar y .main-content)
include 'header_doctores.php'; 
?>

<h1 class="mb-4 fw-light text-primary"><i class="bi bi-calendar-range-fill me-2"></i> Mi Agenda</h1>

<div class="card p-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="card-title mb-0">Gestión de Citas</h5>
        <a href="crear_cita.php" class="btn btn-success"><i class="bi bi-calendar-plus"></i> Añadir Cita</a>
    </div>

    <ul class="nav nav-tabs mb-4" id="agendaTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="hoy-tab" data-bs-toggle="tab" data-bs-target="#hoy" type="button" role="tab" aria-controls="hoy" aria-selected="true">
                <i class="bi bi-calendar-check me-1"></i> Citas de Hoy (<?= count($citas_hoy) ?>)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="semana-tab" data-bs-toggle="tab" data-bs-target="#semana" type="button" role="tab" aria-controls="semana" aria-selected="false">
                <i class="bi bi-calendar-week me-1"></i> Próxima Semana
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="calendario-tab" data-bs-toggle="tab" data-bs-target="#calendario" type="button" role="tab" aria-controls="calendario" aria-selected="false">
                <i class="bi bi-bar-chart-line me-1"></i> Carga Mensual
            </button>
        </li>
    </ul>

    <div class="tab-content" id="agendaTabsContent">
        
        <div class="tab-pane fade show active" id="hoy" role="tabpanel" aria-labelledby="hoy-tab">
            <?php if (empty($citas_hoy)): ?>
                <div class="alert alert-info text-center mt-3" role="alert">
                    <i class="bi bi-emoji-smile me-2"></i> ¡Felicidades! No tienes citas programadas para hoy.
                </div>
            <?php else: ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($citas_hoy as $cita): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <div>
                                <h5 class="mb-1 text-primary"><?= htmlspecialchars($cita['hora']) ?></h5>
                                <div class="fw-bold"><?= htmlspecialchars($cita['paciente']) ?></div>
                                <small class="text-muted"><i class="bi bi-journal-text me-1"></i> <?= htmlspecialchars($cita['motivo']) ?></small>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-<?= htmlspecialchars($cita['color']) ?> me-3"><?= htmlspecialchars($cita['estado']) ?></span>
                                <a href="historial_paciente.php?id=<?= htmlspecialchars($cita['id']) ?>" class="btn btn-sm btn-outline-info me-2" title="Ver Historial">
                                    <i class="bi bi-person-lines-fill"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-secondary" title="Editar Cita" 
                                    data-bs-toggle="modal" data-bs-target="#modalEditarCita"
                                    data-id="<?= htmlspecialchars($cita['id']) ?>" 
                                    data-paciente="<?= htmlspecialchars($cita['paciente']) ?>"
                                    data-hora="<?= htmlspecialchars($cita['hora']) ?>"
                                    data-motivo="<?= htmlspecialchars($cita['motivo']) ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        
        <div class="tab-pane fade" id="semana" role="tabpanel" aria-labelledby="semana-tab">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Día</th>
                            <th>Hora</th>
                            <th>Paciente</th>
                            <th>Motivo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($citas_proxima_semana)): ?>
                             <tr><td colspan="5" class="text-center text-muted">No hay citas programadas para la próxima semana.</td></tr>
                        <?php else: ?>
                            <?php foreach ($citas_proxima_semana as $cita): ?>
                                <tr>
                                    <td><?= htmlspecialchars($cita['dia']) ?></td>
                                    <td><?= htmlspecialchars($cita['hora']) ?></td>
                                    <td><?= htmlspecialchars($cita['paciente']) ?></td>
                                    <td><?= htmlspecialchars($cita['motivo']) ?></td>
                                    <td>
                                        <a href="historial_paciente.php?id=<?= htmlspecialchars($cita['id']) ?>" class="btn btn-sm btn-outline-info me-2" title="Ver Historial">
                                            <i class="bi bi-person-lines-fill"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" title="Editar Cita" 
                                            data-bs-toggle="modal" data-bs-target="#modalEditarCita"
                                            data-id="<?= htmlspecialchars($cita['id']) ?>" 
                                            data-paciente="<?= htmlspecialchars($cita['paciente']) ?>"
                                            data-hora="<?= htmlspecialchars($cita['hora']) ?>"
                                            data-motivo="<?= htmlspecialchars($cita['motivo']) ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="tab-pane fade" id="calendario" role="tabpanel" aria-labelledby="calendario-tab">
             <div class="alert alert-info text-center mt-3" role="alert">
                <i class="bi bi-graph-up-arrow me-2"></i> **Carga Semanal de Consultas:** Un resumen rápido de tu actividad.
            </div>
            <div style="height: 500px;" class="p-3">
                <canvas id="cargaMensualChart"></canvas>
            </div>
            
        </div>
        
    </div>
</div>

<div class="modal fade" id="modalEditarCita" tabindex="-1" aria-labelledby="modalEditarCitaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="procesar_edicion_cita.php" method="POST">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title" id="modalEditarCitaLabel"><i class="bi bi-pencil-square me-2"></i>Editar Cita</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="cita_id" id="editar_cita_id">
                    <p class="mb-3">Editando cita para: <strong id="editar_paciente_nombre"></strong></p>
                    
                    <div class="mb-3">
                        <label for="editar_fecha" class="form-label">Fecha</label>
                        <input type="date" class="form-control" id="editar_fecha" name="fecha" required>
                    </div>
                    <div class="mb-3">
                        <label for="editar_hora" class="form-label">Hora</label>
                        <input type="time" class="form-control" id="editar_hora" name="hora" required>
                    </div>
                    <div class="mb-3">
                        <label for="editar_motivo" class="form-label">Motivo</label>
                        <textarea class="form-control" id="editar_motivo" name="motivo" rows="2" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // 1. Lógica para cargar datos dinámicos en el Modal de Edición
    document.addEventListener('DOMContentLoaded', function() {
        var modalEditarCita = document.getElementById('modalEditarCita');
        if (modalEditarCita) {
            modalEditarCita.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var id = button.getAttribute('data-id');
                var paciente = button.getAttribute('data-paciente');
                var hora = button.getAttribute('data-hora');
                var motivo = button.getAttribute('data-motivo');

                // Asignación de valores
                var today = new Date().toISOString().split('T')[0];
                document.getElementById('editar_cita_id').value = id;
                document.getElementById('editar_paciente_nombre').textContent = paciente;
                // En un entorno real, la fecha debería venir del dato, aquí usamos hoy como placeholder
                document.getElementById('editar_fecha').value = today; 
                document.getElementById('editar_hora').value = hora;
                document.getElementById('editar_motivo').value = motivo;
            });
        }
        
        // 2. Lógica para inicializar el gráfico de Chart.js (Gráfico de Barras Mejorado)
        const ctx = document.getElementById('cargaMensualChart');
        if (ctx && typeof Chart !== 'undefined') {
            new Chart(ctx, {
                type: 'bar', // Cambiado a gráfico de barras
                data: {
                    labels: ['Semana 1', 'Semana 2', 'Semana 3', 'Semana 4'],
                    datasets: [{
                        label: 'Citas Atendidas',
                        data: [45, 52, 38, 61], // Datos de ejemplo
                        backgroundColor: 'rgba(13, 110, 253, 0.8)', // Azul primario
                        borderColor: 'rgba(13, 110, 253, 1)',
                        borderWidth: 1,
                        borderRadius: 5, // Bordes redondeados
                    },
                    {
                        label: 'Citas Canceladas/No show',
                        data: [5, 3, 7, 2], // Datos de ejemplo
                        backgroundColor: 'rgba(255, 193, 7, 0.8)', // Amarillo warning
                        borderColor: 'rgba(255, 193, 7, 1)',
                        borderWidth: 1,
                        borderRadius: 5,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true, // Usa cuadrados de color en la leyenda
                            }
                        },
                        title: {
                            display: true,
                            text: 'Carga de Citas Semanal',
                            font: {
                                size: 16
                            },
                            color: '#212529'
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false // Oculta las líneas verticales de la cuadrícula
                            }
                        },
                        y: {
                            beginAtZero: true,
                            max: 70, // Establece un máximo fijo para mejor comparación
                            ticks: {
                                stepSize: 10
                            },
                            title: {
                                display: true,
                                text: 'Número de Citas'
                            }
                        }
                    }
                }
            });
        }
    });
</script>

<?php
// 4. Incluir el pie de página
include 'footer_doctores.php'; 
?>