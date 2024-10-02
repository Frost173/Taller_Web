<?php
header('Content-Type: application/json');

$config = include 'config.php';
$conn = new mysqli($server, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'error' => 'Conexion fallida: ' . $conn->connect_error]));
}

$result = $conn->query("SELECT * FROM vehiculos WHERE fecha_salida IS NULL ORDER BY fecha_hora DESC");

$vehiculos = [];
while($row = $result->fetch_assoc()) {
    $vehiculos[] = $row;
}

echo json_encode($vehiculos);

$conn->close();
?>