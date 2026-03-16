<?php
session_start();
require_once "../config/conexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Recoger datos
    $id_medico   = $_SESSION['id_personal'] ?? 1;
    $id_paciente = $_POST['id_paciente'];
    $fecha       = $_POST['fecha'];
    $hora        = $_POST['hora'];
    $tipo        = $_POST['tipo_consulta'];
    $motivo      = $_POST['motivo'];
    $id_usuario  = $_SESSION['id_usuario'] ?? 1;

    // 2. Combinar fecha y hora para el campo DATETIME
    $fecha_completa = $fecha . " " . $hora . ":00";

    try {
        // 3. Preparar el INSERT
        // Nota: id_hospital lo obtenemos del personal o lo fijamos según tu lógica
        $sql = "INSERT INTO consultas (id_paciente, id_medico, id_hospital, fecha_consulta, tipo_consulta, motivo, id_usuario) 
                VALUES (:id_p, :id_m, :id_h, :fecha, :tipo, :motivo, :id_u)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id_p'   => $id_paciente,
            ':id_m'   => $id_medico,
            ':id_h'   => 1, // Ajustar si tienes múltiples hospitales
            ':fecha'  => $fecha_completa,
            ':tipo'   => $tipo,
            ':motivo' => $motivo,
            ':id_u'   => $id_usuario
        ]);

        // Redirigir con éxito
        header("Location: agenda.php?msj=registrado");
    } catch (PDOException $e) {
        die("Error al registrar la cita: " . $e->getMessage());
    }
} else {
    header("Location: agenda.php");
}