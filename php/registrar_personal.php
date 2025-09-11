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
    try {
        // Validación de campos obligatorios
        $campos = ['nombre', 'apellido', 'especialidad', 'cargo', 'telefono', 'correo', 'direccion', 'nivel_estudios', 'nacionalidad'];
        foreach ($campos as $campo) {
            if (empty($_POST[$campo])) {
                $_SESSION['error'] = "Todos los campos son obligatorios.";
                registrarLog($pdo, $_SESSION['id_usuario'] ?? null, 'Error Registro Personal', "Faltó el campo: $campo");
                header("Location: ../administrador/personal.php");
                exit();
            }
        }

        // Obtener hospital por defecto
        $stmt = $pdo->query("SELECT id_hospital FROM hospitales ORDER BY id_hospital ASC LIMIT 1");
        $hospital = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$hospital) {
            $_SESSION['error'] = "No existe hospital registrado.";
            registrarLog($pdo, $_SESSION['id_usuario'] ?? null, 'Error Registro Personal', "No hay hospital registrado.");
            header("Location: ../administrador/personal.php");
            exit();
        }
        $id_hospital = $hospital['id_hospital'];

        // Variables
        $nombre        = trim($_POST['nombre']);
        $apellido      = trim($_POST['apellido']);
        $especialidad  = trim($_POST['especialidad']);
        $cargo         = trim($_POST['cargo']);
        $telefono      = trim($_POST['telefono']);
        $correo        = trim($_POST['correo']);
        $direccion     = trim($_POST['direccion']);
        $nivel_estudios= trim($_POST['nivel_estudios']);
        $nacionalidad  = trim($_POST['nacionalidad']);

        // Función para generar código único
        function generarCodigoUnico($nombre, $apellido, $pdo) {
            $iniciales = strtoupper(substr($nombre,0,1) . substr($apellido,0,1));
            do {
                $aleatorio = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
                $codigo = $iniciales . $aleatorio;
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM personal WHERE codigo = :codigo");
                $stmt->execute([':codigo' => $codigo]);
                $existe = $stmt->fetchColumn();
            } while($existe > 0);
            return $codigo;
        }

        $codigo = generarCodigoUnico($nombre, $apellido, $pdo);

        // Insertar en la tabla personal
        $sql = "INSERT INTO personal 
                (id_hospital, nombre, apellido, especialidad, cargo, telefono, correo, direccion, nivel_estudios, nacionalidad, codigo)
                VALUES 
                (:id_hospital, :nombre, :apellido, :especialidad, :cargo, :telefono, :correo, :direccion, :nivel_estudios, :nacionalidad, :codigo)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id_hospital'    => $id_hospital,
            ':nombre'         => $nombre,
            ':apellido'       => $apellido,
            ':especialidad'   => $especialidad,
            ':cargo'          => $cargo,
            ':telefono'       => $telefono,
            ':correo'         => $correo,
            ':direccion'      => $direccion,
            ':nivel_estudios' => $nivel_estudios,
            ':nacionalidad'   => $nacionalidad,
            ':codigo'         => $codigo
        ]);

        $_SESSION['success'] = "Personal <strong>$nombre $apellido</strong> registrado correctamente con código <strong>$codigo</strong>.";

        // Log de registro exitoso
        registrarLog($pdo, $_SESSION['id_usuario'] ?? null, 'Registro Personal', "Se registró a $nombre $apellido con código $codigo");

        header("Location: ../administrador/personal.php");
        exit();

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al registrar: " . $e->getMessage();
        registrarLog($pdo, $_SESSION['id_usuario'] ?? null, 'Error Registro Personal', $e->getMessage());
        header("Location: ../administrador/personal.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Acceso no válido.";
    registrarLog($pdo, $_SESSION['id_usuario'] ?? null, 'Error Registro Personal', "Acceso no válido al archivo registrar_personal.php");
    header("Location: ../administrador/personal.php");
    exit();
}
?>
