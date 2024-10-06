<?php
$config = require 'config.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $stmt = $conn->prepare("SELECT * FROM entrada WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $vehiculo = $result->fetch_assoc();
    
    if ($vehiculo) {
        // Aquí puedes formatear la información del vehículo para impresión
        echo "<h1>Información del Vehículo</h1>";
        echo "<p>Placa: " . htmlspecialchars($vehiculo['placa']) . "</p>";
        echo "<p>Estacionamiento: " . htmlspecialchars($vehiculo['estacionamiento']) . "</p>";
        echo "<p>Fecha y Hora de Entrada: " . htmlspecialchars($vehiculo['fecha_hora']) . "</p>";
        echo "<p>Estado: " . htmlspecialchars($vehiculo['estado']) . "</p>";
        // Agrega más campos según sea necesario
        
        echo "<script>window.print();</script>"; // Esto abrirá automáticamente el diálogo de impresión
    } else {
        echo "Vehículo no encontrado";
    }
} else {
    echo "ID de vehículo no proporcionado";
}

$conn->close();
?>