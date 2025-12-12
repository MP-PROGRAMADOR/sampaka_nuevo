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

$nombre     = trim($_POST['nombre'] ?? '');
$num_cama   = trim($_POST['num_cama'] ?? '');
$id_usuario = $_POST['id_usuario'] ?? '';

/* ========= VALIDACIONES ========= */

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

/* ========= INSERTAR EN BD ========= */

try {

    // Asegurar modo estricto
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("
        INSERT INTO salas (nombre, num_cama, id_usuario)
        VALUES (:nombre, :num_cama, :id_usuario)
    ");

    $stmt->execute([
        ':nombre'     => $nombre,
        ':num_cama'   => $num_cama,
        ':id_usuario' => $id_usuario
    ]);

    // Registrar log del registro exitoso
    registrarLog($pdo, $id_usuario, "REGISTRAR_SALA", "Se registró la sala: {$nombre} con {$num_cama} camas.");

    $_SESSION['success'] = "Sala registrada correctamente.";
    header("Location: ../administrador/salas_hospital.php");
    exit;

} catch (PDOException $e) {

    // Registrar error en logs
    registrarLog($pdo, $id_usuario, "ERROR_REGISTRAR_SALA", $e->getMessage());

    $_SESSION['error'] = "Error al registrar la sala: " . $e->getMessage();
    header("Location: ../administrador/salas_hospital.php");
    exit;
}
?>
