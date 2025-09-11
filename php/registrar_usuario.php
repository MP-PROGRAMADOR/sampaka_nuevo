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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_personal = $_POST['id_personal'] ?? null;
    $username    = trim($_POST['username'] ?? '');
    $password    = trim($_POST['password'] ?? '');
    $rol         = $_POST['rol'] ?? '';
    $estado      = $_POST['estado'] ?? 'Activo';

    // Validaciones iniciales
    if (!$id_personal || !$username || !$password || !$rol) {
        $_SESSION['error'] = "Todos los campos son obligatorios.";
        registrarLog($pdo, $_SESSION['id_usuario'] ?? null, 'Error Registro Usuario', "Faltan campos obligatorios para personal ID $id_personal");
        header("Location: ../administrador/usuarios.php");
        exit;
    }

    try {
        // 1️⃣ Verificar si el personal ya tiene un usuario registrado
        $stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE id_personal = :id_personal");
        $stmt->execute([':id_personal' => $id_personal]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['error'] = "Este personal ya tiene un usuario asignado.";
            registrarLog($pdo, $_SESSION['id_usuario'] ?? null, 'Error Registro Usuario', "Intento de registrar usuario para personal ID $id_personal que ya tiene usuario");
            header("Location: ../administrador/usuarios.php");
            exit;
        }

        // 2️⃣ Verificar si el username ya existe en la tabla
        $stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE username = :username");
        $stmt->execute([':username' => $username]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['error'] = "El nombre de usuario ya está en uso.";
            registrarLog($pdo, $_SESSION['id_usuario'] ?? null, 'Error Registro Usuario', "Intento de registrar usuario con username duplicado: $username");
            header("Location: ../administrador/usuarios.php");
            exit;
        }

        // 3️⃣ Encriptar contraseña
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        // 4️⃣ Insertar usuario
        $stmt = $pdo->prepare("
            INSERT INTO usuarios (id_personal, username, password, rol, estado)
            VALUES (:id_personal, :username, :password, :rol, :estado)
        ");

        $stmt->execute([
            ':id_personal' => $id_personal,
            ':username'    => $username,
            ':password'    => $passwordHash,
            ':rol'         => $rol,
            ':estado'      => $estado
        ]);

        $_SESSION['success'] = "Usuario registrado correctamente.";
        registrarLog($pdo, $_SESSION['id_usuario'] ?? null, 'Registro Usuario', "Usuario $username registrado para personal ID $id_personal");

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al registrar usuario: " . $e->getMessage();
        registrarLog($pdo, $_SESSION['id_usuario'] ?? null, 'Error Registro Usuario', $e->getMessage());
    }

    header("Location: ../administrador/usuarios.php");
    exit;
}
?>
