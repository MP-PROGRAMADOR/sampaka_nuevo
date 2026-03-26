<?php
session_start();
require_once "../config/conexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $concepto = trim($_POST['concepto']);
    $monto = floatval($_POST['monto']);
    $fecha_gasto = $_POST['fecha_gasto'];
    $id_hospital = $_SESSION['id_hospital'] ?? 1;

    // 1. Validación de campos vacíos
    if (empty($concepto) || $monto <= 0) {
        $_SESSION['error'] = "Por favor, introduce un concepto y un monto válido.";
        header("Location: ../administrador/finanzas.php");
        exit();
    }

    try {
       
        $stmtIngresos = $pdo->query("SELECT (SELECT SUM(monto) FROM ingresos) + (SELECT SUM(cantidad) FROM pagos) AS total");
        $totalIngresos = $stmtIngresos->fetchColumn() ?: 0;

        // Sumamos todos los gastos previos
        $stmtGastos = $pdo->query("SELECT SUM(monto) FROM gastos");
        $totalGastosPrevios = $stmtGastos->fetchColumn() ?: 0;

        $saldoDisponible = $totalIngresos - $totalGastosPrevios;

        // 3. VALIDACIÓN DE CAJA
        if ($monto > $saldoDisponible) {
            $_SESSION['error'] = "Fondos insuficientes. El saldo actual es de " . number_format($saldoDisponible, 0) . " FCFA.";
            header("Location: ../administrador/finanzas.php");
            exit();
        }

        // 4. SI HAY SALDO, PROCEDER CON EL REGISTRO
        $sql = "INSERT INTO gastos (id_hospital, concepto, monto, fecha_gasto) 
                VALUES (:id_hospital, :concepto, :monto, :fecha_gasto)";

        $stmt = $pdo->prepare($sql);
        $resultado = $stmt->execute([
            ':id_hospital' => $id_hospital,
            ':concepto'    => $concepto,
            ':monto'       => $monto,
            ':fecha_gasto' => $fecha_gasto
        ]);

        if ($resultado) {
            $_SESSION['success'] = "Gasto registrado correctamente. El nuevo saldo es: " . number_format($saldoDisponible - $monto, 0) . " FCFA.";
        } else {
            $_SESSION['error'] = "No se pudo registrar el gasto.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
    }

    header("Location: ../administrador/finanzas.php");
    exit();
} else {
    header("Location: ../administrador/finanzas.php");
    exit();
}
