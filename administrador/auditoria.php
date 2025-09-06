<?php
include_once '../componentes/header.php';
?>

<body>

    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->

        <?php include_once '../componentes/sidebar.php'; ?>

        <!-- Page Content -->
        <div id="content" class="p-4 bg-gray-100 flex-grow">
            <!-- Navbar -->
              <?php
           include_once '../componentes/barra_nav.php';
        ?>

           

            <!-- Tabla de pacientes -->
            <div class="card shadow-sm rounded-xl">
                <div class="card-body">
                    <h5 class="card-title mb-3">Lista de Logs</h5>
                    <div class="table-responsive">
                       <?php


try {
    $sql = "SELECT l.id_log, 
                   u.id_usuario,  
                   u.rol, 
                   l.accion, 
                   l.descripcion, 
                   l.fecha_hora, 
                   l.ip_origen, 
                   l.dispositivo
            FROM logs l
            LEFT JOIN usuarios u ON l.id_usuario = u.id_usuario
            ORDER BY l.fecha_hora  ASC";
    $stmt = $pdo->query($sql);
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener logs: " . $e->getMessage());
}
?>

<table id="tablaPacientes" class="table table-striped table-hover align-middle mb-0 nowrap" style="width:100%">
    <thead class="table-light">
        <tr>
            <th>ID</th>
            <th>Usuario</th>
            <th>Rol</th>
            <th>Acción</th>
            <th>Descripción</th>
            <th>Fecha/Hora</th>
            <th>IP Origen</th>
            <th>Dispositivo</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($logs as $log): ?>
            <tr>
                <td><?php echo htmlspecialchars($log['id_log']); ?></td>
                <td><?php echo htmlspecialchars($log['id_usuario'] ?? '---'); ?></td>

                <td><?php echo htmlspecialchars($log['rol'] ?? '---'); ?></td>
                <td><span class="badge bg-primary"><?php echo htmlspecialchars($log['accion']); ?></span></td>
                <td><?php echo htmlspecialchars($log['descripcion']); ?></td>
                <td><?php echo htmlspecialchars($log['fecha_hora']); ?></td>
                <td><?php echo htmlspecialchars($log['ip_origen']); ?></td>
                <td><?php echo htmlspecialchars($log['dispositivo']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

                    </div>
                </div>
            </div>
        </div>

        



    </div>


<?php
include_once '../componentes/footer.php';
?>