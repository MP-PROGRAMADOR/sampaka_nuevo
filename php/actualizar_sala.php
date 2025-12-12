<?php
session_start();
require_once "../config/conexion.php";

/* ========= FUNCIONES ========= */

function getIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } else {
        return $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';
    }
}

function getDispositivo() {
    return $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';
}

function registrarLog($pdo, $id_usuario, $accion, $descripcion) {

    try {
        $stmt = $pdo->prepare("
            INSERT INTO logs (id_usuario, accion, descripcion, ip_origen, dispositivo)
            VALUES (:id_usuario, :accion, :descripcion, :ip, :dispositivo)
        ");

        $stmt->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindValue(':accion', $accion, PDO::PARAM_STR);
        $stmt->bindValue(':descripcion', $descripcion, PDO::PARAM_STR);
        $stmt->bindValue(':ip', getIP(), PDO::PARAM_STR);
        $stmt->bindValue(':dispositivo', getDispositivo(), PDO::PARAM_STR);

        $stmt->execute();

    } catch (PDOException $e) {
        // Evitar interrupción si falla el LOG
        error_log("ERROR REGISTRANDO LOG: " . $e->getMessage());
    }
}

/* ========= VALIDACIÓN DEL MÉTODO ========= */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $_SESSION['error'] = "Método no permitido.";
    header("Location: ../administrador/salas_hospital.php");
    exit;
}

/* ========= CAPTURA DE DATOS ========= */

$id_sala     = $_POST['id_sala'] ?? '';
$nombre      = trim($_POST['nombre'] ?? '');
$num_cama    = trim($_POST['num_cama'] ?? '');
$id_usuario  = $_POST['id_usuario'] ?? '';

/* ========= VALIDACIONES ========= */

if (empty($id_sala) || !is_numeric($id_sala)) {
    $_SESSION['error'] = "ID de sala no válido.";
    header("Location: ../administrador/salas_hospital.php");
    exit;
}

if (empty($nombre)) {
    $_SESSION['error'] = "El nombre de la sala es obligatorio.";
    header("Location: ../administrador/salas_hospital.php");
    exit;
}

if ($num_cama === '' || !is_numeric($num_cama) || $num_cama < 0) {
    $_SESSION['error'] = "El número de camas debe ser válido.";
    header("Location: ../administrador/salas_hospital.php");
    exit;
}

if (empty($id_usuario)) {
    $_SESSION['error'] = "El usuario es obligatorio.";
    header("Location: ../administrador/salas_hospital.php");
    exit;
}

/* ========= ACTUALIZACIÓN EN BD ========= */

try {

    // Asegurar modo estricto de errores
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("
        UPDATE salas
        SET nombre = :nombre,
            num_cama = :num_cama,
            id_usuario = :id_usuario
        WHERE id_sala = :id_sala
    ");

    $stmt->execute([
        ':nombre'     => $nombre,
        ':num_cama'   => $num_cama,
        ':id_usuario' => $id_usuario,
        ':id_sala'    => $id_sala
    ]);

    registrarLog($pdo, $id_usuario, "EDITAR_SALA", "Sala actualizada: {$nombre} (ID: {$id_sala})");

    $_SESSION['success'] = "Sala actualizada correctamente.";
    header("Location: ../administrador/salas_hospital.php");
    exit;

} catch (PDOException $e) {

    registrarLog($pdo, $id_usuario, "ERROR_EDITAR_SALA", $e->getMessage());

    $_SESSION['error'] = "Error al actualizar la sala: " . $e->getMessage();
    header("Location: ../administrador/salas_hospital.php");
    exit;
}
?>
