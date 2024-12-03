<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="stylesRegistro.css">
</head>
<body>
<div class="container">
    <div class="form-container">
        <h2>Regístrate</h2>
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
        <form method="post" action="registroAgregado.php">
            <input type="text" name="nombre" placeholder="Nombre" required>
            <input type="text" name="apellido" placeholder="Apellidos" required>
            <input type="date" name="fecha_nacimiento" placeholder="Fecha de Nacimiento" required>
            <input type="email" name="correo" placeholder="Correo Electrónico" required>
            <input type="password" name="contrasena" placeholder="Contraseña" required>
            <button type="submit">Registrarse</button>
        </form>
    </div>
    
    <div class="signup-container">
        <form action="Sesion.php">
            <div>
                <h2>¡Hola, Amigo!</h2>
                <p>Ingresa tus datos personales para disfrutar de todas las funciones del sitio</p>
                <button type="submit">Iniciar Sesión</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>