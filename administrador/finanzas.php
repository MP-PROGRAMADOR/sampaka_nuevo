<?php
include_once '../componentes/header.php';
require_once "../config/conexion.php";

// Lógica de datos (se mantiene igual)
try {
    $resIngresos = $pdo->query("SELECT SUM(monto) as total FROM ingresos")->fetch();
    $resPagos = $pdo->query("SELECT SUM(cantidad) as total FROM pagos")->fetch();
    $totalIngresos = ($resIngresos['total'] ?? 0) + ($resPagos['total'] ?? 0);
    $resGastos = $pdo->query("SELECT SUM(monto) as total FROM gastos")->fetch();
    $totalGastos = $resGastos['total'] ?? 0;
    $balance = $totalIngresos - $totalGastos;

    $sqlIngresos = "SELECT 'Ingreso' as tipo, concepto, monto, fecha_ingreso as fecha FROM ingresos 
                    UNION ALL 
                    SELECT 'Pago Analítica' as tipo, 'Cobro de prueba médica' as concepto, cantidad as monto, fecha_registro as fecha FROM pagos 
                    ORDER BY fecha DESC LIMIT 8";
    $movimientos = $pdo->query($sqlIngresos)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.9);
        --primary-gradient: linear-gradient(135deg, #0d6efd 0%, #0043a8 100%);
        --danger-gradient: linear-gradient(135deg, #dc3545 0%, #a71d2a 100%);
        --success-gradient: linear-gradient(135deg, #198754 0%, #0f5132 100%);
    }

    .card-stats {
        border: none;
        border-radius: 15px;
        transition: transform 0.3s ease;
        overflow: hidden;
    }

    .card-stats:hover {
        transform: translateY(-5px);
    }

    .icon-shape {
        width: 48px;
        height: 48px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .table-modern thead th {
        background-color: #f8f9fa;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
        border: none;
        color: #6c757d;
        padding: 15px;
    }

    .table-modern tbody td {
        padding: 15px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f1f1;
    }

    .btn-rounded {
        border-radius: 10px;
        padding: 10px 20px;
        font-weight: 600;
    }

    .form-control-custom {
        border-radius: 10px;
        border: 1px solid #e0e0e0;
        padding: 12px;
    }

    .form-control-custom:focus {
        box-shadow: 0 0 0 0.25 mil rem rgba(13, 110, 253, 0.1);
        border-color: #0d6efd;
    }
</style>

<div class="d-flex" id="wrapper">
    <?php include_once '../componentes/sidebar.php'; ?>

    <div id="content" class="p-4 bg-light flex-grow-1">
        <?php include_once '../componentes/barra_nav.php'; ?>

        <div class="row mb-4 align-items-center">
            <div class="col">
                <h3 class="fw-bold text-dark mb-1">Centro de Finanzas</h3>
                <p class="text-muted mb-0">Monitor de salud económica del Hospital Sampaka</p>
            </div>
            <div class="col-auto">
                <button onclick="window.print()" class="btn btn-white shadow-sm btn-rounded border text-dark">
                    <i class="bi bi-printer me-2"></i>Imprimir Reporte
                </button>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card card-stats shadow-sm text-white h-100" style="background: var(--primary-gradient);">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-semibold">INGRESOS TOTALES</span>
                            <div class="icon-shape"><i class="bi bi-arrow-up-right"></i></div>
                        </div>
                        <h2 class="fw-bold mb-1"><?= number_format($totalIngresos, 0) ?></h2>
                        <span class="opacity-75 small">FCFA acumulados</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-stats shadow-sm text-white h-100" style="background: var(--danger-gradient);">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-semibold">GASTOS OPERATIVOS</span>
                            <div class="icon-shape"><i class="bi bi-cart-dash"></i></div>
                        </div>
                        <h2 class="fw-bold mb-1"><?= number_format($totalGastos, 0) ?></h2>
                        <span class="opacity-75 small">FCFA en egresos</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-stats shadow-sm text-white h-100" style="background: <?= $balance >= 0 ? 'var(--success-gradient)' : 'var(--danger-gradient)' ?>;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-semibold">BALANCE NETO</span>
                            <div class="icon-shape"><i class="bi bi-wallet2"></i></div>
                        </div>
                        <h2 class="fw-bold mb-1"><?= number_format($balance, 0) ?></h2>
                        <span class="opacity-75 small">Caja disponible</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Registrar Egreso</h5>

                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success border-0 shadow-sm mb-4">
                                <i class="bi bi-check-circle me-2"></i><?= $_SESSION['success']; ?>
                            </div>
                            <?php unset($_SESSION['success']); ?>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger border-0 shadow-sm mb-4">
                                <i class="bi bi-exclamation-circle me-2"></i><?= $_SESSION['error']; ?>
                            </div>
                            <?php unset($_SESSION['error']); ?>
                        <?php endif; ?>


                        <form action="../php/guardar_gasto.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold">CONCEPTO</label>
                                <input type="text" name="concepto" class="form-control form-control-custom" placeholder="Descripción del gasto" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold">MONTO (FCFA)</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light"><i class="bi bi-cash"></i></span>
                                    <input type="number" name="monto" class="form-control form-control-custom border-start-0" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label text-muted small fw-bold">FECHA</label>
                                <input type="date" name="fecha_gasto" class="form-control form-control-custom" value="<?= date('Y-m-d') ?>">
                            </div>
                            <button type="submit" class="btn btn-dark w-100 btn-rounded shadow-sm">
                                <i class="bi bi-plus-lg me-2"></i>Confirmar Gasto
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-0">
                        <div class="p-4 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0">Flujo de Caja Reciente</h5>
                            <a href="historial_completo.php" class="text-primary text-decoration-none small fw-bold">Ver historial completo →</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-modern mb-0">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Categoría</th>
                                        <th>Concepto</th>
                                        <th class="text-end">Monto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($movimientos as $m): ?>
                                        <tr>
                                            <td class="text-muted"><?= date('d/m/Y', strtotime($m['fecha'])) ?></td>
                                            <td>
                                                <?php if ($m['tipo'] == 'Ingreso'): ?>
                                                    <span class="badge rounded-pill bg-primary-subtle text-primary px-3">Ingreso</span>
                                                <?php else: ?>
                                                    <span class="badge rounded-pill bg-info-subtle text-info px-3">Prueba Médica</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="fw-semibold text-dark"><?= htmlspecialchars($m['concepto']) ?></td>
                                            <td class="text-end">
                                                <span class="fw-bold text-success">+ <?= number_format($m['monto'], 0) ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once '../componentes/footer.php'; ?>