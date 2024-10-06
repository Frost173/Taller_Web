<?php
// Prevenir acceso directo al archivo
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

// Configuración de la base de datos
$config = [
    'database' => [
        'host' => 'localhost',
        'port' => '3306',
        'name' => 'sistema_aparcamiento',
        'user' => 'root',
        'password' => ''
    ],
    'app' => [
        'debug' => true // Cambiar a false en producción
    ]
];

$conn = null;  // Inicializar conn como null en caso de error

try {
    // Crear conexión MySQLi
    $conn = new mysqli(
        $config['database']['host'],
        $config['database']['user'],
        $config['database']['password'],
        $config['database']['name'],
        $config['database']['port']
    );

    // Verificar conexión
    if ($conn->connect_error) {
        file_put_contents('debug.log', "Error de conexión a la base de datos: " . $conn->connect_error . "\n", FILE_APPEND);
        throw new Exception("Error de conexión: " . $conn->connect_error);
    } else {
        file_put_contents('debug.log', "Conexión a la base de datos establecida con éxito\n", FILE_APPEND);
    }

    // Establecer charset
    $conn->set_charset("utf8mb4");

} catch (Exception $e) {
    // Log del error
    error_log("Error en config.php: " . $e->getMessage());
    
    // Si estamos en modo debug, mostrar error detallado
    if ($config['app']['debug']) {
        throw new Exception("Error de conexión a la base de datos: " . $e->getMessage());
    }
    
    // En producción, retornar error genérico
}

// Siempre retornar el array con las claves 'config' y 'conn'
return [
    'config' => $config,
    'conn' => $conn
];