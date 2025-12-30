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

<h1 class="mb-4 fw-light text-primary"><i class="bi bi-people-fill me-2"></i> Mis Pacientes</h1>

<div class="card p-4">
    <div class="card-header bg-white border-0 ps-0 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 mt-1">Listado de Pacientes Asignados</h5>
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
    c.temperatura,
    c.presion_arterial,
    c.tension_arterial,
    c.saturacion_oxigeno,
    c.pulso,
    c.peso,
    c.talla,
    c.motivo,
    c.IMC,
    c.fecha_registro,
    c.pagado,
    c.precio,
    c.explo_fisica,



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


        <table id="tablaPacientes" class="table table-striped table-hover align-middle mb-0 mt-2 nowrap" style="width:100%">
            <thead>
                <tr class="table-primary">
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Última Consulta</th>
                    <th>Acciones</th>
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
                                <?php if ($paciente['fecha_consulta']): ?>
                                    <?= date('d/m/Y H:i', strtotime($paciente['fecha_consulta'])) ?>
                                <?php else: ?>
                                    <span class="text-muted">N/A</span>
                                <?php endif; ?>
                            </td>

                            <td class="d-flex">






                                <button class="btn btn-sm btn-info btn-ver-consulta m-1" data-bs-toggle="modal"
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
                        <button type="submit" class="btn btn-warning text-dark"><i class="bi bi-check-circle-fill me-1"></i> Guardar Cita</button>
                    </div>
                </form>
            </div>
        </div>
    </div>






 <div class="modal fade" id="modalVerConsulta" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content shadow-lg rounded-4">

      <!-- HEADER -->
      <div class="modal-header bg-primary text-white rounded-top-4">
        <h5 class="modal-title fw-bold">
          <i class="bi bi-clipboard2-pulse me-2"></i> Detalle de la Consulta Médica
        </h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <!-- BODY -->
      <div class="modal-body bg-light">

        <!-- DATOS GENERALES -->
        <div class="card mb-4 border-0 shadow-sm">
          <div class="card-header bg-white fw-bold text-primary">
            <i class="bi bi-person-badge me-2"></i> Datos Generales
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="text-muted">Paciente</label>
                <div class="fw-semibold" id="mPaciente"></div>
              </div>
              <div class="col-md-3">
                <label class="text-muted">Fecha</label>
                <div id="mFecha"></div>
              </div>
              <div class="col-md-3">
                <label class="text-muted">Tipo</label>
                <div class="badge bg-info text-dark" id="mTipo"></div>
              </div>

              <div class="col-md-6">
                <label class="text-muted">Motivo</label>
                <div id="mMotivo"></div>
              </div>
              <div class="col-md-3">
                <label class="text-muted">Precio</label>
                <div class="fw-bold text-success" id="mPrecio"></div>
              </div>
              <div class="col-md-3">
                <label class="text-muted">Estado de Pago</label>
                <div id="mPagado"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- SIGNOS VITALES -->
        <div class="card mb-4 border-0 shadow-sm">
          <div class="card-header bg-white fw-bold text-danger">
            <i class="bi bi-heart-pulse me-2"></i> Signos Vitales
          </div>
          <div class="card-body">
            <div class="row g-3 text-center">
              <div class="col-md-3">
                <small class="text-muted">Temperatura</small>
                <div class="fw-semibold" id="mTemp"></div>
              </div>
              <div class="col-md-3">
                <small class="text-muted">Presión</small>
                <div id="mPresion"></div>
              </div>
              <div class="col-md-3">
                <small class="text-muted">Tensión</small>
                <div id="mTension"></div>
              </div>
              <div class="col-md-3">
                <small class="text-muted">Saturación O₂</small>
                <div id="mSaturacion"></div>
              </div>

              <div class="col-md-3">
                <small class="text-muted">Pulso</small>
                <div id="mPulso"></div>
              </div>
              <div class="col-md-3">
                <small class="text-muted">Peso</small>
                <div id="mPeso"></div>
              </div>
              <div class="col-md-3">
                <small class="text-muted">Talla</small>
                <div id="mTalla"></div>
              </div>
              <div class="col-md-3">
                <small class="text-muted">IMC</small>
                <div class="fw-bold" id="mIMC"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- ANTECEDENTES -->
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white fw-bold text-secondary">
            <i class="bi bi-journal-medical me-2"></i> Antecedentes Clínicos
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-4">
                <strong>Orina:</strong> <span id="mOrina"></span>
              </div>
              <div class="col-md-4">
                <strong>Defeca:</strong> <span id="mDefeca"></span>
              </div>
              <div class="col-md-4">
                <strong>Horas de sueño:</strong> <span id="mSueno"></span>
              </div>

              <div class="col-md-4">
                <strong>Transfusiones:</strong> <span id="mTransfusiones"></span>
              </div>
              <div class="col-md-4">
                <strong>Ant. Familiares:</strong> <span id="mAF"></span>
              </div>
              <div class="col-md-4">
                <strong>Ant. Cónyuge:</strong> <span id="mAC"></span>
              </div>

              <div class="col-md-6">
                <strong>Alergias:</strong> <span id="mAlergias"></span>
              </div>
              <div class="col-md-6">
                <strong>Operaciones:</strong> <span id="mOperaciones"></span>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- FOOTER -->
      <div class="modal-footer bg-white">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">
          <i class="bi bi-x-circle me-1"></i> Cerrar
        </button>
      </div>

    </div>
  </div>
</div>






<script>
document.addEventListener('click', function (e) {

    const btn = e.target.closest('.btn-ver-consulta');
    if (!btn) return;

    const d = btn.dataset;

    document.getElementById('mPaciente').textContent = d.paciente || '—';
    document.getElementById('mFecha').textContent = d.fecha || '—';
    document.getElementById('mTipo').textContent = d.tipo || '—';
    document.getElementById('mMotivo').textContent = d.motivo || '—';

    document.getElementById('mPrecio').textContent = d.precio 
        ? d.precio + ' FCFA' : '—';

    document.getElementById('mPagado').innerHTML =
        d.pagado == 1
            ? '<span class="badge bg-success">Pagado</span>'
            : '<span class="badge bg-danger">No pagado</span>';

    document.getElementById('mTemp').textContent = d.temperatura || '—';
    document.getElementById('mPresion').textContent = d.presion || '—';
    document.getElementById('mTension').textContent = d.tension || '—';
    document.getElementById('mSaturacion').textContent = d.saturacion || '—';
    document.getElementById('mPulso').textContent = d.pulso || '—';
    document.getElementById('mPeso').textContent = d.peso || '—';
    document.getElementById('mTalla').textContent = d.talla || '—';
    document.getElementById('mIMC').textContent = d.imc || '—';

    document.getElementById('mOrina').textContent = d.orina || '—';
    document.getElementById('mDefeca').textContent = d.defeca || '—';
    document.getElementById('mSueno').textContent = d.horas_sueno || '—';
    document.getElementById('mTransfusiones').textContent = d.transfusiones || '—';
    document.getElementById('mAF').textContent = d.antecedentes_familiares || '—';
    document.getElementById('mAC').textContent = d.antecedentes_conyuge || '—';
    document.getElementById('mAlergias').textContent = d.alergias || '—';
    document.getElementById('mOperaciones').textContent = d.operaciones || '—';

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