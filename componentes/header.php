<?php
include_once '../config/conexion.php';
session_start();
include_once '../php/auth.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración Hospitalario</title>
    <!-- Bootstrap CSS -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/bootstrap-icons.min.css">

    <link rel="stylesheet" href="../css/dataTables.bootstrap5.min.css" />
    <link rel="stylesheet" href="../css/buttons.bootstrap5.min.css" />
    <link rel="stylesheet" href="../css/responsive.bootstrap5.min.css" />

    <link href="../css/mdb.min.css" rel="stylesheet" />

    <!-- Tailwind CSS (para utilidades extra) -->
    <script src="https://cdn.tailwindcss.com"></script>
   <style>
    /*
     * ----------------------------------------------------
     * ESTILOS PROFESIONALES Y MODERNOS
     * ----------------------------------------------------
     */

    /* --- BASE --- */
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f8f9fa; /* Fondo blanco muy limpio */
        color: #34495e;
        line-height: 1.6;
        overflow-x: hidden;
    }

    /* --- SIDEBAR (Azul Marino Oscuro Profesional) --- */
    #sidebar {
        width: 280px;
        min-height: 100vh;
        /* Azul Marino Oscuro Clásico - Da un look corporativo y premium */
        background: #0d2843; 
        color: white;
        transition: width 0.3s ease, left 0.3s ease;
        position: fixed;
        left: 0;
        top: 0;
        /* Sombra limpia */
        box-shadow: 4px 0 15px rgba(0, 0, 0, 0.25);
        z-index: 1050;
        padding-top: 0;
    }

    /* --- ENCABEZADO (Hospital Samapaka) --- */
    .sidebar-heading {
        background-color: #0c233a; 
        padding: 24px 20px !important;
        margin-bottom: 20px; 
        font-size: 1.5rem; 
        font-weight: 700;
        color: #ffffff;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important; 
    }
    
    .sidebar-heading i {
        color: #3498db; 
        font-size: 1.4rem;
    }

    /* --- CONTENEDOR DE ENLACES --- */
    .list-group-flush {
        padding: 0 15px !important;
    }
    
    /* --- ENLACES DE MENÚ BASE --- */
    #sidebar .nav-link {
        color: #c0cff0; 
        padding: 14px 20px !important; 
        border-radius: 6px !important;
        margin-bottom: 4px; 
        display: flex;
        align-items: center;
        font-weight: 500;
        transition: background-color 0.2s ease, color 0.2s ease, transform 0.2s ease;
        text-decoration: none;
    }

    #sidebar .nav-link i {
        margin-right: 15px;
        font-size: 1.1rem; 
    }

    /* --- ESTADO HOVER MEJORADO */
    #sidebar .nav-link:hover:not(.active) {
        background-color: #123455; 
        color: #ffffff;
        transform: translateY(-1px); 
    }
    #sidebar .nav-link.active {
        /* Color de acento brillante */
        background-color: #248aceff !important; 
        color: white !important;
        font-weight: 600;
        transform: none; 
        border-left: 4px solid #ffffff; 
        padding-left: 16px !important; 
    }

    #sidebar .nav-link.active i {
        color: white !important;
    }

    /* --- CONTENIDO PRINCIPAL --- */
    #content {
        flex-grow: 1;
        margin-left: 280px; 
        padding: 30px; /* Más padding para el contenido */
        transition: margin-left 0.3s ease;
    }
</style>
</head>