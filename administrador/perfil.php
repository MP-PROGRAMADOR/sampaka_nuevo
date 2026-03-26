<?php
// Iniciar sesión y componentes
include_once '../componentes/header.php';
require_once "../config/conexion.php";

// Supongamos que el ID del usuario está guardado en la sesión
// session_start(); // Asegúrate de que esto esté en header.php o aquí
$id_usuario_sesion = $_SESSION['id_usuario'] ?? 1;

try {
    // Consulta para obtener datos del usuario y su información personal
    $sql = "SELECT u.username, u.rol, u.estado, p.*, h.nombre as hospital_nombre 
            FROM usuarios u
            JOIN personal p ON u.id_personal = p.id_personal
            JOIN hospitales h ON p.id_hospital = h.id_hospital
            WHERE u.id_usuario = ?";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_usuario_sesion]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<div class="d-flex" id="wrapper">
    <?php include_once '../componentes/sidebar.php'; ?>

    <div id="content" class="p-4 bg-gray-100 flex-grow-1">
        <?php include_once '../componentes/barra_nav.php'; ?>

        <div class="container-fluid">
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success fade-msg"><?= htmlspecialchars($_SESSION['success']); ?></div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger fade-msg"><?= htmlspecialchars($_SESSION['error']); ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>


            <div class="row">
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 text-center p-4 mb-4">
                        <div class="mb-3">
                            <img src="../img/avatar_default.png" class="rounded-circle img-thumbnail" style="width: 120px; height: 120px; object-fit: cover;" alt="Foto de perfil">
                        </div>
                        <h4 class="fw-bold mb-1"><?= htmlspecialchars($user['nombre'] . ' ' . $user['apellido']) ?></h4>
                        <p class="text-muted mb-3"><?= htmlspecialchars($user['cargo']) ?></p>

                        <div class="d-flex justify-content-center gap-2 mb-3">
                            <span class="badge bg-primary px-3 py-2"><?= $user['rol'] ?></span>
                            <span class="badge bg-success px-3 py-2"><?= $user['estado'] ?></span>
                        </div>
                        <hr>
                        <div class="text-start">
                            <p class="small text-muted mb-1"><i class="bi bi-building me-2"></i>Centro Asignado:</p>
                            <p class="fw-bold"><?= htmlspecialchars($user['hospital_nombre']) ?></p>

                            <p class="small text-muted mb-1"><i class="bi bi-qr-code me-2"></i>Código de Empleado:</p>
                            <p class="fw-bold"><?= htmlspecialchars($user['codigo']) ?></p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white p-3">
                            <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
                                <li class="nav-item">
                                    <button class="nav-link active fw-bold" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button">Información Personal</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link fw-bold" id="seguridad-tab" data-bs-toggle="tab" data-bs-target="#seguridad" type="button">Seguridad de la Cuenta</button>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body p-4">
                            <div class="tab-content" id="myTabContent">

                                <div class="tab-pane fade show active" id="info">
                                    <form action="../php/actualizar_perfil.php" method="POST">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Nombre</label>
                                                <input type="text" class="form-control" name="nombre" value="<?= htmlspecialchars($user['nombre']) ?>" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Apellido</label>
                                                <input type="text" class="form-control" name="apellido" value="<?= htmlspecialchars($user['apellido']) ?>" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Correo Electrónico</label>
                                                <input type="email" class="form-control" name="correo" value="<?= htmlspecialchars($user['correo']) ?>" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Teléfono</label>
                                                <input type="text" class="form-control" name="telefono" value="<?= htmlspecialchars($user['telefono']) ?>">
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label small fw-bold">Especialidad / Nivel de Estudios</label>
                                                <input type="text" class="form-control" value="<?= htmlspecialchars($user['especialidad'] . ' - ' . $user['nivel_estudios']) ?>" disabled>
                                                <div class="form-text">Para cambiar datos académicos, contacte con Recursos Humanos.</div>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <button type="submit" class="btn btn-primary px-4">Guardar Cambios</button>
                                        </div>
                                    </form>
                                </div>

                                <div class="tab-pane fade" id="seguridad">
    <form action="../php/cambiar_password.php" method="POST">
        <div class="mb-3">
            <label class="form-label small fw-bold">Contraseña Actual</label>
            <input type="password" class="form-control" name="current_password" placeholder="Ingresa tu clave actual para validar" required>
        </div>
        
        <hr class="my-4">

        <div class="mb-3">
            <label class="form-label small fw-bold">Nombre de Usuario</label>
            <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($user['username']) ?>" readonly>
        </div>
        <div class="mb-3">
            <label class="form-label small fw-bold">Nueva Contraseña</label>
            <input type="password" class="form-control" name="new_password" placeholder="Mínimo 8 caracteres" required>
        </div>
        <div class="mb-3">
            <label class="form-label small fw-bold">Confirmar Nueva Contraseña</label>
            <input type="password" class="form-control" name="confirm_password" required>
        </div>
        <div class="mt-4">
            <button type="submit" class="btn btn-dark">Actualizar Contraseña</button>
        </div>
    </form>
</div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once '../componentes/footer.php'; ?>