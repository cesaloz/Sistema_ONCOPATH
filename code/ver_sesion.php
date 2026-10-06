<?php
session_start();

echo "<h1>🔍 Estado de la Sesión</h1>";

echo "<h2>¿Hay sesión activa?</h2>";
if (isset($_SESSION['id_usuario'])) {
    echo "✅ SÍ hay sesión activa<br><br>";
    echo "<h2>Datos de la sesión:</h2>";
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
} else {
    echo "❌ NO hay sesión activa<br>";
    echo "El usuario no está logueado.";
}
?>