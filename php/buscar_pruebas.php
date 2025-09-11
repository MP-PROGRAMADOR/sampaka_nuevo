<?php
// buscar_pruebas.php
session_start();
require '../config/conexion.php'; // tu conexión PDO

// Obtener el término de búsqueda
$q = $_GET['q'] ?? '';
$q = trim($q);

// Validar longitud mínima
if(strlen($q) < 2){
    echo '';
    exit;
}

try {
    // Preparar consulta: buscar pruebas por nombre, ordenadas por nombre
    $stmt = $pdo->prepare("
        SELECT id_prueba, nombre, precio 
        FROM pruebas_medicas 
        WHERE nombre LIKE :query
        ORDER BY nombre ASC
    ");
    $stmt->execute([
        ':query' => "%$q%"
    ]);

    $pruebas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if($pruebas){
        foreach($pruebas as $prueba){
            echo '<button type="button" class="list-group-item list-group-item-action seleccionar-prueba d-flex justify-content-between align-items-center shadow-sm mb-1"';
            echo ' data-id-prueba="'. $prueba['id_prueba'] .'"';
            echo ' data-nombre-prueba="'. htmlspecialchars($prueba['nombre']) .'">';
            echo '<span>'. htmlspecialchars($prueba['nombre']) .'</span>';
            echo '<span class="badge bg-primary rounded-pill">'. number_format($prueba['precio'], 2) .' €</span>';
            echo '</button>';
        }
    } else {
        echo '<div class="list-group-item text-muted">No se encontraron pruebas.</div>';
    }

} catch(PDOException $e){
    echo '<div class="list-group-item text-danger">Error al buscar pruebas: '. $e->getMessage() .'</div>';
}
