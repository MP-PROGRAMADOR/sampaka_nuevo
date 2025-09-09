<?php
session_start();
require '../config/conexion.php'; // conexión PDO $pdo

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_consulta    = $_POST['id_consulta'] ?? null;
    if (!$id_consulta) {
        $_SESSION['error'] = "ID de consulta no especificado.";
        header("Location: ../administrador/consultas.php");
        exit();
    }

    $id_paciente   = trim($_POST['id_paciente'] ?? '');
    $id_hospital   = trim($_POST['id_hospital'] ?? '');
    $tipo_consulta = trim($_POST['tipo_consulta'] ?? '');
    $motivo        = trim($_POST['motivo'] ?? '');
    $diagnostico   = trim($_POST['diagnostico'] ?? '');
    $precio        = trim($_POST['precio'] ?? 0);
    $pagado        = isset($_POST['pagado']) ? 1 : 0;
    $id_usuario    = $_SESSION['user_id'] ?? null; 
    $id_medico     = $_SESSION['user_id'] ?? null;

    $temperatura        = $_POST['temperatura'] ?? '';
    $presion_arterial   = $_POST['presion_arterial'] ?? '';
    $tension_arterial   = $_POST['tension_arterial'] ?? '';
    $saturacion_oxigeno = $_POST['saturacion_oxigeno'] ?? '';
    $pulso              = $_POST['pulso'] ?? '';
    $peso               = $_POST['peso'] ?? '';
    $talla              = $_POST['talla'] ?? '';

    $orina                    = $_POST['orina'] ?? '';
    $defeca                   = $_POST['defeca'] ?? '';
    $horas_sueno              = $_POST['horas_sueno'] ?? '';
    $transfusiones_sanguineas = $_POST['transfusiones_sanguineas'] ?? '';
    $antecedentes_familiares  = $_POST['antecedentes_familiares'] ?? '';
    $antecedentes_conyuge     = $_POST['antecedentes_conyuge'] ?? '';
    $alergias                 = $_POST['alergias'] ?? '';
    $operaciones              = $_POST['operaciones'] ?? '';

    try {
        $pdo->beginTransaction();

        // ================================
        // Actualizar tabla consultas
        // ================================
        $sql1 = "UPDATE consultas SET
                    id_paciente = :id_paciente,
                    id_hospital = :id_hospital,
                    id_medico = :id_medico,
                    tipo_consulta = :tipo_consulta,
                    motivo = :motivo,
                    diagnostico = :diagnostico,
                    pagado = :pagado,
                    precio = :precio,
                    temperatura = :temperatura,
                    presion_arterial = :presion_arterial,
                    tension_arterial = :tension_arterial,
                    saturacion_oxigeno = :saturacion_oxigeno,
                    pulso = :pulso,
                    peso = :peso,
                    talla = :talla,
                    id_usuario = :id_usuario
                 WHERE id_consulta = :id_consulta";

        $stmt1 = $pdo->prepare($sql1);
        $stmt1->execute([
            ':id_paciente'        => $id_paciente,
            ':id_hospital'        => $id_hospital,
            ':id_medico'          => $id_medico,
            ':tipo_consulta'      => $tipo_consulta,
            ':motivo'             => $motivo,
            ':diagnostico'        => $diagnostico,
            ':pagado'             => $pagado,
            ':precio'             => $precio,
            ':temperatura'        => $temperatura,
            ':presion_arterial'   => $presion_arterial,
            ':tension_arterial'   => $tension_arterial,
            ':saturacion_oxigeno' => $saturacion_oxigeno,
            ':pulso'              => $pulso,
            ':peso'               => $peso,
            ':talla'              => $talla,
            ':id_usuario'         => $id_usuario,
            ':id_consulta'        => $id_consulta,
        ]);

        // ================================
        // Actualizar tabla detalle_consulta
        // ================================
        $sql2 = "UPDATE detalle_consulta SET
                    orina = :orina,
                    defeca = :defeca,
                    horas_sueno = :horas_sueno,
                    transfuciones = :transfuciones,
                    antecedentes_familiares = :antecedentes_familiares,
                    antecedentes_conyuge = :antecedentes_conyuge,
                    alergias = :alergias,
                    operaciones = :operaciones,
                    id_usuario = :id_usuario
                 WHERE id_consulta = :id_consulta";

        $stmt2 = $pdo->prepare($sql2);
        $stmt2->execute([
            ':orina'                   => $orina,
            ':defeca'                  => $defeca,
            ':horas_sueno'             => $horas_sueno,
            ':transfuciones'           => $transfusiones_sanguineas,
            ':antecedentes_familiares' => $antecedentes_familiares,
            ':antecedentes_conyuge'    => $antecedentes_conyuge,
            ':alergias'                => $alergias,
            ':operaciones'             => $operaciones,
            ':id_usuario'              => $id_usuario,
            ':id_consulta'             => $id_consulta,
        ]);

        // ================================
        // Guardar acción en logs
        // ================================
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';
        $dispositivo = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';
        $accion = "Actualización de consulta";
        $descripcion = "Consulta ID $id_consulta actualizada por usuario ID $id_usuario";

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
        $_SESSION['success'] = "Consulta actualizada correctamente y log guardado.";

    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error al actualizar la consulta: " . $e->getMessage();
    }

    header("Location: ../administrador/consultas.php");
    exit();
}
