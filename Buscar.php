<?php
//Archivo Entrada.php
// Incluir el archivo de configuració
$config = include 'config.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar - Panel S PMS</title>
    <link rel="stylesheet" href="stylesBuscar.css">
    <link rel="stylesheet" href="styles.css">    
    <script src="scriptBuscar.js"></script>
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
            <div class="search-section">
                <h2>Buscar Registros</h2>
                <p class="subtitle">Solo los últimos 30 días</p>
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Buscar por vehículo">
                    <button id="searchButton">
                        <i class="search-icon"></i>
                        Buscar
                    </button>
                </div>
                <div id="searchResults"></div>
                <div id="errorMessage" class="error-message"></div>
            </div>
        </main>
    </div>
</body>
</html>