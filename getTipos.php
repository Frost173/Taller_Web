<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    $stmt = $conn->prepare("SELECT id_tipo, tipo, precio FROM tipo WHERE modo = 'activado'");
    $stmt->execute();
    $tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($tipos);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al obtener tipos de vehículos']);
}
?>