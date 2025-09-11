<?php
session_start();
require '../config/conexion.php'; // tu conexión PDO

// Obtener parámetros
$rol =  $_SESSION['rol']; // tipo_consulta que corresponde al rol del usuario
$q = trim($_GET['q'] ?? '');

// Validación rápida
if (!$rol || strlen($q) < 2) {
    echo "<div class='list-group-item'>Introduce al menos 2 caracteres para buscar...</div>";
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            c.id_consulta,
            p.nombre AS paciente_nombre,
            p.apellido AS paciente_apellido,
            p.id_paciente AS id_paciente,
            c.tipo_consulta,
            c.motivo,
            c.fecha_consulta
        FROM consultas c
        INNER JOIN pacientes p ON c.id_paciente = p.id_paciente
        LEFT JOIN detalle_consulta d ON c.id_consulta = d.id_consulta
        WHERE c.tipo_consulta = :rol
          AND c.pagado = 1
          AND (p.nombre LIKE :q OR p.apellido LIKE :q)
        ORDER BY c.fecha_consulta DESC
        LIMIT 10
    ");
    $stmt->execute([
        ':rol' => $rol,
        ':q' => "%$q%"
    ]);

    $consultas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($consultas) === 0) {
        echo "<div class='list-group-item'>No se encontraron resultados.</div>";
    } else {
        foreach ($consultas as $c) {
            $nombreCompleto = htmlspecialchars($c['paciente_nombre'] . ' ' . $c['paciente_apellido']);
            echo "<button type='button' 
                        class='list-group-item list-group-item-action seleccionar-consulta' 
                        data-id-consulta='{$c['id_consulta']}' 
                        data-id-paciente='{$c['id_paciente']}' 
                        data-fecha='{$c['fecha_consulta']}'
                        data-paciente='{$nombreCompleto}'>
                        {$c['fecha_consulta']} - {$nombreCompleto} ({$c['motivo']})
                  </button>";
        }
    }

} catch (PDOException $e) {
    echo "<div class='list-group-item text-danger'>Error al buscar consultas: " . $e->getMessage() . "</div>";
}