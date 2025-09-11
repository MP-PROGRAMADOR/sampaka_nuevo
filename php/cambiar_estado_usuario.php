<?php
session_start();
require_once "../config/conexion.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = $_POST['id_usuario'] ?? null;
    $nuevo_estado = $_POST['nuevo_estado'] ?? '';

    if (!$id_usuario || !$nuevo_estado) {
        $_SESSION['error'] = "Datos incompletos para cambiar el estado.";
        header("Location: ../administrador/usuarios.php");
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE usuarios SET estado = :estado WHERE id_usuario = :id_usuario");
        $stmt->execute([
            ':estado' => $nuevo_estado,
            ':id_usuario' => $id_usuario
        ]);

        $_SESSION['success'] = "Estado del usuario actualizado correctamente.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al cambiar estado: " . $e->getMessage();
    }

    header("Location: ../administrador/usuarios.php");
    exit;
}
?>
