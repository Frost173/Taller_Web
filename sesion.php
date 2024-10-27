<?php
// Incluimos la configuración de la base de datos
include 'config.php';

// Iniciamos la sesión
session_start();

// Verificamos si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtenemos los datos del formulario
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];

    // Preparamos la consulta SQL para verificar el usuario
    $sql = "SELECT * FROM usuarios WHERE correo = ? AND contrasena = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $correo, $contrasena);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificamos si se encontró el usuario
    if ($result->num_rows > 0) {
        // Usuario autenticado, redirigimos a index.html
        header("Location: index.html");
        exit();
    } else {
        // Credenciales incorrectas, mensaje de error
        echo "<script>alert('Correo o contraseña incorrectos');</script>";
        echo "<script>window.location.href = 'Sesion.html';</script>";
    }

    // Cerramos la consulta y la conexión
    $stmt->close();
    $conn->close();
}
?>