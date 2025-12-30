<?php
// index.php
session_start();
// Asegúrate de que esta ruta sea correcta para tu conexión
require_once "../config/conexion.php";

// Variables para el header
$page_title = 'Dashboard';
$page_name = 'Dashboard';

// Incluir el encabezado (abre HTML, Sidebar y .main-content)
include 'header_doctores.php';
?>

<h1 class="mb-4 fw-light text-primary">👋 Hola, Dra. Ana Trini</h1>

<div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card citas p-3">
            <div class="card-body d-flex align-items-center">
                <div class="icon-circle me-3">
                    <i class="bi bi-calendar-check-fill"></i>
                </div>
                <div>
                    <p class="card-title text-muted mb-0">Citas Hoy</p>
                    <h3 class="card-subtitle mb-0 fw-bold">12</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card pacientes p-3">
            <div class="card-body d-flex align-items-center">
                <div class="icon-circle me-3">
                    <i class="bi bi-folder-fill"></i>
                </div>
                <div>
                    <p class="card-title text-muted mb-0">Pacientes Propios</p>
                    <h3 class="card-subtitle mb-0 fw-bold">480</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card resultados p-3">
            <div class="card-body d-flex align-items-center">
                <div class="icon-circle me-3">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div>
                    <p class="card-title text-muted mb-0">Labs. Pendientes</p>
                    <h3 class="card-subtitle mb-0 fw-bold text-warning">5</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card pendientes p-3">
            <div class="card-body d-flex align-items-center">
                <div class="icon-circle me-3">
                    <i class="bi bi-hospital-fill"></i>
                </div>
                <div>
                    <p class="card-title text-muted mb-0">Hospitalizados</p>
                    <h3 class="card-subtitle mb-0 fw-bold">3</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-5">

    <div class="col-md-4">
        <div class="card p-4 h-100">
            <h5 class="card-title text-primary"><i class="bi bi-clock-history me-1"></i> Próxima Cita</h5>
            <ul class="list-group list-group-flush mt-3">
                <li class="list-group-item d-flex justify-content-between align-items-center bg-light rounded mb-2 border-0">
                    <div>
                        <span class="d-block fw-bold">Juan Pérez <small class="text-muted">(Pte #123)</small></span>
                        <small class="text-secondary">Motivo: Control anual</small>
                    </div>
                    <span class="badge bg-success rounded-pill p-2 fs-6">10:00 AM</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center bg-light rounded mb-2 border-0">
                    <div>
                        <span class="d-block fw-bold">María Gómez</span>
                        <small class="text-secondary">Motivo: Resultados de Rx</small>
                    </div>
                    <span class="badge bg-primary rounded-pill p-2 fs-6">11:30 AM</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center bg-light rounded mb-2 border-0">
                    <div>
                        <span class="d-block fw-bold">Luis Rodríguez</span>
                        <small class="text-secondary">Motivo: Evaluación de fiebre</small>
                    </div>
                    <span class="badge bg-info text-dark rounded-pill p-2 fs-6">02:00 PM</span>
                </li>
            </ul>
            <div class="mt-3 text-center">
                <a href="agenda.php" class="btn btn-sm btn-outline-primary"><i class="bi bi-calendar-event"></i> Ver Agenda Completa</a>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card p-4 h-100">
            <h5 class="card-title text-primary"><i class="bi bi-graph-up me-1"></i> Resumen de Carga Mensual</h5>
            <p class="text-muted">Distribución de consultas por día en el último mes.</p>
            <div style="height: 300px;" class="p-3">
                <canvas id="cargaMensualChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm p-4">
            <div class="card-header bg-white border-0 ps-0">
                <h5 class="card-title mb-0 text-danger"><i class="bi bi-exclamation-octagon-fill me-2"></i>Alertas y Resultados Críticos</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr class="table-light">
                            <th scope="col" class="text-secondary fw-normal">Paciente</th>
                            <th scope="col" class="text-secondary fw-normal">Tipo</th>
                            <th scope="col" class="text-secondary fw-normal">Fecha</th>
                            <th scope="col" class="text-secondary fw-normal">Estado</th>
                            <th scope="col" class="text-secondary fw-normal">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table-danger">
                            <td class="fw-bold">Sara Martínez</td>
                            <td>Analítica de Sangre (Urgente)</td>
                            <td>Hoy</td>
                            <td><span class="badge rounded-pill bg-danger">Resultado Crítico</span></td>
                            <td>
                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalRevisarCritico">
                                    <i class="bi bi-eye-fill me-1"></i>Detalle
                                </button>
                            </td>
                        </tr>
                        <tr class="table-warning">
                            <td>Carlos Rivera</td>
                            <td>Radiografía de Tórax</td>
                            <td>11/09/2025</td>
                            <td><span class="badge rounded-pill bg-warning text-dark">Pendiente Revisión</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-warning text-dark" data-bs-toggle="modal" data-bs-target="#modalVerDetalle">
                                    <i class="bi bi-eye-fill"></i> Detalle
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>Laura Fernández</td>
                            <td>ECG</td>
                            <td>10/09/2025</td>
                            <td><span class="badge rounded-pill bg-success">Revisado</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary " data-bs-toggle="modal" data-bs-target="#modalVerDetalle">
                                    <i class="bi bi-eye-fill me-1"></i>Detalle
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalRevisarCritico" tabindex="-1" aria-labelledby="modalRevisarCriticoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalRevisarCriticoLabel"><i class="bi bi-lightning-fill me-2"></i>ALERTA CRÍTICA - Revisión Urgente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Paciente:</strong> Sara Martínez</p>
                <p><strong>Examen:</strong> Analítica de Sangre (Urgente)</p>
                <div class="alert alert-danger" role="alert">
                    <p class="mb-0 fw-bold">Glucosa: 450 mg/dL (VALOR CRÍTICO: Alto)</p>
                </div>
                <p class="text-muted">Aquí se cargaría el PDF o el detalle estructurado del informe con todos los valores para su análisis inmediato.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-danger"><i class="bi bi-telephone-fill"></i> Contactar a Enfermería</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalVerDetalle" tabindex="-1" aria-labelledby="modalVerDetalleLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalVerDetalleLabel"><i class="bi bi-file-earmark-bar-graph me-2"></i> Detalle del Resultado (Radiografía de Tórax)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Paciente:</strong> Carlos Rivera</p>
                <p class="text-muted">Contenido de la Radiografía de Tórax o del ECG. En un sistema real, se mostraría aquí la imagen o el visor DICOM (para Radiografías) o un informe detallado.</p>

                <div style="height: 400px; background-color: #e9ecef; border-radius: 8px;" class="p-3 mt-3 d-flex align-items-center justify-content-center">
                    <span class="text-muted">Espacio para la visualización del archivo o imagen médica.</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <a href="historial_paciente.php?id=..." class="btn btn-primary">Ir al Historial del Paciente</a>
            </div>
        </div>
    </div>
</div>


<script>
    // Datos estáticos para simular la carga mensual
    const datosCarga = {
        labels: ['Día 1', 'Día 2', 'Día 3', 'Día 4', 'Día 5', 'Día 6', 'Día 7'],
        datasets: [{
            label: 'Consultas Realizadas',
            data: [4, 6, 3, 7, 5, 8, 4], 
            backgroundColor: 'rgba(13, 110, 253, 0.7)',
            borderColor: 'rgba(13, 110, 253, 1)',
            borderWidth: 1,
            borderRadius: 5,
        }]
    };

    const config = {
        type: 'bar', 
        data: datosCarga,
        options: {
            responsive: true,
            maintainAspectRatio: false, 
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Nº Consultas'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false 
                }
            }
        },
    };

    
    // Se usa DOMContentLoaded en lugar de window.onload para evitar conflictos con el include 'footer_doctores.php'
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('cargaMensualChart');
        if (ctx) { // Asegurarse de que el elemento existe
            new Chart(ctx, config);
        }
    });
</script>

