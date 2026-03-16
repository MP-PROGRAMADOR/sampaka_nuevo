<?php

try {
    // Consultamos los datos del personal asociado al usuario logueado
    $stmt_user = $pdo->prepare("SELECT p.nombre, p.apellido, p.especialidad 
                                FROM usuarios u 
                                INNER JOIN personal p ON u.id_personal = p.id_personal 
                                WHERE u.id_usuario = :id_u");
    $stmt_user->execute([':id_u' => $id_usuario_sesion]);
    $user_info = $stmt_user->fetch();

    // Si existe el usuario, formateamos el nombre, si no, ponemos uno por defecto
    $nombre_sidebar = ($user_info) ? $user_info['nombre'] . " " . $user_info['apellido'] : "Usuario Médico";
    // Generar iniciales para el placeholder de la imagen
    $iniciales = ($user_info) ? substr($user_info['nombre'], 0, 1) . substr($user_info['apellido'], 0, 1) : "DR";
} catch (PDOException $e) {
    $nombre_sidebar = "Error de conexión";
    $iniciales = "??";
}
?>

<div class="sidebar d-flex flex-column p-3">
    <a href="index.php" class="navbar-brand fs-4 mb-4 text-center text-white">
        <i class="bi bi-clipboard-pulse"></i> Panel Médico
    </a>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="../usuario/index.php" class="nav-link <?= ($page_name == 'Dashboard') ? 'active' : '' ?>">
                <i class="bi bi-house-door-fill me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="../usuario/pacientes.php" class="nav-link <?= ($page_name == 'Mis Pacientes') ? 'active' : '' ?>">
                <i class="bi bi-people-fill me-2"></i> Mis Pacientes
            </a>
        </li>
        <li class="nav-item">
            <a href="mi_agenda.php" class="nav-link <?= ($page_name == 'Mi Agenda') ? 'active' : '' ?>">
                <i class="bi bi-calendar-range-fill me-2"></i> Mi Agenda
            </a>
        </li>
        <li class="nav-item">
            <a href="../usuario/consultas.php" class="nav-link <?= ($page_name == 'Consultas') ? 'active' : '' ?>">
                <i class="bi bi-file-earmark-medical me-2"></i> Consultas
            </a>
        </li>
        <li class="nav-item">
            <a href="../usuario/analitica.php" class="nav-link <?= ($page_name == 'Analíticas') ? 'active' : '' ?>">
                <i class="bi bi-file-medical-fill me-2"></i> Analiticas
            </a>
        </li>
        <li class="nav-item">
            <a href="../usuario/catalogo.php" class="nav-link <?= ($page_name == 'Catalogo') ? 'active' : '' ?>">
                <i class="bi bi-file-earmark-medical me-2"></i> Catalogo
            </a>

        </li>
    </ul>
    <hr class="text-white-50">
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="https://via.placeholder.com/32/333/fff?text=<?= $iniciales ?>" alt="Doctor" width="32" height="32" class="rounded-circle me-2 border border-white">
            <strong><?= htmlspecialchars($nombre_sidebar) ?></strong>
        </a>
        <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser1">
            <li><a class="dropdown-item" href="./perfil.php">Mi Perfil</a></li>
            <li>
                <hr class="dropdown-divider">
            </li>
            <li>
                <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#modalLogout">
                    <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                </a>
            </li>
        </ul>
    </div>
</div>

<div class="modal fade" id="modalLogout" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" style="max-width: 350px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close" style="font-size: 0.8rem;"></button>
            </div>
            <div class="modal-body text-center pb-2">
                <div class="icon-container">
                    <i class="bi bi-door-open-fill"></i>
                </div>
                <h5 class="modal-title-custom">¿Finalizar Sesión?</h5>
                <p class="modal-text-custom">
                    Estás a punto de salir del sistema, <strong><?= htmlspecialchars($user_info['nombre'] ?? 'Usuario') ?></strong>. Asegúrate de haber guardado tus cambios.
                </p>
            </div>
            <div class="modal-footer border-0 p-4 pt-2 d-flex flex-column gap-2">
                <a href="../php/cerrar_sesion.php" class="btn btn-logout-confirm w-100 py-2 shadow-sm">
                    <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
                </a>
                <button type="button" class="btn btn-link btn-stay text-decoration-none small" data-bs-dismiss="modal">
                    Seguir trabajando
                </button>
            </div>
        </div>
    </div>
</div>