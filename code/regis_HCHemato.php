<?php

ini_set('display_errors', 0);
error_reporting(E_ALL);


require_once 'config/inic.php';
verificar_sesion();

if (ob_get_length()) ob_clean();


header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

try {
    $conn = conexion::getConnection();

    // ==========================================
    // 1. DATOS DEL FORMULARIO
    // ==========================================
    $id_paciente            = (int)($_POST['pacienteIdSeleccionado'] ?? 0);
    $fechaPrimeraConsulta   = trim($_POST['fechaPrimeraConsulta'] ?? '');
    $fechaPrimerDiagnostico = trim($_POST['fechaPrimerDiagnostico'] ?? '');
    $fechaPrimerTratamiento = trim($_POST['fechaPrimerTratamiento'] ?? '');
    $diagnosticoInicio      = trim($_POST['diagnosticoInicio'] ?? '');
    $tumorPrimario          = trim($_POST['tumorPrimario'] ?? '');

    // Enfermedad actual
    $enfermedadActual       = trim($_POST['enfermedadActual'] ?? '');
    $aspectoGeneral         = trim($_POST['aspectoGeneral'] ?? '');
    $ecog                   = trim($_POST['ecog'] ?? '');
    $KARNOFSKY              = trim($_POST['KARNOFSKY'] ?? '');
    $ta                     = trim($_POST['ta'] ?? '');
    $fc                     = trim($_POST['fc'] ?? '');
    $fp                     = trim($_POST['fp'] ?? '');
    $ft                     = trim($_POST['ft'] ?? '');
    $fr                     = trim($_POST['fr'] ?? '');

    // Plan de tratamiento
    $tipoTratamiento        = trim($_POST['tipoTratamiento'] ?? '');
    $ciclos                 = trim($_POST['ciclos'] ?? '');
    $descripcionTratamiento = trim($_POST['descripcionTratamiento'] ?? '');
    $observaciones          = trim($_POST['observacionesTratamiento'] ?? '');



    // Antecedentes


    $antecedentes           = $_POST['antecedentes'] ?? [];
    $detalleCardio          = trim($_POST['detalleCardiovascular'] ?? '');
    $detalleResp            = trim($_POST['detalleRespiratorio'] ?? '');
    $detalleGastro          = trim($_POST['detalleGastrointestinal'] ?? '');
    $detalleRenal           = trim($_POST['detalleRenal'] ?? '');
    $detalleTabaco          = trim($_POST['detalleTabaco'] ?? '');
    $detalleORL             = trim($_POST['detalleORL'] ?? '');
    $detalleVascular        = trim($_POST['detalleVascular'] ?? '');
    $detalleHemato          = trim($_POST['detalleHematologico'] ?? '');
    $detalleMenopausica     = trim($_POST['furMenopausica'] ?? '');
    $detallePremenopausica  = trim($_POST['furPremenopausica'] ?? '');

    // Estadiaje
    $estadioTNM              = trim($_POST['estadioTNM'] ?? '');
    $localizacionPrimaria    = trim($_POST['localizacionPrimaria'] ?? '');
    $tipoHistologia          = trim($_POST['tipoHistologia'] ?? '');
    $extensionTumoresSolidos = trim($_POST['extensionTumoresSolidos'] ?? '');

    // Clasificación hematológica
    $llaFab         = trim($_POST['llaFab'] ?? '');
    $llcRai         = trim($_POST['llcRai'] ?? '');
    $llcFab1        = trim($_POST['llcFab1'] ?? '');
    $lmcFase        = trim($_POST['lmcFase'] ?? '');
    $llaFba2        = trim($_POST['llaFba2'] ?? '');
    $mielomaDS      = trim($_POST['mielomaDS'] ?? '');
    $linfomaEstadio = trim($_POST['linfomaEstadio'] ?? '');

    // ==========================================
    // 2. VALIDACIONES
    // ==========================================
    if ($id_paciente <= 0) {
        echo json_encode(['success' => false, 'message' => 'No se seleccionó paciente']);
        exit();
    }

    if (empty($diagnosticoInicio)) {
        echo json_encode(['success' => false, 'message' => 'Falta el diagnóstico de inicio']);
        exit();
    }

    $conn->beginTransaction();

   
    $sqlHistoria = "INSERT INTO historias_clinicas_hemato (
                        id_paciente,
                        fecha_primera_consulta,
                        fecha_primer_diagnostico,
                        fecha_primer_tratamiento,
                        diagnostico_inicio,
                        medico_registrador,
                        tumor_primario,
                        status
                    ) VALUES (
                        :id_paciente,
                        :fecha_primera_consulta,
                        :fecha_primer_diagnostico,
                        :fecha_primer_tratamiento,
                        :diagnostico_inicio,
                        :medico_registrador,
                        :tumor_primario,
                        true
                    ) RETURNING id_historia_onco";

    $stmt = $conn->prepare($sqlHistoria);
    $stmt->execute([
        ':id_paciente'              => $id_paciente,
        ':fecha_primera_consulta'   => $fechaPrimeraConsulta ?: null,
        ':fecha_primer_diagnostico' => $fechaPrimerDiagnostico ?: null,
        ':fecha_primer_tratamiento' => $fechaPrimerTratamiento ?: null,
        ':diagnostico_inicio'       => $diagnosticoInicio,
        ':medico_registrador'       => $_SESSION['nombre_completo'] ?? 'Desconocido',
        ':tumor_primario'           => $tumorPrimario ?: null,
    ]);

    $idHistoria = $stmt->fetchColumn();
        if (!$idHistoria) {
                throw new Exception('No se pudo obtener el ID de la historia');
            }


    $sqlConsulta = "INSERT INTO consultas_oncologicas (
                        id_historia_onco,
                        fecha_consulta,
                        enfermedad_actual,
                        descripcion_examen_fisico,
                        estado_ecog,
                        estado_karnofsky,
                        tension_arterial,
                        pulso,
                        temperatura,
                        peso,
                        frecuencia_respiratoria,
                        plan_tratamiento_medico,
                        medico_responsable,
                        observaciones,
                        tipo_tratamiento,
                        ciclos_sesiones
                    ) VALUES (
                        :id_historia,
                        CURRENT_DATE,
                        :enfermedad_actual,
                        :examen_fisico,
                        :estado_ecog,
                        :estado_karnofsky,
                        :ta,
                        :pulso,
                        :temperatura,
                        :peso,
                        :fr,
                        :plan,
                        :medico,
                        :observaciones,
                        :tipo_tratamiento,
                        :ciclos
                    )";

    $stmt = $conn->prepare($sqlConsulta);
    $stmt->execute([
        ':id_historia'       => $idHistoria,
        ':enfermedad_actual' => $enfermedadActual ?: null,
        ':examen_fisico'     => $aspectoGeneral ?: null,
        ':estado_ecog'       => $ecog !== '' ? (int)$ecog : null,
        ':estado_karnofsky'  => $KARNOFSKY ?: null,
        ':ta'                => $ta ?: null,
        ':pulso'             => $fc ?: null,
        ':temperatura'       => $ft ?: null,
        ':peso'              => $fp ?: null,
        ':fr'                => $fr ?: null,
        ':plan'              => $descripcionTratamiento ?: null,
        ':medico'            => $_SESSION['nombre_completo'] ?? null,
        ':observaciones'     => $observaciones ?: null,
        ':tipo_tratamiento'  => $tipoTratamiento ?: null,
        ':ciclos'            => $ciclos !== '' ? (int)$ciclos : null,
    ]);

    // ==========================================
    // 6. INSERTAR ANTECEDENTES
    // ==========================================
    $sqlAntecedente = "INSERT INTO antecedentes_oncologicos (
                            id_historia_onco,
                            tipo,
                            descripcion
                        ) VALUES (

                            :id_historia,
                            :tipo,
                            :descripcion
                        )";

    $stmtAnt = $conn->prepare($sqlAntecedente);

    $mapeoAntecedentes = [

        'Cardiovascular'   => $detalleCardio,
        'Respiratorios'     => $detalleResp,
        'Gastrointestinal' => $detalleGastro,
        'Renal'            => $detalleRenal,
        'Tabaco y/o Licor' => $detalleTabaco,
        'ORL/CVC'          => $detalleORL,
        'Vascular'         => $detalleVascular,
        'Hematológico'     => $detalleHemato,
        'Menopáusica'        => $detalleMenopausica,
        'Premenopáusica'     => $detallePremenopausica,
    ];

    foreach ($antecedentes as $ant) {
    if (isset($mapeoAntecedentes[$ant])) {
        try {
            $stmtAnt->execute([
                ':id_historia' => $idHistoria,
                ':tipo'        => $ant,
                ':descripcion' => $mapeoAntecedentes[$ant] ?: 'Sin especificar',
            ]);
        } catch (PDOException $e) {
            error_log("Error al insertar antecedente '{$ant}': " . $e->getMessage());
        }
    }
}

   

    if (!empty($localizacionPrimaria) || !empty($tipoHistologia) || !empty($extensionTumoresSolidos)) {
        $sqlTumor = "INSERT INTO tumores (
                        id_historia_onco,
                        localizacion_primaria_topo,
                        tipo_histologico,
                        extension_clinica
                    ) VALUES (
                        :id_historia,
                        :localizacion,
                        :histologia,
                        :extension
                    )";

        $stmtTumor = $conn->prepare($sqlTumor);
        $stmtTumor->execute([
            ':id_historia'  => $idHistoria,
            ':localizacion' => $localizacionPrimaria ?: null,
            ':histologia'   => $tipoHistologia ?: null,
            ':extension'    => $extensionTumoresSolidos ?: null,
        ]);
    }

    // ==========================================
    // 8. INSERTAR EN `estadios_historia_onco`
    // ==========================================
    if (!empty($estadioTNM) || !empty($llaFab) || !empty($llcRai) || 
        !empty($lmcFase) || !empty($llaFba2) || !empty($mielomaDS) || 
        !empty($linfomaEstadio)) {

        $sqlEstadio = "INSERT INTO estadios_historia_onco (
                            id_historia_onco,
                            tipo_estadio,
                            estadio_patologico,
                            leucemia_linfoide_aguda_fau,
                            leucemia_linfoide_cronica_rai,
                            leucemia_mieloide_aguda_fab,
                            leucemia_mieloide_cronica,
                            mieloma_multiple,
                            linfomas,
                            leucemia_linfoide_aguda_fab_variante,
                            medico_clasificador
                        ) VALUES (
                            :id_historia,
                            :tipo_estadio,
                            :estadio_patologico,
                            :llaFab,
                            :llcRai,
                            :llcFab1,
                            :lmcFase,
                            :mielomaDS,
                            :linfomaEstadio,
                            :llaFba2,
                            :medico
                        )";

        $stmtEstadio = $conn->prepare($sqlEstadio);
        $stmtEstadio->execute([
            ':id_historia'        => $idHistoria,
            ':tipo_estadio'       => 'CLINICO',
            ':estadio_patologico' => $estadioTNM ?: null,
            ':llaFab'             => $llaFab ?: null,
            ':llcRai'             => $llcRai ?: null,
            ':llcFab1'            => $llcFab1 ?: null,
            ':lmcFase'            => $lmcFase ?: null,
            ':mielomaDS'          => $mielomaDS ?: null,
            ':linfomaEstadio'     => $linfomaEstadio ?: null,
            ':llaFba2'            => $llaFba2 ?: null,
            ':medico'             => $_SESSION['nombre_completo'] ?? null,
        ]);
    }

    // ==========================================
    // 9. COMMIT
    // ==========================================
    $conn->commit();

    registrar_actividad('Registro de historia clínica hemato', 'historias_clinicas_hemato', $idHistoria);

    echo json_encode([
        'success' => true,
        'message' => 'Historia clínica guardada correctamente',
        'id_historia' => $idHistoria
    ]);

} catch (PDOException $e) {
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    error_log("Error al guardar historia: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar: ' . $e->getMessage()
    ]);
}
?>