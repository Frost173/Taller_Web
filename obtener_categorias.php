<?php
// obtener_categorias.php
header('Content-Type: application/json');

$configData = include 'config.php';
$conn = $configData['conn'];

try {
    // Asegurarse de seleccionar específicamente el id_tipo
    $query = "SELECT id_tipo, tipo, precio, modo FROM Tipo ORDER BY id_tipo";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Log para debugging
    error_log('Categorías obtenidas: ' . print_r($categorias, true));
    
    echo json_encode($categorias);
} catch (PDOException $e) {
    error_log('Error en obtener_categorias.php: ' . $e->getMessage());
    echo json_encode([
        "error" => true,
        "message" => "Error al obtener las categorías: " . $e->getMessage()
    ]);
}
?>