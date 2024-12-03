
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel S PMS</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Alternador de Menú Móvil -->
    <div class="mobile-menu-toggle">
        <img src="imagenes/Menu.png" alt="Menú" class="menu-icon">
    </div>
    <!-- Encabezado -->
    <header class="main-header">
        <div class="logo">
            <img src="imagenes/carro-nuevo.png" alt="Logo" />
            <h2>Panel S PMS</h2>
        </div>
        <div class="user-info">
            <span>Bienvenido, Administrador</span>
        </div>
    </header>

    <!-- Contenedor para Barra Lateral y Contenido Principal -->
    <div class="container">
        <!-- Barra Lateral -->
        <nav class="sidebar">
            <ul>
                <li><a href="http://localhost/sistema_aparcamiento/Index.php">Inicio</a></li>
                <li><a href="http://localhost/sistema_aparcamiento/Categoria.php">Categoría</a></li>
                <li><a href="http://localhost/sistema_aparcamiento/Entrada.php">Entradas</a></li>
                <li><a href="http://localhost/sistema_aparcamiento/Salida.php">Salidas</a></li>
                <li><a href="http://localhost/sistema_aparcamiento/Reportes.php">Reportes</a></li>
                <li><a href="http://localhost/sistema_aparcamiento/Buscar.php">Buscar</a></li>
                <li><a href="http://localhost/sistema_aparcamiento/Ajuste.php">Ajustes</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <section class="dashboard">
                <div class="card card-blue">
                    <h3>0</h3>
                    <p>Vehículos estacionados</p>
                </div>
                <div class="card card-gray">
                    <h3>0</h3>
                    <p>Vehículos Partidos</p>
                </div>
                <div class="card card-red">
                    <h3>0</h3>
                    <p>Categorías disponibles</p>
                </div>
                <div class="card card-yellow">
                    <h3>$0.00</h3>
                    <p>Ganancias Totales</p>
                </div>
                <div class="card card-gray">
                    <h3>0</h3>
                    <p>Registros totales</p>
                </div>
                <div class="card card-blue">
                    <h3></h3>
                    <p>Día actual</p>
                </div>
            </section>
        </main>
    </div>

    <!-- Scripts al final del body -->
    <script src="scriptIndex.js"></script>
</body>
</html>
