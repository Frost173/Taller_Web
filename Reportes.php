<?php
$config = include 'config.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Panel S PMS</title>
    <link rel="stylesheet" href="stylesReportes.css">
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <!-- Agregar Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Tu script -->
    <script src="scriptReportes.js" defer></script>
</head>
<body>
    <!-- Alternador de Menú Móvil -->
    <div class="mobile-menu-toggle">
        <img src="imagenes/Menu.png" alt="Menú" class="menu-icon">
    </div>
    <!-- Header -->
    <header class="main-header">
        <div class="logo">
            <img src="imagenes/carro-nuevo.png" alt="Logo" />
            <h2>Panel S PMS</h2>
        </div>
        <div class="user-info">
            <span>Bienvenido, Administrador</span>
        </div>
    </header>

    <!-- Container for Sidebar and Main Content -->
    <div class="container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <ul>
                <li><a href="http://localhost/sistema_aparcamiento/Index.php">Inicio</a></li>
                <li><a href="http://localhost/sistema_aparcamiento/Categoria.php">Categoría</a></li>
                <li><a href="http://localhost/sistema_aparcamiento/Entrada.php" >Entradas</a></li>
                <li><a href="http://localhost/sistema_aparcamiento/Salida.php">Salidas</a></li>
                <li><a href="http://localhost/sistema_aparcamiento/Reportes.php">Reportes</a></li>
                <li><a href="http://localhost/sistema_aparcamiento/Buscar.php">Buscar</a></li>
                <li><a href="http://localhost/sistema_aparcamiento/Ajuste.php">Ajustes</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <div class="reports-container">
                <div class="info-banner">
                    El informe que se muestra a continuación abarca solo 10 días desde hace 10 días.
                </div>

                <div class="report-section">
                    <h3>Entradas de vehículos estacionados</h3>
                    <div class="chart-container">
                        <canvas id="vehicleEntriesChart"></canvas>
                    </div>
                </div>

                <div class="summary-cards">
                    <div class="summary-card">
                        <h4>Total Entradas</h4>
                        <p id="total-entries">Cargando...</p>
                    </div>
                    <div class="summary-card">
                        <h4>Promedio Diario</h4>
                        <p id="daily-average">Cargando...</p>
                    </div>
                    <div class="summary-card">
                        <h4>Día Más Ocupado</h4>
                        <p id="busiest-day">Cargando...</p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>