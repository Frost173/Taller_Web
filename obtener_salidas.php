<?php
// Verificar acceso seguro
//if (!defined('SECURE_ACCESS')) {
   // define('SECURE_ACCESS', true);
//}

// Incluir configuración
require_once 'config.php';

header('Content-Type: application/json');

try {
    // Obtener todos los vehículos ordenados por fecha de entrada
    $sql = "SELECT id, placa, tipo, estacionamiento, precio, fecha_hora, estado, fecha_salida 
            FROM entrada 
            ORDER BY fecha_hora DESC";
    
    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception("Error al obtener los datos: " . $conn->error);
    }
    
    $vehiculos = array();
    while ($row = $result->fetch_assoc()) {
        $vehiculos[] = $row;
    }
    
    echo json_encode($vehiculos);

} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}

$conn->close();
?>