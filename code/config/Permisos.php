<?php
/* ===================================================================
   ONCOPATH - permisos.php
   Helpers para verificar permisos según el rol del usuario
   + BYPASS para super admins
   =================================================================== */

/* ===================================================================
   CONFIGURACIÓN DE SUPER ADMINS
   Los usuarios en esta lista SIEMPRE ven todo el sistema,
   sin importar su rol.
   =================================================================== */
define('SUPER_ADMINS', [
    'SAOAdmin',
]);

/**
 * Verifica si el usuario actual es super admin
 */
function es_super_admin() {
    $usuario = $_SESSION['nombre_usuario'] ?? '';
    return in_array($usuario, SUPER_ADMINS, true);
}

/**
 * Verifica si el usuario actual puede VER un módulo
 */
function puede_ver($modulo) {
    return verificar_permiso($modulo, 'puede_ver');
}

/**
 * Verifica si el usuario actual puede CREAR en un módulo
 */
function puede_crear($modulo) {
    return verificar_permiso($modulo, 'puede_crear');
}

/**
 * Verifica si el usuario actual puede EDITAR en un módulo
 */
function puede_editar($modulo) {
    return verificar_permiso($modulo, 'puede_editar');
}

/**
 * Verifica si el usuario actual puede ELIMINAR en un módulo
 */
function puede_eliminar($modulo) {
    return verificar_permiso($modulo, 'puede_eliminar');
}

/**
 * Verificación interna de permisos
 * - Super admins → siempre true
 * - Otros → consulta la tabla roles_permisos
 */
function verificar_permiso($modulo, $accion) {
    // 🔓 BYPASS: si es super admin, siempre puede
    if (es_super_admin()) {
        return true;
    }

    if (!isset($_SESSION['rol'])) return false;

    // Caché estático para no repetir queries
    static $permisos = null;

    if ($permisos === null) {
        try {
            $conn = conexion::getConnection();
            $stmt = $conn->prepare("
                SELECT modulo, puede_ver, puede_crear, puede_editar, puede_eliminar
                FROM roles_permisos
                WHERE rol = :rol
            ");
            $stmt->execute([':rol' => $_SESSION['rol']]);
            $permisos = [];
            foreach ($stmt->fetchAll() as $p) {
                $permisos[$p['modulo']] = $p;
            }
        } catch (PDOException $e) {
            error_log("Error en permisos: " . $e->getMessage());
            return false;
        }
    }

    if (!isset($permisos[$modulo])) return false;

    return (bool) $permisos[$modulo][$accion];
}

/**
 * Muestra el nombre del rol de forma legible
 */
function nombre_rol() {
    $roles = [
        'Medico_R'       => 'Médico Residente',
        'Medico_Q'       => 'Médico Quirúrgico',
        'Administrativo' => 'Administrativo',
        'Director'       => 'Director',
        'ADMINISTRADOR'  => 'Administrador',
    ];
    return $roles[$_SESSION['rol'] ?? ''] ?? 'Usuario';
}