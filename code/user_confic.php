<?php
require_once 'config/inic.php';
verificar_sesion();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

try {
    $conn = conexion::getConnection();
    
    $institucion       = trim($_POST['institucion'] ?? '');
    $permitidos = ['IVSS', 'MSDS', 'MINISTERIO DE DEFENSA', 'PRIVADO', 'OTRA'];

    if (!in_array($institucion, $permitidos)) {
    $institucion = null;
}

    $nombre_institu     = trim($_POST['establecimiento'] ?? '');

    $fechaReferencia   = trim($_POST['fechaReferencia'] ?? '');
    $fechaRegistro     = trim($_POST['fecha'] ?? date('Y-m-d'));
    $numeroHistoria    = trim($_POST['numeroHistoria'] ?? '');
    
    $nombresCompletos  = trim($_POST['nombres'] ?? '');
    $apellidosCompletos = trim($_POST['apellidos'] ?? '');
    
    $partesNombre = explode(' ', $nombresCompletos, 2);
    $primerNombre = $partesNombre[0] ?? '';
    $segundoNombre = $partesNombre[1] ?? null;
    
    $partesApellido = explode(' ', $apellidosCompletos, 2);
    $primerApellido = $partesApellido[0] ?? '';
    $segundoApellido = $partesApellido[1] ?? null;

    $tipoDocumento     = trim($_POST['tipoDocumento'] ?? 'V');
    $cedulaNumero      = trim($_POST['cedula'] ?? '');
    $cedula = $tipoDocumento . '-' . $cedulaNumero;
    
    
    $fechaNacimiento   = trim($_POST['fechaNacimiento'] ?? '');
    $edad              = (int)($_POST['edad'] ?? 0);


    $sexoRaw           = trim($_POST['sexo'] ?? '');
    $sexo              = 'M';
    if (stripos($sexoRaw, 'Fem') !== false) {
        $sexo = 'F';
    } elseif (stripos($sexoRaw, 'Masc') !== false) {
        $sexo = 'M';
    } elseif (in_array(strtoupper($sexoRaw), ['M', 'F'])) {
        $sexo = strtoupper($sexoRaw);
    }

    $raza              = trim($_POST['raza'] ?? '');
    
    $nacEstado         = trim($_POST['nacEstado'] ?? '');
    $nacMunicipio      = trim($_POST['nacMunicipio'] ?? '');
    $nacParroquia      = trim($_POST['nacParroquia'] ?? '');
    $nacPais           = trim($_POST['nacPais'] ?? 'Venezuela');
    
    $proEstado         = trim($_POST['proEstado'] ?? '');
    $proMunicipio      = trim($_POST['proMunicipio'] ?? '');
    $proParroquia      = trim($_POST['proParroquia'] ?? '');
    $proPais           = trim($_POST['proPais'] ?? 'Venezuela');
    $direccionHabitacion = trim($_POST['direccionHabitacion'] ?? '');
    $direccionContacto   = trim($_POST['direccionContacto'] ?? '');
    
    $ocupacion         = trim($_POST['ocupacion'] ?? '');
    $anosOcupacion     = trim($_POST['aniosOcupacion'] ?? '');
    $profesion         = trim($_POST['profesion'] ?? '');
    $anosEjercidos     = trim($_POST['aniosEjercidos'] ?? '');
    
    $telefonoHabitacion = trim($_POST['telefonoHabitacion'] ?? '');
    $telefonoContacto   = trim($_POST['telefono'] ?? '');
    $telefonoAlterno    = trim($_POST['telefono2'] ?? '');
    $email              = trim($_POST['email'] ?? '');
    

    if (empty($primerNombre) || empty($primerApellido) || empty($cedulaNumero)) {
        echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios']);
        exit();
    }
    
    $check = $conn->prepare("SELECT id_paciente FROM pacientes WHERE cedula = :cedula LIMIT 1");
    $check->execute([':cedula' => $cedula]);
    if ($check->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Ya existe un paciente con esa cédula']);
        exit();
    }
    
    $sql = "INSERT INTO pacientes (
                no_historia,
                institucion_origen_codigo,
                institucion_origen_nombre,
                cedula,
                primer_nombre,
                segundo_nombre,
                primer_apellido,
                segundo_apellido,
                fecha_nacimiento,
                edad,
                sexo,
                raza_grupo_etnico,
                pais_nacimiento,
                estado_nacimiento,
                municipio_nacimiento,
                parroquia_nacimiento,
                pais_procedencia,
                estado_procedencia,
                municipio_procedencia,
                parroquia_procedencia,

                ocupacion,
                anos_ocupacion,
                profesion,
                anos_ejercidos,
                direccion_habitacion,
                direccion_contacto,
                telefono_habitacion,
                telefono_contac,
                otro_telefono_alternativo,
                email,
                fecha_registro_sistema
            ) VALUES (
                :no_historia,
                :institucion_origen_codigo,
                :institucion_origen_nombre,
                :cedula,
                :primer_nombre,
                :segundo_nombre,
                :primer_apellido,
                :segundo_apellido,
                :fecha_nacimiento,
                :edad,
                :sexo,
                :raza,
                :pais_nacimiento,
                :estado_nacimiento,
                :municipio_nacimiento,
                :parroquia_nacimiento,
                :pais_procedencia,
                :estado_procedencia,
                :municipio_procedencia,
                :parroquia_procedencia,
                :ocupacion,
                :anos_ocupacion,
                :profesion,
                :anos_ejercidos,
                :direccion_habitacion,
                :direccion_contacto,
                :telefono_habitacion,
                :telefono_contac,
                :otro_telefono,
                :email,
                CURRENT_TIMESTAMP
            ) RETURNING id_paciente";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':no_historia'              => $numeroHistoria,
        ':institucion_origen_codigo'  => $institucion ?: null,
        ':institucion_origen_nombre'=> $nombre_institu ?: null,
        ':cedula'                   => $cedula,
        ':primer_nombre'            => $primerNombre,
        ':segundo_nombre'           => $segundoNombre,
        ':primer_apellido'          => $primerApellido,
        ':segundo_apellido'         => $segundoApellido,
        ':fecha_nacimiento'         => $fechaNacimiento ?: null,
        ':edad'                     => $edad,
        ':sexo'                     => $sexo,
        ':raza'                     => $raza ?: null,
        ':pais_nacimiento'          => $nacPais ?: null,
        ':estado_nacimiento'        => $nacEstado ?: null,
        ':municipio_nacimiento'     => $nacMunicipio ?: null,
        ':parroquia_nacimiento'     => $nacParroquia ?: null,
        ':pais_procedencia'         => $proPais ?: null,
        ':estado_procedencia'       => $proEstado ?: null,
        ':municipio_procedencia'    => $proMunicipio ?: null,
        ':parroquia_procedencia'    => $proParroquia ?: null,
        ':ocupacion'                => $ocupacion ?: null,
        ':anos_ocupacion'           => $anosOcupacion ?: null,
        ':profesion'                => $profesion ?: null,
        ':anos_ejercidos'           => $anosEjercidos ?: null,
        ':direccion_habitacion'     => $direccionHabitacion ?: null,
        ':direccion_contacto'       => $direccionContacto ?: null,
        ':telefono_habitacion'      => $telefonoHabitacion ?: null,
        ':telefono_contac'          => $telefonoContacto ?: null,
        ':otro_telefono'            => $telefonoAlterno ?: null,
        ':email'                    => $email ?: null,
    ]);
    
    $idNuevo = $stmt->fetchColumn();
    
    registrar_actividad('Registro de paciente', 'pacientes', $idNuevo);
    
    echo json_encode([
        'success' => true,
        'message' => 'Paciente registrado correctamente',
        'id_paciente' => $idNuevo
    ]);
    
} catch (PDOException $e) {
    error_log("Error al registrar paciente: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar: ' . $e->getMessage()
    ]);
}
?>