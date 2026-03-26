<?php
session_start();
require_once "../config/conexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['id_usuario'])) {
    
    $id_usuario = $_SESSION['id_usuario'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // 1. Validar que no haya campos vacíos
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $_SESSION['error'] = "Todos los campos son obligatorios.";
        header("Location: ../vistas/perfil.php");
        exit();
    }

    try {
        // 2. Obtener la contraseña actual de la base de datos
        $stmt = $pdo->prepare("SELECT password FROM usuarios WHERE id_usuario = ?");
        $stmt->execute([$id_usuario]);
        $user = $stmt->fetch();

        if ($user) {
            // 3. VERIFICAR CONTRASEÑA ACTUAL
            if (!password_verify($current_password, $user['password'])) {
                $_SESSION['error'] = "La contraseña actual es incorrecta.";
                header("Location: ../administrador/perfil.php");
                exit();
            }

            // 4. Validar que las nuevas coincidan
            if ($new_password !== $confirm_password) {
                $_SESSION['error'] = "La nueva contraseña y su confirmación no coinciden.";
                header("Location: ../administrador/perfil.php");
                exit();
            }

            // 5. Validar longitud mínima
            if (strlen($new_password) < 8) {
                $_SESSION['error'] = "La nueva contraseña debe tener al menos 8 caracteres.";
                header("Location: ../administrador/perfil.php");
                exit();
            }

            // 6. Encriptar y Actualizar
            $nueva_hash = password_hash($new_password, PASSWORD_BCRYPT);
            $update = $pdo->prepare("UPDATE usuarios SET password = ? WHERE id_usuario = ?");
            
            if ($update->execute([$nueva_hash, $id_usuario])) {
                $_SESSION['success'] = "¡Contraseña actualizada con éxito!";
            } else {
                $_SESSION['error'] = "Error al intentar actualizar.";
            }
        }

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }

    header("Location: ../administrador/perfil.php");
    exit();
}