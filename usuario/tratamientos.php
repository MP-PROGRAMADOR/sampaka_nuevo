<?php
session_start();
// Asegúrate de que esta ruta sea correcta para tu conexión
require_once "../config/conexion.php"; 

// 1. Configuración para el header (para marcar el enlace activo)
$page_title = 'Prescripciones y Órdenes';
$page_name = 'Prescripciones'; 

// 2. Obtener el ID del doctor
if (!isset($_SESSION['id_personal'])) {
    $doctor_id = 1; // ID de ejemplo, ¡ADAPTAR A LA LÓGICA DE SESIÓN REAL!
} else {
    $doctor_id = $_SESSION['id_personal'];
}

// 3. LÓGICA PARA CARGAR PRESCRIPCIONES (Simulación con datos estáticos)

// Ejemplo de las últimas 5 órdenes generadas por este doctor
$prescripciones_recientes = [
    [
        'id_pres' => 501, 
        'fecha' => '2025-12-14', 
        'paciente' => 'Juan Pérez López', 
        'paciente_id' => 101,
        'tipo' => 'Medicación', 
        'detalle' => 'Amoxicilina 500mg, cada 8 horas por 7 días. Tomar con alimentos.', 
        'estado' => 'Emitida',
        'color' => 'primary'
    ],
    [
        'id_pres' => 502, 
        'fecha' => '2025-12-13', 
        'paciente' => 'Ana Torres García', 
        'paciente_id' => 102,
        'tipo' => 'Laboratorio', 
        'detalle' => 'Hemograma completo y Perfil lipídico. Ayuno de 12 horas requerido.', 
        'estado' => 'Pendiente',
        'color' => 'warning'
    ],
    [
        'id_pres' => 503, 
        'fecha' => '2025-12-12', 
        'paciente' => 'Laura Mena Díaz', 
        'paciente_id' => 103,
        'tipo' => 'Referencia', 
        'detalle' => 'Referencia a Endocrinología por sospecha de diabetes tipo 2. Adjuntar historial clínico y resultados de glucosa.', 
        'estado' => 'Emitida',
        'color' => 'info'
    ],
];

// Incluir el encabezado (abre HTML, Sidebar y .main-content)
include 'header_doctores.php'; 
?>

<h1 class="mb-4 fw-light text-primary"><i class="bi bi-receipt-cutoff me-2"></i> Tratamientos</h1>

<div class="row g-4">
    
    <div class="col-lg-5">
        <div class="card p-4 h-100 shadow-sm">
            <h5 class="card-title text-success"><i class="bi bi-plus-circle me-1"></i> Nuevo Tratamiento</h5>
            <p class="text-muted small">Crea una nueva prescripción médica, orden de laboratorio o referencia.</p>
            
            <form action="procesar_prescripcion.php" method="POST">
                
                <div class="mb-3">
                    <label for="paciente_id" class="form-label">Paciente</label>
                    <select class="form-select" id="paciente_id" name="paciente_id" required>
                        <option value="">Seleccione un paciente...</option>
                        <option value="101">Juan Pérez López</option>
                        <option value="102">Ana Torres García</option>
                        <option value="103">Laura Mena Díaz</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="tipo_orden" class="form-label">Tipo de Orden</label>
                    <select class="form-select" id="tipo_orden" name="tipo_orden" required>
                        <option value="Medicación">Medicación (Receta)</option>
                        <option value="Laboratorio">Laboratorio/Estudios</option>
                        <option value="Referencia">Referencia a Especialista</option>
                        <option value="Otros">Otras Indicaciones</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="detalle" class="form-label">Detalle de la Orden / Posología</label>
                    <textarea class="form-control" id="detalle" name="detalle" rows="5" placeholder="Escriba la dosis, frecuencia, duración o el detalle del estudio solicitado..." required></textarea>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="bi bi-send me-2"></i> Emitir y Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="col-lg-7">
        <div class="card p-4 h-100 shadow-sm">
            <h5 class="card-title text-primary"><i class="bi bi-list-check me-1"></i> Historial Reciente</h5>
            <p class="text-muted small">Las últimas órdenes generadas por su usuario.</p>
            
            <?php if (empty($prescripciones_recientes)): ?>
                <div class="alert alert-info text-center mt-3" role="alert">
                    <i class="bi bi-journal-text me-2"></i> No ha emitido órdenes en los últimos días.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead>
                            <tr class="table-light">
                                <th>ID</th>
                                <th>Fecha</th>
                                <th>Paciente</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($prescripciones_recientes as $pres): ?>
                                <tr>
                                    <td><?= htmlspecialchars($pres['id_pres']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($pres['fecha'])) ?></td>
                                    <td><?= htmlspecialchars($pres['paciente']) ?></td>
                                    <td><?= htmlspecialchars($pres['tipo']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= htmlspecialchars($pres['color']) ?>">
                                            <?= htmlspecialchars($pres['estado']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-primary me-1" 
                                                    title="Ver Detalle" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalPrescripcion<?= htmlspecialchars($pres['id_pres']) ?>">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        
                                        <a href="print_prescripcion.php?id=<?= htmlspecialchars($pres['id_pres']) ?>" class="btn btn-sm btn-outline-secondary" title="Imprimir/PDF" target="_blank">
                                            <i class="bi bi-printer"></i>
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

<?php foreach ($prescripciones_recientes as $pres): ?>
    <div class="modal fade" id="modalPrescripcion<?= htmlspecialchars($pres['id_pres']) ?>" tabindex="-1" aria-labelledby="modalPrescripcionLabel<?= htmlspecialchars($pres['id_pres']) ?>" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalPrescripcionLabel<?= htmlspecialchars($pres['id_pres']) ?>"><i class="bi bi-info-circle me-2"></i>Detalle de la Orden #<?= htmlspecialchars($pres['id_pres']) ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <p class="text-muted mb-0 small">Paciente:</p>
                            <p class="fw-bold mb-0"><?= htmlspecialchars($pres['paciente']) ?></p>
                        </div>
                        <div class="col-md-3">
                            <p class="text-muted mb-0 small">Tipo de Orden:</p>
                            <p class="fw-bold mb-0 text-success"><?= htmlspecialchars($pres['tipo']) ?></p>
                        </div>
                        <div class="col-md-3">
                            <p class="text-muted mb-0 small">Fecha de Emisión:</p>
                            <p class="fw-bold mb-0"><?= date('d/m/Y', strtotime($pres['fecha'])) ?></p>
                        </div>
                        <div class="col-md-2 text-center">
                            <p class="text-muted mb-0 small">Estado:</p>
                            <span class="badge bg-<?= htmlspecialchars($pres['color']) ?>"><?= htmlspecialchars($pres['estado']) ?></span>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h6>Detalle de la Prescripción:</h6>
                    <div class="alert alert-light border p-3">
                        <p class="lead mb-0 text-dark"><?= nl2br(htmlspecialchars($pres['detalle'])) ?></p>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <a href="historial_paciente.php?id=<?= htmlspecialchars($pres['paciente_id']) ?>" class="btn btn-outline-info me-auto">
                        <i class="bi bi-person-lines-fill"></i> Ir a Historial
                    </a>
                  
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?php
// 5. Incluir el pie de página
include 'footer_doctores.php'; 
?>