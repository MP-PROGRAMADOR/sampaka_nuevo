<?php
// Iniciar la sesión para manejar mensajes de estado y el ID del usuario
session_start();
require_once "../config/conexion.php"; // Asegúrate de que esta ruta es correcta


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

/**
 * Registra una acción en la tabla logs.
 * @param PDO $pdo Conexión a la base de datos.
 * @param int|null $id_usuario ID del usuario que realiza la acción.
 * @param string $accion Nombre de la acción (e.g., 'Actualización Paciente').
 * @param string $descripcion Descripción detallada de la acción.
 */
function registrarLog($pdo, $id_usuario, $accion, $descripcion) {
    // Usar el ID de usuario de la sesión si está disponible, si no, null
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
        // En un entorno de producción, solo registrar esto en un log interno, no mostrar al usuario
        error_log("Error al registrar log: " . $e->getMessage());
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Campos obligatorios según el formulario de edición que enviaste en pacientes.php
    $campos_obligatorios = ["id_paciente", "nombre", "apellido", "sexo", "nacionalidad"];

    foreach ($campos_obligatorios as $campo) {
        if (empty($_POST[$campo])) {
            $_SESSION['error'] = "El campo **" . ucfirst($campo) . "** es obligatorio.";
            registrarLog($pdo, $_SESSION['id_usuario'] ?? null, 'Error Actualización Paciente', "Faltó el campo: $campo");
            header("Location: ../administrador/pacientes.php");
            exit;
        }
    }

    // Limpiar y validar entradas
    $id_paciente    = filter_var($_POST['id_paciente'], FILTER_VALIDATE_INT);
    $nombre         = trim($_POST['nombre']);
    $apellido       = trim($_POST['apellido']);
    $sexo           = trim($_POST['sexo']);
    $nacionalidad   = trim($_POST['nacionalidad']);

    // Campos opcionales (asegúrate de que existan en el formulario)
    $telefono       = trim($_POST['telefono']) ?? null;
    $ocupacion      = trim($_POST['ocupacion']) ?? null;
    // Otros campos de registro que no están en el formulario de edición pero sí en la DB:
    // correo, direccion, fecha_nacimiento, id_usuario (de registro)


    // Validar el ID del paciente
    if ($id_paciente === false) {
        $_SESSION['error'] = "ID de paciente inválido.";
        registrarLog($pdo, $_SESSION['id_usuario'] ?? null, 'Error Actualización Paciente', "ID de paciente inválido: " . $_POST['id_paciente']);
        header("Location: ../administrador/pacientes.php");
        exit;
    }

    try {
        // Construir la consulta de actualización
        $sql = "UPDATE pacientes 
                SET nombre = :nombre,
                    apellido = :apellido,
                    sexo = :sexo,
                    nacionalidad = :nacionalidad,
                    telefono = :telefono,
                    ocupacion = :ocupacion
                WHERE id_paciente = :id_paciente";

        $stmt = $pdo->prepare($sql);
        
        // Ejecutar la consulta
        $stmt->execute([
            ':nombre' => $nombre,
            ':apellido' => $apellido,
            ':sexo' => $sexo,
            ':nacionalidad' => $nacionalidad,
            ':telefono' => $telefono,
            ':ocupacion' => $ocupacion,
            ':id_paciente' => $id_paciente
        ]);

        // Verificar si se realizaron cambios
        if ($stmt->rowCount() > 0) {
            $_SESSION['success'] = "Datos del paciente **$nombre $apellido** actualizados correctamente.";
            registrarLog($pdo, $_SESSION['id_usuario'] ?? null, 'Actualización Paciente', "Se actualizaron los datos del paciente ID $id_paciente: $nombre $apellido");
        } else {
            // Esto sucede si los datos enviados son idénticos a los que ya están en la DB
            $_SESSION['info'] = "No se realizaron cambios en los datos del paciente **$nombre $apellido**.";
            registrarLog($pdo, $_SESSION['id_usuario'] ?? null, 'Actualización Paciente', "Intento de actualización sin cambios para paciente ID $id_paciente: $nombre $apellido");
        }

    } catch (PDOException $e) {
        // Manejo de error de la base de datos
        $log_error_msg = "Error al actualizar paciente ID $id_paciente: " . $e->getMessage();
        $_SESSION['error'] = "Error al actualizar el paciente. Por favor, revise los datos e inténtelo de nuevo. Detalles: " . $e->getMessage();
        registrarLog($pdo, $_SESSION['id_usuario'] ?? null, 'Error Actualización Paciente', $log_error_msg);
    }

    // Redirigir de vuelta a la página de pacientes
    header("Location: ../administrador/pacientes.php");
    exit;

} else {
    // Si se accede directamente al archivo sin POST
    $_SESSION['error'] = "Método de acceso no permitido.";
    registrarLog($pdo, $_SESSION['id_usuario'] ?? null, 'Error Actualización Paciente', "Acceso directo/método GET no permitido.");
    header("Location: ../administrador/pacientes.php");
    exit;
}
?>