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
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <link rel="stylesheet" href="../css/dataTables.bootstrap5.min.css" />
    <link rel="stylesheet" href="../css/buttons.bootstrap5.min.css" />
    <link rel="stylesheet" href="../css/responsive.bootstrap5.min.css" />



    <link href="../css/mdb.min.css" rel="stylesheet" />

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