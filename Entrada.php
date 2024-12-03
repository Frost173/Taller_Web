<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrada - Panel S PMS</title>
    <link rel="stylesheet" href="stylesEntrada.css">    
    <link rel="stylesheet" href="styles.css">
    <script src="scriptEntrada.js"></script>
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
            <div class="top-panels">
                <div class="vehicle-panel">
                    <h3>Agregar Vehículo</h3>
                    <form id="addVehicleForm" onsubmit="addVehicle(event)">
                        <label for="placa">Placa</label>
                        <input type="text" id="placa" value="">
                        <label for="tipo">Tipo</label>
                        <select id="tipo">
                        <option value="">Seleccione Tipo</option>                        
                        </select>
                        <label for="precio">Precio</label>
                        <select id="precio">
                            <option value="">Seleccione precio</option>                            
                        </select>
                        <button type="submit" class="add-vehicle">Agregar Vehículo</button>
                    </form>
                </div>

            </div>
            <div class="vehicle-list">
                <h3>Vehículos Actuales</h3>
                <div class="table-controls">
                    <div class="entries-section">
                        <label for="entries">Mostrar </label>
                        <select id="entries" onchange="changeEntries()">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="20">20</option>
                        </select>
                        <span>entradas</span>
                    </div>
    
                    <div class="search-section">
                            <label for="search-input">Buscar: </label>
                        <input 
                            type="text" 
                            id="search-input" 
                            onkeyup="searchTable()" 
                            placeholder="Buscar por ID, placa, fecha, hora o estado..."
                            class="form-control">
                        <div id="search-results" style="display: none; margin-top: 10px; color: #666; font-style: italic;"></div>
                    </div>
                </div>
                <table id="vehicle-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Placa</th>
                            <th>Día de llegada</th>
                            <th>Hora de llegada</th>
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
                <div class="table-pagination">
                    <button onclick="previousPage()">Anterior</button>
                    <span>Página 1 de 1</span>
                    <button onclick="nextPage()">Siguiente</button>
                </div>
            </div>
        </main>
    </div>
</body>
</html>