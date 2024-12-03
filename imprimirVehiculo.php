<?php
require_once 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        $stmt = $conn->prepare("
            SELECT e.id_estacionamiento, v.placa, e.fecha_entrada, e.hora_entrada, 
                   e.estado, t.tipo, t.precio
            FROM estacionamiento e
            JOIN vehiculo v ON e.id_vehiculo = v.id_vehiculo
            JOIN tipo t ON v.id_tipo = t.id_tipo
            WHERE e.id_estacionamiento = ?
        ");
        $stmt->execute([$id]);
        $vehiculo = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($vehiculo) {
            ?>
            <!DOCTYPE html>
            <html>
            <head>
                <title>Ticket de Estacionamiento</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    .ticket { border: 1px solid #ccc; padding: 20px; max-width: 300px; margin: 0 auto; }
                    .header { text-align: center; margin-bottom: 20px; }
                    .details { margin-bottom: 20px; }
                    .details div { margin-bottom: 5px; }
                    @media print {
                        .no-print { display: none; }
                    }
                </style>
            </head>
            <body>
                <div class="ticket">
                    <div class="header">
                        <h2>Ticket de Estacionamiento</h2>
                    </div>
                    <div class="details">
                        <div><strong>Placa:</strong> <?php echo htmlspecialchars($vehiculo['placa']); ?></div>
                        <div><strong>Tipo:</strong> <?php echo htmlspecialchars($vehiculo['tipo']); ?></div>
                        <div><strong>Fecha:</strong> <?php echo htmlspecialchars($vehiculo['fecha_entrada']); ?></div>
                        <div><strong>Hora:</strong> <?php echo htmlspecialchars($vehiculo['hora_entrada']); ?></div>
                        <div><strong>Precio/hora:</strong> $<?php echo htmlspecialchars($vehiculo['precio']); ?></div>
                    </div>
                </div>
                <div class="no-print" style="text-align: center; margin-top: 20px;">
                    <button onclick="window.print()">Imprimir</button>
                </div>
            </body>
            </html>
            <?php
        }
    } catch (PDOException $e) {
        echo "Error al obtener datos del vehículo";
    }
}
?>