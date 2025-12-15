<?php
session_start();
// Asegúrate de que esta ruta sea correcta para tu conexión
require_once "../config/conexion.php"; 

// 1. Configuración para el header (para marcar el enlace activo)
$page_title = 'Resultados de Laboratorio';
$page_name = 'Resultados Labs'; 

// 2. Obtener el ID del doctor
if (!isset($_SESSION['id_personal'])) {
    $doctor_id = 1; // ID de ejemplo, ¡ADAPTAR A LA LÓGICA DE SESIÓN REAL!
} else {
    $doctor_id = $_SESSION['id_personal'];
}

// =========================================================================
// 3. LÓGICA PARA CARGAR RESULTADOS (Simulación con datos estáticos)
// =========================================================================

// Función de simulación para resultados (En un entorno real, esto viene de la DB)
function generar_resultados($estado) {
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
    // Filtramos para simular que solo algunos quedan como "Pendiente" y otros como "Revisado"
    if ($estado === 'Pendiente') {
        return array_slice($datos, 0, 3);
    } else {
        return array_slice($datos, 1, 2); // Simular que Ana y Luis ya fueron revisados
    }
}

$pendientes = generar_resultados('Pendiente');
$revisados = generar_resultados('Revisado');


// Incluir el encabezado (abre HTML, Sidebar y .main-content)
include 'header_doctores.php'; 
?>

<h1 class="mb-4 fw-light text-primary"><i class="bi bi-file-medical-fill me-2"></i> Resultados de Laboratorio</h1>

<div class="card p-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="card-title mb-0">Revisión de Informes</h5>
        <span class="text-muted small">Hoy: <?= date("d/m/Y") ?></span>
    </div>

    <ul class="nav nav-tabs mb-4" id="labsTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pendientes-tab" data-bs-toggle="tab" data-bs-target="#pendientes" type="button" role="tab" aria-controls="pendientes" aria-selected="true">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> Pendientes de Revisión (<?= count($pendientes) ?>)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="revisados-tab" data-bs-toggle="tab" data-bs-target="#revisados" type="button" role="tab" aria-controls="revisados" aria-selected="false">
                <i class="bi bi-check-circle-fill me-1"></i> Historial Revisado (<?= count($revisados) ?>)
            </button>
        </li>
    </ul>

    <div class="tab-content" id="labsTabsContent">
        
        <div class="tab-pane fade show active" id="pendientes" role="tabpanel" aria-labelledby="pendientes-tab">
            <?php if (empty($pendientes)): ?>
                <div class="alert alert-success text-center mt-3" role="alert">
                    <i class="bi bi-check-all me-2"></i> No hay resultados pendientes de revisión.
                </div>
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
                            <?php foreach ($pendientes as $resultado): ?>
                                <tr class="<?= ($resultado['anormalidad'] === 'CRÍTICO') ? 'table-warning' : '' ?>">
                                    <td><?= htmlspecialchars($resultado['nombre']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($resultado['fecha_toma'])) ?></td>
                                    <td><?= htmlspecialchars($resultado['tipo']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= htmlspecialchars($resultado['clase_alerta']) ?>">
                                            <?= htmlspecialchars($resultado['anormalidad']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="ver_informe.php?id=<?= htmlspecialchars($resultado['id_paciente']) ?>&tipo=<?= urlencode($resultado['tipo']) ?>" class="btn btn-sm btn-primary me-2" title="Ver Detalle">
                                            <i class="bi bi-eye"></i> Ver Informe
                                        </a>
                                        <button class="btn btn-sm btn-success" title="Marcar como Revisado">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="tab-pane fade" id="revisados" role="tabpanel" aria-labelledby="revisados-tab">
            <?php if (empty($revisados)): ?>
                 <div class="alert alert-warning text-center mt-3" role="alert">
                    No hay historial reciente de resultados revisados.
                </div>
            <?php else: ?>
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
                            <?php foreach ($revisados as $resultado): ?>
                                <tr>
                                    <td><?= htmlspecialchars($resultado['nombre']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($resultado['fecha_toma'])) ?></td>
                                    <td><?= htmlspecialchars($resultado['tipo']) ?></td>
                                    <td><span class="badge bg-success">Revisado</span></td>
                                    <td>
                                        <a href="ver_informe.php?id=<?= htmlspecialchars($resultado['id_paciente']) ?>&tipo=<?= urlencode($resultado['tipo']) ?>" class="btn btn-sm btn-secondary" title="Ver Detalle">
                                            <i class="bi bi-file-earmark-bar-graph"></i> Detalle
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        
    </div>
</div>

<?php
// 4. Incluir el pie de página
include 'footer_doctores.php'; 
?>