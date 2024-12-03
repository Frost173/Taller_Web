<?php
// Verificar acceso seguro
//if (!defined('SECURE_ACCESS')) {
    //die('Acceso directo no permitido');
//}

// Incluir archivo de configuración
$config = require_once 'config.php';
$conn = $config['conn'];

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
                    <th>Precio</th>
                    <th>Dia de salida</th>
                    <th>Hora de salida</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
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