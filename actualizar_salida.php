<?php
// Desactivar la salida de errores de PHP
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Asegurarse de que la respuesta sea siempre JSON
header('Content-Type: application/json');

// Definir SECURE_ACCESS si no está definido
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

// Función para manejar errores
function handleError($message, $errorCode = 400) {
    http_response_code($errorCode);
    echo json_encode(['success' => false, 'error' => $message]);
    exit();
}

// Capturar todos los errores de PHP
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    handleError("Error PHP: $errstr en $errfile:$errline", 500);
});

try {
    // Verificar si la solicitud es POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    // Obtener y decodificar los datos JSON
    $inputJSON = file_get_contents('php://input');
    $data = json_decode($inputJSON, true);

    // Verificar si se recibieron los datos necesarios
    if (!isset($data['id']) || !isset($data['fecha_salida']) || !isset($data['estado'])) {
        throw new Exception('Datos incompletos');
    }

    $id = filter_var($data['id'], FILTER_VALIDATE_INT);
    $fecha_salida = filter_var($data['fecha_salida'], FILTER_SANITIZE_SPECIAL_CHARS);
    $estado = filter_var($data['estado'], FILTER_SANITIZE_SPECIAL_CHARS);

    if ($id === false || $fecha_salida === false || $estado === false) {
        throw new Exception('Datos inválidos');
    }

    // Incluir archivo de configuración
    $configData = require_once 'config.php';
    $conn = $configData['conn'];

    // Verificar si la conexión es válida
    if (!$conn || $conn->connect_error) {
        throw new Exception('Error de conexión a la base de datos');
    }

    // Preparar la consulta SQL
    $sql = "UPDATE entrada SET estado = ?, fecha_salida = ? WHERE id = ?";

    // Preparar la declaración
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Error en la preparación de la consulta: ' . $conn->error);
    }

    $stmt->bind_param("ssi", $estado, $fecha_salida, $id);

    // Ejecutar la consulta
    if (!$stmt->execute()) {
        throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
    }

    // Cerrar la declaración
    $stmt->close();

    // No cerramos la conexión aquí, ya que puede ser manejada por config.php

    // Si todo va bien, devolver éxito
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    // Log del error
    error_log("Error en actualizar_salida.php: " . $e->getMessage());
    
    // Si estamos en modo debug, mostrar error detallado
    if ($configData['config']['app']['debug']) {
        handleError($e->getMessage(), 500);
    } else {
        handleError('Ha ocurrido un error en el servidor', 500);
    }
}
?>