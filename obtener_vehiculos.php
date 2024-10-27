<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

try {
    $configData = require_once 'config.php';
    $conn = $configData['conn'];

    if (!$conn) {
        throw new Exception("Error de conexión a la base de datos");
    }

    $query = "SELECT id, placa, estacionamiento, fecha_hora, estado 
              FROM entrada 
              WHERE fecha_salida IS NULL 
              ORDER BY fecha_hora DESC";
    
    $result = $conn->query($query);
    
    if ($result === false) {
        throw new Exception("Error en la consulta: " . $conn->error);
    }

    $vehiculos = [];
    while ($row = $result->fetch_assoc()) {
        // Asegurarse de que todas las columnas necesarias estén presentes
        $vehiculos[] = [
            'id' => $row['id'],
            'placa' => $row['placa'],
            'estacionamiento' => $row['estacionamiento'],
            'fecha_hora' => $row['fecha_hora'],
            'estado' => $row['estado']
        ];
    }

    echo json_encode([
        'success' => true,
        'data' => $vehiculos
    ]);

} catch (Exception $e) {
    error_log("Error en obtener_vehiculos.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}