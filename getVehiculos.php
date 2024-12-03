<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    $limit = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 5;
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $offset = ($page - 1) * $limit;
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';

    // Construir la consulta base - Removida la condición WHERE e.estado = 'ocupado'
    $baseQuery = "FROM estacionamiento e
                  JOIN vehiculo v ON e.id_vehiculo = v.id_vehiculo";
    
    // Agregar condición de búsqueda si existe
    $searchCondition = "";
    if ($search !== '') {
        $searchCondition = " WHERE (v.placa LIKE :search 
                            OR e.fecha_entrada LIKE :search 
                            OR e.hora_entrada LIKE :search
                            OR e.estado LIKE :search)";
    }

    // Obtener el total de registros
    $totalQuery = "SELECT COUNT(*) " . $baseQuery . $searchCondition;
    $stmtTotal = $conn->prepare($totalQuery);
    if ($search !== '') {
        $stmtTotal->bindValue(':search', "%$search%", PDO::PARAM_STR);
    }
    $stmtTotal->execute();
    $total = $stmtTotal->fetchColumn();

    // Obtener los registros de la página actual
    $query = "SELECT e.id_estacionamiento, v.placa, 
                     DATE_FORMAT(e.fecha_entrada, '%Y-%m-%d') as fecha_entrada,
                     TIME_FORMAT(e.hora_entrada, '%H:%i:%s') as hora_entrada,
                     e.estado
              " . $baseQuery . $searchCondition . "
              ORDER BY e.fecha_entrada DESC, e.hora_entrada DESC
              LIMIT :limit OFFSET :offset";

    $stmt = $conn->prepare($query);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    if ($search !== '') {
        $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    }
    $stmt->execute();
    $vehiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'vehiculos' => $vehiculos,
        'total' => $total,
        'pages' => ceil($total / $limit),
        'currentPage' => $page
    ]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al obtener vehículos']);
}
?>