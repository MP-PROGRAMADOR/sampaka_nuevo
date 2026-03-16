<?php
include '../componentes/header_usuario.php';


// 1. Verificación de Seguridad
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login.php");
    exit();
}

// Usaremos este ID para TODO el rastreo de datos
$id_usuario_sesion = $_SESSION['id_usuario'];


try {
    // 1. Datos del Usuario Logueado (Verificación básica)
    $stmt = $pdo->prepare("SELECT p.nombre, p.apellido FROM usuarios u 
                           INNER JOIN personal p ON u.id_personal = p.id_personal 
                           WHERE u.id_usuario = :id_u");
    $stmt->execute([':id_u' => $id_usuario_sesion]);
    $user_data = $stmt->fetch();
    $nombre_completo = ($user_data) ? $user_data['nombre'] . " " . $user_data['apellido'] : "Usuario";

    // 2. Mis Citas Hoy
    // Filtramos directamente por el id_usuario que creó/tiene asignada la consulta
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM consultas 
                           WHERE id_usuario = :id_u 
                           AND DATE(fecha_consulta) = CURDATE()");
    $stmt->execute([':id_u' => $id_usuario_sesion]);
    $citas_hoy = $stmt->fetchColumn();

    // 3. Mis Pacientes Totales 
    // Contamos pacientes únicos que han tenido al menos una consulta con este usuario
    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT id_paciente) FROM consultas 
                           WHERE id_usuario = :id_u");
    $stmt->execute([':id_u' => $id_usuario_sesion]);
    $pacientes_propios = $stmt->fetchColumn();

    // 4. Analíticas Pendientes (Relacionadas con las consultas de este usuario)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM analiticas a 
                           INNER JOIN consultas c ON a.id_consulta = c.id_consulta 
                           WHERE c.id_usuario = :id_u AND a.estado = 'Pendiente'");
    $stmt->execute([':id_u' => $id_usuario_sesion]);
    $labs_pendientes = $stmt->fetchColumn();

    // 5. Hospitalizados (Pacientes que ingresó este usuario y siguen activos)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM hospitalizaciones 
                           WHERE id_usuario = :id_u AND fecha_alta IS NULL");
    $stmt->execute([':id_u' => $id_usuario_sesion]);
    $hosp_count = $stmt->fetchColumn();

    // 6. Mi Agenda de Hoy (Tabla detallada)
    $stmt = $pdo->prepare("SELECT p.nombre, p.apellido, c.motivo, c.fecha_consulta, 
                           DATE_FORMAT(c.fecha_consulta, '%H:%i') as hora 
                           FROM consultas c 
                           INNER JOIN pacientes p ON c.id_paciente = p.id_paciente
                           WHERE c.id_usuario = :id_u AND DATE(c.fecha_consulta) = CURDATE()
                           ORDER BY c.fecha_consulta ASC");
    $stmt->execute([':id_u' => $id_usuario_sesion]);
    $proximas_citas = $stmt->fetchAll();

    // 7. Datos para mi Gráfico de Actividad (Últimos 7 días personales)
    $stmt = $pdo->prepare("SELECT DATE_FORMAT(fecha_consulta, '%d/%m') as dia, COUNT(*) as total 
                           FROM consultas 
                           WHERE id_usuario = :id_u 
                           GROUP BY DATE(fecha_consulta) 
                           ORDER BY fecha_consulta DESC LIMIT 7");
    $stmt->execute([':id_u' => $id_usuario_sesion]);
    $datos_grafico = array_reverse($stmt->fetchAll());

    $labels_js = json_encode(array_column($datos_grafico, 'dia'));
    $totales_js = json_encode(array_column($datos_grafico, 'total'));

    // 8. Últimas 5 analíticas solicitadas por MÍ
    $stmt = $pdo->prepare("SELECT p.nombre, p.apellido, pr.nombre as prueba, a.fecha_registro, a.estado
                           FROM analiticas a
                           INNER JOIN pacientes p ON a.id_paciente = p.id_paciente
                           INNER JOIN pruebas_medicas pr ON a.id_prueba = pr.id_prueba
                           INNER JOIN consultas c ON a.id_consulta = c.id_consulta
                           WHERE c.id_usuario = :id_u
                           ORDER BY a.fecha_registro DESC LIMIT 5");
    $stmt->execute([':id_u' => $id_usuario_sesion]);
    $alertas = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Error en el filtrado de datos: " . $e->getMessage());
}


$page_title = 'Dashboard';
$page_name = 'Dashboard';
?>

<style>
    .stat-card {
        border-radius: 20px;
    }

    .stat-card .icon-circle {
        width: 55px;
        height: 55px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white !important;
        /* Fuerza el icono a ser blanco */
    }

    .icon-circle-white {
        background-color: white !important;
        border-radius: 15px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }
</style>

<body>
    <?php include_once '../componentes/slider_usuario.php'; ?>
    <div class="main-content">

        <h1 class="mb-4 fw-light text-primary">👋 Hola, <?php echo htmlspecialchars($nombre_completo); ?></h1>

        <div class="row g-4 mb-4">
            <div class="col-md-6 col-lg-3">
                <div class="card stat-card citas p-3 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon-circle me-3 bg-success"><i class="bi bi-calendar-check-fill fs-3"></i></div>
                        <div>
                            <p class="card-title text-muted mb-0">Mis Citas Hoy</p>
                            <h3 class="card-subtitle mb-0 fw-bold"><?= $citas_hoy ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card stat-card pacientes p-3 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center ">
                        <div class="icon-circle me-3 bg-primary"><i class="bi bi-people-fill fs-3"></i></div>
                        <div>
                            <p class="card-title text-muted mb-0">Mis Pacientes</p>
                            <h3 class="card-subtitle mb-0 fw-bold"><?= $pacientes_propios ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card stat-card resultados p-3 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon-circle me-3 bg-warning"><i class="bi bi-exclamation-triangle-fill fs-3"></i></div>
                        <div>
                            <p class="card-title text-muted mb-0">Mis Labs. Pendientes</p>
                            <h3 class="card-subtitle mb-0 fw-bold"><?= $labs_pendientes ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card stat-card hospital p-3 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon-circle me-3 bg-info"><i class="bi bi-hospital-fill fs-3"></i></div>
                        <div>
                            <p class="card-title text-muted mb-0">Hospitalizados</p>
                            <h3 class="card-subtitle mb-0 fw-bold"><?= $hosp_count ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card p-4 h-100 shadow-sm border-0">
                    <h5 class="card-title text-primary"><i class="bi bi-clock-history me-1"></i> Mi Agenda</h5>
                    <ul class="list-group list-group-flush mt-3">
                        <?php if ($proximas_citas): ?>
                            <?php foreach ($proximas_citas as $cita): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center bg-light rounded mb-2 border-0">
                                    <div>
                                        <span class="d-block fw-bold"><?= htmlspecialchars($cita['nombre'] . " " . $cita['apellido']) ?></span>
                                        <small class="text-secondary"><?= htmlspecialchars($cita['motivo']) ?></small>
                                    </div>
                                    <span class="badge bg-primary rounded-pill"><?= date('H:i', strtotime($cita['fecha_consulta'])) ?></span>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-calendar-x fs-1"></i>
                                <p class="small mt-2">Sin citas para hoy.</p>
                            </div>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card p-4 h-100 shadow-sm border-0">
                    <h5 class="card-title text-primary"><i class="bi bi-graph-up me-1"></i> Mi Actividad</h5>
                    <div style="height: 300px;" class="p-3">
                        <canvas id="cargaMensualChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm p-4 border-0">
                    <h5 class="card-title mb-4 text-primary"><i class="bi bi-clipboard-pulse me-2"></i>Analíticas Solicitadas por Mí</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Paciente</th>
                                    <th>Prueba</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($alertas): foreach ($alertas as $alerta):
                                        $status_class = ($alerta['estado'] == 'Pendiente') ? 'bg-warning text-dark' : 'bg-success';
                                ?>
                                        <tr>
                                            <td class="fw-bold"><?= htmlspecialchars($alerta['nombre'] . " " . $alerta['apellido']) ?></td>
                                            <td><?= htmlspecialchars($alerta['prueba']) ?></td>
                                            <td><?= date('d/m/Y', strtotime($alerta['fecha_registro'])) ?></td>
                                            <td><span class="badge rounded-pill <?= $status_class ?>"><?= $alerta['estado'] ?></span></td>
                                        </tr>
                                    <?php endforeach;
                                else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">No has solicitado analíticas recientemente.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('cargaMensualChart');
            if (ctx) {
                // Creamos un gradiente para el fondo de las barras
                const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
                gradient.addColorStop(0, 'rgba(13, 110, 253, 0.8)'); // Azul vibrante arriba
                gradient.addColorStop(1, 'rgba(13, 110, 253, 0.2)'); // Azul suave abajo

                new Chart(ctx, {
                    type: 'bar', // Cambiado a barras para un look más sólido
                    data: {
                        labels: <?= $labels_js ?>,
                        datasets: [{
                            label: 'Consultas',
                            data: <?= $totales_js ?>,
                            backgroundColor: gradient,
                            borderColor: '#0d6efd',
                            borderWidth: 1,
                            borderRadius: 4, // Bordes redondeados en las barras
                            borderSkipped: false,
                            barPercentage: 0.6 // Hace las barras un poco más delgadas y elegantes
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false // Ocultamos la leyenda para un look más limpio
                            },
                            tooltip: {
                                backgroundColor: '#1e293b', // Fondo oscuro para el tooltip
                                padding: 12,
                                borderRadius: 8,
                                displayColors: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    drawBorder: false,
                                    color: 'rgba(0, 0, 0, 0.05)' // Líneas de fondo muy sutiles
                                },
                                ticks: {
                                    stepSize: 1,
                                    font: {
                                        size: 11
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false // Quitamos las líneas verticales para limpieza
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>

    <?php include '../componentes/footer.php'; ?>
</body>