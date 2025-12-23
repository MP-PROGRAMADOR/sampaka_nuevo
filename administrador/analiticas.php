<?php
// Iniciar la sesión si aún no está iniciada para manejar mensajes

include_once '../componentes/header.php';
?>

<body>

    <div class="d-flex" id="wrapper">
        <?php include_once '../componentes/sidebar.php'; ?>

        <div id="content" class="p-4 bg-gray-100 flex-grow">
            <?php
            include_once '../componentes/barra_nav.php';
            ?>

            <div class="d-flex justify-content-end mb-3">

            </div>


            <div class="card shadow-sm rounded-xl">

                <div class="card-body">
                    <h5 class="card-title mb-3 fw-bold"> Lista de Analíticas</h5>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success fade-msg"><?= htmlspecialchars($_SESSION['success']); ?></div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger fade-msg"><?= htmlspecialchars($_SESSION['error']); ?></div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <?php
                        require_once "../config/conexion.php";

                        try {
                            $stmt = $pdo->query("
                SELECT 
                    a.id_analitica,
                    a.id_consulta,
                    a.id_paciente,
                    a.id_prueba,
                    a.resultado,
                    a.estado,
                    a.comentario,
                    a.fecha_registro,
                    a.pagado,
                    a.valores_referencia,
                    a.archivo,
                    p.nombre AS paciente_nombre,
                    p.apellido AS paciente_apellido,
                    p.codigo AS paciente_codigo,
                    pr.nombre AS prueba_nombre,
                    pr.precio AS prueba_precio,
                    u.username AS usuario
                FROM analiticas a
                LEFT JOIN pacientes p ON a.id_paciente = p.id_paciente
                LEFT JOIN pruebas_medicas pr ON a.id_prueba = pr.id_prueba
                LEFT JOIN usuarios u ON a.id_usuario = u.id_usuario
                ORDER BY a.id_paciente, a.fecha_registro DESC
            ");
                            $analiticas = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            // Agrupar por paciente y fecha (solo YYYY-MM-DD)
                            $grupo_analiticas = [];
                            foreach ($analiticas as $a) {
                                $fecha = substr($a['fecha_registro'], 0, 10); // tomar solo día
                                $paciente = $a['id_paciente'];
                                $grupo_analiticas[$paciente][$fecha][] = $a;
                            }
                        } catch (PDOException $e) {
                            echo "<div class='alert alert-danger'>Error al cargar analíticas: " . $e->getMessage() . "</div>";
                            $grupo_analiticas = [];
                        }
                        ?>

                        <table id="tablaPacientes" class="table table-striped table-hover align-middle mb-0 nowrap" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th>Paciente</th>
                                    <th>Código</th>
                                    <th>Pruebas</th>
                                    <th>Resultados</th>
                                    <th>Estado</th>
                                    <th>Pagado</th>
                                    <th>Comentario</th>
                                    <th>Archivo</th>
                                    <th>Registrado por</th>
                                    <th>Fecha Registro</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($grupo_analiticas)): ?>
                                    <?php foreach ($grupo_analiticas as $paciente_id => $fechas): ?>
                                        <?php foreach ($fechas as $fecha => $analiticas_fecha): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($analiticas_fecha[0]['paciente_nombre'] . ' ' . $analiticas_fecha[0]['paciente_apellido']) ?></td>
                                                <td><?= htmlspecialchars($analiticas_fecha[0]['paciente_codigo']) ?></td>
                                                <td>
                                                    <?php foreach ($analiticas_fecha as $a) echo htmlspecialchars($a['prueba_nombre']) . '<br>'; ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">Resultados</span>
                                                </td>
                                                <td>
                                                    <?php foreach ($analiticas_fecha as $a) echo htmlspecialchars($a['estado']) . '<br>'; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $pagado_total = true;
                                                    foreach ($analiticas_fecha as $a) {
                                                        if ((int)$a['pagado'] === 0) {
                                                            $pagado_total = false;
                                                            break;
                                                        }
                                                    }
                                                    echo $pagado_total ? '<span class="badge bg-success">Pagado</span>' : '<span class="badge bg-danger">No Pagado</span>';
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php foreach ($analiticas_fecha as $a) echo htmlspecialchars($a['comentario']) . '<br>'; ?>
                                                </td>
                                                <td>
                                                    <?php foreach ($analiticas_fecha as $a) {
                                                        if (!empty($a['archivo'])) {
                                                            echo "<a href='../uploads/" . htmlspecialchars($a['archivo']) . "' target='_blank'>Ver archivo</a><br>";
                                                        } else {
                                                            echo "---<br>";
                                                        }
                                                    } ?>
                                                </td>
                                                <td>
                                                    <?php foreach ($analiticas_fecha as $a) echo htmlspecialchars($a['usuario'] ?? '---') . '<br>'; ?>
                                                </td>
                                                <td><?= htmlspecialchars($fecha) ?></td>
                                                <td>
                                                    <?php
                                                    // BOTÓN PAGAR
                                                    $mostrar_pagar = false;
                                                    foreach ($analiticas_fecha as $a) {
                                                        if ((int)$a['pagado'] === 0) {
                                                            $mostrar_pagar = true;
                                                            break;
                                                        }
                                                    }

                                                    if ($mostrar_pagar) {
                                                        echo '
        <button 
            class="btn btn-sm btn-success mb-1 btn-pagar"
            data-id-paciente="' . (int)$analiticas_fecha[0]['id_paciente'] . '"
            data-fecha="' . htmlspecialchars($fecha, ENT_QUOTES) . '"
            data-paciente="' . htmlspecialchars(
                                                            $analiticas_fecha[0]['paciente_nombre'] . ' ' .
                                                                $analiticas_fecha[0]['paciente_apellido'],
                                                            ENT_QUOTES
                                                        ) . '"
        >
            <i class="bi bi-cash"></i> Pagar
        </button>';
                                                    }

                                                    // BOTÓN VER RESULTADOS (solo si todas tienen resultado)
                                                    $mostrar_resultados = true;
                                                    foreach ($analiticas_fecha as $a) {
                                                        if (empty($a['resultado'])) {
                                                            $mostrar_resultados = false;
                                                            break;
                                                        }
                                                    }

                                                    if ($mostrar_resultados) {
                                                        echo '
        <button 
            class="btn btn-sm btn-primary mb-1 btn-resultados"
            data-paciente="' . htmlspecialchars(
                                                            $analiticas_fecha[0]['paciente_nombre'] . ' ' .
                                                                $analiticas_fecha[0]['paciente_apellido'],
                                                            ENT_QUOTES
                                                        ) . '"
            data-fecha="' . htmlspecialchars($fecha, ENT_QUOTES) . '"
            data-resultados=\'' . json_encode($analiticas_fecha, JSON_HEX_APOS | JSON_HEX_QUOT) . '\'
        >
            <i class="bi bi-clipboard-data"></i> Ver Resultados
        </button>';
                                                    }
                                                    ?>
                                                </td>

                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="11" class="text-center text-muted">No hay analíticas registradas</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>



            </div>



        </div>
    </div>






    <!-- Modal Pagar -->
    <div class="modal fade" id="modalPagar" tabindex="-1" aria-labelledby="modalPagarLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <form id="formPagar" method="POST" action="../php/procesar_pago.php">
                <div class="modal-content shadow-lg border-0 rounded-4">

                    <!-- HEADER -->
                    <div class="modal-header bg-primary text-white rounded-top-4">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-credit-card-2-front me-2"></i> Pago de Analíticas Médicas
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- BODY -->
                    <div class="modal-body p-4">

                        <!-- INFO PACIENTE -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <h6 class="text-muted mb-2">
                                            <i class="bi bi-person-badge"></i> Paciente
                                        </h6>
                                        <p class="fw-semibold mb-0" id="modalPaciente">—</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <h6 class="text-muted mb-2">
                                            <i class="bi bi-calendar-event"></i> Fecha
                                        </h6>
                                        <p class="fw-semibold mb-0" id="modalFecha">—</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TABLA ANALÍTICAS -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">
                                    <i class="bi bi-list-check me-1"></i> Pruebas realizadas
                                </h6>

                                <div class="table-responsive">
                                    <table class="table align-middle table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center">✔</th>
                                                <th>Prueba</th>
                                                <th class="text-end">Precio</th>
                                            </tr>
                                        </thead>
                                        <tbody id="modalAnaliticasBody">
                                            <!-- JS -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- RESUMEN DE PAGO -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <label class="form-label fw-semibold">
                                            <i class="bi bi-cash-stack"></i> Total a pagar
                                        </label>
                                        <input type="text" id="totalPagar" class="form-control form-control-lg text-end fw-bold" readonly value="0">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <label class="form-label fw-semibold">
                                            <i class="bi bi-wallet2"></i> Monto recibido
                                        </label>
                                        <input type="number" name="monto" class="form-control form-control-lg text-end" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- HIDDEN INPUTS -->
                        <input type="hidden" name="id_paciente" id="modalIdPaciente">
                        <input type="hidden" name="fecha_pago" id="modalFechaPago">
                        <input type="hidden" name="analiticas_seleccionadas" id="analiticasSeleccionadas">

                    </div>

                    <!-- FOOTER -->
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-check-circle"></i> Procesar Pago
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>



    <!-- modal de resultados -->
   <div class="modal fade" id="modalResultados" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">

            <!-- HEADER -->
            <div class="modal-header bg-primary text-white rounded-top-4">
                <div>
                    <h5 class="modal-title fw-bold mb-0">
                        <i class="bi bi-clipboard-data me-2"></i> Resultados de Analíticas
                    </h5>
                    <small class="opacity-75">Informe clínico del paciente</small>
                </div>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body p-4">

                <!-- INFO PACIENTE -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="bi bi-person-badge fs-5 text-primary"></i>
                                    <span class="text-muted fw-semibold">Paciente</span>
                                </div>
                                <div class="fw-bold fs-6" id="resPaciente">—</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="bi bi-calendar-event fs-5 text-primary"></i>
                                    <span class="text-muted fw-semibold">Fecha</span>
                                </div>
                                <div class="fw-bold fs-6" id="resFecha">—</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TABLA RESULTADOS -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Prueba</th>
                                        <th>Resultado</th>
                                        <th>Valores Ref.</th>
                                        <th>Comentario</th>
                                        <th class="text-center">Archivo</th>
                                    </tr>
                                </thead>
                                <tbody id="resBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

            <!-- FOOTER -->
            <div class="modal-footer bg-light">
                <button class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cerrar
                </button>
            </div>

        </div>
    </div>
</div>






   <script>
document.addEventListener('click', function(e) {
    if (e.target.closest('.btn-resultados')) {
        const btn = e.target.closest('.btn-resultados');

        const paciente = btn.dataset.paciente;
        const fecha = btn.dataset.fecha;
        const resultados = JSON.parse(btn.dataset.resultados);

        document.getElementById('resPaciente').textContent = paciente;
        document.getElementById('resFecha').textContent = fecha;

        const tbody = document.getElementById('resBody');
        tbody.innerHTML = '';

        resultados.forEach(r => {

            let archivoHtml = '—';

            if (r.archivo && r.archivo !== '') {
                archivoHtml = `
                    <a href="../uploads/${r.archivo}" 
                       class="btn btn-sm btn-outline-primary"
                       target="_blank" 
                       download>
                        <i class="bi bi-download"></i>
                    </a>
                `;
            }

            tbody.innerHTML += `
                <tr>
                    <td>${r.prueba_nombre}</td>
                    <td><strong>${r.resultado}</strong></td>
                    <td>${r.valores_referencia ?? '—'}</td>
                    <td>${r.comentario ?? '—'}</td>
                    <td class="text-center">${archivoHtml}</td>
                </tr>
            `;
        });

        new bootstrap.Modal(document.getElementById('modalResultados')).show();
    }
});
</script>










    <script>
        document.addEventListener('click', function(e) {

            const btn = e.target.closest('.btn-pagar');
            if (!btn) return;

            const idPaciente = btn.dataset.idPaciente;
            const fecha = btn.dataset.fecha;
            const paciente = btn.dataset.paciente;

            document.getElementById('modalPaciente').textContent = paciente;
            document.getElementById('modalFecha').textContent = fecha;
            document.getElementById('modalIdPaciente').value = idPaciente;
            document.getElementById('modalFechaPago').value = fecha;

            fetch(`../php/ajax_get_analiticas_pago.php?id_paciente=${idPaciente}&fecha=${fecha}`)
                .then(res => res.json())
                .then(data => {

                    const tbody = document.getElementById('modalAnaliticasBody');
                    tbody.innerHTML = '';

                    data.forEach(a => {
                        const precio = parseFloat(a.precio || 0);

                        tbody.innerHTML += `
                    <tr>
                        <td>
                            <input type="checkbox"
                                   class="chkAnalitica"
                                   value="${a.id_analitica}"
                                   data-precio="${precio}"
                                   checked>
                        </td>
                        <td>${a.prueba_nombre}</td>
                        <td>${precio.toFixed(2)} FCFA</td>
                    </tr>
                `;
                    });

                    function actualizarTotal() {
                        let suma = 0;
                        let seleccionadas = [];

                        document.querySelectorAll('.chkAnalitica').forEach(chk => {
                            if (chk.checked) {
                                suma += parseFloat(chk.dataset.precio);
                                seleccionadas.push(chk.value);
                            }
                        });

                        document.getElementById('totalPagar').value = suma.toFixed(2);
                        document.getElementById('analiticasSeleccionadas').value = JSON.stringify(seleccionadas);
                    }

                    document.querySelectorAll('.chkAnalitica').forEach(chk => {
                        chk.addEventListener('change', actualizarTotal);
                    });

                    actualizarTotal();

                    new bootstrap.Modal(document.getElementById('modalPagar')).show();
                });
        });
    </script>







    <?php
    include_once '../componentes/footer.php';
    ?>