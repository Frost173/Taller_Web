<?php
session_start();
require 'conexion.php'; // Incluye el archivo de conexión

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $correo = $_POST['correo'];
    $contraseña = password_hash($_POST['contraseña'], PASSWORD_DEFAULT); // Encriptar contraseña

    // Insertar en la base de datos
    $sql = "INSERT INTO usuarios (nombre, apellido, fecha_nacimiento, correo, contraseña) VALUES ('$nombre', '$apellido', '$fecha_nacimiento', '$correo', '$contraseña')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Registro completado con éxito.";
        // Redirigir a la página de inicio de sesión o iniciar sesión automáticamente
        header("Location: Sesion.html");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
$conn->close();
?>