<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

header('Content-Type: application/json');

try {
    if (!isset($_GET['id'])) {
        throw new Exception("ID de estacionamiento no proporcionado");
    }

    $id_estacionamiento = intval($_GET['id']);
    
    // Cargar la configuración
    $configData = require_once 'config.php';
    if (!$configData['conn']) {
        throw new Exception("Error en la conexión a la base de datos");
    }
    
    $conn = $configData['conn'];

    // Obtener la información necesaria para el cálculo
    $query = "
        SELECT 
            e.fecha_entrada, e.hora_entrada,
            e.fecha_salida, e.hora_salida,
            t.precio
        FROM Estacionamiento e
        JOIN Vehiculo v ON e.id_vehiculo = v.id_vehiculo
        JOIN Tipo t ON v.id_tipo = t.id_tipo
        WHERE e.id_estacionamiento = ?
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->execute([$id_estacionamiento]);
    $datos = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$datos) {
        throw new Exception("No se encontraron los datos del estacionamiento");
    }

    // Calcular la diferencia de tiempo
    $entrada = new DateTime($datos['fecha_entrada'] . ' ' . $datos['hora_entrada']);
    $salida = new DateTime($datos['fecha_salida'] . ' ' . $datos['hora_salida']);
    
    $intervalo = $entrada->diff($salida);
    
    // Convertir la diferencia a horas
    $horas = $intervalo->days * 24 + $intervalo->h;
    if ($intervalo->i > 0) {
        $horas++; // Si hay minutos adicionales, se cobra la hora completa
    }
    
    // Calcular el monto
    $monto = $horas * $datos['precio'];

    // Verificar si ya existe un registro de pago
    $queryPago = "SELECT id_pago FROM Pago WHERE id_estacionamiento = ?";
    $stmtPago = $conn->prepare($queryPago);
    $stmtPago->execute([$id_estacionamiento]);
    $pagoExistente = $stmtPago->fetch();

    if ($pagoExistente) {
        // Actualizar el pago existente
        $queryUpdate = "UPDATE Pago SET monto = ? WHERE id_estacionamiento = ?";
        $stmtUpdate = $conn->prepare($queryUpdate);
        $stmtUpdate->execute([$monto, $id_estacionamiento]);
    } else {
        // Insertar nuevo pago
        $queryInsert = "INSERT INTO Pago (id_estacionamiento, monto, estado_pago) VALUES (?, ?, 'pendiente')";
        $stmtInsert = $conn->prepare($queryInsert);
        $stmtInsert->execute([$id_estacionamiento, $monto]);
    }

    echo json_encode([
        'success' => true,
        'monto' => $monto,
        'horas' => $horas
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}