<?php
require_once __DIR__ . '/config/inic.php';
require_once __DIR__ . '/config/permisos.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['id_usuario'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Sesión expirada']);
    exit;
}

if (!puede_editar('pacientes')) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'No tiene permiso para reactivar pacientes']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
    exit;
}

$input = file_get_contents('php://input');
$datos = json_decode($input, true);

$idPaciente = (int)($datos['id_paciente'] ?? 0);

if ($idPaciente <= 0) {
    echo json_encode(['ok' => false, 'error' => 'ID inválido']);
    exit;
}

try {
    $conn = conexion::getConnection();

    $stmt = $conn->prepare("SELECT status FROM pacientes WHERE id_paciente = :id");
    $stmt->execute([':id' => $idPaciente]);
    $paciente = $stmt->fetch();

    if (!$paciente) {
        echo json_encode(['ok' => false, 'error' => 'El paciente no existe']);
        exit;
    }

    if ($paciente['status'] === true) {
        echo json_encode(['ok' => false, 'error' => 'El paciente ya está activo']);
        exit;
    }

    $stmt = $conn->prepare("
        UPDATE pacientes
        SET status = TRUE,
            fecha_modificacion = CURRENT_TIMESTAMP
        WHERE id_paciente = :id
    ");
    $stmt->execute([':id' => $idPaciente]);

    if (function_exists('registrar_actividad')) {
        registrar_actividad('Reactivación de paciente', 'pacientes', $idPaciente);
    }

    echo json_encode(['ok' => true, 'mensaje' => 'Paciente reactivado correctamente']);

} catch (PDOException $e) {
    error_log("Error al reactivar paciente: " . $e->getMessage());
    echo json_encode(['ok' => false, 'error' => 'Error del sistema']);
}