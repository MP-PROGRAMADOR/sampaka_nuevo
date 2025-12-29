<?php
session_start();
// Asegúrate de que esta ruta sea correcta para tu conexión
require_once "../config/conexion.php";

// 1. Configuración para el header
$page_title = 'Resultados de Laboratorio';
$page_name = 'Resultados Labs';

// 2. Obtener el ID del doctor
if (!isset($_SESSION['id_personal'])) {
    $doctor_id = 1;
} else {
    $doctor_id = $_SESSION['id_personal'];
}

// =========================================================================
// 3. LÓGICA PARA CARGAR RESULTADOS (Simulación)
// =========================================================================
function generar_resultados($estado)
{
    $datos = [
        [
            'id_paciente' => 101,
            'nombre' => 'Juan Pérez López',
            'fecha_toma' => '2025-12-14',
            'tipo' => 'Hemograma Completo',
            'estado' => $estado,
            'anormalidad' => ($estado === 'Pendiente') ? 'CRÍTICO' : 'Normal',
            'clase_alerta' => ($estado === 'Pendiente') ? 'danger' : 'info'
        ],
        [
            'id_paciente' => 102,
            'nombre' => 'Ana Torres García',
            'fecha_toma' => '2025-12-12',
            'tipo' => 'Química Sanguínea',
            'estado' => $estado,
            'anormalidad' => 'Normal',
            'clase_alerta' => 'success'
        ],
        [
            'id_paciente' => 103,
            'nombre' => 'Luis Mena Diaz',
            'fecha_toma' => '2025-12-08',
            'tipo' => 'Perfil Lipídico',
            'estado' => $estado,
            'anormalidad' => 'FUERA DE RANGO',
            'clase_alerta' => 'warning'
        ],
    ];

    if ($estado === 'Pendiente') {
        return $datos;
    } else {
        return array_slice($datos, 1, 2);
    }
}

$pendientes = generar_resultados('Pendiente');
$revisados = generar_resultados('Revisado');

include 'header_doctores.php';
?>

<h1 class="mb-4 fw-light text-primary"><i class="bi bi-file-medical-fill me-2"></i> Resultados de Laboratorio</h1>

<div class="card shadow-sm p-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="card-title mb-0">Revisión de Informes</h5>
        <span class="text-muted small">Hoy: <?= date("d/m/Y") ?></span>
    </div>

    <ul class="nav nav-tabs mb-4" id="labsTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pendientes-tab" data-bs-toggle="tab" data-bs-target="#pendientes" type="button" role="tab">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> Pendientes (<?= count($pendientes) ?>)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="revisados-tab" data-bs-toggle="tab" data-bs-target="#revisados" type="button" role="tab">
                <i class="bi bi-check-circle-fill me-1"></i> Historial (<?= count($revisados) ?>)
            </button>
        </li>
    </ul>

    <div class="tab-content" id="labsTabsContent">

        <div class="tab-pane fade show active" id="pendientes" role="tabpanel">
            <?php if (empty($pendientes)): ?>
                <div class="alert alert-success text-center mt-3">No hay resultados pendientes.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-danger">
                            <tr>
                                <th>Paciente</th>
                                <th>Fecha Toma</th>
                                <th>Tipo de Examen</th>
                                <th>Anormalidad</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendientes as $res): ?>
                                <tr class="<?= ($res['anormalidad'] === 'CRÍTICO') ? 'table-warning' : '' ?>">
                                    <td><?= htmlspecialchars($res['nombre']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($res['fecha_toma'])) ?></td>
                                    <td><?= htmlspecialchars($res['tipo']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $res['clase_alerta'] ?>"><?= $res['anormalidad'] ?></span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary btn-ver-informe"
                                            data-bs-toggle="modal" data-bs-target="#modalVerInforme"
                                            data-nombre="<?= htmlspecialchars($res['nombre']) ?>"
                                            data-fecha="<?= date('d/m/Y', strtotime($res['fecha_toma'])) ?>"
                                            data-tipo="<?= htmlspecialchars($res['tipo']) ?>"
                                            data-anormalidad="<?= $res['anormalidad'] ?>"
                                            data-clase="<?= $res['clase_alerta'] ?>">
                                            <i class="bi bi-eye"></i> Ver Informe
                                        </button>
                                        
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <div class="tab-pane fade" id="revisados" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Paciente</th>
                            <th>Fecha Toma</th>
                            <th>Tipo de Examen</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($revisados as $res): ?>
                            <tr>
                                <td><?= htmlspecialchars($res['nombre']) ?></td>
                                <td><?= date('d/m/Y', strtotime($res['fecha_toma'])) ?></td>
                                <td><?= htmlspecialchars($res['tipo']) ?></td>
                                <td><span class="badge bg-success">Revisado</span></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-secondary btn-ver-informe"
                                        data-bs-toggle="modal" data-bs-target="#modalVerInforme"
                                        data-nombre="<?= htmlspecialchars($res['nombre']) ?>"
                                        data-fecha="<?= date('d/m/Y', strtotime($res['fecha_toma'])) ?>"
                                        data-tipo="<?= htmlspecialchars($res['tipo']) ?>"
                                        data-anormalidad="Normal"
                                        data-clase="success">
                                        <i class="bi bi-file-earmark-bar-graph"></i> Detalle
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalVerInforme" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-file-earmark-medical me-2"></i> Informe de Laboratorio</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4 border-bottom pb-3">
                    <div class="col-md-6">
                        <p class="mb-1 text-muted small">PACIENTE</p>
                        <h5 id="modalNombrePaciente" class="text-dark fw-bold"></h5>
                    </div>
                    <div class="col-md-3">
                        <p class="mb-1 text-muted small">FECHA TOMA</p>
                        <p id="modalFechaToma" class="fw-bold"></p>
                    </div>
                    <div class="col-md-3 text-end">
                        <span id="modalBadgeAnormalidad" class="badge"></span>
                    </div>
                </div>

                <div class="p-3 bg-light rounded">
                    <h6 class="text-primary border-bottom pb-2 fw-bold" id="modalTipoExamen"></h6>
                    <table class="table table-sm table-borderless mt-3">
                        <thead class="text-muted small">
                            <tr>
                                <th>Análisis</th>
                                <th>Resultado</th>
                                <th>Referencia</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Parámetro General</td>
                                <td class="fw-bold">Normal / Pendiente</td>
                                <td>Rango Estándar</td>
                                <td><i class="bi bi-info-circle text-primary"></i></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer"></i> Imprimir</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modalVerInforme = document.getElementById('modalVerInforme');
        modalVerInforme.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;

            // Inyectar datos en el modal
            modalVerInforme.querySelector('#modalNombrePaciente').textContent = button.getAttribute('data-nombre');
            modalVerInforme.querySelector('#modalFechaToma').textContent = button.getAttribute('data-fecha');
            modalVerInforme.querySelector('#modalTipoExamen').textContent = button.getAttribute('data-tipo');

            var badge = modalVerInforme.querySelector('#modalBadgeAnormalidad');
            badge.textContent = button.getAttribute('data-anormalidad');
            badge.className = 'badge bg-' + button.getAttribute('data-clase');
        });
    });
</script>

<?php include 'footer_doctores.php'; ?>