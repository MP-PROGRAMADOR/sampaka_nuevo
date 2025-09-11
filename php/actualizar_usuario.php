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
    $id_usuario  = $_POST['id_usuario'] ?? null;
    $id_personal = $_POST['id_personal'] ?? null;
    $username    = trim($_POST['username'] ?? '');
    $password    = trim($_POST['password'] ?? '');
    $rol         = $_POST['rol'] ?? '';
    $estado      = $_POST['estado'] ?? 'Activo';

    // Validaciones básicas
    if (!$id_usuario || !$id_personal || !$username || !$rol || !$estado) {
        $_SESSION['error'] = "Todos los campos obligatorios deben completarse.";
        registrarLog($pdo, $_SESSION['id_usuario'] ?? null, 'Error Edición Usuario', "Faltan campos obligatorios para usuario ID $id_usuario");
        header("Location: ../administrador/usuarios.php");
        exit;
    }

    try {
        // Verificar que el username sea único (excepto el usuario actual)
        $stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE username = :username AND id_usuario != :id_usuario");
        $stmt->execute([
            ':username' => $username,
            ':id_usuario' => $id_usuario
        ]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['error'] = "El nombre de usuario ya está en uso por otro usuario.";
            registrarLog($pdo, $_SESSION['id_usuario'] ?? null, 'Error Edición Usuario', "Intento de username duplicado: $username para usuario ID $id_usuario");
            header("Location: ../administrador/usuarios.php");
            exit;
        }

        // Construir query de actualización
        if (!empty($password)) {
            // Encriptar nueva contraseña
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            $sql = "UPDATE usuarios SET username = :username, password = :password, rol = :rol, estado = :estado WHERE id_usuario = :id_usuario";
            $params = [
                ':username' => $username,
                ':password' => $passwordHash,
                ':rol' => $rol,
                ':estado' => $estado,
                ':id_usuario' => $id_usuario
            ];
        } else {
            // No cambiar contraseña
            $sql = "UPDATE usuarios SET username = :username, rol = :rol, estado = :estado WHERE id_usuario = :id_usuario";
            $params = [
                ':username' => $username,
                ':rol' => $rol,
                ':estado' => $estado,
                ':id_usuario' => $id_usuario
            ];
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $_SESSION['success'] = "Usuario actualizado correctamente.";
        registrarLog($pdo, $_SESSION['id_usuario'] ?? null, 'Edición Usuario', "Usuario ID $id_usuario actualizado. Username: $username, Rol: $rol, Estado: $estado");

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al actualizar usuario: " . $e->getMessage();
        registrarLog($pdo, $_SESSION['id_usuario'] ?? null, 'Error Edición Usuario', $e->getMessage());
    }

    header("Location: ../administrador/usuarios.php");
    exit;
}
?>
