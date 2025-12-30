<?php
// olvide_password.php
require_once "../config/conexion.php";

$mensaje = "";
$tipo_alerta = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_input = mysqli_real_escape_string($conexion, $_POST['usuario']);

    // Verificar si el usuario existe en la tabla 'usuarios'
    $query = "SELECT u.id_usuario, p.nombre, p.apellido 
              FROM usuarios u 
              JOIN personal p ON u.id_personal = p.id_personal 
              WHERE u.usuario = '$usuario_input'";

    $resultado = mysqli_query($conexion, $query);

    if (mysqli_num_rows($resultado) > 0) {
        $datos = mysqli_fetch_assoc($resultado);
        $mensaje = "Hola " . $datos['nombre'] . ", se ha enviado un enlace de recuperación a tu correo institucional.";
        $tipo_alerta = "success";

        $id_usu = $datos['id_usuario'];
        $ip = $_SERVER['REMOTE_ADDR'];
        $log_query = "INSERT INTO logs (id_usuario, accion, descripcion, ip_origen) 
                      VALUES ($id_usu, 'RECUPERACION_PASS', 'Solicitud de restablecimiento de contraseña', '$ip')";
        mysqli_query($conexion, $log_query);
    } else {
        $mensaje = "El nombre de usuario no coincide con nuestros registros.";
        $tipo_alerta = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Hospital Sampaka</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fc;
            height: 100vh;
            display: flex;
            align-items: center;
        }

        .card-recovery {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .btn-primary {
            background-color: #4e73df;
            border: none;
            padding: 12px;
            border-radius: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="text-center mb-4">
                    <h3 class="fw-bold text-primary"><i class="bi bi-hospital me-2"></i>Hospital Sampaka</h3>
                </div>

                <div class="card card-recovery p-4">
                    <div class="card-body">
                        <h5 class="card-title fw-bold mb-3">¿Olvidaste tu contraseña?</h5>
                        <p class="text-muted small">Ingresa tu nombre de usuario y te ayudaremos a restablecerla.</p>

                        <?php if ($mensaje): ?>
                            <div class="alert alert-<?php echo $tipo_alerta; ?> small" role="alert">
                                <?php echo $mensaje; ?>
                            </div>
                        <?php endif; ?>

                        <form action="" method="POST">
                            <div class="mb-4">
                                <label class="form-label small fw-bold">Nombre de Usuario</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                                    <input type="text" name="usuario" class="form-control bg-light border-start-0" placeholder="Ej: admin" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 fw-bold mb-3">
                                Enviar Solicitud
                            </button>

                            <div class="text-center">
                                <a href="login.php" class="text-decoration-none small text-secondary">
                                    <i class="bi bi-arrow-left me-1"></i> Volver al inicio de sesión
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>




</body>

</html>