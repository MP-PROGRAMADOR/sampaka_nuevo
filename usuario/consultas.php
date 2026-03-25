<?php

require_once "../config/conexion.php";



include_once '../componentes/header_usuario.php';
$id_usuario_sesion = $_SESSION['id_usuario'] ?? 0;

// 1. Configuración para el header
$page_title = 'Consultas';
$page_name = 'Consultas';

?>

<body>
    <?php include_once '../componentes/slider_usuario.php'; ?>


    <div class="main-content p-4 bg-gray-100 flex-grow ">
        <h1 class="mb-4 fw-light text-primary">
            <i class="bi bi-clipboard2-pulse-fill me-2"></i> Gestión de Pruebas
        </h1>


        <?php if ($usuario_rol === 'General'): ?>
            <!-- Botón añadir paciente -->
            <div class="d-flex justify-content-end mb-3">
                <button class="btn btn-primary btn-rounded shadow-sm" data-bs-toggle="modal" data-bs-target="#modalConsulta">
                    <i class="bi bi-person-plus-fill me-2"></i> Añadir Consulta
                </button>
            </div>
        <?php endif; ?>



        <!-- Tabla de COnsultas -->
        <div class="card p-4 shadow-sm">
            <div class="card-body">


                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success fade-msg"><?= $_SESSION['success']; ?></div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger fade-msg"><?= $_SESSION['error']; ?></div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>



                <div class="table-responsive">
                    <?php
                    try {
                        // 1. Obtenemos el ID del usuario desde la sesión
                        $id_usuario_sesion = $_SESSION['id_usuario'];

                        // 2. Preparamos la consulta SQL
                        $sql = "
                        SELECT 
                            c.id_consulta,
                            p.nombre AS paciente_nombre, p.apellido AS paciente_apellido, p.id_paciente AS id_paciente, c.tipo_consulta, 
                            c.motivo, c.temperatura, c.presion_arterial, c.tension_arterial, c.saturacion_oxigeno,
                            c.pulso, c.peso, c.talla, c.IMC, c.fecha_consulta, c.pagado, d.orina, d.defeca,
                            d.horas_sueno, d.antecedentes_familiares, d.antecedentes_conyuge, d.alergias, d.operaciones, d.transfuciones
                        FROM consultas c
                        INNER JOIN pacientes p ON c.id_paciente = p.id_paciente
                        LEFT JOIN detalle_consulta d ON c.id_consulta = d.id_consulta
                        WHERE c.id_usuario = :id_usuario  -- Filtra estrictamente por tu ID
                        ORDER BY c.fecha_consulta DESC
                    ";

                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([':id_usuario' => $id_usuario_sesion]);

                        $consultas = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    } catch (PDOException $e) {
                        echo "<tr><td colspan='15' class='text-danger'>Error al obtener consultas: " . $e->getMessage() . "</td></tr>";
                        exit;
                    }
                    ?>


                    <table id="tablaPacientes" class="table table-striped table-hover align-middle mb-0 nowrap" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Paciente</th>
                                <th>Tipo</th>
                                <th>Motivo</th>
                                <th>Fecha</th>
                                <th>Estado Pago</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($consultas as $consulta): ?>
                                <tr>
                                    <td><?= htmlspecialchars($consulta['id_consulta']) ?></td>
                                    <td><?= htmlspecialchars($consulta['paciente_nombre'] . " " . $consulta['paciente_apellido']) ?>
                                    </td>
                                    <td><?= htmlspecialchars($consulta['tipo_consulta']) ?></td>
                                    <td><?= htmlspecialchars($consulta['motivo']) ?></td>
                                    <td><?= htmlspecialchars($consulta['fecha_consulta']) ?></td>

                                    <td>
                                        <?php if ($consulta['pagado']): ?>
                                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> Pagado</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Pendiente</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info btn-ver-consulta" data-bs-toggle="modal"
                                            data-bs-target="#modalVerConsulta" data-id="<?= $consulta['id_consulta'] ?>"
                                            data-paciente="<?= htmlspecialchars($consulta['paciente_nombre'] . ' ' . $consulta['paciente_apellido']) ?>"
                                            data-tipo="<?= htmlspecialchars($consulta['tipo_consulta']) ?>"
                                            data-motivo="<?= htmlspecialchars($consulta['motivo']) ?>"
                                            data-fecha="<?= $consulta['fecha_consulta'] ?>"
                                            data-pagado="<?= $consulta['pagado'] ?>"
                                            data-temperatura="<?= $consulta['temperatura'] ?>"
                                            data-presion="<?= $consulta['presion_arterial'] ?>"
                                            data-tension="<?= $consulta['tension_arterial'] ?>"
                                            data-saturacion="<?= $consulta['saturacion_oxigeno'] ?>"
                                            data-pulso="<?= $consulta['pulso'] ?>" data-peso="<?= $consulta['peso'] ?>"
                                            data-talla="<?= $consulta['talla'] ?>" data-imc="<?= $consulta['IMC'] ?>"
                                            data-orina="<?= htmlspecialchars($consulta['orina']) ?>"
                                            data-defeca="<?= htmlspecialchars($consulta['defeca']) ?>"
                                            data-horas_sueno="<?= $consulta['horas_sueno'] ?>"
                                            data-transfusiones="<?= htmlspecialchars($consulta['transfuciones']) ?>"
                                            data-antecedentes_familiares="<?= htmlspecialchars($consulta['antecedentes_familiares']) ?>"
                                            data-antecedentes_conyuge="<?= htmlspecialchars($consulta['antecedentes_conyuge']) ?>"
                                            data-alergias="<?= htmlspecialchars($consulta['alergias']) ?>"
                                            data-operaciones="<?= htmlspecialchars($consulta['operaciones']) ?>">
                                            <i class="bi bi-eye"></i>
                                        </button>


                                        <?php if ($consulta['pagado'] == 0): ?>
                                            <button class="btn btn-sm btn-primary btn-editar-consulta" data-bs-toggle="modal"
                                                data-bs-target="#modalConsulta" data-id="<?= $consulta['id_paciente'] ?>"
                                                data-id_consulta="<?= $consulta['id_consulta'] ?>"
                                                data-paciente="<?= htmlspecialchars($consulta['paciente_nombre'] . ' ' . $consulta['paciente_apellido']) ?>"
                                                data-tipo="<?= htmlspecialchars($consulta['tipo_consulta']) ?>"
                                                data-motivo="<?= htmlspecialchars($consulta['motivo']) ?>"
                                                data-temperatura="<?= $consulta['temperatura'] ?>"
                                                data-presion="<?= htmlspecialchars($consulta['presion_arterial']) ?>"
                                                data-tension="<?= htmlspecialchars($consulta['tension_arterial']) ?>"
                                                data-saturacion="<?= $consulta['saturacion_oxigeno'] ?>"
                                                data-pulso="<?= $consulta['pulso'] ?>" data-peso="<?= $consulta['peso'] ?>"
                                                data-talla="<?= $consulta['talla'] ?>"
                                                data-orina="<?= htmlspecialchars($consulta['orina']) ?>"
                                                data-defeca="<?= htmlspecialchars($consulta['defeca']) ?>"
                                                data-horas_sueno="<?= $consulta['horas_sueno'] ?>"
                                                data-transfusiones="<?= htmlspecialchars($consulta['transfuciones']) ?>"
                                                data-antecedentes-familiares="<?= htmlspecialchars($consulta['antecedentes_familiares']) ?>"
                                                data-antecedentes-conyuge="<?= htmlspecialchars($consulta['antecedentes_conyuge']) ?>"
                                                data-alergias="<?= htmlspecialchars($consulta['alergias']) ?>"
                                                data-operaciones="<?= htmlspecialchars($consulta['operaciones']) ?>"
                                                data-pagado="<?= $consulta['pagado'] ?>">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                        <?php endif; ?>

                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>



    <!-- Modal Evaluar Paciente -->
    <div class="modal fade" id="modalEvaluarPaciente" tabindex="-1" aria-labelledby="modalEvaluarPacienteLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content shadow-lg border-0 rounded-4">

                <!-- Encabezado del especialista -->
                <div class="d-flex align-items-center justify-content-between px-4 py-2 bg-light border-bottom">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width:40px; height:40px;">
                            <i class="bi bi-person-badge fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold text-primary">
                                <?php echo $usuario_nombre . " " . $usuario_apellidos; ?>
                            </h6>
                            <small class="text-muted">Especialista</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <!-- Header principal -->
                <div class="modal-header bg-gradient-primary text-white">
                    <h5 class="modal-title" id="modalEvaluarPacienteLabel">
                        <i class="bi bi-search me-2"></i> Evaluar Paciente
                    </h5>
                </div>

                <!-- Body -->
                <div class="modal-body p-4">
                    <!-- Buscador de pacientes -->
                    <div class="mb-3">
                        <input type="text" id="buscarConsulta" class="form-control form-control-lg shadow-sm" placeholder="Escribe nombre o apellido del paciente..." autofocus>
                    </div>

                    <!-- Resultados de pacientes -->
                    <div id="resultadosConsulta" class="list-group mb-3 shadow-sm rounded" style="max-height:200px; overflow-y:auto;"></div>

                    <!-- Consulta seleccionada -->
                    <div id="consultaSeleccionada" class="alert alert-success d-none rounded-3 d-flex justify-content-between align-items-center shadow-sm mb-3">
                        <div>
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <span id="datosConsulta" class="fw-bold"></span>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="btnQuitarSeleccion" title="Quitar paciente">
                            <i class="bi bi-x-circle"></i>
                        </button>
                        <input type="hidden" id="id_consulta_seleccionada">
                        <input type="hidden" id="id_paciente_seleccionado">
                    </div>

                    <!-- Buscador de pruebas -->
                    <div class="mb-3">
                        <input type="text" id="buscarPrueba" class="form-control form-control-sm shadow-sm" placeholder="Busca una prueba...">
                    </div>
                    <div id="resultadosPruebas" class="list-group mb-2 shadow-sm rounded" style="max-height:200px; overflow-y:auto;"></div>

                    <!-- Pruebas seleccionadas -->
                    <div id="pruebasSeleccionadasContainer" class="mb-4">
                        <label class="form-label fw-bold">Pruebas seleccionadas:</label>
                        <div id="pruebasSeleccionadas" class="d-flex flex-wrap gap-2 mb-3"></div>
                        <input type="hidden" name="ids_pruebas" id="ids_pruebas">
                    </div>

                    <!-- Textareas adicionales -->
                    <div class="mb-3">
                        <label for="historial_enfermedad" class="form-label fw-bold">Historial de enfermedad actual</label>
                        <textarea id="historial_enfermedad" name="historial_enfermedad" rows="3" class="form-control shadow-sm" placeholder="Describe el historial de enfermedad actual..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="exploracion_fisica" class="form-label fw-bold">Exploración física</label>
                        <textarea id="exploracion_fisica" name="exploracion_fisica" rows="3" class="form-control shadow-sm" placeholder="Describe la exploración física del paciente..." required></textarea>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" id="btnConfirmarEvaluacion">
                        <i class="bi bi-check-circle me-1"></i> Guardar Cambios
                    </button>
                </div>
            </div>
        </div>
    </div>





    <!-- Modal Registrar Consulta -->
    <div class="modal fade" id="modalConsulta" tabindex="-1" aria-labelledby="modalConsultaLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content shadow-lg border-0 rounded-3">

                <!-- HEADER -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalConsultaLabel">
                        <i class="bi bi-journal-medical me-2"></i> Nueva Consulta
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>

                <!-- BODY -->
                <div class="modal-body">
                    <!-- BUSCADOR DE PACIENTE -->
                    <div class="mb-3">
                        <label for="buscarPaciente" class="form-label fw-bold">
                            <i class="bi bi-search"></i> Buscar Paciente
                        </label>
                        <input type="text" class="form-control" id="buscarPaciente"
                            placeholder="Escriba nombre, apellido o código...">
                        <div id="resultadosPacientes" class="list-group mt-2"></div>
                    </div>

                    <!-- PACIENTE SELECCIONADO -->
                    <div id="pacienteSeleccionado" class="alert alert-success d-none fw-bold"></div>

                    <!-- FORMULARIO -->
                    <form id="formConsulta" method="POST" action="./guardar_consulta.php">
                        <input type="hidden" id="idPaciente" name="id_paciente" required>
                        <input type="hidden" id="id_consulta" name="id_consulta" required>

                        <!-- NAV TABS -->
                        <ul class="nav nav-tabs" id="consultaTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="datosConsulta-tab" data-bs-toggle="tab"
                                    data-bs-target="#datosConsulta" type="button" role="tab">
                                    <i class="bi bi-clipboard2-plus"></i> Datos de la Consulta
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="detalleConsulta-tab" data-bs-toggle="tab"
                                    data-bs-target="#detalleConsulta" type="button" role="tab">
                                    <i class="bi bi-file-medical"></i> Detalle de Consulta
                                </button>
                            </li>
                        </ul>

                        <!-- TAB CONTENT -->
                        <div class="tab-content mt-3" id="consultaTabsContent">

                            <!-- TAB CONSULTA -->
                            <div class="tab-pane fade show active" id="datosConsulta" role="tabpanel">
                                <div class="row g-3">

                                    <!-- Hospital -->
                                    <div class="col-md-6">
                                        <label for="hospital" class="form-label fw-bold">
                                            <i class="bi bi-hospital"></i> Hospital
                                        </label>
                                        <select class="form-select" id="hospital" name="id_hospital" required>
                                            <?php
                                            $sqlHosp = $pdo->query("SELECT id_hospital, nombre FROM hospitales ORDER BY nombre ASC");
                                            while ($row = $sqlHosp->fetch(PDO::FETCH_ASSOC)) {
                                                echo "<option value='{$row['id_hospital']}'>{$row['nombre']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <!-- Tipo de Consulta -->
                                    <div class="col-md-6">
                                        <label for="tipoConsulta" class="form-label fw-bold">
                                            <i class="bi bi-ui-checks-grid"></i> Tipo de Consulta
                                        </label>
                                        <select class="form-select" id="tipoConsulta" name="tipo_consulta" required>
                                            <option value="">Seleccione...</option>
                                            <option value="General">General</option>
                                            <option value="Urgencias">Urgencias</option>
                                            <option value="Gastroenterología">Gastroenterología</option>
                                            <option value="Ginecología">Ginecología</option>
                                            <option value="Pediatría">Pediatría</option>
                                            <option value="Cardiología">Cardiología</option>
                                            <option value="Dermatología">Dermatología</option>
                                            <option value="Neurología">Neurología</option>
                                            <option value="Traumatología">Traumatología</option>
                                            <option value="Psiquiatría">Psiquiatría</option>
                                            <option value="Oncología">Oncología</option>
                                            <option value="Oftalmología">Oftalmología</option>
                                            <option value="Otorrinolaringología">Otorrinolaringología</option>
                                            <option value="Endocrinología">Endocrinología</option>
                                            <option value="Neumología">Neumología</option>
                                            <option value="Reumatología">Reumatología</option>
                                        </select>
                                    </div>

                                    <!-- Motivo -->
                                    <div class="col-12">
                                        <label for="motivo" class="form-label fw-bold">
                                            <i class="bi bi-chat-dots"></i> Motivo de la Consulta
                                        </label>
                                        <textarea class="form-control" id="motivo" name="motivo" rows="3"
                                            required></textarea>
                                    </div>

                                    <!-- Signos vitales -->
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">
                                            <i class="bi bi-thermometer-half"></i> Temperatura (°C)
                                        </label>
                                        <input type="number" step="0.01" class="form-control" name="temperatura"
                                            required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">
                                            <i class="bi bi-heart-pulse"></i> Presión Arterial
                                        </label>
                                        <input type="text" class="form-control" name="presion_arterial" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">
                                            <i class="bi bi-heart"></i> Tensión Arterial
                                        </label>
                                        <input type="text" class="form-control" name="tension_arterial" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">
                                            <i class="bi bi-activity"></i> Saturación O₂ (%)
                                        </label>
                                        <input type="number" step="0.01" class="form-control" name="saturacion_oxigeno"
                                            required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">
                                            <i class="bi bi-heart-fill"></i> Pulso
                                        </label>
                                        <input type="number" class="form-control" name="pulso" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">
                                            <i class="bi bi-person-lines-fill"></i> Peso (kg)
                                        </label>
                                        <input type="number" step="0.01" class="form-control" name="peso" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">
                                            <i class="bi bi-rulers"></i> Talla (cm)
                                        </label>
                                        <input type="number" step="0.01" class="form-control" name="talla" required>
                                    </div>

                                </div>
                            </div>

                            <!-- TAB DETALLE CONSULTA -->
                            <div class="tab-pane fade" id="detalleConsulta" role="tabpanel">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">
                                            <i class="bi bi-droplet"></i> Orina
                                        </label>
                                        <input type="text" class="form-control" name="orina" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">
                                            <i class="bi bi-bandaid"></i> Defeca
                                        </label>
                                        <input type="text" class="form-control" name="defeca" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">
                                            <i class="bi bi-moon-stars"></i> Horas de Sueño
                                        </label>
                                        <input type="number" class="form-control" name="horas_sueno" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold">
                                            <i class="bi bi-droplet-half"></i> Transfusiones Sanguíneas
                                        </label>
                                        <textarea class="form-control" name="transfusiones_sanguineas" rows="2"
                                            required></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold">
                                            <i class="bi bi-people"></i> Antecedentes Familiares
                                        </label>
                                        <textarea class="form-control" name="antecedentes_familiares" rows="2"
                                            required></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold">
                                            <i class="bi bi-person-hearts"></i> Antecedentes Cónyuge
                                        </label>
                                        <textarea class="form-control" name="antecedentes_conyuge" rows="2"
                                            required></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold">
                                            <i class="bi bi-exclamation-triangle"></i> Alergias
                                        </label>
                                        <textarea class="form-control" name="alergias" rows="2" required></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold">
                                            <i class="bi bi-scissors"></i> Operaciones
                                        </label>
                                        <textarea class="form-control" name="operaciones" rows="2" required></textarea>
                                    </div>
                                </div>
                            </div>

                        </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cerrar
                    </button>
                    <button type="submit" form="formConsulta" class="btn btn-primary">
                        <i class="bi bi-save2 me-1"></i> Guardar Consulta
                    </button>
                </div>

                </form>

            </div>
        </div>
    </div>


    <!-- Modal para editar consulta -->
    <div class="modal fade" id="modalConsulta" tabindex="-1" aria-labelledby="modalConsultaLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content shadow-lg border-0 rounded-3">

                <!-- HEADER -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalConsultaLabel">
                        <i class="bi bi-journal-medical me-2"></i> Nueva Consulta
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>

                <!-- BODY -->
                <div class="modal-body">
                    <!-- BUSCADOR DE PACIENTE -->
                    <div class="mb-3" id="buscadorPacienteContainer">
                        <label for="buscarPaciente" class="form-label fw-bold">
                            <i class="bi bi-search"></i> Buscar Paciente
                        </label>
                        <input type="text" class="form-control" id="buscarPaciente"
                            placeholder="Escriba nombre, apellido o código...">
                        <div id="resultadosPacientes" class="list-group mt-2"></div>
                    </div>

                    <!-- PACIENTE SELECCIONADO -->
                    <div id="pacienteSeleccionado" class="alert alert-success fw-bold d-none"></div>

                    <!-- FORMULARIO -->
                    <form id="formConsulta" method="POST">
                        <input type="hidden" id="idPaciente" name="id_paciente" required>

                        <!-- NAV TABS -->
                        <ul class="nav nav-tabs" id="consultaTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="datosConsulta-tab" data-bs-toggle="tab"
                                    data-bs-target="#datosConsulta" type="button" role="tab">
                                    <i class="bi bi-clipboard2-plus"></i> Datos de la Consulta
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="detalleConsulta-tab" data-bs-toggle="tab"
                                    data-bs-target="#detalleConsulta" type="button" role="tab">
                                    <i class="bi bi-file-medical"></i> Detalle de Consulta
                                </button>
                            </li>
                        </ul>

                        <!-- TAB CONTENT -->
                        <div class="tab-content mt-3" id="consultaTabsContent">

                            <!-- TAB CONSULTA -->
                            <div class="tab-pane fade show active" id="datosConsulta" role="tabpanel">
                                <div class="row g-3">

                                    <!-- Tipo de Consulta -->
                                    <div class="col-md-6">
                                        <label for="tipoConsulta" class="form-label fw-bold">
                                            <i class="bi bi-ui-checks-grid"></i> Tipo de Consulta
                                        </label>
                                        <select class="form-select" id="tipoConsulta" name="tipo_consulta" required>
                                            <option value="">Seleccione...</option>
                                            <option value="General">General</option>
                                            <option value="Urgencias">Urgencias</option>
                                            <option value="Gastroenterología">Gastroenterología</option>
                                            <option value="Ginecología">Ginecología</option>
                                            <option value="Pediatría">Pediatría</option>
                                            <option value="Cardiología">Cardiología</option>
                                            <option value="Dermatología">Dermatología</option>
                                            <option value="Neurología">Neurología</option>
                                            <option value="Traumatología">Traumatología</option>
                                            <option value="Psiquiatría">Psiquiatría</option>
                                            <option value="Oncología">Oncología</option>
                                            <option value="Oftalmología">Oftalmología</option>
                                            <option value="Otorrinolaringología">Otorrinolaringología</option>
                                            <option value="Endocrinología">Endocrinología</option>
                                            <option value="Neumología">Neumología</option>
                                            <option value="Reumatología">Reumatología</option>
                                        </select>
                                    </div>

                                    <!-- Motivo -->
                                    <div class="col-12">
                                        <label for="motivo" class="form-label fw-bold">
                                            <i class="bi bi-chat-dots"></i> Motivo de la Consulta
                                        </label>
                                        <textarea class="form-control" id="motivo" name="motivo" rows="3"
                                            required></textarea>
                                    </div>

                                    <!-- Signos vitales -->
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">
                                            <i class="bi bi-thermometer-half"></i> Temperatura (°C)
                                        </label>
                                        <input type="number" step="0.01" class="form-control" name="temperatura"
                                            required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">
                                            <i class="bi bi-heart-pulse"></i> Presión Arterial
                                        </label>
                                        <input type="text" class="form-control" name="presion_arterial" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">
                                            <i class="bi bi-heart"></i> Tensión Arterial
                                        </label>
                                        <input type="text" class="form-control" name="tension_arterial" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">
                                            <i class="bi bi-activity"></i> Saturación O₂ (%)
                                        </label>
                                        <input type="number" step="0.01" class="form-control" name="saturacion_oxigeno"
                                            required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">
                                            <i class="bi bi-heart-fill"></i> Pulso
                                        </label>
                                        <input type="number" class="form-control" name="pulso" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">
                                            <i class="bi bi-person-lines-fill"></i> Peso (kg)
                                        </label>
                                        <input type="number" step="0.01" class="form-control" name="peso" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">
                                            <i class="bi bi-rulers"></i> Talla (cm)
                                        </label>
                                        <input type="number" step="0.01" class="form-control" name="talla" required>
                                    </div>

                                </div>
                            </div>

                            <!-- TAB DETALLE CONSULTA -->
                            <div class="tab-pane fade" id="detalleConsulta" role="tabpanel">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">
                                            <i class="bi bi-droplet"></i> Orina
                                        </label>
                                        <input type="text" class="form-control" name="orina" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">
                                            <i class="bi bi-bandaid"></i> Defeca
                                        </label>
                                        <input type="text" class="form-control" name="defeca" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">
                                            <i class="bi bi-moon-stars"></i> Horas de Sueño
                                        </label>
                                        <input type="number" class="form-control" name="horas_sueno" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold">
                                            <i class="bi bi-droplet-half"></i> Transfusiones Sanguíneas
                                        </label>
                                        <textarea class="form-control" name="transfusiones_sanguineas" rows="2"
                                            required></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold">
                                            <i class="bi bi-people"></i> Antecedentes Familiares
                                        </label>
                                        <textarea class="form-control" name="antecedentes_familiares" rows="2"
                                            required></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold">
                                            <i class="bi bi-person-hearts"></i> Antecedentes Cónyuge
                                        </label>
                                        <textarea class="form-control" name="antecedentes_conyuge" rows="2"
                                            required></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold">
                                            <i class="bi bi-exclamation-triangle"></i> Alergias
                                        </label>
                                        <textarea class="form-control" name="alergias" rows="2" required></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold">
                                            <i class="bi bi-scissors"></i> Operaciones
                                        </label>
                                        <textarea class="form-control" name="operaciones" rows="2" required></textarea>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>

                <!-- FOOTER -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cerrar
                    </button>
                    <button type="submit" form="formConsulta" class="btn btn-primary">
                        <i class="bi bi-save2 me-1"></i> Guardar Consulta
                    </button>
                </div>

            </div>
        </div>
    </div>


    <div class="modal fade" id="modalVerConsulta" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-4">

                <!-- HEADER -->
                <div class="modal-header bg-gradient bg-primary text-white rounded-top-4">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-journal-medical"></i> Detalles de la Consulta
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- BODY -->
                <div class="modal-body p-4">

                    <!-- Paciente & Fecha -->
                    <div class="row g-4 mb-3">
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm rounded-3 h-100">
                                <div class="card-body">
                                    <h6 class="fw-bold text-primary"><i class="bi bi-person-vcard"></i> Paciente</h6>
                                    <p id="verPaciente" class="fs-5 fw-semibold mb-0"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm rounded-3 h-100">
                                <div class="card-body">
                                    <h6 class="fw-bold text-primary"><i class="bi bi-calendar-event"></i> Fecha</h6>
                                    <p id="verFecha" class="fs-5 mb-0"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Signos vitales -->
                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold text-danger mb-3"><i class="bi bi-heart-pulse"></i> Signos Vitales</h6>
                            <div class="row text-center">
                                <div class="col-md-3"><strong>Temp:</strong> <span id="verTemperatura"></span> °C</div>
                                <div class="col-md-3"><strong>Presión:</strong> <span id="verPresion"></span></div>
                                <div class="col-md-3"><strong>Tensión:</strong> <span id="verTension"></span></div>
                                <div class="col-md-3"><strong>Saturación O₂:</strong> <span id="verSaturacion"></span>%</div>
                                <div class="col-md-3"><strong>Pulso:</strong> <span id="verPulso"></span> bpm</div>
                                <div class="col-md-3"><strong>Peso:</strong> <span id="verPeso"></span> kg</div>
                                <div class="col-md-3"><strong>Talla:</strong> <span id="verTalla"></span> cm</div>
                                <div class="col-md-3"><strong>IMC:</strong> <span id="verIMC"></span></div>
                            </div>
                        </div>
                    </div>

                    <!-- Consulta -->
                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold text-success"><i class="bi bi-clipboard2-pulse"></i> Consulta</h6>
                            <p><strong>Tipo:</strong> <span id="verTipo"></span></p>
                            <p><strong>Motivo:</strong> <span id="verMotivo"></span></p>
                            <p><strong>Diagnóstico:</strong> <span id="verDiagnostico"></span></p>
                        </div>
                    </div>

                    <!-- Detalles adicionales -->
                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold text-warning"><i class="bi bi-list-ul"></i> Detalles Adicionales</h6>
                            <div class="row">
                                <div class="col-md-6"><strong>Orina:</strong> <span id="verOrina"></span></div>
                                <div class="col-md-6"><strong>Defeca:</strong> <span id="verDefeca"></span></div>
                                <div class="col-md-6"><strong>Horas de sueño:</strong> <span id="verHorasSueno"></span></div>
                                <div class="col-md-6"><strong>Transfusiones:</strong> <span id="verTransfusiones"></span></div>
                                <div class="col-md-6"><strong>Antecedentes familiares:</strong> <span id="verAntecedentesFamiliares"></span></div>
                                <div class="col-md-6"><strong>Antecedentes cónyuge:</strong> <span id="verAntecedentesConyuge"></span></div>
                                <div class="col-md-6"><strong>Alergias:</strong> <span id="verAlergias"></span></div>
                                <div class="col-md-6"><strong>Operaciones:</strong> <span id="verOperaciones"></span></div>
                            </div>
                        </div>
                    </div>



                </div>

                <!-- FOOTER -->
                <div class="modal-footer border-0">
                    <button class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>








    <!-- Script AJAX -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ---- Pacientes ----
            const inputBuscar = document.getElementById('buscarConsulta');
            const resultados = document.getElementById('resultadosConsulta');
            const seleccion = document.getElementById('consultaSeleccionada');
            const datosConsulta = document.getElementById('datosConsulta');
            const idConsultaSeleccionada = document.getElementById('id_consulta_seleccionada');
            const idPacienteSeleccionado = document.getElementById('id_paciente_seleccionado');

            inputBuscar.addEventListener('keyup', function() {
                const query = this.value.trim();
                if (query.length < 2) {
                    resultados.innerHTML = '';
                    return;
                }
                fetch('../php/buscar_consultas.php?q=' + encodeURIComponent(query))
                    .then(res => res.text())
                    .then(data => resultados.innerHTML = data);
            });

            resultados.addEventListener('click', function(e) {
                if (e.target.classList.contains('seleccionar-consulta')) {
                    const btn = e.target;
                    const idC = btn.dataset.idConsulta;
                    const idP = btn.dataset.idPaciente;
                    const paciente = btn.dataset.paciente;
                    const fecha = btn.dataset.fecha;

                    idConsultaSeleccionada.value = idC;
                    idPacienteSeleccionado.value = idP;
                    datosConsulta.innerHTML = `<strong>${paciente}</strong> - <small>${fecha}</small>`;
                    seleccion.classList.remove('d-none');
                    resultados.scrollTop = resultados.scrollHeight;
                }
            });

            document.getElementById('btnQuitarSeleccion').addEventListener('click', function() {
                idConsultaSeleccionada.value = '';
                idPacienteSeleccionado.value = '';
                seleccion.classList.add('d-none');
            });

            // ---- Pruebas ----
            const inputBuscarPrueba = document.getElementById('buscarPrueba');
            const resultadosPruebas = document.getElementById('resultadosPruebas');
            const pruebasSeleccionadas = document.getElementById('pruebasSeleccionadas');
            const idsInput = document.getElementById('ids_pruebas');
            let idsSeleccionados = [];

            inputBuscarPrueba.addEventListener('keyup', function() {
                const query = this.value.trim();
                if (query.length < 2) {
                    resultadosPruebas.innerHTML = '';
                    return;
                }

                fetch('../php/buscar_pruebas.php?q=' + encodeURIComponent(query))
                    .then(res => res.text())
                    .then(data => resultadosPruebas.innerHTML = data);
            });

            resultadosPruebas.addEventListener('click', function(e) {
                const btn = e.target.closest('.seleccionar-prueba');
                if (!btn) return;

                const id = btn.dataset.idPrueba;
                const nombre = btn.dataset.nombrePrueba;

                if (idsSeleccionados.includes(id)) return;

                idsSeleccionados.push(id);
                idsInput.value = idsSeleccionados.join(',');

                const chip = document.createElement('div');
                chip.className = 'badge bg-primary text-white px-2 py-1 shadow-sm rounded d-flex align-items-center prueba-seleccionada';
                chip.dataset.idPrueba = id;
                chip.innerHTML = `<span class="me-2">${nombre}</span>
                          <button type="button" class="btn-close btn-close-white btn-sm p-1"></button>`;

                chip.querySelector('button').addEventListener('click', function() {
                    const index = idsSeleccionados.indexOf(id);
                    if (index > -1) idsSeleccionados.splice(index, 1);
                    idsInput.value = idsSeleccionados.join(',');
                    chip.remove();
                });

                pruebasSeleccionadas.appendChild(chip);
            });

            // ---- Confirmar ----
            document.getElementById('btnConfirmarEvaluacion').addEventListener('click', function() {
                const idConsulta = idConsultaSeleccionada.value;
                const idPaciente = idPacienteSeleccionado.value;
                const historial = document.getElementById('historial_enfermedad').value.trim();
                const exploracion = document.getElementById('exploracion_fisica').value.trim();

                // IDs de pruebas
                const pruebaElements = document.querySelectorAll('#pruebasSeleccionadas .prueba-seleccionada');
                const idsPruebas = Array.from(pruebaElements).map(el => el.dataset.idPrueba);

                // Validación
                if (!idConsulta || !idPaciente) {
                    alert('Debes seleccionar una consulta y un paciente.');
                    return;
                }
                if (!historial) {
                    alert('Debes ingresar el historial de enfermedad actual.');
                    return;
                }
                if (!exploracion) {
                    alert('Debes ingresar la exploración física.');
                    return;
                }
                if (idsPruebas.length === 0) {
                    alert('Debes seleccionar al menos una prueba.');
                    return;
                }

                // Crear formulario y enviar POST
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '../php/guardar_evaluacion.php';

                form.innerHTML = `
            <input type="hidden" name="id_consulta" value="${idConsulta}">
            <input type="hidden" name="id_paciente" value="${idPaciente}">
            <input type="hidden" name="historial_enfermedad" value="${historial}">
            <input type="hidden" name="exploracion_fisica" value="${exploracion}">
        `;

                idsPruebas.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids_pruebas[]';
                    input.value = id;
                    form.appendChild(input);
                });

                document.body.appendChild(form);
                form.submit();
            });
        });
    </script>






    <script>
        // Buscar pacientes dinámicamente
        document.getElementById("buscarPaciente").addEventListener("keyup", function() {
            let query = this.value;
            if (query.length > 1) {
                fetch("../php/buscar_paciente.php?q=" + query)
                    .then(res => res.json())
                    .then(data => {
                        let resultados = "";
                        data.forEach(p => {
                            resultados += `
                      <button type="button" class="list-group-item list-group-item-action"
                        onclick="seleccionarPaciente('${p.id_paciente}', '${p.nombre} ${p.apellido}', '${p.codigo}', '${p.fecha_nacimiento}')">
                        <i class="bi bi-person"></i> ${p.nombre} ${p.apellido} 
                        <span class="text-muted">[${p.codigo}]</span>  
                        <span class="text-muted">[${p.fecha_nacimiento}]</span>
                      </button>
                    `;
                        });
                        document.getElementById("resultadosPacientes").innerHTML = resultados;
                    })
                    .catch(err => console.error("Error buscando pacientes:", err));
            } else {
                document.getElementById("resultadosPacientes").innerHTML = "";
            }
        });

        // Seleccionar paciente
        function seleccionarPaciente(id, nombreCompleto, codigo, fecha_nacimiento) {
            document.getElementById("idPaciente").value = id;
            let seleccionado = document.getElementById("pacienteSeleccionado");
            seleccionado.classList.remove("d-none");
            seleccionado.innerHTML = `
        <i class="bi bi-check-circle-fill"></i> 
        Paciente seleccionado: <b>${nombreCompleto}</b> 
        <span class="badge bg-light text-dark">Código: ${codigo}</span> 
        <span class="badge bg-light text-dark">F.Nacimiento: ${fecha_nacimiento}</span>
        <button type="button" class="btn btn-sm btn-outline-danger float-end" onclick="quitarPaciente()">
          <i class="bi bi-x-circle"></i> Quitar
        </button>
      `;
            document.getElementById("resultadosPacientes").innerHTML = "";
            document.getElementById("buscarPaciente").value = "";
        }

        // Quitar paciente seleccionado
        function quitarPaciente() {
            document.getElementById("idPaciente").value = "";
            let seleccionado = document.getElementById("pacienteSeleccionado");
            seleccionado.classList.add("d-none");
            seleccionado.innerHTML = "";
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modalEl = document.getElementById('modalConsulta');
            const modal = new bootstrap.Modal(modalEl); // Crear solo una vez
            const form = document.getElementById('formConsulta');

            // Botones de editar
            document.querySelectorAll('.btn-editar-consulta').forEach(btn => {
                btn.addEventListener('click', () => {
                    // Mostrar modal
                    modal.show();

                    // Cambiar título y acción
                    document.getElementById('modalConsultaLabel').innerHTML = '<i class="bi bi-pencil-square me-2"></i> Editar Consulta';
                    form.action = './actualizar_consulta.php';

                    // Mostrar paciente seleccionado
                    const pacienteNombre = btn.dataset.paciente;
                    const pacienteDiv = document.getElementById('pacienteSeleccionado');
                    pacienteDiv.textContent = pacienteNombre;
                    pacienteDiv.classList.remove('d-none');

                    // Ocultar buscador de paciente
                    document.getElementById('buscadorPacienteContainer').style.display = 'none';
                    document.getElementById('idPaciente').value = btn.dataset.id;

                    // Rellenar campos de la consulta
                    form.querySelector('#id_consulta').value = btn.dataset.id_consulta;
                    form.querySelector('#tipoConsulta').value = btn.dataset.tipo;
                    form.querySelector('#motivo').value = btn.dataset.motivo;
                    form.querySelector('input[name="temperatura"]').value = btn.dataset.temperatura;
                    form.querySelector('input[name="presion_arterial"]').value = btn.dataset.presion;
                    form.querySelector('input[name="tension_arterial"]').value = btn.dataset.tension;
                    form.querySelector('input[name="saturacion_oxigeno"]').value = btn.dataset.saturacion;
                    form.querySelector('input[name="pulso"]').value = btn.dataset.pulso;
                    form.querySelector('input[name="peso"]').value = btn.dataset.peso;
                    form.querySelector('input[name="talla"]').value = btn.dataset.talla;

                    // Rellenar detalle de consulta
                    form.querySelector('input[name="orina"]').value = btn.dataset.orina;
                    form.querySelector('input[name="defeca"]').value = btn.dataset.defeca;
                    form.querySelector('input[name="horas_sueno"]').value = btn.dataset.horas_sueno;
                    form.querySelector('textarea[name="transfusiones_sanguineas"]').value = btn.dataset.transfusiones;
                    form.querySelector('textarea[name="antecedentes_familiares"]').value = btn.dataset.antecedentesFamiliares;
                    form.querySelector('textarea[name="antecedentes_conyuge"]').value = btn.dataset.antecedentesConyuge;
                    form.querySelector('textarea[name="alergias"]').value = btn.dataset.alergias;
                    form.querySelector('textarea[name="operaciones"]').value = btn.dataset.operaciones;
                });
            });

            // Restaurar modal al cerrar
            modalEl.addEventListener('hidden.bs.modal', () => {
                form.reset();
                form.action = '../php/actualizar_consulta.php';
                document.getElementById('buscadorPacienteContainer').style.display = 'block';
                const pacienteDiv = document.getElementById('pacienteSeleccionado');
                pacienteDiv.classList.add('d-none');
                pacienteDiv.textContent = '';
                document.getElementById('modalConsultaLabel').innerHTML = '<i class="bi bi-journal-medical me-2"></i> Nueva Consulta';
            });
        });
    </script>


    <script>
        // ver consulta sin editar
        document.addEventListener("DOMContentLoaded", function() {
            const modal = document.getElementById("modalVerConsulta");

            modal.addEventListener("show.bs.modal", function(event) {
                const button = event.relatedTarget;

                document.getElementById("verPaciente").innerText = button.getAttribute("data-paciente");
                document.getElementById("verFecha").innerText = button.getAttribute("data-fecha");
                document.getElementById("verTipo").innerText = button.getAttribute("data-tipo");
                document.getElementById("verMotivo").innerText = button.getAttribute("data-motivo");
                document.getElementById("verDiagnostico").innerText = button.getAttribute("data-diagnostico");

                document.getElementById("verTemperatura").innerText = button.getAttribute("data-temperatura") + " °C";
                document.getElementById("verPresion").innerText = button.getAttribute("data-presion");
                document.getElementById("verTension").innerText = button.getAttribute("data-tension");
                document.getElementById("verSaturacion").innerText = button.getAttribute("data-saturacion") + " %";
                document.getElementById("verPulso").innerText = button.getAttribute("data-pulso") + " lpm";
                document.getElementById("verPeso").innerText = button.getAttribute("data-peso") + " kg";
                document.getElementById("verTalla").innerText = button.getAttribute("data-talla") + " cm";
                document.getElementById("verIMC").innerText = button.getAttribute("data-imc");

                document.getElementById("verOrina").innerText = button.getAttribute("data-orina");
                document.getElementById("verDefeca").innerText = button.getAttribute("data-defeca");
                document.getElementById("verHorasSueno").innerText = button.getAttribute("data-horas_sueno");
                document.getElementById("verTransfusiones").innerText = button.getAttribute("data-transfusiones");
                document.getElementById("verAntecedentesFamiliares").innerText = button.getAttribute("data-antecedentes_familiares");
                document.getElementById("verAntecedentesConyuge").innerText = button.getAttribute("data-antecedentes_conyuge");
                document.getElementById("verAlergias").innerText = button.getAttribute("data-alergias");
                document.getElementById("verOperaciones").innerText = button.getAttribute("data-operaciones");

                const pagado = button.getAttribute("data-pagado");
                const badge = document.getElementById("verPagado");
                if (pagado == "1") {
                    badge.innerText = "Pagado";
                    badge.className = "badge bg-success";
                } else {
                    badge.innerText = "Pendiente";
                    badge.className = "badge bg-danger";
                }
            });
        });
    </script>


    <?php
    include_once '../componentes/footer_usuario.php';
    ?>