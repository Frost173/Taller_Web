<?php
header('Content-Type: application/json');

$config = include 'config.php';

$result = $conn->query("SELECT id, placa, estacionamiento, fecha_hora, estado FROM entrada WHERE fecha_salida IS NULL ORDER BY fecha_hora DESC");

$vehiculos = [];
while($row = $result->fetch_assoc()) {
    $vehiculos[] = $row;
}

echo json_encode($vehiculos);

$conn->close();
?>