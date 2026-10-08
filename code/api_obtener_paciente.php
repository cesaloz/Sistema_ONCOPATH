<?php
require_once __DIR__ . '../config/inic.php';

header('Content-Type: application/json; charset=utf-8');

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    echo json_encode(['error' => 'ID inválido']);
    exit;
}

try {
    $con = conexion::getConnection();

    $stmt = $con->prepare("
        SELECT
            p.id_paciente,
            p.no_historia,
            p.cedula,
            p.primer_nombre,
            p.segundo_nombre,
            p.primer_apellido,
            p.segundo_apellido,
            TRIM(
                COALESCE(p.primer_nombre, '') || ' ' ||
                COALESCE(p.segundo_nombre, '') || ' ' ||
                COALESCE(p.primer_apellido, '') || ' ' ||
                COALESCE(p.segundo_apellido, '')
            ) AS nombre_completo,
            p.fecha_nacimiento,
            p.edad,
            p.sexo,
            p.raza_grupo_etnico,
            p.estado_civil,
            p.nacionalidad,
            p.estado_nacimiento,
            p.municipio_nacimiento,
            p.parroquia_nacimiento,
            p.estado_procedencia,
            p.municipio_procedencia,
            p.parroquia_procedencia,
            p.direccion_habitacion,
            p.direccion_contacto,
            p.telefono_habitacion,
            p.telefono_contac,
            p.otro_telefono_alternativo,
            p.email,
            p.ocupacion,
            p.anos_ocupacion,
            p.profesion,
            p.anos_ejercidos,
            p.fecha_ingreso_sistema
        FROM pacientes p
        WHERE p.id_paciente = :id
        LIMIT 1
    ");
    $stmt->execute([':id' => $id]);
    $paciente = $stmt->fetch();

    if (!$paciente) {
        echo json_encode(['error' => 'Paciente no encontrado']);
        exit;
    }

    echo json_encode($paciente, JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}