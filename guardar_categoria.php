<?php
$configData = include 'config.php';
$conn = $configData['conn'];

if (!$conn) {
    echo json_encode(["status" => "error", "message" => "Error de conexión"]);
    exit;
}

$vehicle_type = $_POST['vehicle-type'];
$charge = $_POST['charge'];

try {
    $query = "INSERT INTO Tipo (tipo, precio) VALUES (:tipo, :precio)";
    $stmt = $conn->prepare($query);
    $stmt->execute([
        ':tipo' => $vehicle_type,
        ':precio' => $charge
    ]);
    
    echo json_encode([
        "status" => "success",
        "message" => "Categoría guardada exitosamente"
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Error al guardar la categoría: " . $e->getMessage()
    ]);
}