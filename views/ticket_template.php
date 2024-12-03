<?php
// Verificar que tenemos los datos necesarios
if (!isset($vehiculo) || !is_array($vehiculo)) {
    throw new Exception("Datos del vehículo no disponibles");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket de Estacionamiento</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .ticket {
            width: 80mm;
            margin: 0 auto;
            padding: 10px;
            border: 1px solid #000;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
        }
        .info-row {
            margin: 10px 0;
            display: flex;
            justify-content: space-between;
        }
        .label {
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            border-top: 1px solid #000;
            padding-top: 10px;
        }
        @media print {
            body {
                width: 80mm;
                margin: 0;
                padding: 0;
            }
            .ticket {
                border: none;
            }
        }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="header">
            <h2>TICKET DE ESTACIONAMIENTO</h2>
            <p>Sistema de Estacionamiento</p>
        </div>
        
        <div class="info-row">
            <span class="label">Placa:</span>
            <span><?php echo htmlspecialchars($vehiculo['placa'] ?? ''); ?></span>
        </div>
        
        <div class="info-row">
            <span class="label">Tipo de Vehículo:</span>
            <span><?php echo htmlspecialchars($vehiculo['tipo'] ?? ''); ?></span>
        </div>
        
        <div class="info-row">
            <span class="label">Tarifa:</span>
            <span>$<?php echo number_format($vehiculo['precio'] ?? 0, 2); ?></span>
        </div>

        <div class="info-row">
            <span class="label">Fecha de Entrada:</span>
            <span><?php echo htmlspecialchars($vehiculo['fecha_entrada'] ?? ''); ?></span>
        </div>
        
        <div class="info-row">
            <span class="label">Hora de Entrada:</span>
            <span><?php echo htmlspecialchars($vehiculo['hora_entrada'] ?? ''); ?></span>
        </div>

        <div class="info-row">
            <span class="label">Fecha de Salida:</span>
            <span><?php echo htmlspecialchars($vehiculo['fecha_salida']); ?></span>
        </div>
        
        <div class="info-row">
            <span class="label">Hora de Salida:</span>
            <span><?php echo htmlspecialchars($vehiculo['hora_salida']); ?></span>
        </div>

        <div class="info-row">
            <span class="label">Tiempo Total:</span>
            <span>
                <?php
                $entrada = new DateTime($vehiculo['fecha_entrada'] . ' ' . $vehiculo['hora_entrada']);
                $salida = new DateTime($vehiculo['fecha_salida'] . ' ' . $vehiculo['hora_salida']);
                $intervalo = $entrada->diff($salida);
                echo $intervalo->days . ' días, ' . $intervalo->h . ' horas, ' . $intervalo->i . ' minutos';
                ?>
            </span>
        </div>

        <div class="info-row">
            <span class="label">Tarifa por Hora:</span>
            <span>$<?php echo number_format($vehiculo['precio'], 2); ?></span>
        </div>

        <div class="info-row">
            <span class="label">Monto Total:</span>
            <span>$<?php echo number_format($vehiculo['monto'], 2); ?></span>
        </div>
        
        <div class="footer">
            <p>Gracias por usar nuestro servicio</p>
            <p>Conserve este ticket</p>
            <p><?php echo date('d/m/Y H:i:s'); ?></p>
        </div>
    </div>
    
    <script>
        window.onload = function() {
            try {
                setTimeout(function() {
                    window.print();
                }, 1000);
            } catch (error) {
                console.error('Error al imprimir:', error);
            }
        }
    </script>
</body>
</html>