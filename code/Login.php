<?php
require_once 'config/inic.php';
 
// Si ya está logueado, redirigir al Dashboard
if (isset($_SESSION['id_usuario'])) {
    header('location: Dashboard.php');
    exit();
}

// Generar token CSRF si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';
$usuario_ingresado = '';

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validar CSRF
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
        die('Token CSRF inválido. Recargue la página.');
    }

    $usuario_ingresado = trim($_POST['usuario'] ?? '');
    $contrasena = $_POST['password'] ?? '';

    if (empty($usuario_ingresado) || empty($contrasena)) {
        $error = 'Por favor, complete todos los campos';
    } else {
        try {
            $conn = conexion::getConnection();

            $stmt = $conn->prepare("
                SELECT 
                    id_usuario,
                    nombre_usuario,
                    contrasena_hash,
                    nombre_completo,
                    rol_sistema,
                    status
                FROM usuarios
                WHERE nombre_usuario = :usuario
                LIMIT 1
            ");
            $stmt->execute([':usuario' => $usuario_ingresado]);
            $user = $stmt->fetch();

            if ($user) {
                if (!$user['status']) {
                    $error = 'Su cuenta está desactivada. Contacte al administrador del sistema.';
                } else {
                    if (password_verify($contrasena, $user['contrasena_hash'])) {

                        session_regenerate_id(true);

                        $_SESSION['id_usuario'] = $user['id_usuario'];
                        $_SESSION['nombre_usuario'] = $user['nombre_usuario'];
                        $_SESSION['nombre_completo'] = $user['nombre_completo'];
                        $_SESSION['rol'] = $user['rol_sistema'];
                        $_SESSION['fecha_login'] = date('Y-m-d H:i:s');

                        $sql_update = "UPDATE usuarios
                                       SET ultimo_acceso = CURRENT_TIMESTAMP
                                       WHERE id_usuario = :id";
                        $conn->prepare($sql_update)->execute([':id' => $user['id_usuario']]);

                        registrar_actividad('Inicio de sesión', 'usuarios', $user['id_usuario']);

                        header('location: Dashboard.php');
                        exit();

                    } else {
                        $error = 'Usuario o contraseña incorrectos';
                    }
                }
            } else {
                $error = 'Usuario o contraseña incorrectos';
            }

        } catch (PDOException $e) {
            error_log("Error en login: " . $e->getMessage());
            $error = 'Error del sistema. Intente más tarde.';
        }
    }
}

// Mensaje de "no sesión" solo si no hay otro error
if (empty($error) && isset($_GET['error']) && $_GET['error'] === 'no_sesion') {
    $error = '🔒 Debes iniciar sesión para acceder a esa página.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ONCOPATH - Iniciar Sesión</title>
    <link rel="stylesheet" href="css/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

    <div class="box">

        <!-- Encabezado -->
        <div class="login-principal">
            <h1>ONCOPATH</h1>
            <p>Por favor Inicie Sesion para continuar</p>
        </div>

        <form id="formLogin" method="POST" action="">

            <?php if (!empty($error)): ?>
                <div class="login_verificacion error" style="display:block;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

            <input type="text"
                    id="usuario"
                    name="usuario"
                    placeholder="Usuario"
                    value="<?php echo htmlspecialchars($usuario_ingresado); ?>"
                    required
            >

            <input 
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Contraseña"
                    required
            >

            <button type="submit">Iniciar Sesión</button>
            <div id="mensajeLogin" style="display:none; margin-top: 10px;"></div>

        </form>

    </div>

    <div>
        <footer class="login_Emblema">
            <img src="css/IMG/image1.png" alt="Logo Oncopath">
            <p>Derechos de autor © 2026 Servicio Oncológico del Estado Lara</p>
        </footer>
    </div>

    <script src="<?php echo BASE_URL; ?>/JS/Sesion.JS"></script>

</body>
</html>