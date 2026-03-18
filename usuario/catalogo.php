<?php
require_once "../config/conexion.php";



// 1. Configuración para el header
$page_title = 'Catalogo';
$page_name = 'Catalogo';

include_once '../componentes/header_usuario.php';
$id_usuario_sesion = $_SESSION['id_usuario'] ?? 0;


try {
    $stmt = $pdo->query("
        SELECT pm.id_prueba, pm.nombre, pm.precio, u.username AS usuario
        FROM pruebas_medicas pm
        LEFT JOIN usuarios u ON pm.id_usuario = u.id_usuario
        ORDER BY pm.id_prueba DESC
    ");
    $pruebas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_db = $e->getMessage();
    $pruebas = [];
}
?>

<body>
    <?php include '../componentes/slider_usuario.php'; ?>

    <div class="main-content">
        <h1 class="mb-4 fw-light text-primary">
            <i class="bi bi-clipboard2-pulse-fill me-2"></i> Pruebas
        </h1>

        <div class="card p-4 shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0 fw-bold">Lista de Pruebas Médicas</h5>

            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show fade-msg" role="alert">
                    <i class="bi bi-check-circle me-2"></i> <?= htmlspecialchars($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($error_db)): ?>
                <div class="alert alert-danger">Error: <?= htmlspecialchars($error_db); ?></div>
            <?php endif; ?>

            <div class="table-responsive">
                <table id="tablaPacientes" class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-muted">ID</th>
                            <th>Nombre de la Prueba</th>
                            <th>Precio (FCFA)</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pruebas as $p): ?>
                            <tr>
                                <td class="fw-bold text-muted">#<?= $p['id_prueba'] ?></td>
                                <td><?= htmlspecialchars($p['nombre']) ?></td>
                                <td class="text-primary fw-bold"><?= number_format($p['precio'], 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php include_once '../componentes/footer_usuario.php'; ?>

</body>

</html>