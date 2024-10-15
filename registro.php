<?php
// Incluir el archivo de conexión
include 'conexion.php';

// Verificar que los datos han sido enviados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $correo = $_POST['correo'];
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_BCRYPT); // Encriptar la contraseña

    // Preparar la consulta de inserción
    $sql = "INSERT INTO usuarios (nombre, apellidos, fecha_nacimiento, correo, contrasena) 
            VALUES ('$nombre', '$apellidos', '$fecha_nacimiento', '$correo', '$contrasena')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>
                alert('Registro exitoso. Serás redirigido al inicio de sesión.');
                window.location.href = 'Sesion.html';
              </script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Cerrar la conexión
    $conn->close();
}
?>