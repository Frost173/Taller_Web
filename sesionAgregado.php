<?php
require_once 'config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $correo = $_POST['correo'];
        $contrasena = $_POST['contrasena'];
        
        // Preparar la consulta
        $stmt = $conn->prepare("SELECT id_usuario, nombre, contrasena FROM usuarios WHERE correo = ? AND contrasena = ?");
        $stmt->execute([$correo, $contrasena]);
        $usuario = $stmt->fetch();
        
        if ($usuario) {
            // Credenciales correctas
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nombre'] = $usuario['nombre'];
            
            header("Location: index.php");
            exit();
        } else {
            echo "<script>
                    alert('Correo o contraseña incorrectos');
                    window.location.href = 'Sesion.php';
                  </script>";
        }
    } catch(PDOException $e) {
        if ($config['app']['debug']) {
            echo "Error: " . $e->getMessage();
        } else {
            echo "<script>
                    alert('Error en el inicio de sesión. Por favor, intenta nuevamente.');
                    window.location.href = 'Sesion.php';
                  </script>";
        }
    }
}
?>