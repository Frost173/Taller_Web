<?php
session_start();
require 'conexion.php'; // Incluye el archivo de conexión

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = $_POST['correo'];
    $contraseña = $_POST['contraseña'];

    // Verificar las credenciales del usuario
    $sql = "SELECT * FROM usuarios WHERE correo='$correo'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Verificar la contraseña
        if (password_verify($contraseña, $row['contraseña'])) {
            $_SESSION['usuario_id'] = $row['id']; // Guardar ID del usuario en sesión
            header("Location: inicio.php"); // Redirigir a una página protegida
            exit();
        } else {
            echo "Contraseña incorrecta.";
        }
    } else {
        echo "No se encontró ningún usuario con ese correo.";
    }
}
$conn->close();
?>