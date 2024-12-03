<?php
header('Content-Type: application/json');

$configData = include 'config.php';
$conn = $configData['conn'];

if (!$conn) {
    echo json_encode(["success" => false, "message" => "Error de conexión a la base de datos"]);
    exit;
}

$jsonData = file_get_contents("php://input");
$data = json_decode($jsonData, true);

if (!isset($data['id']) || !isset($data['modo'])) {
    echo json_encode([
        "success" => false, 
        "message" => "Datos incompletos",
        "received" => $data
    ]);
    exit;
}

$id = filter_var($data['id'], FILTER_VALIDATE_INT);
$modo = strtolower($data['modo']); // Convertir a minúsculas para coincidir con el enum

// Validar el ID
if ($id === false) {
    echo json_encode([
        "success" => false, 
        "message" => "ID inválido",
        "received_id" => $data['id']
    ]);
    exit;
}

// Validar el modo
if (!in_array($modo, ['activado', 'desactivado'])) {
    echo json_encode([
        "success" => false, 
        "message" => "Modo inválido",
        "received_modo" => $modo
    ]);
    exit;
}

try {
    $query = "UPDATE Tipo SET modo = :modo WHERE id_tipo = :id";
    $stmt = $conn->prepare($query);
    
    $result = $stmt->execute([
        ':modo' => $modo,
        ':id' => $id
    ]);
    
    if ($result) {
        echo json_encode([
            "success" => true,
            "message" => "Modo actualizado correctamente",
            "data" => ["id" => $id, "modo" => $modo]
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "No se pudo actualizar el modo",
            "error" => $stmt->errorInfo()
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error en la base de datos: " . $e->getMessage(),
        "error_code" => $e->getCode()
    ]);
}