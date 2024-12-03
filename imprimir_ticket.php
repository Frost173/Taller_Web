<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

// Asegurarse de que la respuesta sea en HTML
header('Content-Type: text/html; charset=UTF-8');

try {
    // Verificar si el ID está presente
    if (!isset($_GET['id'])) {
        throw new Exception("ID de estacionamiento no proporcionado");
    }

    $id_estacionamiento = intval($_GET['id']);
    if ($id_estacionamiento <= 0) {
        throw new Exception("ID de estacionamiento inválido");
    }

    // Cargar la configuración
    $configData = require_once 'config.php';
    if (!$configData['conn']) {
        throw new Exception("Error en la conexión a la base de datos");
    }
    
    $conn = $configData['conn'];

    // Preparar y ejecutar la consulta
    $query = "
        SELECT 
            v.placa,
            t.tipo,
            t.precio,
            e.fecha_entrada,
            e.hora_entrada,
            e.estado,
            p.estado_pago,
            COALESCE(p.monto, 0) as monto,
            IFNULL(e.fecha_salida, 'Pendiente') as fecha_salida,
            IFNULL(e.hora_salida, 'Pendiente') as hora_salida
        FROM Estacionamiento e
        JOIN Vehiculo v ON e.id_vehiculo = v.id_vehiculo
        JOIN Tipo t ON v.id_tipo = t.id_tipo
        LEFT JOIN Pago p ON e.id_estacionamiento = p.id_estacionamiento
        WHERE e.id_estacionamiento = ?
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->execute([$id_estacionamiento]);
    $vehiculo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$vehiculo) {
        throw new Exception("No se encontró el registro del vehículo");
    }

    // Log de depuración
    error_log("Datos del vehículo recuperados correctamente para ID: " . $id_estacionamiento);
    
    // Incluir la plantilla del ticket
    require_once 'views/ticket_template.php';

} catch (Exception $e) {
    // Log del error
    error_log("Error en imprimir_ticket.php: " . $e->getMessage());
    
    // Enviar respuesta de error
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
    exit;
}