<?php
session_start();
require_once "../config/conexion.php";

// 1. Configuración del Header
$page_title = 'Tratamientos del Paciente';
$page_name = 'Tratamientos';



include 'header_doctores.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="mis_pacientes.php">Pacientes</a></li>
                    <li class="breadcrumb-item active">Tratamientos</li>
                </ol>
            </nav>
            <h1 class="fw-light text-primary">
                <i class="bi bi-receipt-cutoff me-2"></i>
                Tratamientos: <span class="fw-bold"><?= htmlspecialchars(($info_paciente['nombre'] ?? '') . ' ' . ($info_paciente['apellido'] ?? '')) ?></span>
            </h1>
        </div>
        <a href="pacientes.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver al listado
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">

            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger"><?= $error_message ?></div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Medicamento</th>
                            <th>Dosis</th>
                            <th>Frecuencia</th>
                            <th>Fecha Inicio</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($resultados_tratamientos)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    No hay tratamientos registrados para este paciente.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($resultados_tratamientos as $t): ?>
                                <tr>
                                    <td class="fw-bold text-dark"><?= htmlspecialchars($t['medicamento']) ?></td>
                                    <td><?= htmlspecialchars($t['dosis']) ?></td>
                                    <td><?= htmlspecialchars($t['frecuencia']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($t['fecha_inicio'])) ?></td>
                                    <td>
                                        <?php if ($t['estado'] == 'Activo'): ?>
                                            <span class="badge bg-success-subtle text-success border border-success">Activo</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary">Finalizado</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary" title="Ver Detalles">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" title="Detener Tratamiento">
                                            <i class="bi bi-stop-circle"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer_doctores.php'; ?>