<?php
session_start();
require_once "../config/conexion.php"; 

// 1. Configuración de la página
$page_title = 'Mi Perfil';
$page_name = 'Perfil del Doctor'; 

// 2. Simulación de datos del doctor (Aquí conectarías con tu tabla 'personal' o 'usuarios')
// En un caso real: $query = "SELECT * FROM personal WHERE id_personal = $doctor_id";
$doctor_info = [
    'nombre' => 'Ana Trini',
    'apellido' => 'Maye Bokuy',
    'especialidad' => 'Cardiología',
    'correo' => 'a.martinez@clinica.com',
    'telefono' => '+52 555 123 4567',
    'cedula' => '12345678',
    'foto' => null // URL de la imagen si existiera
];

include 'header_doctores.php'; 
?>

<style>
    .profile-header {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        border-radius: 20px;
        padding: 40px;
        color: white;
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
    }
    .profile-header::after {
        content: "";
        position: absolute;
        top: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
    }
    .avatar-circle {
        width: 120px;
        height: 120px;
        background-color: white;
        border: 4px solid rgba(255,255,255,0.3);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        color: #4e73df;
        font-weight: bold;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .info-card {
        border: none;
        border-radius: 15px;
        transition: transform 0.3s ease;
    }
    .info-card:hover {
        transform: translateY(-5px);
    }
    .icon-badge {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
    }
</style>

<div class="container-fluid py-4">
    <div class="profile-header shadow-sm d-flex align-items-center flex-wrap">
        <div class="avatar-circle me-4">
            <?php 
                $iniciales = substr($doctor_info['nombre'], 0, 1) . substr($doctor_info['apellido'], 0, 1);
                echo $iniciales;
            ?>
        </div>
        <div>
            <h1 class="display-6 fw-bold mb-1"><?= $doctor_info['nombre'] . ' ' . $doctor_info['apellido'] ?></h1>
            <p class="lead mb-0 opacity-75"><?= $doctor_info['especialidad'] ?></p>
            <span class="badge bg-white text-primary mt-2 px-3 py-2 rounded-pill">Doctor Activo</span>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card info-card shadow-sm p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold text-dark mb-0">Información Personal</h5>
                    <!-- <button class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        <i class="bi bi-pencil me-1"></i> Editar
                    </button> -->
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <div class="icon-badge bg-light text-primary">
                                <i class="bi bi-envelope"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Correo Electrónico</small>
                                <span class="fw-bold"><?= $doctor_info['correo'] ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <div class="icon-badge bg-light text-success">
                                <i class="bi bi-telephone"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Teléfono</small>
                                <span class="fw-bold"><?= $doctor_info['telefono'] ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <div class="icon-badge bg-light text-info">
                                <i class="bi bi-card-text"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Cédula Profesional</small>
                                <span class="fw-bold"><?= $doctor_info['cedula'] ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <div class="icon-badge bg-light text-warning">
                                <i class="bi bi-building"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Departamento</small>
                                <span class="fw-bold">Consulta Externa</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card info-card shadow-sm p-4 text-center">
                <h6 class="text-muted text-uppercase small fw-bold mb-4">Resumen de Actividad</h6>
                <div class="row border-bottom pb-3 mb-3">
                    <div class="col-6 border-end">
                        <h4 class="fw-bold text-primary mb-0">124</h4>
                        <small class="text-muted">Pacientes</small>
                    </div>
                    <div class="col-6">
                        <h4 class="fw-bold text-primary mb-0">12</h4>
                        <small class="text-muted">Hoy</small>
                    </div>
                </div>
                <div class="text-start mt-3">
                    <p class="small text-muted mb-2"><i class="bi bi-clock-history me-2"></i> Último acceso: Hoy, 08:30 AM</p>
                    <p class="small text-muted mb-0"><i class="bi bi-shield-check me-2"></i> Cuenta Verificada</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
include 'footer_doctores.php'; 
?>