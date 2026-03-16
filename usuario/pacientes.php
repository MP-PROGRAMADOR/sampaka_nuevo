<?php

include '../componentes/header_usuario.php';

$page_title = 'Gestión de Pacientes';
$page_name = 'Mis Pacientes';

$id_usuario_sesion = $_SESSION['id_usuario'] ?? null;
if (!$id_usuario_sesion) {
    header("Location: ../login.php");
    exit();
}

$resultados = [];
try {
    $sql = "SELECT 
                p.id_paciente, 
                p.nombre AS paciente_nombre, 
                p.apellido AS paciente_apellido, 
                p.codigo, 
                p.sexo, 
                MAX(c.fecha_consulta) AS ultima_consulta
            FROM pacientes p
            INNER JOIN consultas c ON p.id_paciente = c.id_paciente 
            WHERE c.id_usuario = :id_u
            GROUP BY p.id_paciente, p.nombre, p.apellido, p.codigo, p.sexo
            ORDER BY ultima_consulta DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id_u' => $id_usuario_sesion]);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // El conteo reflejará solo los pacientes atendidos por este médico
    $total_pacientes_mostrados = count($resultados);
} catch (PDOException $e) {
    $error_message = "Error al obtener tus pacientes: " . $e->getMessage();
}

?>


<body>
    <?php include_once '../componentes/slider_usuario.php'; ?>

    <div class="main-content">
        <h1 class="text-primary fw-light mb-4"><i class="bi bi-people-fill me-2"></i> Lista de Pacientes</h1>


        <div class="card p-4 shadow-sm">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0 fw-bold">Gestión de Pacientes</h5>

            </div>


            <div class="table-responsive">
                <table id="tablaPacientes" class="table table-hover align-middle w-100">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Código</th>
                            <th>Sexo</th>
                            <th>Última Visita</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resultados as $p): ?>
                            <tr>
                                <td class="text-dark fw-bold"><?= $p['id_paciente'] ?></td>
                                <td><?= htmlspecialchars($p['paciente_nombre']) ?></td>
                                <td><?= htmlspecialchars($p['paciente_apellido']) ?></td>
                                <td><span class="badge bg-light text-primary border"><?= $p['codigo'] ?></span></td>
                                <td><?= $p['sexo'] ?></td>
                                <td>
                                    <div class="fw-bold text-dark" style="font-size: 0.85rem;">
                                        <?= date('d/m/Y', strtotime($p['ultima_consulta'])) ?>
                                    </div>
                                    <div class="text-muted small">
                                        <i class="bi bi-clock me-1"></i><?= date('h:i A', strtotime($p['ultima_consulta'])) ?>
                                    </div>
                                </td>
                                <td class="text-center ">
                                    <a href="historial_clinico.php?id=<?= $p['id_paciente'] ?>" class="btn-action-row btn-edit btn btn-primary " title="Historial">
                                        <i class="bi bi-clipboard2-pulse"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>


        <?php include_once '../componentes/footer_usuario.php'; ?>



</body>