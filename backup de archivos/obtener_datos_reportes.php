<?php
ob_start();
header('Content-Type: application/json');

date_default_timezone_set('America/Lima');

error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    require 'config.php';
    
    $pdo = new PDO(
        "mysql:host={$config['database']['host']};dbname={$config['database']['name']};charset=utf8",
        $config['database']['user'],
        $config['database']['pass']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = "
        SELECT 
            DATE(fecha_entrada) as fecha,
            COUNT(*) as total_entradas
        FROM Estacionamiento
        WHERE fecha_entrada >= DATE_SUB(CURDATE(), INTERVAL 9 DAY)
        GROUP BY DATE(fecha_entrada)
        ORDER BY fecha ASC
    ";

    $stmt = $pdo->query($query);
    $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    ob_clean();
    
    if ($datos === false) {
        throw new Exception('Error al obtener datos de la base de datos');
    }

    // Debug: Imprimir los datos antes de enviarlos
    error_log('Datos a enviar: ' . print_r($datos, true));

    echo json_encode([
        'success' => true,
        'data' => $datos
    ]);

} catch (Exception $e) {
    ob_clean();
    
    error_log('Error en obtener_datos_reportes.php: ' . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'error' => 'Error en la base de datos: ' . $e->getMessage()
    ]);
}

exit();
?>