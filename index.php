<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso - Sistema Hospitalario</title>
    <!-- Incluye Bootstrap 5 CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!-- Incluye Bootstrap Icons para un toque profesional -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        /* Estilos personalizados para el login */
        body {
            /* Imagen de fondo del body */
            background-image: url('img/login5.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: 'Inter', sans-serif;
            position: relative;
        }

        /* Capa oscura semitransparente para mejorar la legibilidad del texto */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            /* Color oscuro con 50% de opacidad */
            backdrop-filter: blur(5px);
            /* Efecto de desenfoque en la imagen de fondo */
            z-index: -1;
        }

        .login-card {
            border: none;
            border-radius: 1rem;
            animation: fadeIn 1s ease-in-out;
            max-width: 450px;
            width: 100%;
            background-color: rgba(255, 255, 255, 0.9);
            /* Fondo semitransparente para el card */
        }

        .form-floating label {
            color: #6c757d;
        }

        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
            transform: translateY(-2px);
        }

        .fade-msg {
            opacity: 1;
            transition: opacity 1s ease-in-out;
        }

        .fade-msg.hide {
            opacity: 0;
        }


        /* Animación para que el formulario aparezca suavemente */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center min-vh-100">

    <div class="card login-card p-4 shadow-lg">
        <div class="card-body">
            <!-- Título y logo de la aplicación -->
            <div class="text-center mb-4">
                <i class="bi bi-hospital-fill text-primary" style="font-size: 3.5rem;"></i>
                <h3 class="mt-3 fw-bold">Sistema Hospitalario</h3>
                <p class="text-muted">Acceso seguro para el personal</p>
            </div>



            <?php session_start(); ?>
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success fade-msg"><?= $_SESSION['success']; ?></div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger fade-msg"><?= $_SESSION['error']; ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>




            <!-- Formulario de inicio de sesión -->
          <form autocomplete="off" method="POST" action="php/login.php">
                <!-- Campo de nombre de usuario -->
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="username" name="username" placeholder="Nombre de usuario" autocomplete="off" required>
                    <label for="username"><i class="bi bi-person-circle me-2"></i>Nombre de Usuario</label>
                </div>

                <!-- Campo de contraseña -->
                <div class="form-floating mb-4">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" autocomplete="off" required>
                    <label for="password"><i class="bi bi-lock-fill me-2"></i>Contraseña</label>
                </div>

                <!-- Botón de Iniciar Sesión -->
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill">Iniciar Sesión</button>
                </div>
            </form>

            <!-- Enlace para recuperar contraseña -->
            <div class="text-center mt-4">
                <a href="./php/olvide_password.php" class="text-decoration-none text-muted">¿Olvidaste tu contraseña?</a>
            </div>
        </div>
    </div>

    <!-- Incluye Bootstrap 5 JavaScript -->
    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
        // Espera 5 segundos antes de desaparecer
        setTimeout(() => {
            const alerts = document.querySelectorAll('.fade-msg');
            alerts.forEach(alert => {
                alert.classList.add('hide'); // aplica la clase fade out
                // Remueve el elemento del DOM después de la transición
                setTimeout(() => alert.remove(), 1000);
            });
        }, 5000);
    </script>
</body>
</html>