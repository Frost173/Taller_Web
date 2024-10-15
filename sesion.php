<?php
// Incluir el archivo de conexión
include 'config.php';

// Verificar que los datos han sido enviados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];

    // Preparar la consulta para verificar el usuario
    $sql = "SELECT contrasena FROM usuarios WHERE correo = '$correo'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Obtener la contraseña encriptada de la base de datos
        $row = $result->fetch_assoc();
        $hashed_password = $row['contrasena'];

        // Verificar la contraseña
        if (password_verify($contrasena, $hashed_password)) {
           
            header("Location: index.html");
            exit();
        } else {
            
            header("Location: Sesion.html?error=contrasena");
            exit();
        }
    } else {
        header("Location: Sesion.html?error=correo");
        exit();
    }

    // Cerrar la conexión
    $conn->close();
}
?>