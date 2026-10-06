<?php
if (session_status() == PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
    session_start();
}

date_default_timezone_set('America/Caracas');


define('BASE_URL', '/code');

define('APP_NAME', 'Sistema de Información Oncológica');
define('APP_VERSION', '1.0');
define('APP_INSTITUTION', 'Servicio Desconcentrado Especializado en Diagnóstico y Tratamiento de Enfermedades Oncológicas del Estado Lara');

require_once __DIR__ . '/conecion.php';


function verificar_sesion() {
    if (!isset($_SESSION['id_usuario']) || empty($_SESSION['id_usuario'])) {
        header('Location: ' . BASE_URL . '/Login.php?error=no_sesion');
        exit();
    }
}

function verificar_rol($roles_permitidos = []) {
    if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], $roles_permitidos)) {
        header('Location: ' . BASE_URL . '/Dashboard.php?error=sin_permiso');
        exit();
    }
}

function usuario_actual() {
    return $_SESSION['nombre_completo'] ?? 'Usuario';
}

function registrar_actividad($accion, $tabla = null, $registro_id = null) {
    try {
        $conn = conexion::getConnection();
        $sql = "INSERT INTO registro_actividad 
                (id_usuario, accion, tabla_afectada, registro_id, ip_origen) 
                VALUES (:id_usuario, :accion, :tabla, :registro_id, :ip)";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':id_usuario' => $_SESSION['id_usuario'] ?? 1,
            ':accion' => $accion,
            ':tabla' => $tabla ?? 'sistema',
            ':registro_id' => $registro_id ?? 0,
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'
        ]);
    } catch(PDOException $e) {
        error_log("Error en auditoría: " . $e->getMessage());
    }
}

function formatear_fecha($fecha) {
    if (empty($fecha) || $fecha === '0000-00-00' || $fecha === null) {
        return '—';
    }
    $timestamp = strtotime($fecha);
    if ($timestamp === false) return '—';
    return date('d/m/Y', $timestamp);
}
?>