<?php
// Verificar acceso seguro
//if (!defined('SECURE_ACCESS')) {
    //die('Acceso directo no permitido');
//}

// Configurar la zona horaria
date_default_timezone_set('America/Mexico_City');

// Definir la ruta del archivo de log
$logFile = __DIR__ . '/logs/error_log_' . date('Y-m-d') . '.txt';

// Asegurarse de que el directorio de logs existe
if (!file_exists(dirname($logFile))) {
    mkdir(dirname($logFile), 0755, true);
}

// Obtener los datos del error
$postData = json_decode(file_get_contents('http://localhost/sistema_aparcamiento/Salida.php'), true);

if ($postData) {
    $logMessage = date('Y-m-d H:i:s') . " - ";
    $logMessage .= "Message: " . $postData['message'] . "\n";
    
    if (isset($postData['error'])) {
        $logMessage .= "Error: " . $postData['error'] . "\n";
    }
    
    $logMessage .= "User Agent: " . $postData['userAgent'] . "\n";
    $logMessage .= "URL: " . $postData['url'] . "\n";
    $logMessage .= "Timestamp: " . $postData['timestamp'] . "\n";
    $logMessage .= "--------------------\n";

    // Escribir en el archivo de log
    file_put_contents($logFile, $logMessage, FILE_APPEND);

    // Responder con éxito
    echo json_encode(['success' => true]);
} else {
    // Responder con error si no hay datos
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No se recibieron datos de error']);
}
?>