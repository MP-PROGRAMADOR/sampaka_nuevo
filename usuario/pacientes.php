<?php
session_start();
// Asegúrate de que esta ruta sea correcta para tu conexión
require_once "../config/conexion.php";

// 1. Configuración de la página para el header
$page_title = 'Gestión de Pacientes';
$page_name = 'Mis Pacientes';

// 2. Obtener el ID del doctor (ASUMIDO: usa 'id_personal' de la sesión)
if (!isset($_SESSION['id_personal'])) {
    $doctor_id = 1;
} else {
    $doctor_id = $_SESSION['id_personal'];
}

// Inicializar la variable de resultados
$resultados = [];
$error_message = null;


// Ejemplo de datos simulados (reemplazar con la lógica de tu conexión a DB)
try {
    $resultados = [
        ['id_paciente' => 101, 'paciente_nombre' => 'Juan', 'paciente_apellido' => 'Pérez', 'ultima_consulta' => '2025-11-20'],
        ['id_paciente' => 102, 'paciente_nombre' => 'María', 'paciente_apellido' => 'Gómez', 'ultima_consulta' => '2025-12-10'],
        ['id_paciente' => 103, 'paciente_nombre' => 'Luis', 'paciente_apellido' => 'Rodríguez', 'ultima_consulta' => '2025-10-01'],
    ];
} catch (Exception $e) {
    $error_message = "Error al cargar pacientes: " . $e->getMessage();
}

// 4. Incluir el encabezado (abre HTML, Sidebar y .main-content)
include 'header_doctores.php';
?>

<style>
    .card {
        border: none;
        border-radius: 12px;
    }

    .table thead th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }

    .btn-action {
        transition: all 0.2s;
        border-radius: 8px;
    }

    .btn-action:hover {
        transform: translateY(-2px);
    }

    .search-container .input-group-text {
        border-radius: 10px 0 0 10px;
    }

    .search-container .form-control {
        border-radius: 0 10px 10px 0;
    }

    .badge-date {
        font-size: 0.9rem;
        padding: 6px 12px;
        border-radius: 6px;
    }
</style>

<div class="card p-4">
    <div class="card-header bg-white border-0 ps-0 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 mt-5">Listado de Pacientes Asignados</h5>
        <div class="d-flex">
            <input type="text" class="form-control me-2 mb-5" placeholder="Buscar paciente...">
            <!-- <button class="btn btn-success"><i class="bi bi-plus-circle me-1"></i> Nuevo Paciente</button> -->
        </div>
    </div>

    <?php if ($error_message): ?>
        <div class="alert alert-danger mt-3"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>

    <div class="table-responsive mt-3">


        <?php
        require_once "../config/conexion.php"; // ajusta la ruta si es necesario

        $sql = "
   SELECT 
    p.id_paciente,
    p.nombre   AS paciente_nombre,
    p.apellido AS paciente_apellido,

    c.id_consulta,
    c.fecha_consulta,
    c.tipo_consulta,
    c.diagnostico,
    c.motivo,
    c.pagado,
    c.precio,
    presion_arterial,
    tension_arterial,
    saturacion_oxigeno,
    pulso,
    peso,
    talla,
    IMC,
    temperatura,
  






    dc.id_detalle,
    dc.orina,
    dc.defeca,
    dc.horas_sueno,
    dc.antecedentes_familiares,
    dc.antecedentes_conyuge,
    dc.alergias,
    dc.operaciones,
    dc.transfuciones,

    ult.ultima_consulta
FROM pacientes p

/* Subconsulta para obtener la última consulta por paciente */
LEFT JOIN (
    SELECT 
        id_paciente,
        MAX(fecha_consulta) AS ultima_consulta
    FROM consultas
    GROUP BY id_paciente
) ult 
    ON ult.id_paciente = p.id_paciente

/* Traemos solo la consulta más reciente */
LEFT JOIN consultas c 
    ON c.id_paciente = p.id_paciente
   AND c.fecha_consulta = ult.ultima_consulta

/* Detalle de esa consulta */
LEFT JOIN detalle_consulta dc 
    ON dc.id_consulta = c.id_consulta

ORDER BY ult.ultima_consulta DESC;

";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>





        <table id="tablaPacientes" class="table table-striped table-hover align-middle mb-0 nowrap" style="width:100%">
            <thead>
                <?php   $rol=$usuario_rol;   ?>
                <tr class="table-primary">
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Última Consulta</th>
                    <th>Acciones</th>
                    <th>  <?=    $rol;   ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($resultados)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            No se encontraron pacientes asignados.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($resultados as $paciente): ?>
                        <tr>
                            <td><?= htmlspecialchars($paciente['id_paciente']) ?></td>
                            <td><?= htmlspecialchars($paciente['paciente_nombre']) ?></td>
                            <td><?= htmlspecialchars($paciente['paciente_apellido']) ?></td>
                            <td>
                                <?php if ($paciente['ultima_consulta']): ?>
                                    <?= date('d/m/Y H:i', strtotime($paciente['ultima_consulta'])) ?>
                                <?php else: ?>
                                    <span class="text-muted">N/A</span>
                                <?php endif; ?>
                            </td>

                           

                            <td class="d-flex">


                             <button class="btn btn-sm btn-info btn-ver-consulta me-2" data-bs-toggle="modal"
                                    data-bs-target="#modalVerConsulta" data-id="<?= $paciente['id_consulta'] ?>"
                                    data-paciente="<?= htmlspecialchars($paciente['paciente_nombre'] . ' ' . $paciente['paciente_apellido']) ?>"
                                    data-tipo="<?= htmlspecialchars($paciente['tipo_consulta']) ?>"
                                    data-motivo="<?= htmlspecialchars($paciente['motivo']) ?>"
                                    data-fecha="<?= $paciente['fecha_consulta'] ?>"
                                    data-precio="<?= $paciente['precio'] ?>"
                                    data-pagado="<?= $paciente['pagado'] ?>"
                                    data-temperatura="<?= $paciente['temperatura'] ?>"
                                    data-presion="<?= $paciente['presion_arterial'] ?>"
                                    data-tension="<?= $paciente['tension_arterial'] ?>"
                                    data-saturacion="<?= $paciente['saturacion_oxigeno'] ?>"
                                    data-pulso="<?= $paciente['pulso'] ?>" data-peso="<?= $paciente['peso'] ?>"
                                    data-talla="<?= $paciente['talla'] ?>" data-imc="<?= $paciente['IMC'] ?>"
                                    data-orina="<?= htmlspecialchars($paciente['orina']) ?>"
                                    data-defeca="<?= htmlspecialchars($paciente['defeca']) ?>"
                                    data-horas_sueno="<?= $paciente['horas_sueno'] ?>"
                                    data-transfusiones="<?= htmlspecialchars($paciente['transfuciones']) ?>"
                                    data-antecedentes_familiares="<?= htmlspecialchars($paciente['antecedentes_familiares']) ?>"
                                    data-antecedentes_conyuge="<?= htmlspecialchars($paciente['antecedentes_conyuge']) ?>"
                                    data-alergias="<?= htmlspecialchars($paciente['alergias']) ?>"
                                    data-operaciones="<?= htmlspecialchars($paciente['operaciones']) ?>">
                                    <i class="bi bi-eye"></i>
                                </button>



                                <a href="historial_clinico.php?id=<?= htmlspecialchars($paciente['id_paciente']) ?>"
                                    class="btn btn-sm btn-info me-2" title="Ver Historial">
                                    <i class="bi bi-person-lines-fill"></i> Historial
                                </a>

                                <a href="prescripciones.php?paciente_id=<?= htmlspecialchars($paciente['id_paciente']) ?>"
                                    class="btn btn-sm btn-primary me-2" title="Órdenes y Recetas">
                                    <i class="bi bi-receipt-cutoff"></i> Tratamientos
                                </a>

                               

                                <button type="button"
                                    class="btn btn-sm btn-warning text-dark"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalCrearCita"
                                    data-paciente-id="<?= htmlspecialchars($paciente['id_paciente']) ?>"
                                    data-paciente-nombre="<?= htmlspecialchars($paciente['paciente_nombre'] . ' ' . $paciente['paciente_apellido']) ?>"
                                    title="Crear Cita">
                                    <i class="bi bi-calendar-plus"></i> Cita
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

    </div>

</div>
<div class="mt-3">

    <div class="modal fade" id="modalCrearCita" tabindex="-1" aria-labelledby="modalCrearCitaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formCrearCita" action="procesar_cita.php" method="POST">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title" id="modalCrearCitaLabel"><i class="bi bi-calendar-plus me-2"></i>Nueva Cita para: <span id="pacienteNombreModal" class="fw-bold"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="paciente_id" id="inputPacienteId">

                        <div class="mb-3">
                            <label for="fechaCita" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="fechaCita" name="fecha_cita" required>
                        </div>
                        <div class="mb-3">
                            <label for="horaCita" class="form-label">Hora</label>
                            <input type="time" class="form-control" id="horaCita" name="hora_cita" required>
                        </div>
                        <div class="mb-3">
                            <label for="motivoCita" class="form-label">Motivo de la Cita</label>
                            <textarea class="form-control" id="motivoCita" name="motivo_cita" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning text-dark"><i class="bi bi-check-lg"></i> Confirmar Cita</button>
                    </div>
                </form>
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

                    <!-- Pago -->
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-body">
                            <h6 class="fw-bold text-primary"><i class="bi bi-cash-coin"></i> Pago</h6>
                            <p><strong>Precio:</strong> XAF <span id="verPrecio"></span></p>
                            <p><strong>Estado:</strong>
                                <span id="verPagado" class="badge fs-6 px-3 py-2"></span>
                            </p>
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
                document.getElementById("verPrecio").innerText = parseFloat(button.getAttribute("data-precio")).toFixed(2);
            });
        });
    </script>








    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modalCrearCita = document.getElementById('modalCrearCita');
            modalCrearCita.addEventListener('show.bs.modal', function(event) {
                // Botón que disparó el modal
                var button = event.relatedTarget;

                // Extraer información del paciente de los atributos data-*
                var pacienteId = button.getAttribute('data-paciente-id');
                var pacienteNombre = button.getAttribute('data-paciente-nombre');

                // Actualizar el título y los campos ocultos del modal
                var modalTitle = modalCrearCita.querySelector('#pacienteNombreModal');
                var inputId = modalCrearCita.querySelector('#inputPacienteId');

                modalTitle.textContent = pacienteNombre;
                inputId.value = pacienteId;

                // Opcional: Limpiar fecha y motivo cada vez que se abre el modal
                modalCrearCita.querySelector('#fechaCita').value = '';
                modalCrearCita.querySelector('#horaCita').value = '';
                modalCrearCita.querySelector('#motivoCita').value = '';
            });
        });
    </script>

    <?php
    // 5. Incluir el pie de página (cierra .main-content, body y HTML)
    include '../componentes/footer_usuario.php';
    ?>
</div>