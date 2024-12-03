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
    // Crear conexión PDO
    $dsn = "mysql:host={$config['database']['host']};port={$config['database']['port']};dbname={$config['database']['name']};charset=utf8mb4";
    
    $conn = new PDO(
        $dsn,
        $config['database']['user'],
        $config['database']['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

    file_put_contents('debug.log', "Conexión a la base de datos establecida con éxito\n", FILE_APPEND);

} catch (PDOException $e) {
    // Log del error
    error_log("Error en config.php: " . $e->getMessage());
    file_put_contents('debug.log', "Error de conexión a la base de datos: " . $e->getMessage() . "\n", FILE_APPEND);
    
    // Si estamos en modo debug, mostrar error detallado
    if ($config['app']['debug']) {
        throw new PDOException("Error de conexión a la base de datos: " . $e->getMessage());
    }
}

// Siempre retornar el array con las claves 'config' y 'conn'
return [
    'config' => $config,
    'conn' => $conn
];