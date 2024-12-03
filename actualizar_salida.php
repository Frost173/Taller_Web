<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);

date_default_timezone_set('America/Lima');
header('Content-Type: application/json');

if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

function handleError($message, $errorCode = 400) {
    http_response_code($errorCode);
    echo json_encode(['success' => false, 'error' => $message]);
    exit();
}

try {
    $config_data = require_once 'config.php';
    
    if (!isset($config_data['conn']) || !$config_data['conn'] instanceof PDO) {
        throw new Exception('Error de conexión a la base de datos');
    }
    
    $conn = $config_data['conn'];
    $conn->exec("SET time_zone = '-05:00'");

    $input = file_get_contents('php://input');
    if (empty($input)) {
        throw new Exception('No se recibieron datos');
    }

    $data = json_decode($input, true);
    if (!isset($data['id']) || !is_numeric($data['id'])) {
        throw new Exception('ID inválido o no proporcionado');
    }
    
    $id = intval($data['id']);
    
    $conn->beginTransaction();
    
    // Verificar el estado actual del vehículo
    $check_stmt = $conn->prepare("SELECT estado FROM Estacionamiento WHERE id_estacionamiento = ?");
    $check_stmt->execute([$id]);
    $estado_actual = $check_stmt->fetchColumn();
    
    if ($estado_actual === false) {
        throw new Exception('Registro no encontrado');
    }
    
    if ($estado_actual === 'Partio') {
        throw new Exception('El vehículo ya ha partido');
    }
    
    // Actualizar el registro de estacionamiento
    $update_stmt = $conn->prepare("
        UPDATE Estacionamiento 
        SET fecha_salida = CURRENT_DATE(),
            hora_salida = CURRENT_TIME(),
            estado = 'Partio'
        WHERE id_estacionamiento = ?
    ");
    
    $update_stmt->execute([$id]);
    
    // Obtener el precio
    $precio_stmt = $conn->prepare("
        SELECT t.precio 
        FROM Tipo t 
        JOIN Vehiculo v ON t.id_tipo = v.id_tipo
        JOIN Estacionamiento e ON v.id_vehiculo = e.id_vehiculo
        WHERE e.id_estacionamiento = ?
    ");
    
    $precio_stmt->execute([$id]);
    $precio = $precio_stmt->fetchColumn();
    
    if ($precio === false) {
        throw new Exception('No se pudo obtener el precio');
    }
    
    // Insertar el pago con la estructura actual de la tabla
    $pago_stmt = $conn->prepare("
        INSERT INTO Pago (id_estacionamiento, monto, estado_pago)
        VALUES (?, ?, 'completo')
    ");
    
    $pago_stmt->execute([$id, $precio]);
    
    $conn->commit();
    
    // Log de éxito
    $fecha_log = new DateTime('now', new DateTimeZone('America/Lima'));
    file_put_contents('debug.log', $fecha_log->format('Y-m-d H:i:s') . " - Actualización exitosa para ID: {$id}\n", FILE_APPEND);
    
    echo json_encode([
        'success' => true,
        'message' => 'Salida registrada correctamente'
    ]);
    
} catch(Exception $e) {
    $fecha_log = new DateTime('now', new DateTimeZone('America/Lima'));
    file_put_contents(
        'debug.log', 
        $fecha_log->format('Y-m-d H:i:s') . " - Error en actualizar_salida.php: " . $e->getMessage() . "\n", 
        FILE_APPEND
    );
    
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    
    handleError($e->getMessage(), 500);
}
?>