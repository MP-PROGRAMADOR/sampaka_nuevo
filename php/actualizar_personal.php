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

// Función para registrar logs
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
    $campos_obligatorios = ["id_personal", "nombre", "apellido", "especialidad", "cargo", "telefono", "correo", "direccion", "nivel_estudios", "nacionalidad"];

    foreach ($campos_obligatorios as $campo) {
        if (empty($_POST[$campo])) {
            $_SESSION['error'] = "El campo $campo es obligatorio.";
            registrarLog($pdo, $_SESSION['id_usuario'] ?? null, 'Error Actualización Personal', "Faltó el campo: $campo");
            header("Location: ../administrador/personal.php");
            exit;
        }
    }

    // Limpiar entradas
    $id_personal     = intval($_POST['id_personal']);
    $nombre          = trim($_POST['nombre']);
    $apellido        = trim($_POST['apellido']);
    $especialidad    = trim($_POST['especialidad']);
    $cargo           = trim($_POST['cargo']);
    $telefono        = trim($_POST['telefono']);
    $correo          = trim($_POST['correo']);
    $direccion       = trim($_POST['direccion']);
    $nivel_estudios  = trim($_POST['nivel_estudios']);
    $nacionalidad    = trim($_POST['nacionalidad']);

    try {
        // Actualizar datos en la base de datos
        $sql = "UPDATE personal 
                SET nombre = :nombre,
                    apellido = :apellido,
                    especialidad = :especialidad,
                    cargo = :cargo,
                    telefono = :telefono,
                    correo = :correo,
                    direccion = :direccion,
                    nivel_estudios = :nivel_estudios,
                    nacionalidad = :nacionalidad
                WHERE id_personal = :id_personal";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre,
            ':apellido' => $apellido,
            ':especialidad' => $especialidad,
            ':cargo' => $cargo,
            ':telefono' => $telefono,
            ':correo' => $correo,
            ':direccion' => $direccion,
            ':nivel_estudios' => $nivel_estudios,
            ':nacionalidad' => $nacionalidad,
            ':id_personal' => $id_personal
        ]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['success'] = "Datos del personal actualizados correctamente.";
            registrarLog($pdo, $_SESSION['id_usuario'] ?? null, 'Actualización Personal', "Se actualizaron los datos del personal ID $id_personal: $nombre $apellido");
        } else {
            $_SESSION['info'] = "No se realizaron cambios en los datos del personal.";
            registrarLog($pdo, $_SESSION['id_usuario'] ?? null, 'Actualización Personal', "Intento de actualización sin cambios para personal ID $id_personal: $nombre $apellido");
        }

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al actualizar el personal: " . $e->getMessage();
        registrarLog($pdo, $_SESSION['id_usuario'] ?? null, 'Error Actualización Personal', $e->getMessage());
    }

    header("Location: ../administrador/personal.php");
    exit;
} else {
    $_SESSION['error'] = "Método no permitido.";
    registrarLog($pdo, $_SESSION['id_usuario'] ?? null, 'Error Actualización Personal', "Acceso no permitido al archivo editar_personal.php");
    header("Location: ../administrador/personal.php");
    exit;
}
?>
