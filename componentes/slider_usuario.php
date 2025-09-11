<div class="sidebar d-flex flex-column p-3 bg-light shadow vh-100">
    <!-- Logo / Marca -->
    <a href="#" class="d-flex align-items-center mb-4 text-dark text-decoration-none">
        <i class="bi bi-hospital fs-3 me-2 text-primary"></i>
        <span class="fs-5 fw-bold">Admin Hospital</span>
    </a>

    <!-- Menú principal -->
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="#" class="nav-link active d-flex align-items-center">
                <i class="bi bi-speedometer2 me-2"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="consultas.php" class="nav-link text-dark d-flex align-items-center">
                <i class="bi bi-people me-2"></i>
                <span>Consultas</span>
            </a>
        </li>
        <li>
            <a href="#" class="nav-link text-dark d-flex align-items-center">
                <i class="bi bi-person-fill-gear me-2"></i>
                <span>Médicos</span>
            </a>
        </li>
        <li>
            <a href="#" class="nav-link text-dark d-flex align-items-center">
                <i class="bi bi-calendar-check me-2"></i>
                <span>Citas</span>
            </a>
        </li>
        <li>
            <a href="#" class="nav-link text-dark d-flex align-items-center">
                <i class="bi bi-box-seam me-2"></i>
                <span>Inventario</span>
            </a>
        </li>
        <li>
            <a href="#" class="nav-link text-dark d-flex align-items-center">
                <i class="bi bi-file-earmark-bar-graph me-2"></i>
                <span>Reportes</span>
            </a>
        </li>
    </ul>

    <hr>

    <!-- Usuario -->
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle"
           id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="https://via.placeholder.com/40" alt="profile" width="40" height="40" class="rounded-circle me-2 border">
            <div class="d-flex flex-column">
                <strong><?= $usuario_nombre  ?></strong>
                <small class="text-muted"><?= $usuario_rol  ?></small>
            </div>
        </a>
        <ul class="dropdown-menu shadow" aria-labelledby="dropdownUser1">
            <li><a class="dropdown-item" href="#"><i class="bi bi-person-circle me-2"></i> Perfil</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal"><i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión</a></li>
        </ul>
    </div>
</div>

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