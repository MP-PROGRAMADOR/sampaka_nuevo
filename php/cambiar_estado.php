<?php
session_start();
require_once "../includes/config.php";

// Función para obtener IP real
function getRealIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return $_SERVER['HTTP_X_FORWARDED_FOR'];
    return $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';
}

// Función para obtener info del navegador/dispositivo
function getUserDevice() {
    return $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Recoger y sanitizar datos
    $id_usuario = isset($_POST["id_usuario"]) ? intval($_POST["id_usuario"]) : 0;
    $estado = isset($_POST["estado"]) ? trim($_POST["estado"]) : "";

    // Validaciones
    if ($id_usuario <= 0 || !in_array($estado, ['Activo','Inactivo'])) {
        $_SESSION['error'] = "Datos inválidos para cambiar el estado";
        header("Location: ../administrador/usuarios.php");
        exit;
    }

    try {
        // Actualizar estado en la base de datos
        $sql = "UPDATE usuarios SET estado = :estado WHERE id_usuario = :id_usuario";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':estado' => $estado,
            ':id_usuario' => $id_usuario
        ]);

        // Guardar acción en logs
        $ip = getRealIP();
        $device = getUserDevice();
        $accion = "Cambio de estado";
        $descripcion = "Se cambió el estado del usuario con ID $id_usuario a '$estado'";

        $log = $pdo->prepare("INSERT INTO logs (id_usuario, accion, descripcion, ip_origen, dispositivo)
                              VALUES (:id_usuario, :accion, :descripcion, :ip, :device)");
        // id_usuario del que realiza la acción (el log se guarda con el usuario logueado)
        $log->execute([
            ':id_usuario' => $_SESSION['user_id'] ?? NULL,
            ':accion' => $accion,
            ':descripcion' => $descripcion,
            ':ip' => $ip,
            ':device' => $device
        ]);

        $_SESSION['success'] = "Estado actualizado correctamente";
        header("Location: ../administrador/usuarios.php");
        exit;

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al actualizar el estado: " . $e->getMessage();
        header("Location: ../administrador/usuarios.php");
        exit;
    }
}
?>

