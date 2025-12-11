<?php
session_start();
require_once "../config/conexion.php";

// Función para guardar logs
function registrar_log($pdo, $id_usuario, $accion, $descripcion) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';
    $dispositivo = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';

    $stmt = $pdo->prepare("INSERT INTO logs (id_usuario, accion, descripcion, ip_origen, dispositivo) 
                           VALUES (:id_usuario, :accion, :descripcion, :ip, :dispositivo)");
    $stmt->execute([
        ':id_usuario' => $id_usuario,
        ':accion' => $accion,
        ':descripcion' => $descripcion,
        ':ip' => $ip,
        ':dispositivo' => $dispositivo
    ]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $nombre = trim($_POST['nombre'] ?? '');
        $precio = trim($_POST['precio'] ?? '');
        $id_usuario = $_SESSION['id_usuario'] ?? null;

        // 🔹 Validación
        if (empty($nombre) || empty($precio)) {
            $_SESSION['error'] = "❌ Todos los campos son obligatorios.";
            header("Location: ../administrador/pruebas_hosptalarias.php");
            exit;
        }

        if (!is_numeric($precio) || $precio <= 0) {
            $_SESSION['error'] = "❌ El precio debe ser un número válido mayor que 0.";
            header("Location: ../administrador/pruebas_hosptalarias.php");
            exit;
        }

        // 🔹 Insertar prueba médica
        $stmt = $pdo->prepare("INSERT INTO pruebas_medicas (nombre, precio, id_usuario)
                               VALUES (:nombre, :precio, :id_usuario)");

        $stmt->execute([
            ':nombre' => $nombre,
            ':precio' => $precio,
            ':id_usuario' => $id_usuario
        ]);

        // 🔹 Registrar log
        registrar_log(
            $pdo,
            $id_usuario,
            "REGISTRO_PRUEBA_MEDICA",
            "Se registró la prueba médica: {$nombre}, precio {$precio} FCFA"
        );

        $_SESSION['success'] = "✅ Prueba médica registrada correctamente.";
        header("Location: ../administrador/pruebas_hosptalarias.php");
        exit;

    } catch (PDOException $e) {
        $_SESSION['error'] = "❌ Error al registrar prueba médica: " . $e->getMessage();
        registrar_log($pdo, $id_usuario ?? null, "ERROR_REGISTRO_PRUEBA", $e->getMessage());
        header("Location: ../administrador/pruebas_hosptalarias.php");
        exit;
    }
} else {
    header("Location: ../administrador/pruebas_hosptalarias.php");
    exit;
}
?>
