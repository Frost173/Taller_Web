<?php
// Verificar acceso seguro
//if (!defined('SECURE_ACCESS')) {
    //die('Acceso directo no permitido');
//}

// Incluir archivo de configuración
$config = require_once 'config.php';
$conn = $config['conn'];

// Crear tabla si no existe
$sql = "CREATE TABLE IF NOT EXISTS entrada (
    id INT(11) NOT NULL AUTO_INCREMENT,
    placa VARCHAR(20) NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    estacionamiento VARCHAR(50) NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    fecha_hora DATETIME NOT NULL,
    estado VARCHAR(20) NOT NULL DEFAULT 'Estacionado',
    fecha_salida DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    KEY idx_estado (estado),
    KEY idx_placa (placa),
    KEY idx_fecha_hora (fecha_hora),
    KEY idx_fecha_salida (fecha_salida)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

if ($conn->query($sql) !== TRUE) {
    die("Error creando tabla: " . $conn->error);
}

// Función para obtener registros
function obtenerRegistros($conn, $limite = 10) {
    // Modificamos la consulta para obtener solo registros de los últimos 30 días
    $sql = "SELECT * FROM entrada 
            WHERE fecha_hora >= DATE_SUB(NOW(), INTERVAL 30 DAY) 
            ORDER BY fecha_hora DESC 
            LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limite);
    $stmt->execute();
    return $stmt->get_result();
}

// Procesar la página actual
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$por_pagina = isset($_GET['espectaculo']) ? (int)$_GET['espectaculo'] : 10;
$inicio = ($pagina - 1) * $por_pagina;

// Obtener registros para la página actual
$resultado = obtenerRegistros($conn, $por_pagina);

// Obtener el total de vehículos que han partido
$sql_partidos = "SELECT COUNT(*) as total FROM entrada WHERE estado = 'Partio'";
$resultado_partidos = $conn->query($sql_partidos);
$vehiculos_partidos = $resultado_partidos->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel PMS - Sistema de Gestión de Estacionamiento</title>
    <link rel="stylesheet" href="stylesSalida.css">    
    <script src="scriptSalida.js"></script>
</head>
<body>
    <div class="container">
        <header>
            <h1>Administrar vehículo</h1>
            <p class="info-text">Aquí solo están disponibles los registros de los últimos 30 días</p>
            <p class="contador-salidas">Total de vehículos que han partido: <span id="contador-salidas"><?php echo $vehiculos_partidos; ?></span></p>
        </header>

        <div class="controls">
            <div class="entries-control">
                <label>Espectáculo</label>
                <select name="espectaculo" id="espectaculo">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                <span>entradas</span>
            </div>
            <div class="search-control">
                <label>Buscar:</label>
                <input type="text" name="buscar" id="search-input">
            </div>
        </div>

        <table class="vehicles-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Número de vehículo</th>
                    <th>Tipo</th>
                    <th>N° Area</th>
                    <th>Precio</th>
                    <th>Hora de llegada</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['placa']); ?></td>
                    <td><?php echo htmlspecialchars($row['tipo']); ?></td>
                    <td><?php echo htmlspecialchars($row['estacionamiento']); ?></td>
                    <td>$<?php echo htmlspecialchars($row['precio']); ?></td>
                    <td><?php echo htmlspecialchars($row['fecha_hora']); ?></td>
                    <td><span class="status <?php echo strtolower($row['estado']); ?>" id="status-<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['estado']); ?></span></td>
                    <td>
                        <button 
                            class="action-btn" 
                            id="btn-<?php echo $row['id']; ?>"
                            onclick="registrarSalida(<?php echo $row['id']; ?>)"
                            <?php echo $row['estado'] === 'Partio' ? 'disabled' : ''; ?>
                        >
                            Hecho
                        </button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="pagination">
            <span>Mostrando <?php echo ($inicio + 1); ?> a <?php echo min($inicio + $por_pagina, $resultado->num_rows); ?> de <?php echo $resultado->num_rows; ?> entradas</span>
            <div class="pagination-controls">
                <button class="pagination-btn" onclick="previousPage()" <?php echo $pagina === 1 ? 'disabled' : ''; ?>>Anterior</button>
                <button class="pagination-btn active"><?php echo $pagina; ?></button>
                <button class="pagination-btn" onclick="nextPage()" <?php echo ($pagina * $por_pagina >= $resultado->num_rows) ? 'disabled' : ''; ?>>Próximo</button>
            </div>
        </div>
    </div>
</body>
</html>