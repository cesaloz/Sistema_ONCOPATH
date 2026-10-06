<?php
require_once __DIR__ . '/../config/inic.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $conn = conexion::getConnection();
    $stmt = $conn->query("SELECT id_estado, estado FROM estados ORDER BY estado");
    $estados = $stmt->fetchAll();
    echo json_encode($estados, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}