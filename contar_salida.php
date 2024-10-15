<?php
// Verificar acceso seguro
//if (!defined('SECURE_ACCESS')) {
    //die('Acceso directo no permitido');
//}

// Incluir archivo de configuración
$config = require_once 'config.php';
$conn = $config['conn'];

// Preparar la consulta SQL
$sql = "SELECT COUNT(*) as total FROM entrada WHERE estado = 'Partio'";

// Ejecutar la consulta
$resultado = $conn->query($sql);

if ($resultado) {
    $row = $resultado->fetch_assoc();
    echo json_encode(['success' => true, 'total' => $row['total']]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

// Cerrar la conexión
$conn->close();
?>