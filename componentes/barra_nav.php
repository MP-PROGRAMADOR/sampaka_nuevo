

<nav class="navbar navbar-expand-lg navbar-light bg-white mb-4 rounded-xl shadow-sm">
    <div class="container-fluid">
        <h1 class="text-xl md:text-2xl font-bold text-gray-800">Panel de Administración</h1>
        <div class="dropdown">
            <a class="d-flex align-items-center text-decoration-none dropdown-toggle text-gray-700" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle fs-3 me-2"></i>
                <span class="d-none d-md-inline"><?php echo htmlspecialchars($nombre_usuario); ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="#">Perfil</a></li>
                <!-- Botón para abrir el modal -->
                <li>
                    <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                        <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Modal de Confirmación de Cierre de Sesión -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow-lg border-0 rounded-3">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold text-danger" id="logoutModalLabel">
          <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirmar Cierre de Sesión
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body text-center">
        <div class="mb-3">
          <i class="bi bi-person-circle text-primary" style="font-size: 4rem;"></i>
        </div>
        <h5 class="fw-bold mb-1">¿Seguro que deseas cerrar sesión?</h5>
        <p class="text-muted">Usuario: <span class="fw-semibold"><?php echo htmlspecialchars($usuario_nombre . " " . $usuario_apellidos); ?></span></p>
      </div>
      <div class="modal-footer border-0 d-flex justify-content-between">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
          <i class="bi bi-x-circle me-1"></i> Cancelar
        </button>
        <a href="../php/cerrar_sesion.php" class="btn btn-danger">
          <i class="bi bi-box-arrow-right me-1"></i> Cerrar Sesión
        </a>
      </div>
    </div>
  </div>
</div>
