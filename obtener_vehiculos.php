<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

header('Content-Type: application/json');

try {
    // Capturar salida
    ob_start();
    
    // Registrar la llamada
    error_log("obtener_vehiculos.php llamado: " . date('Y-m-d H:i:s'));

    // Incluir el archivo de configuración
    $configData = require 'config.php';
    
    if (!isset($configData['conn']) || !$configData['conn'] instanceof PDO) {
        throw new Exception('Error de configuración: conexión no disponible');
    }
    
    $conn = $configData['conn'];

    // Consulta SQL modificada para obtener todos los vehículos, incluyendo los que ya partieron
    $query = "
        SELECT 
            e.id_estacionamiento as id,
            v.placa,
            t.tipo,
            t.precio,
            DATE_FORMAT(e.fecha_entrada, '%d/%m/%Y') as fecha_entrada,
            TIME_FORMAT(e.hora_entrada, '%H:%i') as hora_entrada,
            e.estado,
            COALESCE(p.estado_pago, 'pendiente') as estado_pago,
            DATE_FORMAT(e.fecha_salida, '%d/%m/%Y') as fecha_salida,
            TIME_FORMAT(e.hora_salida, '%H:%i') as hora_salida
        FROM Estacionamiento e
        JOIN Vehiculo v ON e.id_vehiculo = v.id_vehiculo
        JOIN Tipo t ON v.id_tipo = t.id_tipo
        LEFT JOIN Pago p ON e.id_estacionamiento = p.id_estacionamiento
        ORDER BY 
            CASE 
                WHEN e.estado = 'Estacionado' THEN 1
                ELSE 2
            END,
            e.fecha_entrada DESC, 
            e.hora_entrada DESC";

    $statement = $conn->prepare($query);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);

    // Procesar los resultados para asegurar formato consistente
    foreach ($result as &$row) {
        // Asegurar que todas las fechas y horas tengan un formato consistente
        if (empty($row['fecha_salida'])) {
            $row['fecha_salida'] = null;
        }
        if (empty($row['hora_salida'])) {
            $row['hora_salida'] = null;
        }
        
        // Asegurar que el estado esté correctamente establecido
        if ($row['fecha_salida'] !== null) {
            $row['estado'] = 'Partio';
        }
    }
    unset($row); // Romper la referencia

    $response = ['success' => true, 'data' => $result];

} catch (PDOException $e) {
    error_log("Error en obtener_vehiculos.php: " . $e->getMessage());
    $response = ['success' => false, 'error' => 'Error en la base de datos: ' . $e->getMessage()];
} catch (Exception $e) {
    error_log("Error en obtener_vehiculos.php: " . $e->getMessage());
    $response = ['success' => false, 'error' => 'Error del servidor: ' . $e->getMessage()];
} finally {
    // Limpiar el buffer de salida
    $output = ob_get_clean();
    if (!empty($output)) {
        error_log("Salida inesperada en obtener_vehiculos.php: " . $output);
    }

    // Enviar respuesta
    echo json_encode($response);
}
?>