<?php
session_start();
require_once "../config/conexion.php"; // conexión PDO

// Limpiar POST
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

// Inicializar error
$_SESSION['error'] = '';

// Control de intentos
if (!isset($_SESSION['login_attempts'])) $_SESSION['login_attempts'] = 0;
if (!isset($_SESSION['last_attempt_time'])) $_SESSION['last_attempt_time'] = 0;

// Tiempo de bloqueo (2 minutos)
$lockout_time = 120;

// Función LOG
function registrar_log($pdo, $id_usuario, $accion, $descripcion) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';
    $dispositivo = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';

    $stmt = $pdo->prepare("
        INSERT INTO logs (id_usuario, accion, descripcion, ip_origen, dispositivo) 
        VALUES (:id_usuario, :accion, :descripcion, :ip, :dispositivo)
    ");
    $stmt->execute([
        ':id_usuario' => $id_usuario,
        ':accion' => $accion,
        ':descripcion' => $descripcion,
        ':ip' => $ip,
        ':dispositivo' => $dispositivo
    ]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $current_time = time();

    // Verificar bloqueo
    if ($_SESSION['login_attempts'] >= 4 && ($current_time - $_SESSION['last_attempt_time']) < $lockout_time) {
        $remaining = $lockout_time - ($current_time - $_SESSION['last_attempt_time']);
        $_SESSION['error'] = "Demasiados intentos fallidos. Intenta nuevamente en {$remaining} segundos.";
        registrar_log($pdo, null, "BLOQUEO_TEMPORAL", "Intentos excedidos para '{$username}'");
        header("Location: ../");
        exit;
    } elseif (($current_time - $_SESSION['last_attempt_time']) >= $lockout_time) {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['last_attempt_time'] = 0;
    }

    // Validaciones
    if (empty($username)) {
        $_SESSION['error'] = "El nombre de usuario es obligatorio.";
        registrar_log($pdo, null, "LOGIN_FALLIDO", "Username vacío");
        header("Location: ../");
        exit;
    }

    if (empty($password)) {
        $_SESSION['error'] = "La contraseña es obligatoria.";
        registrar_log($pdo, null, "LOGIN_FALLIDO", "Password vacío para '{$username}'");
        header("Location: ../");
        exit;
    }

    try {
        // Buscar usuario
        $stmt = $pdo->prepare("
            SELECT u.id_usuario, u.username, u.password, u.rol, u.estado,
                   p.nombre, p.apellido
            FROM usuarios u
            LEFT JOIN personal p ON u.id_personal = p.id_personal
            WHERE u.username = :username
            LIMIT 1
        ");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        if (!$user) {
            $_SESSION['login_attempts']++;
            $_SESSION['last_attempt_time'] = time();
            $_SESSION['error'] = "Usuario o contraseña incorrectos.";
            registrar_log($pdo, null, "LOGIN_FALLIDO", "Usuario '{$username}' no existe");
            header("Location: ../");
            exit;
        }

        if ($user['estado'] !== 'Activo') {
            $_SESSION['error'] = "El usuario está inactivo. Contacte al administrador.";
            registrar_log($pdo, $user['id_usuario'], "USUARIO_INACTIVO", "Intento de login");
            header("Location: ../");
            exit;
        }

        if (!password_verify($password, $user['password'])) {
            $_SESSION['login_attempts']++;
            $_SESSION['last_attempt_time'] = time();
            $_SESSION['error'] = "Usuario o contraseña incorrectos.";
            registrar_log($pdo, $user['id_usuario'], "LOGIN_FALLIDO", "Contraseña incorrecta");
            header("Location: ../");
            exit;
        }

        // Login correcto
        $_SESSION['login_attempts'] = 0;
        $_SESSION['last_attempt_time'] = 0;

        $_SESSION['id_usuario'] = $user['id_usuario'];
        $_SESSION['username']   = $user['username'];
        $_SESSION['rol']        = $user['rol'];
        $_SESSION['nombre']     = $user['nombre'] ?? '';
        $_SESSION['apellido']   = $user['apellido'] ?? '';
        $_SESSION['logged_in']  = true;

        registrar_log($pdo, $user['id_usuario'], "LOGIN_EXITO", "Inicio de sesión exitoso");

        // 🔥 REDIRECCIÓN SEGÚN ROL
        if (in_array($user['rol'], ['Administrador', 'Laboratorio'])) {
            header("Location: ../administrador/");
        } else {
            header("Location: ../usuario/");
        }
        exit;

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error de conexión.";
        registrar_log($pdo, null, "ERROR_CONEXION", $e->getMessage());
        header("Location: ../");
        exit;
    }

} else {
    header("Location: ../");
    exit;
}
