<?php
// Incluir el archivo de configuració
$config = include 'config.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salidas - Panel S PMS</title>
    <link rel="stylesheet" href="stylesEntrada.css">
    <script src="scriptEntrada.js"></script>
</head>

<body>
    <!-- Header -->
    <header class="main-header">
        <div class="logo">
            <img src="imagenes/carro-nuevo.png" alt="Logo" />
            <h2>Panel S PMS</h2>
        </div>
        <div class="user-info">
            <span>Bienvenido, Administrador</span>
            <button class="logout">Salir</button>
        </div>
    </header>

    <!-- Container for Sidebar and Main Content -->
    <div class="container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <ul>
                <li><a href="index.html">Inicio</a></li>
                <li><a href="Categoria.html">Categoría</a></li>
                <li><a href="Entrada.html" class="active">Entradas</a></li>
                <li><a href="Salidas.html">Salidas</a></li>
                <li><a href="#">Informes</a></li>
                <li><a href="#">Buscar</a></li>
                <li><a href="#">Ajustes</a></li>
                <li><a href="#">Cerrar sesión</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <div class="top-panels">
                <div class="vehicle-panel">
                    <h3>Agregar Vehículo</h3>
                    <form>
                        <label for="placa">Placa</label>
                        <input type="text" id="placa" value="">

                        <label for="tipo">Tipo</label>
                        <select id="tipo">
                            <option value="">Seleccione Tipo</option>
                            <option value="">Auto</option>
                            <option value="">Motocicleta</option>
                            <option value="">Mini Furgoneta</option>
                            <option value="">Furgoneta de recogida</option>
                            <option value="">Microbus</option>
                            <option value="">Camión</option>
                        </select>

                        <label for="numero-estacionamiento">Número de estacionamiento</label>
                        <select id="numero-estacionamiento">
                            <option value="">Seleccione aparcamiento</option>
                            <option value="">2 → (Coche)</option>
                            <option value="">6 → (Motocicleta)</option>
                            <option value="">2 → (Mini Furgoneta)</option>
                            <option value="">7 → (Camioneta)</option>
                            <option value="">9 → (Minibus)</option>
                            <option value="">20 → (Camion)</option>
                        </select>

                        <label for="precio">Precio</label>
                        <select id="precio">
                            <option value="">Seleccione precio</option>
                            <option value="">$4 → (Coche)</option>
                            <option value="">$2 → (Motocicleta)</option>
                            <option value="">$5 → (Mini Furgoneta)</option>
                            <option value="">$5 → (Camioneta de recogida)</option>
                            <option value="">$6 → (Minibús)</option>
                            <option value="">$20 → (Camión)</option>
                        </select>

                        <button type="submit" class="add-vehicle">Agregar Vehículo</button>
                    </form>
                </div>


                <div class="vehicle-limits">
                    <h3>Límite de Vehículos</h3>
                    <ul>
                        <li>
                             <span class="vehicle-type">Limite de Coches:</span><span><span class="current-count">18</span> de 18</span>
                        </li>
                        <li>
                            <span class="vehicle-type">Limite de Motocicletas:</span><span><span class="current-count">26</span> de 26</span>
                        </li>
                        <li>
                            <span class="vehicle-type">Limite de Mini Van:</span><span><span class="current-count">8</span> de 8</span>
                        </li>
                        <li>
                            <span class="vehicle-type">Limite de Furgonetas:</span><span><span class="current-count">11</span> de 11</span>
                        </li>
                        <li>
                            <span class="vehicle-type">Limite de Minibús:</span><span><span class="current-count">6</span> de 6</span>
                        </li>
                        <li>
                            <span class="vehicle-type">Limite de Camión:</span><span><span class="current-count">10</span> de 10</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="vehicle-list">
                <h3>Vehículos Actuales</h3>
                <div class="table-controls">
                    <label for="entries">Mostrar </label>
                    <select id="entries" onchange="changeEntries()">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="20">20</option>
                    </select>
                    entradas
                    <label for="search-input">Buscar: </label>
                    <input type="text" id="search-input" onkeyup="searchTable()" placeholder="Buscar vehículo...">
                </div>
                <table id="vehicle-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Número de vehículo</th>
                            <th>Número de área</th>
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