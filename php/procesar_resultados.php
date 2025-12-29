<?php
session_start();
require_once "../config/conexion.php";

// ================== VALIDACIONES BÁSICAS ==================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../administrador/analiticas.php");
    exit;
}

if (empty($_POST['id_analitica']) || empty($_POST['resultado'])) {
    $_SESSION['error'] = "Datos incompletos.";
    header("../administrador/analiticas.php");
    exit;
}

// Usuario autenticado
$id_usuario = $_SESSION['id_usuario'] ?? null;
if (!$id_usuario) {
    $_SESSION['error'] = "Sesión inválida.";
    header("../administrador/analiticas.php");
    exit;
}

// ================== CONFIG SUBIDA ARCHIVOS ==================
$uploadDir = "../uploads/";
$allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
$maxFileSize = 5 * 1024 * 1024; // 5MB

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

try {
    $pdo->beginTransaction();

    // ================== VALIDAR QUE TODAS ESTÉN PAGADAS ==================
    $sqlCheck = "SELECT pagado FROM analiticas WHERE id_analitica = :id_analitica LIMIT 1";
    $stmtCheck = $pdo->prepare($sqlCheck);

    foreach ($_POST['id_analitica'] as $id_analitica) {
        $stmtCheck->execute([':id_analitica' => $id_analitica]);
        $analitica = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if (!$analitica || (int)$analitica['pagado'] !== 1) {
            throw new Exception(
                "Por favor, pague primero las analíticas antes de registrar los resultados."
            );
        }
    }

    // ================== GUARDAR RESULTADOS ==================
    foreach ($_POST['id_analitica'] as $index => $id_analitica) {

        $resultado = trim($_POST['resultado'][$index]);
        $valores_ref = $_POST['valores_referencia'][$index] ?? null;
        $archivoFinal = null;

        // ---------- SUBIDA DE ARCHIVO ----------
        if (!empty($_FILES['archivo']['name'][$index])) {

            $tmp  = $_FILES['archivo']['tmp_name'][$index];
            $name = $_FILES['archivo']['name'][$index];
            $size = $_FILES['archivo']['size'][$index];
            $err  = $_FILES['archivo']['error'][$index];

            if ($err !== UPLOAD_ERR_OK) {
                throw new Exception("Error al subir archivo.");
            }

            if ($size > $maxFileSize) {
                throw new Exception("El archivo supera el tamaño permitido (5MB).");
            }

            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedExtensions)) {
                throw new Exception("Tipo de archivo no permitido.");
            }

            $archivoFinal = 'analitica_' . $id_analitica . '_' . uniqid() . '.' . $ext;

            if (!move_uploaded_file($tmp, $uploadDir . $archivoFinal)) {
                throw new Exception("No se pudo guardar el archivo.");
            }
        }

        // ---------- UPDATE ----------
        $sqlUpdate = "UPDATE analiticas
                      SET resultado = :resultado,
                          valores_referencia = :valores_ref,
                          archivo = :archivo,
                          estado = 'Entregado',
                          id_usuario = :id_usuario
                      WHERE id_analitica = :id_analitica";

        $stmtUpdate = $pdo->prepare($sqlUpdate);
        $stmtUpdate->execute([
            ':resultado'    => $resultado,
            ':valores_ref'  => $valores_ref,
            ':archivo'      => $archivoFinal,
            ':id_usuario'   => $id_usuario,
            ':id_analitica' => $id_analitica
        ]);
    }

    $pdo->commit();
    $_SESSION['success'] = "Resultados registrados correctamente.";

} catch (Exception $e) {

    $pdo->rollBack();

    // Mensaje claro para el usuario
    $_SESSION['error'] = $e->getMessage();

    // Debug opcional
    // error_log($e->getMessage());
}

header("Location: ../administrador/analiticas.php");
exit;
