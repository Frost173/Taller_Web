<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar datos de entrada
    if (empty($_POST['tipo']) || empty($_POST['placa'])) {
        echo json_encode([
            'success' => false,
            'message' => 'La placa y el tipo de vehículo son obligatorios'
        ]);
        exit;
    }

    try {
        // Verificar si la placa ya existe
        $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM vehiculo WHERE placa = ?");
        $stmtCheck->execute([$_POST['placa']]);
        if ($stmtCheck->fetchColumn() > 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Esta placa ya está registrada en el sistema'
            ]);
            exit;
        }

        $conn->beginTransaction();

        // Insertar en la tabla vehiculo
        $stmtVehiculo = $conn->prepare("INSERT INTO vehiculo (id_tipo, placa) VALUES (?, ?)");
        $stmtVehiculo->execute([$_POST['tipo'], strtoupper($_POST['placa'])]);
        $vehiculoId = $conn->lastInsertId();

        // Insertar en la tabla estacionamiento
        $stmtEstacionamiento = $conn->prepare(
            "INSERT INTO estacionamiento (id_vehiculo, fecha_entrada, hora_entrada, estado) 
             VALUES (?, CURDATE(), CURTIME(), 'estacionado')"
        );
        $stmtEstacionamiento->execute([$vehiculoId]);

        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Vehículo registrado exitosamente'
        ]);
    } catch (PDOException $e) {
        $conn->rollBack();
        echo json_encode([
            'success' => false,
            'message' => 'Error al registrar el vehículo: ' . $e->getMessage()
        ]);
    }
}
?>