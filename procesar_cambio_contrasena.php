<?php
// Definir SECURE_ACCESS para acceder a config.php
define('SECURE_ACCESS', true);

// Incluir el archivo de configuración
$config = require_once 'config.php';
$conn = $config['conn'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = filter_input(INPUT_POST, 'correo', FILTER_SANITIZE_EMAIL);
    $nueva_contrasena = $_POST['nueva_contrasena'];
    $confirmar_contrasena = $_POST['confirmar_contrasena'];

    // Verificar que las contraseñas coincidan
    if ($nueva_contrasena !== $confirmar_contrasena) {
        header('Location: ajustes.php?mensaje=Las contraseñas no coinciden&tipo=error');
        exit;
    }

    try {
        // Verificar si el correo existe
        $stmt = $conn->prepare('SELECT id_usuario FROM usuarios WHERE correo = ?');
        $stmt->execute([$correo]);
        
        if ($stmt->rowCount() === 0) {
            header('Location: ajustes.php?mensaje=El correo no existe en el sistema&tipo=error');
            exit;
        }

        // Hash de la nueva contraseña
        $contrasena_plana = $nueva_contrasena;

        // Actualizar la contraseña
        $stmt = $conn->prepare('UPDATE usuarios SET contrasena = ? WHERE correo = ?');
        $resultado = $stmt->execute([$contrasena_plana, $correo]);

        if ($resultado) {
            header('Location: sesion.php?mensaje=Contraseña actualizada exitosamente&tipo=success');
        } else {
            header('Location: ajustes.php?mensaje=Error al actualizar la contraseña&tipo=error');
        }
    } catch (PDOException $e) {
        // Log del error
        error_log("Error en cambio de contraseña: " . $e->getMessage());
        header('Location: ajustes.php?mensaje=Error en el sistema. Por favor, intente más tarde&tipo=error');
    }
} else {
    header('Location: ajustes.php');
}
exit;