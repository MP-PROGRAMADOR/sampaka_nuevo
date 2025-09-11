<?php
require_once "../config/conexion.php";

if (isset($_GET['q'])) {
    $q = trim($_GET['q']);

    try {
        $stmt = $pdo->prepare("SELECT id_personal, nombre, apellido, cargo 
                               FROM personal 
                               WHERE nombre LIKE :q OR apellido LIKE :q 
                               ORDER BY nombre ASC 
                               LIMIT 10");
        $stmt->execute([':q' => "%$q%"]);
        $personal = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($personal);
    } catch (PDOException $e) {
        echo json_encode([]);
    }
}
?>
