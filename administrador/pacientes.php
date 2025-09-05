<?php
include_once '../componentes/header.php';
?>

<body>

    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->

        <?php include_once '../componentes/sidebar.php'; ?>

        <!-- Page Content -->
        <div id="content" class="p-4 bg-gray-100 flex-grow">
            <!-- Navbar -->
              <?php
           include_once '../componentes/barra_nav.php';
        ?>

            <!-- Botón añadir paciente -->
            <div class="d-flex justify-content-end mb-3">
                <button class="btn btn-primary btn-rounded shadow-sm">
                    <i class="bi bi-person-plus-fill me-2"></i> Añadir Nuevo Paciente
                </button>
            </div>

            <!-- Tabla de pacientes -->
            <div class="card shadow-sm rounded-xl">
                <div class="card-body">
                    <h5 class="card-title mb-3">Lista de Pacientes</h5>
                    <div class="table-responsive">
                        <table id="tablaPacientes" class="table table-striped table-hover align-middle mb-0 nowrap" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Apellido</th>
                                    <th>Sexo</th>
                                    <th>Nacionalidad</th>
                                    <th>Teléfono</th>
                                    <th>Ocupación</th>
                                    <th>Fecha Registro</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Juan</td>
                                    <td>Pérez</td>
                                    <td>M</td>
                                    <td>Guinea Ecuatorial</td>
                                    <td>+240 555 123456</td>
                                    <td>Estudiante</td>
                                    <td>2025-09-05</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary me-2"><i class="bi bi-pencil-square"></i></button>
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                                <!-- Más filas dinámicas desde la BD -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        



    </div>


<?php
include_once '../componentes/footer.php';
?>