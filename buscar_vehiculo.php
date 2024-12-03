<?php
$config = require_once 'config.php';
$conn = $config['conn'];

try {
    if (isset($_GET['placa'])) {
        $placa = trim($_GET['placa']);
        
        $query = "SELECT DISTINCT
                    e.id_estacionamiento,
                    e.fecha_entrada,
                    e.hora_entrada,
                    e.fecha_salida,
                    e.hora_salida,
                    e.estado as estado_estacionamiento,
                    v.placa, 
                    v.id_tipo,
                    t.tipo,
                    t.precio,
                    p.monto, 
                    p.estado_pago,
                    CASE 
                        WHEN e.estado = 'Estacionado' THEN 'Estacionado'
                        WHEN e.estado = 'Partio' THEN 'Partio'
                    END as estado
                FROM Estacionamiento e 
                JOIN Vehiculo v ON e.id_vehiculo = v.id_vehiculo 
                JOIN Tipo t ON v.id_tipo = t.id_tipo
                LEFT JOIN Pago p ON e.id_estacionamiento = p.id_estacionamiento 
                WHERE v.placa = :placa 
                ORDER BY e.fecha_entrada DESC, e.hora_entrada DESC";
        
        $stmt = $conn->prepare($query);
        $stmt->execute(['placa' => $placa]);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($resultados) {
            // Procesar cada resultado para asegurar estados correctos
            foreach ($resultados as &$resultado) {
                // El estado viene directamente de la tabla Estacionamiento
                // El estado_pago viene directamente de la tabla Pago
                
                // Si el vehículo ha partido, el pago debería estar completo
                if ($resultado['estado'] === 'Partio' && !$resultado['estado_pago']) {
                    $resultado['estado_pago'] = 'completo';
                }
            }
            
            echo json_encode([
                'success' => true, 
                'data' => $resultados
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'data' => []
            ]);
        }
    } else {
        echo json_encode(['error' => 'No se proporcionó una placa']);
    }
} catch (PDOException $e) {
    error_log("Error en la búsqueda: " . $e->getMessage());
    echo json_encode(['error' => 'Error en la búsqueda']);
}