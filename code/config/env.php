<?php


// Función principal
function cargar_env($ruta = null) {
    
    // Si no se especifica ruta, usar la raíz del proyecto
    if ($ruta === null) {
        $ruta = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env';
    }
    
    // Verificar si el archivo existe
    if (!file_exists($ruta)) {
        // Si no existe, intentar con .env.example
        $ruta_ejemplo = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env.example';
        
        if (file_exists($ruta_ejemplo)) {
            die("❌ Error: No existe el archivo .env. 
                 Copia .env.example a .env y configura tus datos.");
        }
        
        die("❌ Error: No se encontró el archivo .env");
    }
    
    // Leer todas las líneas del archivo
    $lineas = file($ruta, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    // Si no se pudo leer
    if ($lineas === false) {
        die("❌ Error: No se pudo leer el archivo .env");
    }
    
    // Recorrer cada línea
    foreach ($lineas as $linea) {
        
        // Eliminar espacios al inicio y final
        $linea = trim($linea);
        
        // Ignorar líneas vacías
        if (empty($linea)) {
            continue;
        }
        
        // Ignorar comentarios (líneas que empiezan con #)
        if (strpos($linea, '#') === 0) {
            continue;
        }
        
        // Ignorar líneas sin "="
        if (strpos($linea, '=') === false) {
            continue;
        }
        
        // Separar la variable del valor
        // Ejemplo: "DB_PASSWORD=admin123"
        //   $clave = "DB_PASSWORD"
        //   $valor = "admin123"
        $partes = explode('=', $linea, 2);
        $clave = trim($partes[0]);
        $valor = trim($partes[1]);
        
        // Quitar comillas si las tiene
        $valor = trim($valor, '"\'');
        
        // Guardar como variable de entorno
        putenv("$clave=$valor");
        
        // También guardar en $_ENV por si acaso
        $_ENV[$clave] = $valor;
    }
    
    return true;
}

// Ejecutar la función automáticamente al incluir este archivo
cargar_env();
?>