<?php
session_start();
require_once "../config/conexion.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Detectar si es edición (viene ID) o nuevo (no viene ID)
    $id_hosp     = !empty($_POST['id_hospitalizacion']) ? $_POST['id_hospitalizacion'] : null;
    
    $id_paciente = $_POST['id_paciente'] ?? null;
    $id_hospital = $_POST['id_hospital'] ?? null;
    $id_sala     = $_POST['id_sala'] ?? null;
    $numero_cama = trim($_POST['numero_cama'] ?? '');
    $fecha_ing   = $_POST['fecha_ingreso'] ?? date('Y-m-d');
    $causa       = trim($_POST['causa'] ?? '');
    $id_usuario  = $_SESSION['id_usuario'] ?? 1;

    if (!$id_paciente || !$id_sala || empty($numero_cama)) {
        $_SESSION['error'] = "Faltan datos obligatorios para procesar el registro.";
        header("Location: ../administrador/hospitalizaciones.php");
        exit;
    }

    try {
        if ($id_hosp) {
            // LÓGICA DE EDICIÓN
            $sql = "UPDATE hospitalizaciones SET 
                        id_paciente = :id_p, id_hospital = :id_h, 
                        id_sala = :id_s, numero_cama = :cama, 
                        fecha_ingreso = :fecha, causa = :causa 
                    WHERE id_hospitalizacion = :id_hosp";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id_p' => $id_paciente, ':id_h' => $id_hospital,
                ':id_s' => $id_sala, ':cama' => $numero_cama,
                ':fecha' => $fecha_ing, ':causa' => $causa,
                ':id_hosp' => $id_hosp
            ]);
            $_SESSION['success'] = "Registro actualizado correctamente.";
        } else {
            // LÓGICA DE NUEVO INGRESO
            $sql = "INSERT INTO hospitalizaciones (id_paciente, id_hospital, id_sala, numero_cama, fecha_ingreso, causa, id_usuario) 
                    VALUES (:id_p, :id_h, :id_s, :cama, :fecha, :causa, :id_u)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id_p' => $id_paciente, ':id_h' => $id_hospital,
                ':id_s' => $id_sala, ':cama' => $numero_cama,
                ':fecha' => $fecha_ing, ':causa' => $causa,
                ':id_u' => $id_usuario
            ]);
            $_SESSION['success'] = "Ingreso registrado con éxito.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }

    header("Location: ../administrador/hospitalizaciones.php");
    exit;
}