<?php
session_start();
require_once "../config/conexion.php";

// Función para generar código único de paciente
function generarCodigo($nombre, $apellido) {
    $inicialNombre = strtoupper(substr($nombre, 0, 1));
    $inicialApellido = strtoupper(substr($apellido, 0, 1));
    $anio = date("y");
    $random = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 4);

    return $inicialNombre . $inicialApellido . $anio . $random;
}

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
        $apellido = trim($_POST['apellido'] ?? '');
        $sexo = trim($_POST['sexo'] ?? '');
        $fecha_nacimiento = trim($_POST['fecha_nacimiento'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $nacionalidad = trim($_POST['nacionalidad'] ?? '');
        $ocupacion = trim($_POST['ocupacion'] ?? '');
        $id_usuario = $_SESSION['id_usuario'] ?? null;

        // 🔹 Validaciones de campos obligatorios
        if (
            empty($nombre) || empty($apellido) || empty($sexo) || empty($fecha_nacimiento) ||
            empty($correo) || empty($direccion) || empty($telefono) ||
            empty($nacionalidad) || empty($ocupacion)
        ) {
            $_SESSION['error'] = "❌ Todos los campos son obligatorios. Verifique e intente nuevamente.";
            header("Location: ../administrador/pacientes.php");
            exit;
        }

        // Validación extra de email
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "❌ El correo electrónico no es válido.";
            header("Location: ../administrador/pacientes.php");
            exit;
        }

        // Generar código único
        $codigo = generarCodigo($nombre, $apellido);

        // Insertar paciente
        $stmt = $pdo->prepare("INSERT INTO pacientes 
            (codigo, nombre, apellido, sexo, fecha_nacimiento, correo, direccion, telefono, nacionalidad, ocupacion, id_usuario) 
            VALUES (:codigo, :nombre, :apellido, :sexo, :fecha_nacimiento, :correo, :direccion, :telefono, :nacionalidad, :ocupacion, :id_usuario)");

        $stmt->execute([
            ':codigo' => $codigo,
            ':nombre' => $nombre,
            ':apellido' => $apellido,
            ':sexo' => $sexo,
            ':fecha_nacimiento' => $fecha_nacimiento,
            ':correo' => $correo,
            ':direccion' => $direccion,
            ':telefono' => $telefono,
            ':nacionalidad' => $nacionalidad,
            ':ocupacion' => $ocupacion,
            ':id_usuario' => $id_usuario
        ]);

        registrar_log($pdo, $id_usuario, "REGISTRO_PACIENTE", "Se registró al paciente {$nombre} {$apellido} con código {$codigo}");

        $_SESSION['success'] = "✅ Paciente registrado correctamente con código: {$codigo}";
        header("Location: ../administrador/pacientes.php");
        exit;

    } catch (PDOException $e) {
        $_SESSION['error'] = "❌ Error al registrar paciente: " . $e->getMessage();
        registrar_log($pdo, $id_usuario ?? null, "ERROR_REGISTRO", $e->getMessage());
        header("Location: ../administrador/pacientes.php");
        exit;
    }
} else {
    header("Location: ../pacientes.php");
    exit;
}
?>
