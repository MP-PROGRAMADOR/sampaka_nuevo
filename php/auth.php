<?php
require_once "../config/conexion.php"; 

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../index.php?error=sesion_expirada");
    exit();
}

$tiempo_inactividad = 300; // 5 minutos
if (isset($_SESSION['ultimo_movimiento'])) {
    $tiempo_transcurrido = time() - $_SESSION['ultimo_movimiento'];

    if ($tiempo_transcurrido > $tiempo_inactividad) {
        // Datos para log
        $id_usuario_log = $_SESSION['id_usuario'];
        $accion = "Cierre de sesión";
        $descripcion = "Sesión cerrada automáticamente por inactividad de más de 5 minutos";
        $ip_origen = $_SERVER['REMOTE_ADDR'] ?? 'Desconocido';
        $dispositivo = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';

        $stmt = $pdo->prepare("INSERT INTO logs (id_usuario, accion, descripcion, ip_origen, dispositivo) 
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$id_usuario_log, $accion, $descripcion, $ip_origen, $dispositivo]);

        session_unset();
        session_destroy();
        header("Location: ../index.php?error=inactividad");
        exit();
    }
}

$_SESSION['ultimo_movimiento'] = time();

// Variables de sesión disponibles
$id_usuariop       = $_SESSION['id_usuario'];
$nombre_usuario    = $_SESSION['username'];
$usuario_rol       = $_SESSION['rol'];
$usuario_nombre    = $_SESSION['nombre'];
$usuario_apellidos = $_SESSION['apellido'];
?>
