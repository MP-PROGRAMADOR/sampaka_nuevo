<?php
session_start();
require_once "../config/conexion.php";

/* ========= FUNCIONES LOG ========= */

function getIP()
{
    return $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';
}

function getDispositivo()
{
    return $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';
}

function registrarLog($pdo, $id_usuario, $accion, $descripcion)
{
    $stmt = $pdo->prepare("
        INSERT INTO logs (id_usuario, accion, descripcion, ip_origen, dispositivo)
        VALUES (:id_usuario, :accion, :descripcion, :ip, :dispositivo)
    ");
    $stmt->execute([
        ':id_usuario' => $id_usuario,
        ':accion'     => $accion,
        ':descripcion' => $descripcion,
        ':ip'         => getIP(),
        ':dispositivo' => getDispositivo()
    ]);
}

/* ========= VALIDAR MÉTODO ========= */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $_SESSION['error'] = "Método no permitido.";
    header("Location: ../administrador/analiticas.php");
    exit;
}

/* ========= DATOS ========= */
$id_usuario = $_SESSION['id_usuario'] ?? null;
$id_paciente = $_POST['id_paciente'] ?? null;
$fecha_pago  = $_POST['fecha_pago'] ?? null;
$monto       = $_POST['monto'] ?? null;
$seleccionadas_json = $_POST['analiticas_seleccionadas'] ?? '[]';

$analiticas = json_decode($seleccionadas_json, true);

/* ========= VALIDACIONES ========= */
if (!$id_usuario || !$id_paciente || !$fecha_pago || empty($analiticas)) {
    $_SESSION['error'] = "Datos incompletos para procesar el pago.";
    header("Location: ../administrador/analiticas.php");
    exit;
}

if (!is_numeric($monto) || $monto <= 0) {
    $_SESSION['error'] = "Monto recibido no válido.";
    header("Location: ../administrador/analiticas.php");
    exit;
}

try {
    $pdo->beginTransaction();

    $total_calculado = 0;
    $detalle_pruebas = [];

    foreach ($analiticas as $id_analitica) {

        $stmt = $pdo->prepare("
            SELECT a.id_analitica, a.id_prueba, pr.nombre, pr.precio
            FROM analiticas a
            JOIN pruebas_medicas pr ON a.id_prueba = pr.id_prueba
            WHERE a.id_analitica = :id
              AND a.pagado = 0
        ");
        $stmt->execute([':id' => $id_analitica]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            throw new Exception("Analítica inválida o ya pagada.");
        }

        $precio = (float)$row['precio'];
        $total_calculado += $precio;

        $detalle_pruebas[] = $row['nombre'] . " ({$precio} FCFA)";

        /* INSERTAR PAGO */
        $stmtPago = $pdo->prepare("
            INSERT INTO pagos (cantidad, id_analitica, id_prueba, id_usuario)
            VALUES (:cantidad, :id_analitica, :id_prueba, :id_usuario)
        ");
        $stmtPago->execute([
            ':cantidad'     => $precio,
            ':id_analitica' => $row['id_analitica'],
            ':id_prueba'    => $row['id_prueba'],
            ':id_usuario'   => $id_usuario
        ]);

        /* MARCAR COMO PAGADO */
        $stmtUpd = $pdo->prepare("
            UPDATE analiticas 
            SET pagado = 1 
            WHERE id_analitica = :id
        ");
        $stmtUpd->execute([':id' => $row['id_analitica']]);
    }

    // Normalizar a 2 decimales para evitar errores flotantes
    $monto = round((float)$monto, 2);
    $total_calculado = round((float)$total_calculado, 2);

    if ($monto !== $total_calculado) {
        throw new Exception(
            "El monto recibido ({$monto} FCFA) no coincide con el total a pagar ({$total_calculado} FCFA)."
        );
    }


    /* ========= LOG DE ÉXITO ========= */
    $descripcion = "Pago registrado. Paciente ID: {$id_paciente}. "
        . "Total: {$total_calculado} FCFA. "
        . "Pruebas: " . implode(', ', $detalle_pruebas);

    registrarLog($pdo, $id_usuario, "REGISTRO_PAGO", $descripcion);

    $pdo->commit();

    $_SESSION['success'] = "✅ Pago registrado correctamente. Total: {$total_calculado} FCFA";
    header("Location: ../administrador/analiticas.php");
    exit;
} catch (Exception $e) {

    $pdo->rollBack();

    /* ========= LOG DE ERROR ========= */
    registrarLog(
        $pdo,
        $id_usuario,
        "ERROR_PAGO",
        "Error al procesar pago del paciente ID {$id_paciente}: " . $e->getMessage()
    );

    $_SESSION['error'] = "❌ Error en el pago: " . $e->getMessage();
    header("Location: ../administrador/analiticas.php");
    exit;
}
