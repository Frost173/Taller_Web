<?php
header('Content-Type: application/json');

$config = include 'config.php';
$conn = new mysqli($server, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'error' => 'Conexion fallida: ' . $conn->connect_error]));
}

$data = json_decode(file_get_contents('php://input'), true);

$stmt = $conn->prepare("INSERT INTO vehiculos (placa, tipo, estacionamiento, precio, fecha_hora, estado) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssdss", $data['placa'], $data['tipo'], $data['estacionamiento'], $data['precio'], $data['fechaHora'], $data['estado']);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>