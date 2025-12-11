<?php

session_start();
// Asegúrate de que esta ruta es correcta para tu entorno.
require_once "../config/conexion.php"; 

// --- Funciones de utilidad (se mantienen igual, son correctas) ---

function getIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return $_SERVER['HTTP_X_FORWARDED_FOR'];
    return $_SERVER['REMOTE_ADDR'];
}

function getDispositivo() {
    // Uso correcto del operador de fusión de null (??)
    return $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido'; 
}


function registrarLog($pdo, $id_usuario, $accion, $descripcion) {
    $log_id_usuario = $id_usuario ?? null;
    $stmt = $pdo->prepare("INSERT INTO logs (id_usuario, accion, descripcion, ip_origen, dispositivo)
                             VALUES (:id_usuario, :accion, :descripcion, :ip_origen, :dispositivo)");
    try {
        $stmt->execute([
            ':id_usuario' => $log_id_usuario,
            ':accion'     => $accion,
            ':descripcion'=> $descripcion,
            ':ip_origen'  => getIP(),
            ':dispositivo'=> getDispositivo()
        ]);
    } catch (PDOException $e) {
        // En caso de fallo de log, solo registrar internamente.
        error_log("Error al registrar log: " . $e->getMessage()); 
    }
}

// LÓGICA DE ELIMINACIÓN

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    
    $id_paciente = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    
    if ($id_paciente === false || $id_paciente <= 0) {
        $_SESSION['error'] = "ID de paciente inválido para la eliminación.";
        registrarLog($pdo, $_SESSION['id_usuario'] ?? null, 'Error Eliminación Paciente', "ID inválido: " . ($_GET['id'] ?? 'N/A'));
        header("Location: ../administrador/pacientes.php");
        exit;
    }

    try {
        // 1. Obtener nombre del paciente antes de intentar eliminar (para el log y mensajes)
        $paciente_nombre = '';
        $stmt_info = $pdo->prepare("SELECT nombre, apellido FROM pacientes WHERE id_paciente = :id");
        $stmt_info->execute([':id' => $id_paciente]);
        $info = $stmt_info->fetch(PDO::FETCH_ASSOC);

        if ($info) {
            $paciente_nombre = $info['nombre'] . ' ' . $info['apellido'];
        } else {
            // El paciente no existe. Redirigir con un mensaje informativo.
            $_SESSION['info'] = "El paciente con ID $id_paciente no existe o ya fue eliminado.";
            header("Location: ../administrador/pacientes.php");
            exit;
        }

        // 2. Definir tablas relacionadas (con 'id_paciente' como FK)
        $tablas_relacionadas = [
            'consultas',
            'hospitalizaciones',
            'analiticas',
            'vacunaciones',
            'defunciones'
        ];
        
        $tiene_dependencias = false;
        $tabla_dependiente = '';
        
        foreach ($tablas_relacionadas as $tabla) {
            // Se asume que los nombres de tabla son seguros (están hardcodeados en una lista blanca).
            $stmt = $pdo->prepare("SELECT 1 FROM $tabla WHERE id_paciente = :id LIMIT 1");
            $stmt->execute([':id' => $id_paciente]);
            
            if ($stmt->fetchColumn()) {
                $tiene_dependencias = true;
                $tabla_dependiente = $tabla; 
                break;
            }
        }

        if ($tiene_dependencias) {
            
            $_SESSION['error'] = "No se puede eliminar al paciente $paciente_nombre (ID: $id_paciente). Tiene registros asociados en la tabla " . ucfirst($tabla_dependiente) . "**, lo que afectaría la integridad del historial clínico.";
            registrarLog($pdo, $_SESSION['id_usuario'] ?? null, 'Eliminación Fallida Paciente', "Intento de eliminar paciente ID $id_paciente ($paciente_nombre) fallido por dependencia en la tabla: $tabla_dependiente");
            
        } else {
            // 3. Si no hay dependencias, proceder con la eliminación transaccional
            
            $pdo->beginTransaction();

            $stmt_del = $pdo->prepare("DELETE FROM pacientes WHERE id_paciente = :id");
            $stmt_del->execute([':id' => $id_paciente]);

            if ($stmt_del->rowCount() > 0) {
                $pdo->commit();
                $_SESSION['success'] = "El paciente $paciente_nombre (ID: $id_paciente) ha sido eliminado correctamente.";
                registrarLog($pdo, $_SESSION['id_usuario'] ?? null, 'Eliminación Exitosa Paciente', "Paciente ID $id_paciente ($paciente_nombre) eliminado.");
            } else {
                $pdo->rollBack();
                $_SESSION['info'] = "No se encontró el paciente con ID $id_paciente para eliminar.";
            }
        }
        
    } catch (PDOException $e) {
        // Asegurar que la transacción se revierta si falla cualquier operación dentro del try.
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        // Mensaje más genérico al usuario final, con registro detallado en el log.
        $_SESSION['error'] = "Error grave en la base de datos al intentar eliminar. Por favor, contacte a soporte.";
        registrarLog($pdo, $_SESSION['id_usuario'] ?? null, 'Error Fatal Eliminación', "Error de PDO: " . $e->getMessage());
    }

} else {
    $_SESSION['error'] = "Acceso no permitido o ID de paciente no proporcionado.";
    registrarLog($pdo, $_SESSION['id_usuario'] ?? null, 'Error Eliminación Paciente', "Acceso no permitido o falta de ID.");
}

// Redirigir de vuelta a la página principal de pacientes
header("Location: ../administrador/pacientes.php");
exit;
?>