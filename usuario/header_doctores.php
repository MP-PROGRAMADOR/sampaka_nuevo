<?php
require_once "../php/auth.php"; // conexión PDO

// Variables para controlar el título de la página y el enlace activo
if (!isset($page_title)) $page_title = "Panel Médico";
if (!isset($page_name)) $page_name = "Dashboard";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> | Panel Hospitalario</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        /* Estilos base */
        body {
            background-color: #f8f9fa;
        }

        .sidebar {
            width: 250px;
            background-color: #0d6efd;
            /* ... otros estilos de color ... */
            height: 100vh;
            position: fixed;
            /* <-- CLAVE 1: Fija la barra a la izquierda */
            top: 0;
            left: 0;
            padding-top: 1rem;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
            z-index: 1000;
        }

        .main-content {
            /* <-- CLAVE 2: Mueve el contenido 250px a la derecha para evitar superposición */
            margin-left: 250px;
            padding: 20px;
        }

        /* ... Estilos de enlaces, tarjetas, iconos, etc. ... */

        .sidebar .navbar-brand {
            color: white !important;
            font-weight: 700;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.7);
            padding: 10px 15px;
            margin-bottom: 5px;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
        }

        .sidebar .nav-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar .nav-link.active {
            background-color: white;
            color: #0d6efd;
            font-weight: 600;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }

        .stat-card .icon-circle {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .stat-card.citas .icon-circle {
            background-color: #198754;
        }

        .stat-card.pacientes .icon-circle {
            background-color: #0d6efd;
        }

        .stat-card.resultados .icon-circle {
            background-color: #ffc107;
        }

        .stat-card.pendientes .icon-circle {
            background-color: #dc3545;
        }

        /* Efecto de desenfoque al fondo cuando el modal aparece */
        .modal-backdrop.show {
            backdrop-filter: blur(4px);
            background-color: rgba(0, 0, 0, 0.4);
        }

        #modalLogout .modal-content {
            border-radius: 24px;
            border: none;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
        }

        /* Cabecera sutil */
        #modalLogout .modal-header {
            border: none;
            padding-top: 1.5rem;
        }

        /* Contenedor del Icono con animación */
        #modalLogout .icon-container {
            width: 90px;
            height: 90px;
            margin: 0 auto 1.5rem;
            background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%);
            color: #e53e3e;
            border-radius: 30px;
            /* Estilo Squircle */
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            transform: rotate(-5deg);
            transition: transform 0.3s ease;
        }

        #modalLogout:hover .icon-container {
            transform: rotate(0deg) scale(1.05);
        }

        /* Tipografía */
        #modalLogout .modal-title-custom {
            font-size: 1.25rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        #modalLogout .modal-text-custom {
            color: #718096;
            font-size: 0.95rem;
            line-height: 1.5;
            padding: 0 10px;
        }

        /* Botones Modernos */
        #modalLogout .btn-logout-confirm {
            background: linear-gradient(135deg, #f56565 0%, #c53030 100%);
            border: none;
            color: white;
            padding: 12px;
            border-radius: 15px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(229, 62, 62, 0.2);
        }

        #modalLogout .btn-logout-confirm:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(229, 62, 62, 0.3);
            color: white;
        }

        #modalLogout .btn-stay {
            color: #a0aec0;
            font-weight: 500;
            transition: color 0.2s;
        }

        #modalLogout .btn-stay:hover {
            color: #4a5568;
        }

        /* Responsive: Elimina el 'fixed' y el 'margin-left' en pantallas pequeñas */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                /* Ya no es fijo */
                box-shadow: none;
            }

            .main-content {
                margin-left: 0;
                /* Vuelve a su lugar */
            }
        }
    </style>
</head>


<div class="sidebar d-flex flex-column p-3">
    <a href="index.php" class="navbar-brand fs-4 mb-4 text-center">
        <i class="bi bi-clipboard-pulse"></i> Panel Médico
    </a>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="index.php" class="nav-link <?= ($page_name == 'Dashboard') ? 'active' : '' ?>" aria-current="page">
                <i class="bi bi-house-door-fill me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="pacientes.php" class="nav-link <?= ($page_name == 'Mis Pacientes') ? 'active' : '' ?>">
                <i class="bi bi-people-fill me-2"></i> Mis Pacientes
            </a>
        </li>
        <li class="nav-item">
            <a href="agenda.php" class="nav-link <?= ($page_name == 'Mi Agenda') ? 'active' : '' ?>">
                <i class="bi bi-calendar-range-fill me-2"></i> Mi Agenda
            </a>
        </li>
        <li class="nav-item">
            <a href="tratamientos.php" class="nav-link <?= ($page_name == 'Prescripciones') ? 'active' : '' ?>">
                <i class="bi bi-receipt-cutoff me-2"></i> Tratamientos
            </a>
        </li>
        <!-- <li class="nav-item">
            <a href="historial_clinico.php" class="nav-link <?= ($page_name == 'Historiales Clínicos') ? 'active' : '' ?>">
                <i class="bi bi-prescription2 me-2"></i> Historiales Clínicos
            </a>
        </li> -->
        <li class="nav-item">
            <a href="resultados_labs.php" class="nav-link <?= ($page_name == 'Resultados Labs') ? 'active' : '' ?>">
                <i class="bi bi-file-medical-fill me-2"></i> Resultados Labs
            </a>
        </li>
    </ul>
    <hr class="text-white-50">
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="https://via.placeholder.com/32/333/fff?text=DR" alt="Doctor" width="32" height="32" class="rounded-circle me-2 border border-white">
            <strong>Dra. Ana Trini</strong>
        </a>
        <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser1">
            <li><a class="dropdown-item" href="./perfil.php">Mi Perfil</a></li>
            <li> <hr class="dropdown-divider"> </li>
            <li>
                <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#modalLogout">
                    <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                </a>
            </li>
        </ul>
    </div>

</div>


<!-- modal de Cerrar Sesión -->
<div class="modal fade" id="modalLogout" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" style="max-width: 350px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close" style="font-size: 0.8rem;"></button>
            </div>

            <div class="modal-body text-center pb-2">
                <div class="icon-container">
                    <i class="bi bi-door-open-fill"></i>
                </div>

                <h5 class="modal-title-custom">¿Finalizar Sesión?</h5>
                <p class="modal-text-custom">
                    Estás a punto de salir del sistema. Asegúrate de haber guardado tus cambios.
                </p>
            </div>

            <div class="modal-footer border-0 p-4 pt-2 d-flex flex-column gap-2">
                <a href="../php/cerrar_sesion.php" class="btn btn-logout-confirm w-100 py-2 shadow-sm">
                    <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
                </a>
                <button type="button" class="btn btn-link btn-stay text-decoration-none small" data-bs-dismiss="modal">
                    Seguir trabajando
                </button>
            </div>
        </div>
    </div>
</div>

<div class="main-content">