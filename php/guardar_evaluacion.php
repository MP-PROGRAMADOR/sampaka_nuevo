<?php
session_start();
require '../config/conexion.php'; // Tu conexión PDO

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_consulta = $_POST['id_consulta'] ?? null;
    $id_paciente = $_POST['id_paciente'] ?? null;
    $historial = trim($_POST['historial_enfermedad'] ?? '');
    $exploracion = trim($_POST['exploracion_fisica'] ?? '');
    $ids_pruebas = $_POST['ids_pruebas'] ?? [];

    // Validación básica
    if (!$id_consulta || !$id_paciente || empty($historial) || empty($exploracion)) {
        $_SESSION['error'] = "Todos los campos son obligatorios.";
        header("Location: ../administrador/consultas.php");
        exit;
    }

    try {
        // Iniciar transacción
        $pdo->beginTransaction();

        // 1. Actualizar tabla consultas
        $stmt = $pdo->prepare("UPDATE consultas SET IMC = :historial, explo_fisica = :exploracion WHERE id_consulta = :id");
        $stmt->execute([
            ':historial' => $historial,
            ':exploracion' => $exploracion,
            ':id' => $id_consulta
        ]);

        // 2. Insertar analíticas
        $stmtAnalitica = $pdo->prepare("
            INSERT INTO analiticas (id_consulta, id_paciente, id_prueba, id_usuario)
            VALUES (:id_consulta, :id_paciente, :id_prueba, :id_usuario)
        ");

        $id_usuario = $_SESSION['id_usuario']; // id del usuario logueado
        foreach ($ids_pruebas as $id_prueba) {
            $stmtAnalitica->execute([
                ':id_consulta' => $id_consulta,
                ':id_paciente' => $id_paciente,
                ':id_prueba' => $id_prueba,
                ':id_usuario' => $id_usuario
            ]);
        }

        // Confirmar transacción
        $pdo->commit();
        $_SESSION['success'] = "Evaluación registrada correctamente.";
        header("Location: ../administrador/consultas.php");
        exit;

    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error al guardar la evaluación: " . $e->getMessage();
        header("Location: ../administrador/consultas.php");
        exit;
    }

} else {
    $_SESSION['error'] = "Método no permitido.";
    header("Location: ../administrador/consultas.php");
    exit;
}
