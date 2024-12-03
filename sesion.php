<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="styleSesion.css">
    <title>Iniciar Sesión</title>
</head>
<body>
    <div class="container" id="container">
        <div class="form-container sign-in">
            <form id="login-form" action="sesionAgregado.php" method="POST">
                <h1>Inicia Sesión</h1>
                <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger">
                        <?php 
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                        ?>
                    </div>
                <?php endif; ?>
                <?php if(isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <?php 
                        echo $_SESSION['success'];
                        unset($_SESSION['success']);
                        ?>
                    </div>
                <?php endif; ?>
                <input type="email" name="correo" placeholder="Correo" required>
                <input type="password" name="contrasena" placeholder="Contraseña" required>
                <a href="#">¿Olvidaste tu contraseña?</a>
                <button type="submit">Iniciar Sesión</button>
            </form>
        </div>

        <form action="Registro.php">
            <div class="toggle-container">
                <div class="toggle">
                    <div class="toggle-panel toggle-right">
                        <h1>¿No tienes una cuenta?</h1>
                        <p>Regístrate para acceder a todas las funciones</p>
                        <button type="submit">Registrarse</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</body>
</html>