<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel S PMS</title>
    <link rel="stylesheet" href="stylesAjustes.css">
    <link rel="stylesheet" href="styles.css">
    <script src="scriptAjustes.js"></script>
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
            <div class="settings-container">
                <h2><i class="fas fa-cog"></i> Cambiar contraseña</h2>
                <?php if (isset($_GET['mensaje'])): ?>
                    <div class="alert <?php echo ($_GET['tipo'] == 'error') ? 'alert-error' : 'alert-success'; ?>">
                        <?php echo htmlspecialchars($_GET['mensaje']); ?>
                    </div>
                <?php endif; ?>
                <form class="change-password-form" action="procesar_cambio_contrasena.php" method="POST">
                    <div class="form-group">
                        <label for="correo">Correo electrónico</label>
                        <input type="email" id="correo" name="correo" placeholder="Ingrese su correo electrónico" required>
                    </div>
                    <div class="form-group">
                        <label for="nueva-contrasena">Nueva contraseña</label>
                        <input type="password" id="nueva-contrasena" name="nueva_contrasena" placeholder="Nueva contraseña" required>
                    </div>
                    <div class="form-group">
                        <label for="confirmar-contrasena">Confirmar nueva contraseña</label>
                        <input type="password" id="confirmar-contrasena" name="confirmar_contrasena" placeholder="Confirmar nueva contraseña" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Cambiar contraseña</button>
                </form>
            </div>
        </main>
    </div>

    <script>
        document.querySelector('.change-password-form').addEventListener('submit', function(e) {
            const nuevaContrasena = document.getElementById('nueva-contrasena').value;
            const confirmarContrasena = document.getElementById('confirmar-contrasena').value;

            if (nuevaContrasena !== confirmarContrasena) {
                e.preventDefault();
                alert('Las contraseñas no coinciden. Por favor, verifique.');
            }
        });
    </script>
</body>
</html>