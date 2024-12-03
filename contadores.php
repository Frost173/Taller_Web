<?php
// contadores.php
ini_set('display_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', 'debug.log');

header('Content-Type: application/json; charset=utf-8');

try {
    $dbConfig = require_once 'config.php';
    $conn = $dbConfig['conn'];

    if (!$conn || !($conn instanceof PDO)) {
        throw new Exception('Error de conexión a la base de datos');
    }

    // Obtener la fecha de inicio y fin de la semana actual
    $inicio_semana = date('Y-m-d', strtotime('monday this week'));
    $fin_semana = date('Y-m-d', strtotime('sunday this week'));

    // 1. Vehículos estacionados
    $sql_estacionados = "SELECT COUNT(*) as total FROM Estacionamiento 
                        WHERE estado = 'estacionado' 
                        AND fecha_entrada BETWEEN :inicio AND :fin";
    $stmt = $conn->prepare($sql_estacionados);
    $stmt->execute(['inicio' => $inicio_semana, 'fin' => $fin_semana]);
    $estacionados = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // 2. Categorías activas
    $sql_categorias = "SELECT COUNT(*) as total FROM Tipo WHERE modo = 'activado'";
    $stmt = $conn->prepare($sql_categorias);
    $stmt->execute();
    $categorias = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // 3. Ganancias totales
    $sql_ganancias = "SELECT COALESCE(SUM(p.monto), 0) as total 
                      FROM Pago p 
                      INNER JOIN Estacionamiento e ON p.id_estacionamiento = e.id_estacionamiento 
                      WHERE e.fecha_entrada BETWEEN :inicio AND :fin";
    $stmt = $conn->prepare($sql_ganancias);
    $stmt->execute(['inicio' => $inicio_semana, 'fin' => $fin_semana]);
    $ganancias = number_format((float)$stmt->fetch(PDO::FETCH_ASSOC)['total'], 2, '.', '');

    // 4. Registros totales de la semana
    $sql_registros = "SELECT COUNT(*) as total FROM Vehiculo v 
                     INNER JOIN Estacionamiento e ON v.id_vehiculo = e.id_vehiculo 
                     WHERE e.fecha_entrada BETWEEN :inicio AND :fin";
    $stmt = $conn->prepare($sql_registros);
    $stmt->execute(['inicio' => $inicio_semana, 'fin' => $fin_semana]);
    $registros = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $response = [
        'success' => true,
        'data' => [
            'estacionados' => $estacionados,
            'categorias' => $categorias,
            'ganancias' => $ganancias,
            'registros' => $registros,
            'fecha_actual' => date('d/m/Y')
        ]
    ];

    echo json_encode($response, JSON_THROW_ON_ERROR);

} catch(Exception $e) {
    error_log("Error en contadores.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error al obtener los contadores',
        'details' => $e->getMessage()
    ], JSON_THROW_ON_ERROR);
}