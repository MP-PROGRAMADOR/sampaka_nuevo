<?php
require '../config/conexion.php'; // tu archivo de conexión PDO

if (isset($_GET['q'])) {
    $q = "%".$_GET['q']."%";

    $sql = "SELECT id_paciente, nombre, apellido, codigo, fecha_nacimiento 
            FROM pacientes 
            WHERE nombre LIKE :q1 OR apellido LIKE :q2 OR codigo LIKE :q3 
            LIMIT 10";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":q1", $q, PDO::PARAM_STR);
    $stmt->bindValue(":q2", $q, PDO::PARAM_STR);
    $stmt->bindValue(":q3", $q, PDO::PARAM_STR);
    $stmt->execute();

    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($resultados);
    exit;
}
?>

