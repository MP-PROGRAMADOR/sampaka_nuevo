<?php
require_once "../config/conexion.php";

$idPaciente = $_GET['id_paciente'];
$fecha      = $_GET['fecha'];

$stmt = $pdo->prepare("
    SELECT 
        a.id_analitica,
        pr.nombre AS prueba_nombre,
        pr.precio
    FROM analiticas a
    INNER JOIN pruebas_medicas pr ON a.id_prueba = pr.id_prueba
    WHERE a.id_paciente = :id
      AND DATE(a.fecha_registro) = :fecha
      AND a.pagado = 0
");

$stmt->execute([
    ':id' => $idPaciente,
    ':fecha' => $fecha
]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
