<?php
require_once "../config/conexion.php";

$id = intval($_GET['id'] ?? 0);
if($id <= 0){
    echo json_encode(['success'=>false]);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM pacientes WHERE id_paciente = :id LIMIT 1");
$stmt->execute([':id'=>$id]);
$paciente = $stmt->fetch(PDO::FETCH_ASSOC);

if($paciente){
    echo json_encode(['success'=>true, 'paciente'=>$paciente]);
} else {
    echo json_encode(['success'=>false]);
}
