<?php

require_once 'config/inic.php';

if (isset($_SESSION['id_usuario'])) {
    registrar_actividad('Cierre de sesión', 'usuarios', $_SESSION['id_usuario']);
}


$_SESSION = array();


if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destruir la sesión
session_destroy();




header("Location: Login.php");
exit();
?>