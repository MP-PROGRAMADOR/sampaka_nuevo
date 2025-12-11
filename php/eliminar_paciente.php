<?php

session_start();
require_once "../config/conexion.php"; // Asegúrate de que esta ruta es correcta

// ... Funciones getIP, getDispositivo, registrarLog (sin cambios, son correctas) ...

function getIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return $_SERVER['HTTP_X_FORWARDED_FOR'];
    return $_SERVER['REMOTE_ADDR'];
}

function getDispositivo() {
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
        error_log("Error al registrar log: " . $e->getMessage());
    }
}

// LÓGICA DE ELIMINACIÓN

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    
    $id_paciente = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    
    if ($id_paciente === false) {
        $_SESSION['error'] = "ID de paciente inválido para la eliminación.";
        registrarLog($pdo, $_SESSION['id_usuario'] ?? null, 'Error Eliminación Paciente', "ID inválido: " . $_GET['id']);
        header("Location: ../administrador/pacientes.php");
        exit;
    }

    try {
        // 1. Verificar si el paciente tiene registros relacionados
        $tablas_relacionadas = [
            'consultas'         => 'id_consulta',
            'hospitalizaciones' => 'id_hospitalizacion',
            'analiticas'        => 'id_analitica',
            'vacunaciones'      => 'id_vacunacion',
            'defunciones'       => 'id_defuncion'
        ];
        
        $paciente_nombre = '';
        
        // Obtener nombre y apellido del paciente antes de intentar eliminar
        $stmt_info = $pdo->prepare("SELECT nombre, apellido FROM pacientes WHERE id_paciente = :id");
        $stmt_info->execute([':id' => $id_paciente]);
        $info = $stmt_info->fetch(PDO::FETCH_ASSOC);

        if ($info) {
            $paciente_nombre = $info['nombre'] . ' ' . $info['apellido'];
        } else {
            $_SESSION['error'] = "El paciente con ID $id_paciente no existe.";
            header("Location: ../administrador/pacientes.php");
            exit;
        }


        $tiene_dependencias = false;
        foreach ($tablas_relacionadas as $tabla => $columna_id) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM $tabla WHERE id_paciente = :id");
            $stmt->execute([':id' => $id_paciente]);
            
            if ($stmt->fetchColumn() > 0) {
                $tiene_dependencias = true;
                $tabla_dependiente = $tabla; 
                break;
            }
        }

        if ($tiene_dependencias) {
            // CORRECCIÓN: Se agrega negrita y se corrige la cadena para mejor formato.
            $_SESSION['error'] = "No se puede eliminar al paciente $paciente_nombre (ID: $id_paciente). Tiene registros asociados en la tabla **" . ucfirst($tabla_dependiente) . "**, lo que afectaría la integridad del historial clínico.";
            registrarLog($pdo, $_SESSION['id_usuario'] ?? null, 'Eliminación Fallida Paciente', "Intento de eliminar paciente ID $id_paciente ($paciente_nombre) fallido por dependencia en la tabla: $tabla_dependiente");
            
        } else {
            // 2. Si no hay dependencias, proceder con la eliminación
            
            // Iniciar transacción para asegurar la operación
            $pdo->beginTransaction();

            // Eliminar al paciente
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
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $_SESSION['error'] = "Error grave en la base de datos al intentar eliminar: " . $e->getMessage();
        registrarLog($pdo, $_SESSION['id_usuario'] ?? null, 'Error Fatal Eliminación', $e->getMessage());
    }

} else {
    $_SESSION['error'] = "Acceso no permitido o ID de paciente no proporcionado.";
    registrarLog($pdo, $_SESSION['id_usuario'] ?? null, 'Error Eliminación Paciente', "Acceso no permitido o falta de ID.");
}

// Redirigir de vuelta a la página principal de pacientes
header("Location: ../administrador/pacientes.php");
exit;
?>