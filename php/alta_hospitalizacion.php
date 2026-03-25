<?php
session_start();
require_once "../config/conexion.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_hosp    = $_POST['id_hospitalizacion'] ?? null;
    $fecha_alta = $_POST['fecha_alta'] ?? date('Y-m-d');
    $estado     = $_POST['estado_alta'] ?? 'Mejorado';

    if (!$id_hosp) {
        $_SESSION['error'] = "ID de hospitalización no válido.";
        header("Location: ../administrador/hospitalizaciones.php");
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE hospitalizaciones SET 
                                fecha_alta = :fecha, 
                                estado_alta = :estado 
                              WHERE id_hospitalizacion = :id");
        $stmt->execute([
            ':fecha'  => $fecha_alta,
            ':estado' => $estado,
            ':id'     => $id_hosp
        ]);

        $_SESSION['success'] = "Alta médica registrada correctamente.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al procesar el alta: " . $e->getMessage();
    }

    header("Location: ../administrador/hospitalizaciones.php");
    exit;
}