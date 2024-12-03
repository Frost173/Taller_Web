<?php
// Deshabilitar la salida de errores al navegador
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Log de errores a un archivo
ini_set('log_errors', 1);
ini_set('error_log', 'debug.log');

// Definir SECURE_ACCESS para config.php
define('SECURE_ACCESS', true);

// Configurar el header de Content-Type una sola vez al inicio
header('Content-Type: application/json');

try {
    // Incluir el archivo de configuración
    $dbConfig = require_once 'config.php';
    $conn = $dbConfig['conn'];

    // Verificar que la conexión existe y está activa
    if (!$conn || !($conn instanceof PDO)) {
        throw new Exception('La conexión a la base de datos no está configurada correctamente');
    }

    $sql = "SELECT 
                e.id_estacionamiento as id,
                v.placa,
                t.tipo,
                e.fecha_entrada,
                e.hora_entrada,
                e.fecha_salida,
                e.hora_salida,
                e.estado,
                t.precio
            FROM Estacionamiento e
            JOIN Vehiculo v ON e.id_vehiculo = v.id_vehiculo
            JOIN Tipo t ON v.id_tipo = t.id_tipo
            ORDER BY e.fecha_entrada DESC, e.hora_entrada DESC";
    
    // Log de la consulta SQL para debugging
    error_log("Ejecutando consulta: " . $sql);
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new PDOException('Error preparando la consulta');
    }
    
    if (!$stmt->execute()) {
        $error = $stmt->errorInfo();
        throw new PDOException('Error ejecutando la consulta: ' . implode(', ', $error));
    }

    $vehiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($vehiculos === false) {
        throw new PDOException('Error obteniendo los resultados');
    }
    
    // Log del número de resultados
    error_log("Número de vehículos encontrados: " . count($vehiculos));
    
    // Formatear los datos para el frontend
    foreach ($vehiculos as &$vehiculo) {
        // Asegurar que todos los campos existan para evitar errores en el frontend
        $vehiculo = array_merge([
            'id' => null,
            'placa' => '',
            'tipo' => '',
            'fecha_entrada' => null,
            'hora_entrada' => null,
            'fecha_salida' => null,
            'hora_salida' => null,
            'estado' => '',
            'precio' => 0
        ], $vehiculo);

        if ($vehiculo['fecha_entrada'] && $vehiculo['hora_entrada']) {
            $vehiculo['fecha_hora'] = $vehiculo['fecha_entrada'] . ' ' . $vehiculo['hora_entrada'];
        } else {
            $vehiculo['fecha_hora'] = null;
        }
    }
    
    echo json_encode($vehiculos, JSON_THROW_ON_ERROR);
    
} catch(PDOException $e) {
    error_log("Error PDO: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Error de base de datos',
        'details' => $e->getMessage()
    ], JSON_THROW_ON_ERROR);
} catch(JsonException $e) {
    error_log("Error JSON: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Error codificando JSON',
        'details' => $e->getMessage()
    ], JSON_THROW_ON_ERROR);
} catch(Exception $e) {
    error_log("Error general: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Error general',
        'details' => $e->getMessage()
    ], JSON_THROW_ON_ERROR);
}