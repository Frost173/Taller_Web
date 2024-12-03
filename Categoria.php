<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categoria - Panel S PMS</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="stylesCategoria.css">
    <script src="scriptCategoria.js" defer></script>
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

        <main class="main-content">
            <section class="category-section">
                <!-- Formulario para añadir categoría -->
                <div class="add-category">
                    <h3>Añadir Categoría</h3>
                    <form action="guardar_categoria.php" method="POST" id="form-categoria">
                        <div class="form-group">
                            
                        </div>
                        <div class="form-group">
                            <label for="vehicle-type">Tipo de vehículo</label>
                            <input type="text" id="vehicle-type" name="vehicle-type" required>
                        </div>
                        <div class="form-group">
                           
                        </div>
                        <div class="form-group">
                            <label for="charge">Cargo de estacionamiento</label>
                            <input type="number" id="charge" name="charge" step="0.01" required>
                        </div>
                        <button type="submit" class="btn-submit">Enviar</button>
                    </form>
                </div>

                <!-- Detalles de categorías -->
                <div class="category-details">
                    <h3>Detalles</h3>
                    <div class="details-list" id="details-list">
                        <!-- Aquí se añadirán los detalles dinámicamente -->
                    </div>
                </div>

            </section>

            <div class="admin-categorias">
                <h2>Administrar Categorías</h2>
                <table class="table-categorias">
                    <thead>
                        <tr>
                            <th>#</th>                            
                            <th>Tipo de Vehículo</th>                            
                            <th>Precio</th>
                            <th>Acción</th>
                            <th>Modo</th>
                        </tr>
                    </thead>
                    <tbody id="admin-table-body">
                        <!-- Aquí se mostrarán las categorías dinámicamente -->
                    </tbody>
                </table>
                <div class="pagination">
                    <button>Anterior</button>
                    <label>1</label>
                    <button>Siguiente</button>
                </div>
            </div>
        </main>
    </div>
    
    
        
</body>
</html>
