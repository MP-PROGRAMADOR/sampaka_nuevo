<?php
include '../componentes/header_usuario.php';

// 1. Configuración para el header
$page_title = 'Analíticas';
$page_name = 'Analíticas';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login.php");
    exit();
}
$id_usuario_sesion = $_SESSION['id_usuario'];

// 3. LÓGICA DE CARGA DESDE BASE DE DATOS
try {

    function obtener_analiticas_reales($pdo, $id_u, $estado_filtro)
    {
        $sql = "SELECT 
                    a.id_analitica,
                    p.nombre, 
                    p.apellido,
                    a.fecha_registro as fecha_toma,
                    pr.nombre as tipo_examen,
                    a.estado,
                    a.resultado,
                    a.valores_referencia,
                    a.comentario
                FROM analiticas a
                INNER JOIN pacientes p ON a.id_paciente = p.id_paciente
                INNER JOIN pruebas_medicas pr ON a.id_prueba = pr.id_prueba
                INNER JOIN consultas c ON a.id_consulta = c.id_consulta
                WHERE c.id_usuario = :id_u AND a.estado = :estado
                ORDER BY a.fecha_registro DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id_u' => $id_u,
            ':estado' => $estado_filtro
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cargar listas usando el ID de la sesión automáticamente
    $pendientes = obtener_analiticas_reales($pdo, $id_usuario_sesion, 'Pendiente');
    $revisados = obtener_analiticas_reales($pdo, $id_usuario_sesion, 'Entregado');
} catch (PDOException $e) {
    error_log("Error en analíticas: " . $e->getMessage());
    $pendientes = [];
    $revisados = [];
}
?>

<body>
    <?php include '../componentes/slider_usuario.php'; ?>
    <div class="main-content">

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
                        <i class="bi bi-check-circle-fill me-1"></i> Atendidos (<?= count($revisados) ?>)
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="labsTabsContent">
                <!-- Atendidos -->
                <div class="tab-pane fade show active" id="pendientes" role="tabpanel">
                    <?php if (empty($pendientes)): ?>
                        <div class="alert alert-light text-center mt-3 border">No hay resultados pendientes.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table id="tablaPacientes" class="table table-striped table-hover align-middle">
                                <thead class="table-warning text-white">
                                    <tr>
                                        <th>Paciente</th>
                                        <th>Fecha Toma</th>
                                        <th>Tipo de Examen</th>
                                        <th>Resultado Preliminar</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendientes as $res): ?>
                                        <tr>
                                            <td class="fw-bold"><?= htmlspecialchars($res['nombre'] . ' ' . $res['apellido']) ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($res['fecha_toma'])) ?></td>
                                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($res['tipo_examen']) ?></span></td>
                                            <td><span class="text-truncate d-inline-block" style="max-width: 150px;"><?= htmlspecialchars($res['resultado'] ?? 'Pendiente de carga') ?></span></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary btn-ver-informe"
                                                    data-bs-toggle="modal" data-bs-target="#modalVerInforme"
                                                    data-nombre="<?= htmlspecialchars($res['nombre'] . ' ' . $res['apellido']) ?>"
                                                    data-fecha="<?= date('d/m/Y H:i', strtotime($res['fecha_toma'])) ?>"
                                                    data-tipo="<?= htmlspecialchars($res['tipo_examen']) ?>"
                                                    data-resultado="<?= htmlspecialchars($res['resultado'] ?? 'Sin resultado') ?>"
                                                    data-referencia="<?= htmlspecialchars($res['valores_referencia'] ?? 'N/A') ?>"
                                                    data-comentario="<?= htmlspecialchars($res['comentario'] ?? 'Sin observaciones') ?>"
                                                    data-clase="warning"
                                                    data-estado="Pendiente">
                                                    <i class="bi bi-eye"></i> Ver Detalle
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
                <!-- Pendientes -->
                <div class="tab-pane fade" id="revisados" role="tabpanel">
                    <?php if (empty($revisados)): ?>
                        <div class="alert alert-light text-center mt-3 border">No hay analíticas marcadas como entregadas.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table id="tablaPacientes" class="table table-striped table-hover align-middle w-100">
                                <thead class="table-success">
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
                                            <td><?= htmlspecialchars($res['nombre'] . ' ' . $res['apellido']) ?></td>
                                            <td><?= date('d/m/Y', strtotime($res['fecha_toma'])) ?></td>
                                            <td><?= htmlspecialchars($res['tipo_examen']) ?></td>
                                            <td><span class="badge bg-success">Entregado</span></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-secondary btn-ver-informe"
                                                    data-bs-toggle="modal" data-bs-target="#modalVerInforme"
                                                    data-nombre="<?= htmlspecialchars($res['nombre'] . ' ' . $res['apellido']) ?>"
                                                    data-fecha="<?= date('d/m/Y', strtotime($res['fecha_toma'])) ?>"
                                                    data-tipo="<?= htmlspecialchars($res['tipo_examen']) ?>"
                                                    data-resultado="<?= htmlspecialchars($res['resultado']) ?>"
                                                    data-referencia="<?= htmlspecialchars($res['valores_referencia']) ?>"
                                                    data-comentario="<?= htmlspecialchars($res['comentario']) ?>"
                                                    data-clase="success"
                                                    data-estado="Entregado">
                                                    <i class="bi bi-file-earmark-check"></i> Informe Final
                                                </button>
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

        <div class="modal fade" id="modalVerInforme" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="bi bi-file-earmark-medical me-2"></i> Detalle de Analítica</h5>
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
                                <p id="modalFechaToma" class="fw-bold mb-0"></p>
                            </div>
                            <div class="col-md-3 text-end">
                                <span id="modalBadgeEstado" class="badge"></span>
                            </div>
                        </div>

                        <div class="p-4 bg-light rounded">
                            <h6 class="text-primary border-bottom pb-2 fw-bold" id="modalTipoExamen"></h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless mt-3">
                                    <thead class="text-muted small border-bottom">
                                        <tr>
                                            <th>Análisis</th>
                                            <th>Resultado</th>
                                            <th>Referencia</th>
                                        </tr>
                                    </thead>
                                    <tbody id="modalCuerpoResultados">
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3 border-top pt-2">
                                <p class="text-muted small mb-1">OBSERVACIONES</p>
                                <p id="modalObservaciones" class="fst-italic"></p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-primary" onclick="window.print()">
                            <i class="bi bi-printer"></i> Imprimir Informe
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var modalVerInforme = document.getElementById('modalVerInforme');
                modalVerInforme.addEventListener('show.bs.modal', function(event) {
                    var button = event.relatedTarget;

                    // Inyectar datos en cabecera
                    modalVerInforme.querySelector('#modalNombrePaciente').textContent = button.getAttribute('data-nombre');
                    modalVerInforme.querySelector('#modalFechaToma').textContent = button.getAttribute('data-fecha');
                    modalVerInforme.querySelector('#modalTipoExamen').textContent = button.getAttribute('data-tipo');
                    modalVerInforme.querySelector('#modalObservaciones').textContent = button.getAttribute('data-comentario');

                    // Estado Badge
                    var badge = modalVerInforme.querySelector('#modalBadgeEstado');
                    badge.textContent = button.getAttribute('data-estado');
                    badge.className = 'badge bg-' + button.getAttribute('data-clase');

                    // Cuerpo de la tabla de resultados
                    var res = button.getAttribute('data-resultado');
                    var ref = button.getAttribute('data-referencia');
                    var tipo = button.getAttribute('data-tipo');

                    modalVerInforme.querySelector('#modalCuerpoResultados').innerHTML = `
                        <tr>
                            <td>${tipo}</td>
                            <td class="fw-bold text-dark">${res}</td>
                            <td class="text-muted">${ref}</td>
                        </tr>
                    `;
                });
            });
        </script>

    </div>

    <?php include_once '../componentes/footer_usuario.php'; ?>

</body>