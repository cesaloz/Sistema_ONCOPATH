<?php
require_once 'config/inic.php';
verificar_sesion();

header('Content-Type: application/json; charset=utf-8');

$tipo = $_GET['tipo'] ?? '';
$filtro = $_GET['filtro'] ?? '';

try {
    $conn = conexion::getConnection();

    // ==========================================
    // ESTADOS
    // ==========================================
    if ($tipo === 'estados') {
        $stmt = $conn->query("
            SELECT id_estado, estado
            FROM estados
            ORDER BY estado ASC
        ");
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'datos' => $datos
        ]);
        exit();
    }

    // ==========================================
    // MUNICIPIOS (por estado)
    // ==========================================
    if ($tipo === 'municipios') {
        $stmt = $conn->prepare("
            SELECT m.id_municipio, m.municipio
            FROM municipios m
            INNER JOIN estados e ON m.id_estado = e.id_estado
            WHERE e.estado = :filtro
            ORDER BY m.municipio ASC
        ");
        $stmt->execute([':filtro' => $filtro]);
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'datos' => $datos
        ]);
        exit();
    }

    // ==========================================
    // PARROQUIAS (por municipio)
    // ==========================================
    if ($tipo === 'parroquias') {
        $stmt = $conn->prepare("
            SELECT p.id_parroquia, p.parroquia
            FROM parroquias p
            INNER JOIN municipios m ON p.id_municipio = m.id_municipio
            WHERE m.municipio = :filtro
            ORDER BY p.parroquia ASC
        ");
        $stmt->execute([':filtro' => $filtro]);
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'datos' => $datos
        ]);
        exit();
    }

    // Si no se especificó tipo válido
    echo json_encode([
        'success' => false,
        'message' => 'Tipo no especificado'
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage(),
        'datos' => []
    ]);
}
?>