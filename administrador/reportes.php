<?php
include_once '../componentes/header.php';
require_once "../config/conexion.php";

// 1. GESTIÓN DE FILTROS (Normalización de fechas para DATETIME)
$fecha_inicio = $_GET['desde'] ?? date('Y-m-01');
$fecha_fin    = $_GET['hasta'] ?? date('Y-m-d');
$modulo       = $_GET['modulo'] ?? 'pacientes';

// Ajuste para incluir todo el día final (23:59:59)
$f_inicio_full = $fecha_inicio . " 00:00:00";
$f_fin_full    = $fecha_fin . " 23:59:59";

try {
    $hospital = $pdo->query("SELECT * FROM hospitales LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    
    // 2. CONSULTAS DINÁMICAS (Consistencia de nombres de columna)
    // Usamos alias (AS) para que la tabla HTML no cambie su lógica
    switch ($modulo) {
        case 'finanzas':
            $sql = "SELECT id as cod, concepto as descr, fecha as fec, 'Ingreso' as cat, monto as val 
                    FROM ingresos WHERE fecha BETWEEN ? AND ? ORDER BY fecha DESC";
            break;
        case 'consultas':
            $sql = "SELECT id_consulta as cod, tipo_consulta as descr, fecha_consulta as fec, 'Médica' as cat, precio as val 
                    FROM consultas WHERE fecha_consulta BETWEEN ? AND ? ORDER BY fecha_consulta DESC";
            break;
        default: // Pacientes
            $sql = "SELECT codigo as cod, CONCAT(nombre, ' ', apellido) as descr, fecha_registro as fec, nacionalidad as cat, '0' as val 
                    FROM pacientes WHERE fecha_registro BETWEEN ? AND ? ORDER BY fecha_registro DESC";
            break;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$f_inicio_full, $f_fin_full]);
    $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. ESTADÍSTICAS PARA GRÁFICAS (Basadas en el módulo actual)
    // Esto hace que la gráfica cambie según lo que estés viendo
    $columna_fecha = ($modulo == 'consultas') ? 'fecha_consulta' : (($modulo == 'finanzas') ? 'fecha' : 'fecha_registro');
    $tabla_actual = ($modulo == 'finanzas') ? 'ingresos' : $modulo;
    
    $sqlGrafica = "SELECT DATE($columna_fecha) as dia, COUNT(*) as total 
                   FROM $tabla_actual 
                   WHERE $columna_fecha BETWEEN ? AND ? 
                   GROUP BY dia ORDER BY dia ASC";
                   
    $stmtG = $pdo->prepare($sqlGrafica);
    $stmtG->execute([$f_inicio_full, $f_fin_full]);
    $resumenGrafica = $stmtG->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) { echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>"; }
?>

<style>
    :root { --primary-color: #4e73df; --secondary-color: #858796; }
    body { background-color: #f8f9fc; font-family: 'Nunito', sans-serif; }
    
    /* Estilo de Tarjetas y Filtros */
    .filter-card { background: white; border-radius: 15px; border-left: 5px solid var(--primary-color); box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15); }
    .stat-card { border: none; border-radius: 12px; transition: transform 0.2s; }
    .stat-card:hover { transform: translateY(-5px); }
    
    /* Tabla Estilizada */
    .table-report { background: white; border-radius: 15px; overflow: hidden; }
    .table thead { background: var(--primary-color); color: white; }
    
    @media print {
        .no-print, .sidebar, .navbar { display: none !important; }
        #content { margin: 0 !important; padding: 0 !important; width: 100%; }
        .filter-card { border: 1px solid #ddd !important; box-shadow: none !important; }
        .print-only { display: block !important; }
    }
    .print-only { display: none; }
</style>

<div class="d-flex" id="wrapper">
    <?php include_once '../componentes/sidebar.php'; ?>

    <div id="content" class="p-4 flex-grow-1">
        
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <div>
                <h2 class="fw-bold text-gray-800">Panel de Reportes</h2>
                <p class="text-muted">Análisis de datos hospitalarios en tiempo real</p>
            </div>
            <button onclick="window.print()" class="btn btn-primary shadow-sm rounded-pill px-4">
                <i class="bi bi-file-earmark-pdf-fill me-2"></i>Exportar Informe
            </button>
        </div>

        <div class="filter-card p-4 mb-4 no-print">
            <form method="GET" action="reportes.php" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small text-uppercase fw-bold text-muted">Rango Inicial</label>
                    <input type="date" name="desde" class="form-control form-control-lg" value="<?= $fecha_inicio ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-uppercase fw-bold text-muted">Rango Final</label>
                    <input type="date" name="hasta" class="form-control form-control-lg" value="<?= $fecha_fin ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-uppercase fw-bold text-muted">Módulo de Análisis</label>
                    <select class="form-select form-select-lg" name="modulo">
                        <option value="pacientes" <?= $modulo == 'pacientes' ? 'selected' : '' ?>>📋 Registro de Pacientes</option>
                        <option value="finanzas" <?= $modulo == 'finanzas' ? 'selected' : '' ?>>💰 Balance de Finanzas</option>
                        <option value="consultas" <?= $modulo == 'consultas' ? 'selected' : '' ?>>🩺 Consultas Médicas</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm">Actualizar</button>
                </div>
            </form>
        </div>

        <div class="print-only text-center mb-5">
            <h1 class="fw-bold"><?= strtoupper($hospital['nombre'] ?? 'HOSPITAL CENTRAL') ?></h1>
            <p class="lead"><?= $hospital['direccion'] ?> | Tel: <?= $hospital['telefono'] ?></p>
            <div class="badge bg-dark p-2 px-4">REPORTE OFICIAL DE <?= strtoupper($modulo) ?></div>
            <p class="mt-2 text-muted">Generado el: <?= date('d/m/Y H:i') ?> | Periodo: <?= $fecha_inicio ?> al <?= $fecha_fin ?></p>
            <hr>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-8">
                <div class="card stat-card shadow-sm p-4 h-100">
                    <h6 class="text-primary fw-bold text-uppercase mb-3">Flujo de actividad</h6>
                    <div style="height: 250px;"><canvas id="chartPrincipal"></canvas></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card shadow-sm p-4 h-100 text-center border-bottom border-primary border-5">
                    <h6 class="text-muted fw-bold text-uppercase">Total Registrados</h6>
                    <h1 class="display-3 fw-bold text-dark my-3"><?= count($datos) ?></h1>
                    <p class="text-success small"><i class="bi bi-graph-up"></i> Datos procesados correctamente</p>
                    <hr>
                    <div style="height: 150px;"><canvas id="chartDona"></canvas></div>
                </div>
            </div>
        </div>

        <div class="table-report shadow-sm mb-5">
            <table class="table table-borderless table-hover align-middle mb-0">
                <thead>
                    <tr class="text-center">
                        <th class="p-3">Código</th>
                        <th class="text-start">Descripción / Nombre</th>
                        <th>Fecha de Registro</th>
                        <th>Categoría</th>
                        <th class="text-end p-3">Monto / Valor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($datos)): ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">No existen registros para el rango seleccionado.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($datos as $d): ?>
                    <tr class="text-center">
                        <td><span class="badge bg-light text-dark border">#<?= $d['cod'] ?></span></td>
                        <td class="text-start fw-bold text-dark"><?= htmlspecialchars($d['descr']) ?></td>
                        <td><?= date('d/m/Y', strtotime($d['fec'])) ?></td>
                        <td><span class="badge rounded-pill bg-info text-dark"><?= $d['cat'] ?></span></td>
                        <td class="text-end fw-bold text-primary p-3"><?= $d['val'] > 0 ? number_format($d['val'], 0) . ' FCFA' : '---' ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="print-only mt-5">
            <div class="d-flex justify-content-around text-center pt-5">
                <div><div style="border-top: 2px solid #000; width: 200px;"></div><p class="fw-bold mt-2">Firma Autorizada</p></div>
                <div><div style="border-top: 2px solid #000; width: 200px;"></div><p class="fw-bold mt-2">Sello del Hospital</p></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Gráfica de Líneas Dinámica
    const ctxMain = document.getElementById('chartPrincipal').getContext('2d');
    new Chart(ctxMain, {
        type: 'line',
        data: {
            labels: [<?php foreach($resumenGrafica as $rg) echo "'".date('d/m', strtotime($rg['dia']))."',"; ?>],
            datasets: [{
                label: 'Volumen de <?= ucfirst($modulo) ?>',
                data: [<?php foreach($resumenGrafica as $rg) echo $rg['total'].","; ?>],
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointBackgroundColor: '#4e73df'
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
    });

    // Gráfica de Dona
    const ctxDona = document.getElementById('chartDona').getContext('2d');
    new Chart(ctxDona, {
        type: 'doughnut',
        data: {
            labels: ['Actual', 'Meta'],
            datasets: [{
                data: [<?= count($datos) ?>, 100],
                backgroundColor: ['#4e73df', '#eaecf4'],
                hoverOffset: 4
            }]
        },
        options: { cutout: '70%', maintainAspectRatio: false, plugins: { legend: { display: false } } }
    });
</script>