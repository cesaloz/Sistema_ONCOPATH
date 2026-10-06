<?php
require_once __DIR__ . '/../config/inic.php';

header('Content-Type: application/json; charset=utf-8');

$idMunicipio = isset($_GET['id_municipio']) ? (int) $_GET['id_municipio'] : 0;

if ($idMunicipio <= 0) {
    echo json_encode([]);
    exit;
}

try {
    $conn = conexion::getConnection();
    $stmt = $conn->prepare("SELECT id_parroquia, parroquia FROM parroquias WHERE id_municipio = :id ORDER BY parroquia");
    $stmt->execute([':id' => $idMunicipio]);
    echo json_encode($stmt->fetchAll(), JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}