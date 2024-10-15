<?php

include 'config.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];

    
    $sql = "SELECT contrasena FROM usuarios WHERE correo = '$correo'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
       
        $row = $result->fetch_assoc();
        $hashed_password = $row['contrasena'];

     
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

    
    $conn->close();
}
?>