<?php
require_once __DIR__ . '/../config/inic.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $conn = conexion::getConnection();

    $sql = "SELECT 
                p.id_paciente,
                p.no_historia,
                p.cedula,
                p.primer_nombre,
                p.segundo_nombre,
                p.primer_apellido,
                p.segundo_apellido,
                p.edad,
                p.sexo,
                p.fecha_ingreso_sistema,
                p.fecha_nacimiento,
                e.estado AS estado_procedencia
            FROM pacientes p
            LEFT JOIN estados e ON p.id_estado_procedencia = e.id_estado
            ORDER BY p.id_paciente DESC";

    $stmt = $conn->query($sql);
    $pacientes = $stmt->fetchAll();

    echo json_encode($pacientes, JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}