<?php
include_once '../config/conexion.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once '../php/auth.php';

// Configuración de títulos por defecto
if (!isset($page_title)) $page_title = "Panel Médico";
if (!isset($page_name)) $page_name = "Dashboard";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> | Panel Hospitalario</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="../css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

    <style>
        :root {
            --sidebar-bg: #0d2843;
            --sidebar-hover: #123455;
            --accent-color: #248ace;
            --text-light: #c0cff0;
            --sidebar-width: 280px;
            --bg-body: #f8f9fa;
        }

        /* --- BASE Y RENDIMIENTO --- */
        body {
            background-color: var(--bg-body);
            font-family: 'Inter', sans-serif;
            color: #34495e;
            overflow-x: hidden;
            text-rendering: optimizeLegibility;
        }

        /* Bloqueo de conflictos de MDB con DataTables */
        .dt-buttons .btn {
            transform: none !important;
            /* Evita el agrandamiento */
            box-shadow: none !important;
            /* Evita sombras raras al clickear */
            transition: background 0.2s ease-in-out !important;
        }

        /* Evita que los iconos dentro de los botones se muevan */
        .dt-buttons .btn i {
            transform: none !important;
            display: inline-block !important;
        }

        /* Corrección de la posición del dropdown de Columnas */
        .buttons-columnVisibility {
            padding: 10px 20px !important;
        }

        /* --- BLOQUEO ANTI-AGRANDAMIENTO (Consolidado) --- */
        .btn,
        .dt-button,
        .buttons-html5,
        .buttons-print,
        .btn-primary,
        .btn-floating,
        [class*="btn-"],
        .nav-link {
            transform: none !important;
            transition: background 0.15s ease-in-out, color 0.15s ease-in-out !important;
            box-shadow: none !important;
            animation: none !important;
            scale: 1 !important;
        }

        .btn:hover,
        .dt-button:hover,
        [class*="btn-"]:hover,
        .nav-link:hover {
            transform: none !important;
            scale: 1 !important;
        }

        /* Desactivar efectos de onda (Ripple) de MDB */
        .ripple-surface,
        .ripple-surface-primary {
            position: static !important;
        }

        .btn i,
        .nav-link i {
            transform: none !important;
            display: inline-block !important;
        }

        /* --- SIDEBAR --- */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1050;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.15);
        }

        .sidebar-heading {
            background-color: #0c233a;
            padding: 24px 20px;
            font-size: 1.3rem;
            font-weight: 700;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
        }

        .sidebar-heading i {
            color: var(--accent-color);
            margin-right: 12px;
        }

        .sidebar .nav-link {
            color: var(--text-light);
            margin: 4px 15px;
            padding: 12px 18px;
            border-radius: 8px;
            font-weight: 500;
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .sidebar .nav-link:hover:not(.active) {
            background-color: var(--sidebar-hover);
            color: white;
            padding-left: 23px !important;
            /* Pequeño desplazamiento lateral en lugar de agrandar */
        }

        .sidebar .nav-link.active {
            background-color: var(--accent-color) !important;
            color: white !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2) !important;
            border-left: 4px solid white;
        }

        /* --- CONTENIDO PRINCIPAL --- */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 35px;
            min-height: 100vh;
            transition: all 0.3s ease;
            contain: content;
        }

        /* --- DATA TABLES CUSTOM --- */
        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 4px 8px;
        }

        /* --- RESPONSIVE --- */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
                padding: 20px;
            }

            .sidebar.show {
                transform: translateX(0);
            }
        }

        /* Modals */
        .modal-backdrop.show {
            backdrop-filter: blur(4px);
        }

        .modal-content {
            border-radius: 18px;
            border: none;
            overflow: hidden;
        }
    </style>
</head>