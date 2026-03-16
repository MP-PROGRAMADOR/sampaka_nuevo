<?php
// agenda.php
include '../componentes/header_usuario.php';

// 1. Configuración de página
$page_title = 'Mi Agenda';
$page_name = 'Mi Agenda';

// 2. Verificación de Sesión y obtención de ID Médico
$id_usuario_sesion = $_SESSION['id_usuario'] ?? null;

if (!$id_usuario_sesion) {
    header("Location: ../login.php");
    exit();
}

try {
    // 1. Verificar qué ID de usuario tenemos en sesión
    if (!isset($_SESSION['id_usuario'])) {
        die("Error: No hay sesión activa.");
    }

    $stmt_user = $pdo->prepare("SELECT id_personal FROM usuarios WHERE id_usuario = :id_u");
    $stmt_user->execute([':id_u' => $_SESSION['id_usuario']]);
    $user_map = $stmt_user->fetch(PDO::FETCH_ASSOC);

    // 2. ¿Existe el médico?
    $id_medico = (!empty($user_map['id_personal'])) ? $user_map['id_personal'] : 0;

    if ($id_medico == 0) {
        echo "<div class='alert alert-warning m-4'>Atención: Tu usuario (ID: {$_SESSION['id_usuario']}) no tiene un ID de Personal vinculado. Revisa la tabla 'usuarios'.</div>";
    }

    // 3. Consulta de hoy (Simplificada para asegurar que traiga datos)
    $sql_hoy = "SELECT c.id_consulta as id, 
                       DATE_FORMAT(c.fecha_consulta, '%H:%i') as hora, 
                       CONCAT(p.nombre, ' ', p.apellido) as paciente, 
                       c.motivo, 
                       c.tipo_consulta as estado 
                FROM consultas c
                INNER JOIN pacientes p ON c.id_paciente = p.id_paciente
                WHERE c.id_medico = :id_m 
                AND DATE(c.fecha_consulta) = CURDATE()
                ORDER BY c.fecha_consulta ASC";

    $stmt = $pdo->prepare($sql_hoy);
    $stmt->execute([':id_m' => $id_medico]);
    $res_hoy = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $citas_hoy = [];
    foreach ($res_hoy as $r) {
        $color = match ($r['estado']) {
            'Urgencias' => 'danger',
            'Pediatría' => 'info',
            'Cardiología' => 'warning',
            default     => 'success'
        };

        $citas_hoy[] = [
            'id' => $r['id'],
            'hora' => $r['hora'],
            'paciente' => $r['paciente'],
            'motivo' => $r['motivo'],
            'estado' => $r['estado'],
            'color' => $color
        ];
    }

    // 4. CARGAR CITAS DE LA PRÓXIMA SEMANA (Excluyendo hoy)
    $sql_semana = "SELECT c.id_consulta as id, 
                          DATE_FORMAT(c.fecha_consulta, '%d/%m/%Y') as dia, 
                          DATE_FORMAT(c.fecha_consulta, '%H:%i') as hora, 
                          CONCAT(p.nombre, ' ', p.apellido) as paciente, 
                          c.motivo,
                          c.tipo_consulta as estado 
                   FROM consultas c
                   INNER JOIN pacientes p ON c.id_paciente = p.id_paciente
                   WHERE c.id_medico = :id_m 
                   AND DATE(c.fecha_consulta) > CURDATE() 
                   AND DATE(c.fecha_consulta) <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                   ORDER BY c.fecha_consulta ASC";

    $stmt = $pdo->prepare($sql_semana);
    $stmt->execute([':id_m' => $id_medico]);
    $res_semana = $stmt->fetchAll();

    $citas_proxima_semana = [];
    foreach ($res_semana as $r) {
        $color = match ($r['estado']) {
            'Urgencias' => 'danger',
            'Pediatría' => 'info',
            default     => 'success'
        };

        $citas_proxima_semana[] = [
            'id' => $r['id'],
            'dia' => $r['dia'],
            'hora' => $r['hora'],
            'paciente' => $r['paciente'],
            'motivo' => $r['motivo'],
            'estado' => $r['estado'],
            'color' => $color
        ];
    }

    // 5. DATOS PARA EL GRÁFICO (Carga mensual por semanas)
    $sql_chart = "SELECT WEEK(fecha_consulta, 1) as num_semana, COUNT(*) as total 
                  FROM consultas 
                  WHERE id_medico = :id_m 
                  AND MONTH(fecha_consulta) = MONTH(CURDATE())
                  AND YEAR(fecha_consulta) = YEAR(CURDATE())
                  GROUP BY WEEK(fecha_consulta, 1)";

    $stmt = $pdo->prepare($sql_chart);
    $stmt->execute([':id_m' => $id_medico]);
    $chart_data = $stmt->fetchAll();

    $labels_semanas = [];
    $valores_semanas = [];
    foreach ($chart_data as $index => $data) {
        $labels_semanas[] = "Semana " . ($index + 1);
        $valores_semanas[] = $data['total'];
    }
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    include '../componentes/footer_usuario.php';
    exit();
}
?>


<body>
    <?php include '../componentes/slider_usuario.php'; ?>

    <div class="main-content">
        <h1 class="mb-4 fw-light text-primary"><i class="bi bi-calendar-range-fill me-2"></i> Mi Agenda</h1>

        <div class="card p-4 shadow-sm">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0 fw-bold">Gestión de Agenda</h5>

            </div>

            <ul class="nav nav-tabs mb-4" id="agendaTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="hoy-tab" data-bs-toggle="tab" data-bs-target="#hoy" type="button" role="tab">
                        <i class="bi bi-calendar-check me-1"></i> Citas de Hoy (<?= count($citas_hoy) ?>)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="semana-tab" data-bs-toggle="tab" data-bs-target="#semana" type="button" role="tab">
                        <i class="bi bi-calendar-week me-1"></i> Próxima Semana (<?= count($citas_proxima_semana) ?>)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="calendario-tab" data-bs-toggle="tab" data-bs-target="#calendario" type="button" role="tab">
                        <i class="bi bi-bar-chart-line me-1"></i> Carga Mensual
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="agendaTabsContent">
                <!-- Citas de Hoy -->

                <div class="tab-pane fade show active" id="hoy" role="tabpanel">
                    <?php if (empty($citas_hoy)): ?>
                        <div class="alert alert-light text-center mt-3 border" role="alert">
                            <i class="bi bi-emoji-smile fs-3 d-block mb-2"></i> No tienes citas para hoy.
                        </div>
                    <?php else: ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($citas_hoy as $cita): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                    <div>
                                        <h5 class="mb-1 text-primary"><?= $cita['hora'] ?></h5>
                                        <div class="fw-bold"><?= htmlspecialchars($cita['paciente']) ?></div>
                                        <small class="text-muted"><i class="bi bi-journal-text me-1"></i> <?= htmlspecialchars($cita['motivo']) ?></small>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-<?= $cita['color'] ?> me-3"><?= $cita['estado'] ?></span>
                                        <a href="historial_clinico.php?id=<?= $cita['id'] ?>" class="btn btn-sm btn-info me-2 text-white" title="Historial">
                                            <i class="bi bi-person-lines-fill"></i>
                                        </a>

                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
                <!-- Proxima Semana -->
                <div class="tab-pane fade" id="semana" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Día</th>
                                    <th>Hora</th>
                                    <th>Paciente</th>
                                    <th>Estado</th>
                                    <th>Motivo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($citas_proxima_semana)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No hay citas para los próximos 7 días.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($citas_proxima_semana as $cita_s): ?>
                                        <tr>
                                            <td class="text-capitalize"><?= $cita_s['dia'] ?></td>
                                            <td class="fw-bold text-primary"><?= $cita_s['hora'] ?></td>
                                            <td><?= htmlspecialchars($cita_s['paciente']) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $cita_s['color'] ?>"><?= $cita_s['estado'] ?></span>
                                            </td>
                                            <td><small class="text-muted"><?= htmlspecialchars($cita_s['motivo']) ?></small></td>

                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="calendario" role="tabpanel">
                    <div style="height: 400px;" class="p-3">
                        <canvas id="cargaMensualChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="modalNuevaCita" tabindex="-1" aria-labelledby="modalNuevaCitaLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content shadow-lg border-0 rounded-4">

                        <div class="d-flex align-items-center justify-content-between px-4 py-2 bg-light border-bottom rounded-top-4">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width:35px; height:35px;">
                                    <i class="bi bi-calendar-event fs-6"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-primary">Programación Médica</h6>
                                    <small class="text-muted">Dr. <?= $_SESSION['nombre_doctor'] ?? 'Especialista' ?></small>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>

                        <div class="modal-header bg-primary text-white border-0 py-3">
                            <h5 class="modal-title" id="modalNuevaCitaLabel">
                                <i class="bi bi-person-plus me-2"></i> Registrar Cita
                            </h5>
                        </div>

                        <form action="procesar_cita.php" method="POST">
                            <div class="modal-body p-4">
                                <div class="mb-4">
                                    <label class="form-label fw-bold"><i class="bi bi-search me-1"></i> Buscar Paciente</label>
                                    <input type="text" id="buscarPacienteCita" class="form-control form-control-lg shadow-sm border-primary-subtle" placeholder="Nombre o Apellido..." autocomplete="off">
                                    <div id="resultadosPacientes" class="list-group mt-2 shadow-sm rounded-3 overflow-auto" style="max-height: 150px;"></div>
                                </div>

                                <div id="pacienteSeleccionado" class="alert alert-info d-none d-flex justify-content-between align-items-center rounded-3 border-0 shadow-sm mb-4">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-check-circle-fill me-2 fs-4"></i>
                                        <div>
                                            <small class="d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Paciente Seleccionado:</small>
                                            <span id="nombrePacienteLabel" class="fw-bold"></span>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-link text-danger" id="btnQuitarPaciente"><i class="bi bi-trash"></i></button>
                                    <input type="hidden" name="id_paciente" id="id_paciente_input" required>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Fecha</label>
                                        <input type="date" name="fecha_consulta" class="form-control shadow-sm" value="<?= date('Y-m-d') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Hora</label>
                                        <input type="time" name="hora_consulta" class="form-control shadow-sm" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold">Tipo de Consulta</label>
                                        <select name="tipo_consulta" class="form-select shadow-sm">
                                            <option value="General">Consulta General</option>
                                            <option value="Pediatría">Pediatría</option>
                                            <option value="Urgencias">Urgencias</option>
                                            <option value="Control">Control/Seguimiento</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold">Motivo</label>
                                        <textarea name="motivo" class="form-control shadow-sm" rows="3" placeholder="Breve descripción del motivo..." required></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer bg-light border-0 rounded-bottom-4">
                                <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold shadow-sm">
                                    <i class="bi bi-check-circle me-1"></i> Confirmar Cita
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>




    <script>
        // Buscador de Pacientes en el modal de citas
        document.getElementById('buscarPacienteCita').addEventListener('input', function() {
            let query = this.value;
            let resultados = document.getElementById('resultadosPacientes');

            if (query.length > 2) {
                fetch(`buscar_pacientes_ajax.php?q=${query}`)
                    .then(response => response.json())
                    .then(data => {
                        let html = '';
                        if (data.length > 0) {
                            data.forEach(p => {
                                html += `
                        <button type="button" class="list-group-item list-group-item-action py-2" 
                                onclick="seleccionarPacienteCita(${p.id_paciente}, '${p.nombre} ${p.apellido}')">
                            <i class="bi bi-person me-2"></i> ${p.nombre} ${p.apellido}
                            <small class="text-muted d-block" style="font-size:0.75rem;">Código: ${p.codigo}</small>
                        </button>`;
                            });
                        } else {
                            html = '<div class="list-group-item text-muted">No se encontraron resultados</div>';
                        }
                        resultados.innerHTML = html;
                    });
            } else {
                resultados.innerHTML = '';
            }
        });

        function seleccionarPacienteCita(id, nombre) {
            document.getElementById('id_paciente_input').value = id;
            document.getElementById('nombrePacienteLabel').textContent = nombre;
            document.getElementById('pacienteSeleccionado').classList.remove('d-none');
            document.getElementById('buscarPacienteCita').parentElement.classList.add('d-none');
            document.getElementById('resultadosPacientes').innerHTML = '';
        }

        document.getElementById('btnQuitarPaciente').addEventListener('click', function() {
            document.getElementById('pacienteSeleccionado').classList.add('d-none');
            document.getElementById('buscarPacienteCita').parentElement.classList.remove('d-none');
            document.getElementById('id_paciente_input').value = '';
            document.getElementById('buscarPacienteCita').value = '';
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Gráfico dinámico
        const ctx = document.getElementById('cargaMensualChart');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($labels_semanas) ?>,
                datasets: [{
                    label: 'Citas registradas',
                    data: <?= json_encode($valores_semanas) ?>,
                    backgroundColor: 'rgba(13, 110, 253, 0.8)',
                    borderRadius: 5,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    </script>

    <?php include_once '../componentes/footer_usuario.php'; ?>


</body>