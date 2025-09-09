<?php
session_start();
require '../config/conexion.php'; // conexión PDO $pdo

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura de variables obligatorias
    $id_paciente   = trim($_POST['id_paciente'] ?? '');
    $id_hospital   = trim($_POST['id_hospital'] ?? '');
    $tipo_consulta = trim($_POST['tipo_consulta'] ?? '');
    $motivo        = trim($_POST['motivo'] ?? '');
    $id_usuario    = $_SESSION['id_usuario'] ?? null; 
    $id_medico     = $_SESSION['id_usuario'] ?? null; 

    // Captura signos vitales
    $temperatura        = $_POST['temperatura'] ?? '';
    $presion_arterial   = $_POST['presion_arterial'] ?? '';
    $tension_arterial   = $_POST['tension_arterial'] ?? '';
    $saturacion_oxigeno = $_POST['saturacion_oxigeno'] ?? '';
    $pulso              = $_POST['pulso'] ?? '';
    $peso               = $_POST['peso'] ?? '';
    $talla              = $_POST['talla'] ?? '';

    // Captura detalle
    $orina                    = $_POST['orina'] ?? '';
    $defeca                   = $_POST['defeca'] ?? '';
    $horas_sueno              = $_POST['horas_sueno'] ?? '';
    $transfusiones_sanguineas = $_POST['transfusiones_sanguineas'] ?? '';
    $antecedentes_familiares  = $_POST['antecedentes_familiares'] ?? '';
    $antecedentes_conyuge     = $_POST['antecedentes_conyuge'] ?? '';
    $alergias                 = $_POST['alergias'] ?? '';
    $operaciones              = $_POST['operaciones'] ?? '';

    // =============================
    // VALIDACIONES
    // =============================
    if (empty($id_paciente) || empty($id_hospital) || empty($tipo_consulta) || 
        empty($motivo) || empty($temperatura) || empty($presion_arterial) ||
        empty($tension_arterial) || empty($saturacion_oxigeno) || empty($pulso) ||
        empty($peso) || empty($talla) || empty($orina) || empty($defeca) ||
        empty($horas_sueno) || empty($antecedentes_familiares) || 
        empty($antecedentes_conyuge) || empty($alergias) || empty($operaciones)) {

        $_SESSION['error'] = "Todos los campos obligatorios deben ser completados.";
        header("Location: ../administrador/consultas.php");
        exit();
    }

    try {
        $pdo->beginTransaction();

        // Insertar en consultas
        $sql1 = "INSERT INTO consultas 
            (id_paciente, id_hospital, id_medico, tipo_consulta, motivo, 
             temperatura, presion_arterial, tension_arterial, saturacion_oxigeno, pulso, 
             peso, talla, id_usuario)
            VALUES
            (:id_paciente, :id_hospital, :id_medico, :tipo_consulta, :motivo,
             :temperatura, :presion_arterial, :tension_arterial, :saturacion_oxigeno, :pulso,
             :peso, :talla, :id_usuario)";
        
        $stmt1 = $pdo->prepare($sql1);
        $stmt1->execute([
            ':id_paciente'        => $id_paciente,
            ':id_hospital'        => $id_hospital,
            ':id_medico'          => $id_medico,
            ':tipo_consulta'      => $tipo_consulta,
            ':motivo'             => $motivo,
            ':temperatura'        => $temperatura,
            ':presion_arterial'   => $presion_arterial,
            ':tension_arterial'   => $tension_arterial,
            ':saturacion_oxigeno' => $saturacion_oxigeno,
            ':pulso'              => $pulso,
            ':peso'               => $peso,
            ':talla'              => $talla,
            ':id_usuario'         => $id_usuario,
        ]);

        $id_consulta = $pdo->lastInsertId();

        // Insertar en detalle_consulta
        $sql2 = "INSERT INTO detalle_consulta 
            (id_consulta, orina, defeca, horas_sueno, transfuciones, antecedentes_familiares,
             antecedentes_conyuge, alergias, operaciones, id_usuario)
            VALUES
            (:id_consulta, :orina, :defeca, :horas_sueno, :transfuciones, :antecedentes_familiares,
             :antecedentes_conyuge, :alergias, :operaciones, :id_usuario)";

        $stmt2 = $pdo->prepare($sql2);
        $stmt2->execute([
            ':id_consulta'             => $id_consulta,
            ':orina'                   => $orina,
            ':defeca'                  => $defeca,
            ':horas_sueno'             => $horas_sueno,
            ':transfuciones'           => $transfusiones_sanguineas,
            ':antecedentes_familiares' => $antecedentes_familiares,
            ':antecedentes_conyuge'    => $antecedentes_conyuge,
            ':alergias'                => $alergias,
            ':operaciones'             => $operaciones,
            ':id_usuario'              => $id_usuario,
        ]);

        // =============================
        // Insertar log
        // =============================
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';
        $dispositivo = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';
        $accion = "Creación de consulta";
        $descripcion = "Consulta ID $id_consulta creada para el paciente ID $id_paciente";

        $sqlLog = "INSERT INTO logs (id_usuario, accion, descripcion, ip_origen, dispositivo)
                   VALUES (:id_usuario, :accion, :descripcion, :ip_origen, :dispositivo)";
        $stmtLog = $pdo->prepare($sqlLog);
        $stmtLog->execute([
            ':id_usuario'  => $id_usuario,
            ':accion'      => $accion,
            ':descripcion' => $descripcion,
            ':ip_origen'   => $ip,
            ':dispositivo' => $dispositivo
        ]);

        $pdo->commit();
        $_SESSION['success'] = "Consulta registrada correctamente y log guardado.";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error al guardar la consulta: " . $e->getMessage();
    }

    header("Location: ../administrador/consultas.php");
    exit();
}
