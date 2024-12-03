<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

header('Content-Type: application/json');

try {
    if (!isset($_GET['id'])) {
        throw new Exception("ID de estacionamiento no proporcionado");
    }

    $id_estacionamiento = intval($_GET['id']);
    
    // Cargar la configuración
    $configData = require_once 'config.php';
    if (!$configData['conn']) {
        throw new Exception("Error en la conexión a la base de datos");
    }
    
    $conn = $configData['conn'];

    // Verificar si tiene fecha y hora de salida
    $query = "SELECT fecha_salida, hora_salida FROM Estacionamiento WHERE id_estacionamiento = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id_estacionamiento]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'tieneSalida' => (!empty($resultado['fecha_salida']) && !empty($resultado['hora_salida']))
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}