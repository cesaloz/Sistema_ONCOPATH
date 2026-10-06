<?php 
require_once 'config/inic.php';
verificar_sesion();

header('Content-Type: application/json; charset=utf-8');

try {
    $conn = conexion::getConnection();

    // ==========================================
    // 1. PACIENTES POR ESTADO (texto de la columna)
    // ==========================================
    $sqlEstados = "
        SELECT 
            COALESCE(NULLIF(TRIM(estado_procedencia), ''), 'Sin especificar') AS estado,
            COUNT(*) AS total
        FROM pacientes
        GROUP BY COALESCE(NULLIF(TRIM(estado_procedencia), ''), 'Sin especificar')
        ORDER BY total DESC
        LIMIT 15
    ";
    $stmt = $conn->query($sqlEstados);
    $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $estados = [];
    foreach ($filas as $row) {
        $estados[(string)$row['estado']] = (int)$row['total'];
    }

    // ==========================================
    // 2. MUNICIPIOS POR ESTADO (texto de la columna)
    // ==========================================
    $sqlMunicipios = "
        SELECT 
            COALESCE(NULLIF(TRIM(estado_procedencia), ''), 'Sin especificar') AS estado,
            COALESCE(NULLIF(TRIM(municipio_procedencia), ''), 'Sin especificar') AS municipio,
            COUNT(*) AS total
        FROM pacientes
        GROUP BY 
        COALESCE(NULLIF(TRIM(estado_procedencia), ''), 'Sin especificar'),
        COALESCE(NULLIF(TRIM(municipio_procedencia), ''), 'Sin especificar')
        ORDER BY estado, total DESC;
    ";
    $stmt = $conn->query($sqlMunicipios);
    $filasMuni = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $municipiosPorEstado = [];
    foreach ($filasMuni as $row) {
        $estado = (string)$row['estado'];
        $municipio = (string)$row['municipio'];
        if (!isset($municipiosPorEstado[$estado])) {
            $municipiosPorEstado[$estado] = [];
        }
        $municipiosPorEstado[$estado][$municipio] = (int)$row['total'];
    }

    echo json_encode([
        'success' => true,
        'estados' => $estados,
        'municipiosPorEstado' => $municipiosPorEstado
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage(),
        'estados' => [],
        'municipiosPorEstado' => []
    ]);
}
?>