<?php
session_start();

require_once "../config/conexion.php";

// 3. Verificar que la petición sea POST y que el usuario esté logueado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['id_usuario'])) {
    
    $id_usuario = $_SESSION['id_usuario'];
    
    // 4. Sanitizar y recibir los datos del formulario
    $nombre   = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $apellido = isset($_POST['apellido']) ? trim($_POST['apellido']) : '';
    $correo   = isset($_POST['correo']) ? trim($_POST['correo']) : '';
    $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';

    // 5. Validaciones básicas
    if (empty($nombre) || empty($apellido) || empty($correo)) {
        $_SESSION['error'] = "El nombre, apellido y correo son campos obligatorios.";
        header("Location: ../administrador/perfil.php");
        exit();
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "El formato del correo electrónico no es válido.";
        header("Location: ../administrador/perfil.php");
        exit();
    }

    try {
        // 6. Iniciar una transacción (Opcional pero recomendado)
        $pdo->beginTransaction();

      
        $stmtId = $pdo->prepare("SELECT id_personal FROM usuarios WHERE id_usuario = ?");
        $stmtId->execute([$id_usuario]);
        $userRow = $stmtId->fetch(PDO::FETCH_ASSOC);

        if ($userRow) {
            $id_personal = $userRow['id_personal'];

          
            $sql = "UPDATE personal SET 
                    nombre = :nombre, 
                    apellido = :apellido, 
                    correo = :correo, 
                    telefono = :telefono 
                    WHERE id_personal = :id_personal";
            
            $stmtUpdate = $pdo->prepare($sql);
            $resultado = $stmtUpdate->execute([
                ':nombre'      => $nombre,
                ':apellido'    => $apellido,
                ':correo'      => $correo,
                ':telefono'    => $telefono,
                ':id_personal' => $id_personal
            ]);

            if ($resultado) {
                $pdo->commit();
                $_SESSION['success'] = "¡Información actualizada correctamente!";
            } else {
                $pdo->rollBack();
                $_SESSION['error'] = "No se realizaron cambios en el perfil.";
            }
        } else {
            $_SESSION['error'] = "Usuario no encontrado.";
        }

    } catch (PDOException $e) {
        // Si algo falla, revertimos la transacción
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $_SESSION['error'] = "Error en la base de datos: " . $e->getMessage();
    }

    // 9. Redirigir de vuelta a la página de perfil
    header("Location: ../administrador/perfil.php");
    exit();

} else {
    // Si intentan entrar al archivo por URL sin enviar el formulario
    header("Location: ../administrador/perfil.php");
    exit();
}