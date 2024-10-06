<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

// Capturar salida
ob_start();

file_put_contents('debug.log', "agregar_vehiculo.php llamado: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
file_put_contents('debug.log', "Datos recibidos: " . file_get_contents('php://input') . "\n", FILE_APPEND);
header('Content-Type: application/json');
// Incluir el archivo de configuración
$configData = require_once 'config.php';
$config = $configData['config'];
$conn = $configData['conn'];

// Verificar si la conexión está disponible
if (!isset($conn) || $conn->connect_error) {
    error_log("Error de conexión a la base de datos en agregar_vehiculo.php");
    die(json_encode(['success' => false, 'error' => 'Database connection failed']));
}

// Obtener y decodificar los datos JSON enviados
$data = json_decode(file_get_contents('php://input'), true);

// Validar los datos recibidos
if (!isset($data['placa']) || !isset($data['tipo']) || !isset($data['estacionamiento']) || 
    !isset($data['precio']) || !isset($data['fechaHora']) || !isset($data['estado'])) {
    die(json_encode(['success' => false, 'error' => 'Datos incompletos']));
}

try {
    // Preparar la consulta SQL
    $stmt = $conn->prepare("INSERT INTO entrada (placa, tipo, estacionamiento, precio, fecha_hora, estado) VALUES (?, ?, ?, ?, ?, ?)");
    
    if ($stmt === false) {
        throw new Exception("Error preparing statement: " . $conn->error);
    }

    // Vincular los parámetros
    $stmt->bind_param("sssdss", $data['placa'], $data['tipo'], $data['estacionamiento'], $data['precio'], $data['fechaHora'], $data['estado']);
    
    // Ejecutar la consulta
    if ($stmt->execute()) {
        $response = ['success' => true, 'message' => 'Vehículo agregado correctamente'];
    } else {
        throw new Exception("Error executing statement: " . $stmt->error);
    }
} catch (Exception $e) {
    error_log("Exception in agregar_vehiculo.php: " . $e->getMessage());
    $response = ['success' => false, 'error' => 'Error al agregar vehículo: ' . $e->getMessage()];
}

// Capturar cualquier salida y registrarla
$output = ob_get_clean();
if (!empty($output)) {
    error_log("Salida inesperada en agregar_vehiculo.php: " . $output);
}

// Asegurarse de que solo se envíe JSON
header('Content-Type: application/json');
echo json_encode($response);
// Cerrar la conexión y liberar recursos
if (isset($stmt)) {
    $stmt->close();
}
if (isset($conn)) {
    $conn->close();
}
?>