<?php
// obtener_tipos.php
header('Content-Type: application/json');

try {
    require_once 'conexion.php'; // Asegúrate de tener este archivo con la conexión a la BD

    $query = "SELECT id_tipo, tipo, precio, modo FROM Tipo ORDER BY tipo";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    $tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $tipos
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Error al obtener los tipos de vehículos: ' . $e->getMessage()
    ]);
}
?>