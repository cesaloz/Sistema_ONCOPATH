<?php
require_once __DIR__ . '/config/inic.php';
require_once __DIR__ . '/config/permisos.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['ok' => false, 'error' => 'No hay sesión activa']);
    exit;
}

if (!puede_editar('pacientes')) {
    echo json_encode(['ok' => false, 'error' => 'No tiene permiso para editar pacientes']);
    exit;
}

$input = file_get_contents('php://input');
$datos = json_decode($input, true);

if (!$datos) {
    echo json_encode(['ok' => false, 'error' => 'Datos inválidos']);
    exit;
}

$idPaciente = isset($datos['id_paciente']) ? (int) $datos['id_paciente'] : 0;

if ($idPaciente <= 0) {
    echo json_encode(['ok' => false, 'error' => 'ID de paciente inválido']);
    exit;
}

$errores = [];

if (empty($datos['primer_nombre'])) $errores[] = "El primer nombre es obligatorio";
if (empty($datos['primer_apellido'])) $errores[] = "El primer apellido es obligatorio";
if (empty($datos['cedula'])) $errores[] = "La cédula es obligatoria";
if (empty($datos['no_historia'])) $errores[] = "El número de historia es obligatorio";
if (empty($datos['fecha_nacimiento'])) $errores[] = "La fecha de nacimiento es obligatoria";

$edad = (int) ($datos['edad'] ?? 0);
if ($edad < 0 || $edad > 120) $errores[] = "La edad debe estar entre 0 y 120";

$sexo = strtoupper($datos['sexo'] ?? '');
if (!in_array($sexo, ['M', 'F'])) $errores[] = "El sexo debe ser M o F";

if (!empty($errores)) {
    echo json_encode(['ok' => false, 'error' => implode('. ', $errores)]);
    exit;
}

try {
    $conn = conexion::getConnection();

    $stmt = $conn->prepare("SELECT id_paciente FROM pacientes WHERE id_paciente = :id");
    $stmt->execute([':id' => $idPaciente]);
    if (!$stmt->fetch()) {
        echo json_encode(['ok' => false, 'error' => 'El paciente no existe']);
        exit;
    }

    $stmt = $conn->prepare("SELECT id_paciente FROM pacientes WHERE cedula = :cedula AND id_paciente != :id");
    $stmt->execute([':cedula' => $datos['cedula'], ':id' => $idPaciente]);
    if ($stmt->fetch()) {
        echo json_encode(['ok' => false, 'error' => 'Ya existe otro paciente con esa cédula']);
        exit;
    }

    $stmt = $conn->prepare("SELECT id_paciente FROM pacientes WHERE no_historia = :nh AND id_paciente != :id");
    $stmt->execute([':nh' => $datos['no_historia'], ':id' => $idPaciente]);
    if ($stmt->fetch()) {
        echo json_encode(['ok' => false, 'error' => 'Ya existe otro paciente con ese número de historia']);
        exit;
    }

    $sql = "UPDATE pacientes SET
                primer_nombre = :primer_nombre,
                segundo_nombre = :segundo_nombre,
                primer_apellido = :primer_apellido,
                segundo_apellido = :segundo_apellido,
                cedula = :cedula,
                no_historia = :no_historia,
                fecha_nacimiento = :fecha_nacimiento,
                edad = :edad,
                sexo = :sexo,
                raza_grupo_etnico = :raza,
                direccion_habitacion = :direccion,
                telefono_contac = :telefono,
                email = :email
            WHERE id_paciente = :id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':primer_nombre'    => $datos['primer_nombre'],
        ':segundo_nombre'   => $datos['segundo_nombre'] ?? null,
        ':primer_apellido'  => $datos['primer_apellido'],
        ':segundo_apellido' => $datos['segundo_apellido'] ?? null,
        ':cedula'           => $datos['cedula'],
        ':no_historia'      => $datos['no_historia'],
        ':fecha_nacimiento' => $datos['fecha_nacimiento'],
        ':edad'             => $edad,
        ':sexo'             => $sexo,
        ':raza'             => $datos['raza_grupo_etnico'] ?? null,
        ':direccion'        => $datos['direccion_habitacion'] ?? null,
        ':telefono'         => $datos['telefono_contac'] ?? null,
        ':email'            => $datos['email'] ?? null,
        ':id'               => $idPaciente
    ]);

    if (function_exists('registrar_actividad')) {
        registrar_actividad('Edición de paciente', 'pacientes', $idPaciente);
    }

    echo json_encode([
        'ok' => true,
        'mensaje' => 'Paciente actualizado correctamente'
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'ok' => false,
        'error' => 'Error en la BD: ' . $e->getMessage()
    ]);
}