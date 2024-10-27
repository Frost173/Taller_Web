<?php

include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena']; // Almacena la contraseña sin encriptar

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