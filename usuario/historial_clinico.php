<?php
include_once '../config/conexion.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once '../php/auth.php';

// 1. Obtener y validar el ID del paciente
$paciente_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($paciente_id <= 0) {
    header("Location: ./pacientes.php?error=PacienteNoEncontrado");
    exit;
}

try {
    // 2. Obtener datos generales del paciente y su detalle (alergias, antecedentes)
    // Usamos LEFT JOIN para que si no tiene "detalle_consulta" aún, el paciente aparezca igual
    $sql_paciente = "SELECT p.*, d.alergias, d.antecedentes_familiares, d.operaciones, d.transfuciones 
                     FROM pacientes p 
                     LEFT JOIN consultas c ON p.id_paciente = c.id_paciente
                     LEFT JOIN detalle_consulta d ON c.id_consulta = d.id_consulta
                     WHERE p.id_paciente = :id
                     ORDER BY c.fecha_consulta DESC LIMIT 1";

    $stmt = $pdo->prepare($sql_paciente);
    $stmt->execute([':id' => $paciente_id]);
    $paciente_db = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$paciente_db) {
        die("Error: El paciente no existe en el sistema.");
    }

    // 3. Obtener el historial de consultas (Línea de tiempo)
    $sql_consultas = "SELECT c.*, per.nombre AS dr_nombre, per.apellido AS dr_apellido 
                      FROM consultas c
                      INNER JOIN personal per ON c.id_medico = per.id_personal
                      WHERE c.id_paciente = :id
                      ORDER BY c.fecha_consulta DESC";

    $stmt_c = $pdo->prepare($sql_consultas);
    $stmt_c->execute([':id' => $paciente_id]);
    $timeline_consultas = $stmt_c->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error de base de datos: " . $e->getMessage());
}

// Configuración para el header
$page_title = 'Historial de Pacientes';
$page_name = 'Historial de Pacientes ';

include '../componentes/header_usuario.php';
?>


<body>

    <?php include '../componentes/slider_usuario.php'; ?>
    <div class="main-content">
        <h1 class="mb-4 fw-light text-primary">
            <i class="bi bi-person-lines-fill me-2"></i> Historial Clínico: <?= htmlspecialchars($paciente_db['nombre'] . ' ' . $paciente_db['apellido']) ?>
        </h1>

        <div class="d-flex justify-content-start mb-4 no-print">

            <button class="btn btn-outline-secondary" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Imprimir Historial
            </button>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0"><i class="bi bi-card-list me-1"></i> Información Clínica Básica</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="text-muted small d-block">Código Paciente</label>
                                <span class="fw-bold"><?= htmlspecialchars($paciente_db['codigo']) ?></span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="text-muted small d-block">Edad / Sexo</label>
                                <?php
                                $nacimiento = new DateTime($paciente_db['fecha_nacimiento']);
                                $hoy = new DateTime();
                                $edad = $hoy->diff($nacimiento)->y;
                                ?>
                                <span class="fw-bold"><?= $edad ?> años / <?= $paciente_db['sexo'] == 'M' ? 'Masculino' : 'Femenino' ?></span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="text-muted small d-block">Alergias</label>
                                <span class="badge bg-danger"><?= htmlspecialchars($paciente_db['alergias'] ?? 'Ninguna registrada') ?></span>
                            </div>
                        </div>

                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Antecedentes Familiares</h6>
                                <p class="text-muted small"><?= nl2br(htmlspecialchars($paciente_db['antecedentes_familiares'] ?? 'No especificado')) ?></p>
                            </div>
                            <div class="col-md-6">
                                <h6>Operaciones / Transfusiones</h6>
                                <p class="text-muted small">
                                    <strong>Cirugías:</strong> <?= htmlspecialchars($paciente_db['operaciones'] ?? 'Ninguna') ?><br>
                                    <strong>Transfusiones:</strong> <?= htmlspecialchars($paciente_db['transfuciones'] ?? 'Ninguna') ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-dark text-white py-3">
                        <h5 class="mb-0"><i class="bi bi-clock-history me-1"></i> Histórico de Consultas</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($timeline_consultas)): ?>
                            <div class="alert alert-light text-center border">Este paciente aún no registra consultas médicas.</div>
                        <?php else: ?>
                            <div class="timeline">
                                <?php foreach ($timeline_consultas as $consulta): ?>
                                    <div class="timeline-item pb-4">
                                        <div class="timeline-dot"></div>
                                        <div class="timeline-content ms-3">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <h5 class="fw-bold text-primary mb-0">
                                                    <?= htmlspecialchars($consulta['tipo_consulta']) ?>
                                                </h5>
                                                <small class="text-muted fw-bold"><?= date('d/m/Y H:i', strtotime($consulta['fecha_consulta'])) ?></small>
                                            </div>
                                            <span class="badge bg-light text-dark border mb-3">Atendido por: Dr. <?= htmlspecialchars($consulta['dr_nombre'] . " " . $consulta['dr_apellido']) ?></span>

                                            <div class="row bg-light p-3 rounded-3 mb-2 g-2">
                                                <div class="col-6 col-md-3 small"><strong>PA:</strong> <?= htmlspecialchars($consulta['presion_arterial']) ?></div>
                                                <div class="col-6 col-md-3 small"><strong>Peso:</strong> <?= htmlspecialchars($consulta['peso']) ?> kg</div>
                                                <div class="col-6 col-md-3 small"><strong>Temp:</strong> <?= htmlspecialchars($consulta['temperatura']) ?>°C</div>
                                                <div class="col-6 col-md-3 small"><strong>IMC:</strong> <?= htmlspecialchars($consulta['IMC']) ?></div>
                                            </div>

                                            <p class="mb-1 text-dark"><strong>Motivo:</strong> <?= htmlspecialchars($consulta['motivo']) ?></p>
                                            <p class="mb-0 text-secondary"><strong>Diagnóstico:</strong> <?= nl2br(htmlspecialchars($consulta['diagnostico'])) ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-primary"><i class="bi bi-telephone-fill me-2"></i>Contacto</h5>
                        <hr>
                        <p class="mb-2"><strong>Teléfono:</strong> <?= htmlspecialchars($paciente_db['telefono']) ?></p>
                        <p class="mb-2"><strong>Email:</strong> <span class="small"><?= htmlspecialchars($paciente_db['correo']) ?></span></p>
                        <p class="mb-0"><strong>Dirección:</strong> <span class="small text-muted"><?= htmlspecialchars($paciente_db['direccion']) ?></span></p>
                    </div>
                </div>

                <div class="card border-0 shadow-sm bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-info-circle me-2"></i>Otros Datos</h5>
                        <hr class="bg-white">
                        <p class="small mb-2"><strong>Nacionalidad:</strong> <?= htmlspecialchars($paciente_db['nacionalidad']) ?></p>
                        <p class="small mb-2"><strong>Ocupación:</strong> <?= htmlspecialchars($paciente_db['ocupacion']) ?></p>
                        <p class="small mb-0"><strong>Registrado el:</strong> <?= date('d/m/Y', strtotime($paciente_db['fecha_registro'])) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .timeline {
                position: relative;
                padding: 10px 0;
            }

            .timeline-item {
                position: relative;
                border-left: 2px solid #dee2e6;
                padding-left: 20px;
            }

            .timeline-dot {
                position: absolute;
                left: -9px;
                top: 5px;
                width: 16px;
                height: 16px;
                background-color: var(--accent-color);
                border: 3px solid white;
                border-radius: 50%;
                box-shadow: 0 0 0 2px var(--accent-color);
            }

            @media print {
                .no-print {
                    display: none !important;
                }

                .card {
                    border: 1px solid #ddd !important;
                    box-shadow: none !important;
                }
            }
        </style>

    </div>
</body>





<?php include_once '../componentes/footer_usuario.php'; ?>