<?php
//Archivo Entrada.php
// Incluir el archivo de configuració
$config = include 'config.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salida - Panel S PMS</title>
    <link rel="stylesheet" href="stylesSalida.css">    
    <link rel="stylesheet" href="styles.css">
    <script src="scriptSalida.js"></script>
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
            <div class="dashboard-summary">
                <div class="summary-card">
                    <h4>Total Salidas Hoy</h4>
                    <p id="contador-salidas">0</p>
                </div>
            </div>

            <div class="vehicle-list">
                <h3>Registro de Salidas de Vehículos</h3>
                <div class="table-controls">
                    <div class="entries-control">
                        <label for="espectaculo">Mostrar </label>
                        <select id="espectaculo" onchange="changeEntries()">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="15">15</option>
                            <option value="20">20</option>
                        </select>
                        <span>entradas</span>
                    </div>
                    <div class="search-control">
                        <label for="search-input">Buscar: </label>
                        <input type="text" name="buscar" id="search-input" placeholder="Buscar vehículo...">
                    </div>
                </div>

                <table class="vehicles-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Número de Vehículo</th>
                            <th>Tipo</th>
                            <th>Precio</th>
                            <th>Día de salida</th>
                            <th>Hora de salida</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="6">No hay datos disponibles en la tabla</td>
                        </tr>
                    </tbody>
                </table>

                <div class="pagination">
                    <button class="pagination-btn" onclick="previousPage()">Anterior</button>
                    <span>Mostrando 0 a 0 de 0 entradas</span>
                    <button class="pagination-btn" onclick="nextPage()">Siguiente</button>
                </div>
            </div>
        </main>
    </div>
</body>
</html>