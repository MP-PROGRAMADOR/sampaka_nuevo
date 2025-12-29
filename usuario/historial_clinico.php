<?php
session_start();
require_once "../config/conexion.php";

// 1. Configuración para el header
$page_title = 'Historial Clínico';
$page_name = 'Historiales Clínicos';



// Simulación de datos del paciente
$paciente = [
    'nombre_completo' => 'Ana Torres García',
 
    'edad' => 45,
    'genero' => 'Femenino',
    'contacto' => '555-1234',
    'alergias' => 'Penicilina, Polen',
    'grupo_sanguineo' => 'A+',
    'antecedentes_personales' => 'Hipertensión controlada (desde 2010), cirugía de apéndice (1998).',
    'antecedentes_familiares' => 'Padre con diabetes tipo 2, madre con artritis.',
    'medicamentos_actuales' => 'Lisinopril 10mg diario.'
];

// Simulación de la línea de tiempo de consultas (ordenadas por fecha descendente)
$timeline_consultas = [
    [
        'fecha' => '2025-12-10',
        'motivo' => 'Control de Hipertensión',
        'diagnostico' => 'Estable. Se mantiene Lisinopril. PA: 125/80.',
        'doctor' => 'Dr. Javier Soto',
        'prescripciones' => ['Lisinopril 10mg (Receta #005)', 'Revisión en 3 meses']
    ],
    [
        'fecha' => '2025-08-15',
        'motivo' => 'Chequeo anual',
        'diagnostico' => 'Resultados de laboratorio normales. Sin cambios relevantes.',
        'doctor' => 'Dr. Javier Soto',
        'prescripciones' => ['Análisis de sangre anual (Orden #150)']
    ],
    [
        'fecha' => '2024-03-20',
        'motivo' => 'Dolor abdominal agudo',
        'diagnostico' => 'Gastroenteritis viral. Tratamiento sintomático.',
        'doctor' => 'Dra. María Paz',
        'prescripciones' => ['Loperamida (si es necesario)', 'Dieta blanda']
    ],
];

// Incluir el encabezado
include 'header_doctores.php';
?>

<h1 class="mb-4 fw-light text-primary">
    <i class="bi bi-person-lines-fill me-2"></i> Historial Clínico: <?= htmlspecialchars($paciente['nombre_completo']) ?>
</h1>

<div class="d-flex justify-content-start mb-4">
    <a href="crear_consulta.php?paciente_id=<?= htmlspecialchars($paciente_id) ?>" class="btn btn-primary me-2">
        <i class="bi bi-journal-plus me-1"></i> Nueva Consulta
    </a>
    
    <button class="btn btn-outline-secondary" onclick="window.print()">
        <i class="bi bi-printer me-1"></i> Imprimir Historial
    </button>
</div>

<div class="row g-4">

    <div class="col-lg-8">

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-card-list me-1"></i> Información Clínica Básica</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-2"><strong>ID Paciente:</strong> <?= htmlspecialchars($paciente['id']) ?></div>
                    <div class="col-md-6 mb-2"><strong>Edad/Género:</strong> <?= htmlspecialchars($paciente['edad']) ?> años / <?= htmlspecialchars($paciente['genero']) ?></div>
                    <div class="col-md-6 mb-2"><strong>Grupo Sanguíneo:</strong> <span class="badge bg-danger"><?= htmlspecialchars($paciente['grupo_sanguineo']) ?></span></div>
                    <div class="col-md-6 mb-2"><strong>Alergias:</strong> <span class="text-danger fw-bold"><?= htmlspecialchars($paciente['alergias']) ?></span></div>
                </div>

                <hr>
                <h6>Antecedentes Personales Relevantes</h6>
                <p class="text-muted small"><?= nl2br(htmlspecialchars($paciente['antecedentes_personales'])) ?></p>

                <h6>Antecedentes Familiares</h6>
                <p class="text-muted small"><?= nl2br(htmlspecialchars($paciente['antecedentes_familiares'])) ?></p>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="bi bi-clock-history me-1"></i> Línea de Tiempo de Consultas</h5>
            </div>
            <div class="card-body">
                <?php if (empty($timeline_consultas)): ?>
                    <div class="alert alert-warning text-center">No hay registros de consultas previas.</div>
                <?php else: ?>
                    <ul class="list-group list-group-flush timeline">
                        <?php foreach ($timeline_consultas as $consulta): ?>
                            <li class="list-group-item timeline-item">
                                <div class="timeline-dot"></div>
                                <div class="timeline-content">
                                    <h5 class="fw-bold mb-1 text-primary">
                                        Consulta del <?= date('d/m/Y', strtotime($consulta['fecha'])) ?>
                                    </h5>
                                    <span class="badge bg-secondary mb-2">Dr. <?= htmlspecialchars($consulta['doctor']) ?></span>

                                    <p class="mb-1"><strong>Motivo:</strong> <?= htmlspecialchars($consulta['motivo']) ?></p>
                                    <p class="mb-1"><strong>Diagnóstico:</strong> <?= htmlspecialchars($consulta['diagnostico']) ?></p>

                                    <div class="mt-2">
                                        <h6>Prescripciones y Órdenes:</h6>
                                        <ul class="small ps-3">
                                            <?php foreach ($consulta['prescripciones'] as $p): ?>
                                                <li><?= htmlspecialchars($p) ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">

        <div class="card mb-4 bg-light">
            <div class="card-body">
                <h5 class="card-title text-success"><i class="bi bi-pill me-1"></i> Medicamentos Activos</h5>
                <p class="small text-muted">Última actualización: 10/12/2025</p>

                <ul class="list-group list-group-flush">
                    <?php if (empty($paciente['medicamentos_actuales'])): ?>
                        <li class="list-group-item text-center">Ninguno registrado.</li>
                    <?php else: ?>
                        <li class="list-group-item fw-bold"><?= nl2br(htmlspecialchars($paciente['medicamentos_actuales'])) ?></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title text-info"><i class="bi bi-clipboard-data me-1"></i> Últimos Laboratorios</h5>
                <p class="small text-muted">Glucosa: 95 mg/dL (10/12/2025)</p>
                <p class="small text-muted">Colesterol Total: 180 mg/dL (10/12/2025)</p>
                <a href="resultados_labs.php?id=<?= htmlspecialchars($paciente_id) ?>" class="btn btn-sm btn-outline-info">Ver Todos los Resultados</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title text-secondary"><i class="bi bi-telephone me-1"></i> Contacto</h5>
                <p class="mb-0">Teléfono: <?= htmlspecialchars($paciente['contacto']) ?></p>
                <p class="mb-0">Contacto de emergencia: Hermano (555-5678)</p>
            </div>
        </div>

    </div>
</div>

<style>
    .timeline {
        padding-left: 0;
        list-style: none;
    }

    .timeline-item {
        position: relative;
        padding-left: 20px;
        margin-bottom: 30px;
        border-left: 2px solid #e9ecef;
        /* Línea vertical gris */
    }

    .timeline-dot {
        position: absolute;
        left: -8px;
        /* Posiciona el punto en la línea */
        top: 0;
        width: 15px;
        height: 15px;
        background-color: #0d6efd;
        /* Punto azul */
        border-radius: 50%;
        border: 2px solid white;
        z-index: 1;
    }

    .timeline-content {
        padding-bottom: 20px;
        padding-top: 5px;
    }
</style>


<?php
// Incluir el pie de página
include 'footer_doctores.php';
?>