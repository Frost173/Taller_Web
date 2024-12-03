<?php
// Deshabilitar la salida de errores al navegador
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Log de errores a un archivo
ini_set('log_errors', 1);
ini_set('error_log', 'debug.log');

// Establecer el header de Content-Type
header('Content-Type: application/json');

try {
    // Incluir archivo de configuración
    $dbConfig = require_once 'config.php';
    $conn = $dbConfig['conn'];

    // Verificar que la conexión existe y está activa
    if (!$conn || !($conn instanceof PDO)) {
        throw new Exception('La conexión a la base de datos no está configurada correctamente');
    }

    $sql = "SELECT COUNT(*) as total FROM Estacionamiento WHERE estado = 'Partio'";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt->execute()) {
        throw new PDOException('Error ejecutando la consulta');
    }
    
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($resultado === false) {
        throw new PDOException('Error obteniendo los resultados');
    }

    echo json_encode([
        'success' => true,
        'total' => (int)$resultado['total']
    ], JSON_THROW_ON_ERROR);
    
} catch(PDOException $e) {
    error_log("Error PDO en contar_salidas.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error de base de datos',
        'details' => $e->getMessage()
    ], JSON_THROW_ON_ERROR);
} catch(Exception $e) {
    error_log("Error general en contar_salidas.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error general',
        'details' => $e->getMessage()
    ], JSON_THROW_ON_ERROR);
}