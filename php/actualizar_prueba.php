<?php
session_start();
require_once "../config/conexion.php";

// Función para obtener IP
function getIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return $_SERVER['HTTP_X_FORWARDED_FOR'];
    return $_SERVER['REMOTE_ADDR'];
}

// Función para obtener tipo de dispositivo
function getDispositivo() {
    return $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';
}

// Registrar log
function registrarLog($pdo, $id_usuario, $accion, $descripcion) {
    $stmt = $pdo->prepare("INSERT INTO logs (id_usuario, accion, descripcion, ip_origen, dispositivo)
                           VALUES (:id_usuario, :accion, :descripcion, :ip_origen, :dispositivo)");
    $stmt->execute([
        ':id_usuario' => $id_usuario,
        ':accion'     => $accion,
        ':descripcion'=> $descripcion,
        ':ip_origen'  => getIP(),
        ':dispositivo'=> getDispositivo()
    ]);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Solo se piden los campos de la prueba
    $campos_obligatorios = ["id_prueba", "nombre", "precio"];

    foreach ($campos_obligatorios as $campo) {
        if (empty($_POST[$campo])) {
            $_SESSION['error'] = "El campo $campo es obligatorio.";
            registrarLog($pdo, $_SESSION['id_usuario'] ?? null, 'Error Actualizando Prueba', "Faltó el campo: $campo");
            header("Location: ../administrador/pruebas_hosptalarias.php");
            exit;
        }
    }

    // Campos enviados
    $id_prueba  = intval($_POST['id_prueba']);
    $nombre     = trim($_POST['nombre']);
    $precio     = floatval($_POST['precio']);

    // Usuario logueado
    $id_usuario = $_SESSION['id_usuario'] ?? null;

    try {
        // Actualizar prueba
        $sql = "UPDATE pruebas_medicas 
                SET nombre = :nombre,
                    precio = :precio,
                    id_usuario = :id_usuario
                WHERE id_prueba = :id_prueba";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nombre'     => $nombre,
            ':precio'     => $precio,
            ':id_usuario' => $id_usuario,
            ':id_prueba'  => $id_prueba
        ]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['success'] = "Prueba actualizada correctamente.";
            registrarLog($pdo, $id_usuario, 'Actualización Prueba',
                "Prueba ID $id_prueba actualizada: $nombre (precio $precio)");
        } else {
            $_SESSION['info'] = "No se realizaron cambios en la prueba.";
        }

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al actualizar la prueba: " . $e->getMessage();
        registrarLog($pdo, $id_usuario, 'Error Actualizando Prueba', $e->getMessage());
    }

    header("Location: ../administrador/pruebas_hosptalarias.php");
    exit;

} else {
    $_SESSION['error'] = "Método no permitido.";
    header("Location: ../administrador/pruebas_hosptalarias.php");
    exit;
}
?>
