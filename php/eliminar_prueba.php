<?php
session_start();
require_once "../config/conexion.php";

// Funciones para IP y dispositivo
function getIP() {
    return $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';
}

function getDispositivo() {
    return $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';
}

// Función para registrar logs
function registrarLog($pdo, $id_usuario, $accion, $descripcion) {
    $stmt = $pdo->prepare("INSERT INTO logs (id_usuario, accion, descripcion, ip_origen, dispositivo)
                           VALUES (:id_usuario, :accion, :descripcion, :ip, :dispositivo)");
    $stmt->execute([
        ':id_usuario' => $id_usuario,
        ':accion' => $accion,
        ':descripcion' => $descripcion,
        ':ip' => getIP(),
        ':dispositivo' => getDispositivo()
    ]);
}

// Revisar método POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (empty($_POST["id_prueba"])) {
        $_SESSION['error'] = "El ID de la prueba es obligatorio.";
        header("Location: ../administrador/pruebas_hosptalarias.php");
        exit;
    }

    $id_prueba = intval($_POST["id_prueba"]);
    $id_usuario = $_SESSION["id_usuario"] ?? null;

    try {
        // Obtener nombre de la prueba para log
        $stmt = $pdo->prepare("SELECT nombre FROM pruebas_medicas WHERE id_prueba = :id");
        $stmt->execute([':id' => $id_prueba]);
        $prueba = $stmt->fetch(PDO::FETCH_ASSOC);
        $nombrePrueba = $prueba['nombre'] ?? '';

        // Eliminar la prueba
        $stmt = $pdo->prepare("DELETE FROM pruebas_medicas WHERE id_prueba = :id_prueba");
        $stmt->execute([':id_prueba' => $id_prueba]);

        registrarLog($pdo, $id_usuario, "ELIMINAR_PRUEBA", "Eliminó la prueba: {$nombrePrueba} (ID {$id_prueba})");

        $_SESSION['success'] = "✅ La prueba '{$nombrePrueba}' fue eliminada correctamente.";
        header("Location: ../administrador/pruebas_hosptalarias.php");
        exit;

    } catch (PDOException $e) {
        $_SESSION['error'] = "❌ Error al eliminar la prueba: " . $e->getMessage();
        registrarLog($pdo, $id_usuario, "ERROR_ELIMINAR_PRUEBA", $e->getMessage());
        header("Location: ../administrador/pruebas_hosptalarias.php");
        exit;
    }

} else {
    $_SESSION['error'] = "Método no permitido.";
    header("Location: ../administrador/pruebas_hosptalarias.php");
    exit;
}
