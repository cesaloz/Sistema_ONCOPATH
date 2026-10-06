<?php
require_once __DIR__ . '../config/inic.php';

header('Content-Type: application/json; charset=utf-8');

$idPaciente = isset($_GET['id_paciente']) ? (int) $_GET['id_paciente'] : 0;

if ($idPaciente <= 0) {
    echo json_encode([]);
    exit;
}

try {
    $con = conexion::getConnection();
    $historiales = [];

    /* ===== HISTORIAL HEMATO ===== */
    $stmt = $con->prepare("
        SELECT
            id_historia_onco AS id,
            'Hemato' AS tipo,
            'Historia Clínica Hemato' AS titulo,
            COALESCE(diagnostico_inicio, 'Sin diagnóstico') AS descripcion,
            fecha_registro,
            medico_registrador AS medico,
            'fa-microscope' AS icono
        FROM historias_clinicas_hemato
        WHERE id_paciente = :id
        ORDER BY fecha_registro DESC
    ");
    $stmt->execute([':id' => $idPaciente]);
    foreach ($stmt->fetchAll() as $row) {
        $historiales[] = $row;
    }

    /* ===== HISTORIAL CIRUGÍA (si existe la tabla) ===== */
    // Descomentar cuando tengas la tabla creada
    /*
    $stmt = $con->prepare("
        SELECT
            id_cirugia AS id,
            'Cirugía' AS tipo,
            'Historia Clínica Cirugía' AS titulo,
            diagnostico AS descripcion,
            fecha_registro,
            medico_registrador AS medico,
            'fa-notes-medical' AS icono
        FROM historias_cirugia
        WHERE id_paciente = :id
        ORDER BY fecha_registro DESC
    ");
    $stmt->execute([':id' => $idPaciente]);
    foreach ($stmt->fetchAll() as $row) {
        $historiales[] = $row;
    }
    */

    /* ===== HISTORIAL MAMOGRAFÍA (si existe la tabla) ===== */
    /*
    $stmt = $con->prepare("
        SELECT
            id_mamografia AS id,
            'Mamografía' AS tipo,
            'Historia Clínica Mamografía' AS titulo,
            diagnostico_mastologia AS descripcion,
            fecha_consulta AS fecha_registro,
            medico_mastologo AS medico,
            'fa-clipboard-list' AS icono
        FROM consulta_mastologica
        WHERE id_paciente = :id
        ORDER BY fecha_consulta DESC
    ");
    $stmt->execute([':id' => $idPaciente]);
    foreach ($stmt->fetchAll() as $row) {
        $historiales[] = $row;
    }
    */

    // Ordenar todos por fecha descendente
    usort($historiales, function ($a, $b) {
        return strtotime($b['fecha_registro']) - strtotime($a['fecha_registro']);
    });

    echo json_encode($historiales, JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}