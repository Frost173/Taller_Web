<?php
$host = 'localhost'; // o la dirección de tu servidor
$user = 'root'; // tu usuario de MySQL
$password = ''; // tu contraseña de MySQL
$database = 'crearcuenta'; // nombre de la base de datos

// Crear conexión
$conn = new mysqli($host, $user, $password, $database);

// Comprobar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>