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
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }

        #sidebar {
            width: 250px;
            min-height: 100vh;
            background: #2c3e50;
            color: white;
            transition: all 0.3s;
        }

        #sidebar .nav-link {
            color: #bdc3c7;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
        }

        #sidebar .nav-link.active {
            background-color: #34495e;
            color: #ecf0f1;
        }

        #content {
            flex-grow: 1;
        }

        .card-custom {
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .chart-container {
            position: relative;
            height: 400px;
        }

        .table-container {
            overflow-x: auto;
        }
    </style>
</head>