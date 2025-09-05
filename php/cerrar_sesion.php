<?php
session_start();
require_once "../config/conexion.php"; // tu archivo de conexión PDO

// Función para registrar logs
function registrar_log($pdo, $id_usuario, $accion, $descripcion) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';
    $dispositivo = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';

    try {
        $stmt = $pdo->prepare("INSERT INTO logs (id_usuario, accion, descripcion, ip_origen, dispositivo) 
                               VALUES (:id_usuario, :accion, :descripcion, :ip, :dispositivo)");
        $stmt->execute([
            ':id_usuario' => $id_usuario,
            ':accion' => $accion,
            ':descripcion' => $descripcion,
            ':ip' => $ip,
            ':dispositivo' => $dispositivo
        ]);
    } catch (PDOException $e) {
        // Si falla el log, lo ignoramos para no romper el logout
    }
}

// Registrar log de cierre de sesión si había usuario
if (isset($_SESSION['id_usuario'])) {
    $id_usuario = $_SESSION['id_usuario'];
    $username = $_SESSION['username'] ?? '';
    $nombre = $_SESSION['nombre'] ?? '';
    $apellidos = $_SESSION['apellidos'] ?? '';

    $descripcion = "El usuario '{$username}' ({$nombre} {$apellidos}) cerró sesión.";
    registrar_log($pdo, $id_usuario, "LOGOUT", $descripcion);
}

// Limpiar variables pero dejar la sesión viva para el mensaje
$_SESSION = [];
session_unset();

// Mensaje flash
$_SESSION['success'] = "Sesión cerrada correctamente.";

// Ahora sí destruir totalmente en la próxima carga
session_write_close();

// Redirigir al login
header("Location: ../");
exit;
