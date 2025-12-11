<div class="border-end" id="sidebar">
    <?php
    $current_page = $_SERVER['PHP_SELF'];

    function get_active_class($link_file, $current_page)
    {
        $current_file = basename($current_page);
        if ($current_file === $link_file) {
            return 'active';
        }
        return '';
    }
    ?>

    <div class="sidebar-heading border-bottom p-3">
        <i class="bi bi-hospital-fill me-2 text-xl"></i>
        <span class="fs-5 fw-bold">Hospital Sampaka</span>
    </div>

    <div class="list-group list-group-flush p-3">

        <a href="index.php" class="nav-link text-white 
            <?php echo get_active_class('index.php', $current_page); ?> p-3 rounded-xl mb-2">
            <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>

        <a href="pacientes.php" class="nav-link text-white 
            <?php echo get_active_class('pacientes.php', $current_page); ?> p-3 rounded-xl mb-2">
            <i class="bi bi-person-fill me-2"></i> Pacientes
        </a>

        <a href="consultas.php" class="nav-link text-white 
            <?php echo get_active_class('consultas.php', $current_page); ?> p-3 rounded-xl mb-2">
            <i class="bi bi-file-earmark-medical me-2"></i> Consultas
        </a>

         <a href="../administrador/pruebas_hosptalarias.php" class="nav-link text-white 
            <?php echo get_active_class('pruebas_hosptalarias', $current_page); ?> p-3 rounded-xl mb-2">
            <i class="bi bi-file-earmark-medical me-2"></i> Pruebas Hospitalarias
        </a>

        <a href="usuarios.php" class="nav-link text-white 
            <?php echo get_active_class('usuarios.php', $current_page); ?> p-3 rounded-xl mb-2">
            <i class="bi bi-person-bounding-box"></i> Usuarios
        </a>

        <a href="hospitalizaciones.php" class="nav-link text-white 
            <?php echo get_active_class('hospitalizaciones.php', $current_page); ?> p-3 rounded-xl mb-2">
            <i class="bi bi-bed-fill me-2"></i> Hospitalizaciones
        </a>

        <a href="personal.php" class="nav-link text-white 
            <?php echo get_active_class('personal.php', $current_page); ?> p-3 rounded-xl mb-2">
            <i class="bi bi-people-fill me-2"></i> Personal
        </a>

    
        

        <a href="finanzas.php" class="nav-link text-white 
            <?php echo get_active_class('finanzas.php', $current_page); ?> p- rounded-xl mb-2">
            <i class="bi bi-currency-dollar me-2"></i> Finanzas
        </a>

        <a href="reportes.php" class="nav-link text-white 
            <?php echo get_active_class('reportes.php', $current_page); ?> p-3 rounded-xl mb-2">
            <i class="bi bi-graph-up me-2"></i> Reportes
        </a>

        <a href="auditoria.php" class="nav-link text-white 
            <?php echo get_active_class('auditoria.php', $current_page); ?> p-3 rounded-xl mb-2">
            <i class="bi bi-gear-fill me-2"></i> Configuración
        </a>

    </div>
</div>