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
            <li><a class="dropdown-item" href="#">Mi Perfil</a></li>
            <li>
                <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a></li>
        </ul>
    </div>
</div>

<div class="main-content">