<?php
session_start();
require '../config/conexion.php'; // conexión PDO

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_consulta = $_POST['id_consulta'] ?? null;
    $cantidad = $_POST['cantidad'] ?? null;

    // Validar campos obligatorios
    if (empty($id_consulta) || empty($cantidad)) {
        $_SESSION['error'] = "Todos los campos son obligatorios.";
        header("Location: ../administrador/consultas.php");
        exit;
    }

    // Validar que cantidad sea un número válido (entero o decimal con 2 decimales)
    if (!preg_match('/^\d+(\.\d{1,2})?$/', $cantidad)) {
        $_SESSION['error'] = "La cantidad ingresada no es válida. Solo se permiten números.";
        header("Location: ../administrador/consultas.php");
        exit;
    }

    // Convertir a float para validación
    $cantidad = (float)$cantidad;

    // Validar mínimo
    if ($cantidad < 500) {
        $_SESSION['error'] = "La cantidad mínima a pagar es 500.";
        header("Location: ../administrador/consultas.php");
        exit;
    }

    try {
        // Verificar si ya fue pagada
        $stmt = $pdo->prepare("SELECT pagado FROM consultas WHERE id_consulta = :id");
        $stmt->execute([':id' => $id_consulta]);
        $consulta = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$consulta) {
            $_SESSION['error'] = "La consulta no existe.";
            header("Location: ../administrador/consultas.php");
            exit;
        }

        if ($consulta['pagado'] == 1) {
            $_SESSION['info'] = "Esta consulta ya fue pagada previamente.";
            header("Location: ../administrador/consultas.php");
            exit;
        }

        // Actualizar la consulta: pagado = 1 y precio = cantidad ingresada
        $stmt = $pdo->prepare("
            UPDATE consultas 
            SET pagado = 1, precio = :cantidad 
            WHERE id_consulta = :id
        ");
        $stmt->execute([
            ':cantidad' => $cantidad,
            ':id' => $id_consulta
        ]);

        $_SESSION['success'] = "Pago registrado correctamente.";
        header("Location: ../administrador/consultas.php");
        exit;

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al registrar el pago: " . $e->getMessage();
        header("Location: ../administrador/consultas.php");
        exit;
    }

} else {
    $_SESSION['error'] = "Método no permitido.";
    header("Location: ../administrador/consultas.php");
    exit;
}
