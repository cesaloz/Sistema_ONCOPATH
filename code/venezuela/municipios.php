<?php
require_once __DIR__ . '/../config/inic.php';

header('Content-Type: application/json; charset=utf-8');

$idEstado = isset($_GET['id_estado']) ? (int) $_GET['id_estado'] : 0;

if ($idEstado <= 0) {
    echo json_encode([]);
    exit;
}

try {
    $conn = conexion::getConnection();
    $stmt = $conn->prepare("SELECT id_municipio, municipio FROM municipios WHERE id_estado = :id ORDER BY municipio");
    $stmt->execute([':id' => $idEstado]);
    echo json_encode($stmt->fetchAll(), JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}