<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración Hospitalario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f0f2f5;
        }
        .sidebar {
            width: 250px;
            background-color: #ffffff;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 1rem;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .navbar-brand {
            font-weight: 700;
        }
        .nav-link {
            font-weight: 500;
            color: #495057;
        }
        .nav-link.active {
            background-color: #0d6efd;
            color: white;
            border-radius: 5px;
        }
        .stat-card .icon {
            font-size: 2rem;
            color: #0d6efd;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                box-shadow: none;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>

<div class="sidebar d-flex flex-column p-3">
    <a href="#" class="navbar-brand text-dark fs-4 mb-4 text-center">
        <i class="bi bi-hospital me-2"></i>Admin Hospital
    </a>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="#" class="nav-link active" aria-current="page">
                <i class="bi bi-speedometer2 me-2"></i>
                Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="bi bi-people me-2"></i>
                Pacientes
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="bi bi-person-fill-gear me-2"></i>
                Médicos
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="bi bi-calendar-check me-2"></i>
                Citas
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="bi bi-box-seam me-2"></i>
                Inventario
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="bi bi-file-earmark-bar-graph me-2"></i>
                Reportes
            </a>
        </li>
    </ul>
    <hr>
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="https://via.placeholder.com/32" alt="mdo" width="32" height="32" class="rounded-circle me-2">
            <strong>Admin</strong>
        </a>
        <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser1">
            <li><a class="dropdown-item" href="#">Perfil</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#">Cerrar Sesión</a></li>
        </ul>
    </div>
</div>

<div class="main-content">
    <nav class="navbar navbar-expand-lg bg-light rounded shadow-sm mb-4">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-bell me-1"></i>Notificaciones</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-gear me-1"></i>Configuración</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card p-3">
                <div class="card-body d-flex align-items-center">
                    <div class="icon me-3">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <h5 class="card-title text-muted mb-0">Pacientes</h5>
                        <h2 class="card-subtitle mb-0 text-dark">1,250</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card p-3">
                <div class="card-body d-flex align-items-center">
                    <div class="icon me-3">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div>
                        <h5 class="card-title text-muted mb-0">Médicos</h5>
                        <h2 class="card-subtitle mb-0 text-dark">50</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card p-3">
                <div class="card-body d-flex align-items-center">
                    <div class="icon me-3">
                        <i class="bi bi-calendar-check-fill"></i>
                    </div>
                    <div>
                        <h5 class="card-title text-muted mb-0">Citas Hoy</h5>
                        <h2 class="card-subtitle mb-0 text-dark">75</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card stat-card p-3">
                <div class="card-body d-flex align-items-center">
                    <div class="icon me-3">
                        <i class="bi bi-box-seam-fill"></i>
                    </div>
                    <div>
                        <h5 class="card-title text-muted mb-0">Inventario</h5>
                        <h2 class="card-subtitle mb-0 text-dark">2,100</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-4 mb-5">
        <div class="col-md-8">
            <div class="card p-4">
                <h5 class="card-title">Resumen de Citas Mensuales</h5>
                <p>Aquí puedes integrar un gráfico con una biblioteca como **Chart.js** o **ApexCharts**.</p>
                <div style="height: 300px; background-color: #f8f9fa; border-radius: 8px;"></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-4">
                <h5 class="card-title">Citas Próximas</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <span class="d-block fw-bold">Juan Pérez</span>
                            <small class="text-muted">Cardiología</small>
                        </div>
                        <span class="badge bg-primary rounded-pill">10:00 AM</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <span class="d-block fw-bold">María Gómez</span>
                            <small class="text-muted">Pediatría</small>
                        </div>
                        <span class="badge bg-primary rounded-pill">11:30 AM</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <span class="d-block fw-bold">Luis Rodríguez</span>
                            <small class="text-muted">Dermatología</small>
                        </div>
                        <span class="badge bg-primary rounded-pill">02:00 PM</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>


 <!-- Tabla de últimas citas con diseño mejorado -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm p-4">
                    <div class="card-header bg-white border-0 ps-0">
                        <h5 class="card-title mb-0">Últimas Citas Programadas</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr class="table-light">
                                    <th scope="col" class="text-secondary fw-normal">Paciente</th>
                                    <th scope="col" class="text-secondary fw-normal">Médico</th>
                                    <th scope="col" class="text-secondary fw-normal">Fecha</th>
                                    <th scope="col" class="text-secondary fw-normal">Hora</th>
                                    <th scope="col" class="text-secondary fw-normal">Estado</th>
                                    <th scope="col" class="text-secondary fw-normal">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Juan Pérez</td>
                                    <td>Dra. Ana Torres</td>
                                    <td>12/09/2025</td>
                                    <td>10:00 AM</td>
                                    <td><span class="badge rounded-pill bg-success">Confirmada</span></td>
                                    <td><button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button></td>
                                </tr>
                                <tr>
                                    <td>María García</td>
                                    <td>Dr. Luis Mendoza</td>
                                    <td>12/09/2025</td>
                                    <td>11:30 AM</td>
                                    <td><span class="badge rounded-pill bg-warning text-dark">Pendiente</span></td>
                                    <td><button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button></td>
                                </tr>
                                <tr>
                                    <td>Carlos Rivera</td>
                                    <td>Dra. Sofía Valdés</td>
                                    <td>12/09/2025</td>
                                    <td>02:00 PM</td>
                                    <td><span class="badge rounded-pill bg-info text-dark">Reprogramada</span></td>
                                    <td><button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button></td>
                                </tr>
                                <tr>
                                    <td>Laura Fernández</td>
                                    <td>Dr. Luis Mendoza</td>
                                    <td>13/09/2025</td>
                                    <td>09:00 AM</td>
                                    <td><span class="badge rounded-pill bg-success">Confirmada</span></td>
                                    <td><button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


</div>





<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>