<?php
require_once 'config/inic.php';

try {
    $conn = conexion::getConnection();
    
    echo "<h1>✅ Conexión exitosa</h1>";
    echo "<p>Host: " . getenv('DB_HOST') . "</p>";
    echo "<p>Base de datos: " . getenv('DB_NAME') . "</p>";
    echo "<p>Usuario: " . getenv('DB_USER') . "</p>";
    echo "<p><strong>Contraseña:</strong> [OCULTA POR SEGURIDAD]</p>";
    
    // Contar estados
    $total = $conn->query("SELECT COUNT(*) FROM estados")->fetchColumn();
    echo "<p>Total estados: $total</p>";
    
} catch(PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>