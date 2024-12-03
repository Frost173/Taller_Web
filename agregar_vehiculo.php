<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');
header('Content-Type: application/json');
try {
    // Capturar salida
    ob_start();    
    // Obtener y validar datos de entrada
    $input_data = file_get_contents('php://input');
    error_log("Datos recibidos en agregar_vehiculo.php: " . $input_data);    
    $data = json_decode($input_data, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Error al decodificar JSON: ' . json_last_error_msg());
    }
    // Validar campos requeridos
    $required_fields = ['placa', 'tipo', 'precio'];
    $missing_fields = array_diff($required_fields, array_keys(array_filter($data)));
    if (!empty($missing_fields)) {
        throw new Exception('Campos requeridos faltantes: ' . implode(', ', $missing_fields));
    }
    // Incluir configuración y obtener conexión
    $configData = require 'config.php';
    if (!isset($configData['conn']) || !$configData['conn'] instanceof PDO) {
        throw new Exception('Error de configuración: conexión no disponible');
    }
    $conn = $configData['conn'];
    $conn->beginTransaction();
    // 1. Verificar si el vehículo ya está estacionado
    $stmt = $conn->prepare("
        SELECT e.id_estacionamiento 
        FROM Estacionamiento e
        JOIN Vehiculo v ON e.id_vehiculo = v.id_vehiculo
        WHERE v.placa = ? AND e.fecha_salida IS NULL
    ");
    $stmt->execute([$data['placa']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);    
    if ($result) {
        throw new Exception('El vehículo ya se encuentra estacionado');
    }
    // 2. Obtener o crear tipo de vehículo
    $stmt = $conn->prepare("SELECT id_tipo FROM Tipo WHERE tipo = ? AND precio = ? AND modo = 'entrada'");
    $stmt->execute([$data['tipo'], $data['precio']]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);    
    if ($row) {
        $id_tipo = $row['id_tipo'];
    } else {
        $stmt = $conn->prepare("INSERT INTO Tipo (tipo, precio, modo) VALUES (?, ?, 'entrada')");
        $stmt->execute([$data['tipo'], $data['precio']]);
        $id_tipo = $conn->lastInsertId();
    }
    // 3. Obtener o crear vehículo
    $stmt = $conn->prepare("SELECT id_vehiculo FROM Vehiculo WHERE placa = ?");
    $stmt->execute([$data['placa']]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);    
    if ($row) {
        $id_vehiculo = $row['id_vehiculo'];
    } else {
        $stmt = $conn->prepare("INSERT INTO Vehiculo (id_tipo, placa) VALUES (?, ?)");
        $stmt->execute([$id_tipo, $data['placa']]);
        $id_vehiculo = $conn->lastInsertId();
    }
    // 4. Crear registro de estacionamiento
    $stmt = $conn->prepare("
        INSERT INTO Estacionamiento (id_vehiculo, fecha_entrada, hora_entrada, estado) 
        VALUES (?, CURDATE(), CURTIME(), 'Estacionado')
    ");
    $stmt->execute([$id_vehiculo]);
    $id_estacionamiento = $conn->lastInsertId();
    // 5. Crear registro de pago
    $stmt = $conn->prepare("
        INSERT INTO Pago (id_estacionamiento, monto, estado_pago) 
        VALUES (?, ?, 'pendiente')
    ");
    $stmt->execute([$id_estacionamiento, $data['precio']]);
    $conn->commit();    
    $response = [
        'success' => true,
        'message' => 'Vehículo agregado correctamente',
        'id_estacionamiento' => $id_estacionamiento
    ];
} catch (PDOException $e) {
    if (isset($conn) && $conn instanceof PDO) {
        $conn->rollback();
    }
    error_log("Error en agregar_vehiculo.php: " . $e->getMessage());
    $response = ['success' => false, 'error' => $e->getMessage()];
} catch (Exception $e) {
    if (isset($conn) && $conn instanceof PDO) {
        $conn->rollback();
    }    error_log("Error en agregar_vehiculo.php: " . $e->getMessage());
    $response = ['success' => false, 'error' => $e->getMessage()];
} finally {
    // Limpiar el buffer de salida
    $output = ob_get_clean();
    if (!empty($output)) {
        error_log("Salida inesperada en agregar_vehiculo.php: " . $output);
    }
    // Enviar respuesta
    echo json_encode($response);
}
?>