<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $nombre = $_POST['nombre'];
        $apellidos = $_POST['apellido'];
        $fecha_nacimiento = $_POST['fecha_nacimiento'];
        $correo = $_POST['correo'];
        $contrasena = $_POST['contrasena']; // Sin encriptación
        
        // Verificar si el correo ya existe
        $stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE correo = ?");
        $stmt->execute([$correo]);
        
        if ($stmt->rowCount() > 0) {
            echo "<script>
                    alert('Este correo ya está registrado');
                    window.location.href = 'Registro.php';
                  </script>";
            exit;
        }
        
        // Preparar y ejecutar la inserción
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, apellido, fecha_nacimiento, correo, contrasena) 
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nombre, $apellidos, $fecha_nacimiento, $correo, $contrasena]);
        
        echo "<script>
                alert('Registro exitoso. Serás redirigido al inicio de sesión.');
                window.location.href = 'Sesion.php';
              </script>";
    } catch(PDOException $e) {
        if ($config['app']['debug']) {
            echo "Error: " . $e->getMessage();
        } else {
            echo "<script>
                    alert('Error en el registro. Por favor, intenta nuevamente.');
                    window.location.href = 'Registro.php';
                  </script>";
        }
    }
}
?>