<?php
require_once __DIR__ . '/../config/inic.php';
verificar_sesion();
try {
    $con = conexion::getConnection();

    /* ===== TRAER HISTORIAS HEMATO CON DATOS DEL PACIENTE ===== */
    $stmt = $con->query("
    SELECT
        id_paciente,
        cedula,
        no_historia,
        TRIM(
            COALESCE(primer_nombre, '') || ' ' ||
            COALESCE(segundo_nombre, '') || ' ' ||
            COALESCE(primer_apellido, '') || ' ' ||
            COALESCE(segundo_apellido, '')
        ) AS nombre_completo,
        edad,
        sexo,
        fecha_ingreso_sistema
    FROM pacientes
    ORDER BY id_paciente DESC
    LIMIT 100
");
$pacientes = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ONCOPATH - Historia Clínica Hemato</title>
    <link rel="stylesheet" href="../css/Base.css">
<link rel="stylesheet" href="css/HCHemato.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

    <div class="dashboard-container">

        <!-- ===== BARRA SUPERIOR ===== -->
        <div class="dashboard-container">

        <header class="BarraSuperior">
            <div class="BarraIzq">
                <i class="fas fa-hospital-user logo-icon"></i>
                <h1>Oncopath</h1>
            </div>

            <div class="BarraDere">
                <div class="user-info">
                    <i class="fas fa-user-circle user-avatar"></i>
                    <span class="user-name"><?php echo htmlspecialchars($_SESSION['nombre_completo']); ?></span>
                </div>
                <button class="logout-btn" onclick="cerrarSesion()">
                    <i class="fas fa-sign-out-alt"></i>
                    Cerrar Sesión
                </button>
            </div>
        </header>

        <div class="main-content">

            <!-- ===== BARRA LATERAL ===== -->
            <aside class="sidebar">
                <nav>
                    <ul>
                        <li><a href="../Dashboard.php"><i class="fas fa-chart-pie"></i> Inicio</a></li>
                        <li><a href="../pacientes.php"><i class="fas fa-users"></i> Pacientes</a></li>

                        <li class="has-submenu open">
                            <a href="HCHemato.php" class="active menu-toggle" onclick="toggleSubmenu(event)">
                                <i class="fas fa-edit"></i> Llenado de Historial
                                <i class="fas fa-chevron-down arrow"></i>
                            </a>
                            <ul class="submenu">
                                <li><a href="Registro_Paciente.php"><i class="fas fa-user-plus"></i> REGISTRO DE PACIENTES</a></li>
                                <li><a href="HCHemato.php" class="active1"><i class="fas fa-microscope"></i> HISTORIA CLÍNICA HEMATO</a></li>
                                <li><a href="HCCirugia.php"><i class="fas fa-notes-medical"></i> HISTORIA CLÍNICA CIRUGÍA</a></li>
                                <li><a href="HCMamografia.php"><i class="fas fa-clipboard-list"></i> HISTORIA CLÍNICA MAMOGRAFÍA</a></li>
                            </ul>
                        </li>

                        <li><a href="../diagnostico.php"><i class="fas fa-stethoscope"></i> Diagnóstico</a></li>
                        <li><a href="../consulta.php"><i class="fas fa-comments"></i> Consulta</a></li>
                        <li><a href="../cirugia.php"><i class="fas fa-syringe"></i> Cirugía</a></li>
                        <li><a href="../Historiales.php"><i class="fas fa-file-medical"></i> Historiales</a></li>
                    </ul>
                </nav>
            </aside>

            <main class="content">

                <div class="content-header">
                    <div>
                        <h2>REGISTRO DE HISTORIA CLÍNICA HEMATO - ONCOLOGÍA</h2>
                        <p class="subtitle">Complete los pasos para registrar la historia hematológica del paciente</p>
                    </div>
                    <button type="button" class="btn-secondary" onclick="window.location.href='../Pacientes.html'">
                        <i class="fas fa-arrow-left"></i> Volver
                    </button>
                </div>

                <!-- ===== PESTAÑAS (4 pasos) ===== -->
                <div class="tabs-nav">
                    <button type="button" class="tab-btn active" onclick="mostrarTab(event, 'tab-buscar')">
                        <i class="fas fa-search"></i> 1. Buscar Paciente
                    </button>

                    <button type="button" class="tab-btn locked" id="btnTabConsulta" onclick="intentarIrATab('tab-consulta')" disabled>
                        <i class="fas fa-notes-medical"></i> 2. Consulta y Diagnóstico
                        <i class="fas fa-lock" style="font-size: 0.7rem; margin-left: 0.3rem;"></i>
                    </button>

                    <button type="button" class="tab-btn locked" id="btnTabClinica" onclick="intentarIrATab('tab-clinica')" disabled>
                        <i class="fas fa-user-md"></i> 3. Clínica del Paciente
                        <i class="fas fa-lock" style="font-size: 0.7rem; margin-left: 0.3rem;"></i>
                    </button>

                    <button type="button" class="tab-btn locked" id="btnTabTratamiento" onclick="intentarIrATab('tab-tratamiento')" disabled>
                        <i class="fas fa-clipboard-list"></i> 4. Plan de Tratamiento
                        <i class="fas fa-lock" style="font-size: 0.7rem; margin-left: 0.3rem;"></i>
                    </button>
                </div>

                <form id="formPaciente" onsubmit="event.preventDefault();">

                    <!-- ============================================= -->
                    <!-- TAB 1: BUSCAR PACIENTE -->
                    <!-- ============================================= -->
                    <div id="tab-buscar" class="tab-content active">

                        <!-- ===== LISTA DE HISTORIAS CLÍNICAS HEMATO ===== -->
<!-- ===== LISTA DE PACIENTES PARA SELECCIONAR ===== -->
<section class="table-container">

    <div class="table-header">
        <h3>
            Lista de Pacientes
            <span class="count-badge">
                <?php echo count($pacientes ?? []); ?>
            </span>
        </h3>
        <input type="text"
               id="buscarPaciente"
               placeholder="Buscar por nombre o cédula..."
               class="search-input"
               oninput="filtrarPacientes()">
    </div>

    <table id="tablaPacientes">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Cédula</th>
                <th>Edad</th>
                <th>N° Historia</th>
                <th>Fecha Ingreso</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($pacientes)): ?>
                <tr>
                    <td colspan="7" style="text-align:center; padding: 2rem; color: #94a3b8;">
                        <i class="fas fa-user-slash" style="font-size: 2rem; display: block; margin-bottom: 0.5rem;"></i>
                        No hay pacientes registrados aún.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($pacientes as $p): ?>
                    <tr>
                        <td>#<?= htmlspecialchars($p['id_paciente']) ?></td>
                        <td><?= htmlspecialchars($p['nombre_completo']) ?></td>
                        <td><?= htmlspecialchars($p['cedula']) ?></td>
                        <td><?= htmlspecialchars($p['edad']) ?></td>
                        <td><?= htmlspecialchars($p['no_historia']) ?></td>
                        <td><?= formatear_fecha($p['fecha_ingreso_sistema']) ?></td>
                        <td>
                            <button type="button"
                                    class="btn-select-paciente"
                                    onclick="seleccionarPaciente(this, <?= $p['id_paciente'] ?>, '<?= htmlspecialchars($p['nombre_completo']) ?>', '<?= htmlspecialchars($p['cedula']) ?>')">
                                <i class="fas fa-check"></i> Seleccionar
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Mensaje de "no hay resultados" -->
    <div class="empty-state" id="emptyState" style="display:none;">
        <i class="fas fa-user-slash"></i>
        <p>No se encontraron pacientes con esos filtros.</p>
    </div>

    <!-- Aviso de paciente seleccionado -->
    <div id="pacienteSeleccionado" class="paciente-seleccionado" style="display:none;">
        <i class="fas fa-user-check"></i>
        <div>
            <strong>Paciente seleccionado:</strong>
            <span id="nombreSeleccionado"></span>
        </div>
    </div>

</section>

                        <div class="form-actions">
                            <button type="button" class="action-btn save-btn" onclick="continuarPaso('tab-consulta')">
                                Continuar <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>

                    </div>

                    <!-- ============================================= -->
                    <!-- TAB 2: CONSULTA Y DIAGNÓSTICO -->
                    <!-- ============================================= -->
                    <div id="tab-consulta" class="tab-content">

                        <section class="form-section">
                            <div class="section-header">
                                <div class="section-icon red"><i class="fas fa-notes-medical"></i></div>
                                <div>
                                    <h3>Consulta y Diagnóstico</h3>
                                    <p>Fechas y diagnóstico inicial del paciente</p>
                                </div>
                            </div>

                            <div class="form-grid grid-3">
                                <div class="form-group">
                                    <label for="fechaPrimeraConsulta">Fecha de la Primera Consulta</label>
                                    <input type="date" id="fechaPrimeraConsulta" name="fechaPrimeraConsulta" step="1" value="<?php echo date('Y-m-d\TH:i:s'); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="fechaPrimerDiagnostico">Fecha del Primer Diagnóstico</label>
                                    <input type="date" id="fechaPrimerDiagnostico" name="fechaPrimerDiagnostico">
                                </div>
                                <div class="form-group">
                                    <label for="fechaPrimerTratamiento">Fecha del Primer Tratamiento</label>
                                    <input type="date" id="fechaPrimerTratamiento" name="fechaPrimerTratamiento">
                                </div>
                                <div class="form-group full-width">
                                    <label for="diagnosticoInicio">Diagnóstico de Inicio</label>
                                    <textarea id="diagnosticoInicio" name="diagnosticoInicio" rows="3"
                                              placeholder="Describa el diagnóstico inicial del paciente..."></textarea>
                                </div>
                            </div>

                            <!-- Tumor Primario -->
                            <div class="form-group" style="margin-top: 1.2rem;">
                                <label>Tumor Primario</label>
                                <div class="radio-inline">
                                    <label class="radio-pill">
                                        <input type="radio" name="tumorPrimario" value="UNICO">
                                        <span>Único</span>
                                    </label>
                                    <label class="radio-pill">
                                        <input type="radio" name="tumorPrimario" value="MULTIPLE">
                                        <span>Multiple</span>
                                    </label>
                                    <label class="radio-pill">
                                        <input type="radio" name="tumorPrimario" value="DUDOSO">
                                        <span>Dudoso</span>
                                    </label>
                                    <label class="radio-pill">
                                        <input type="radio" name="tumorPrimario" value="NO ESPECIFICADO">
                                        <span>No Especificado</span>
                                    </label>
                                </div>
                            </div>
                        </section>

                        <section class="form-section">
                            <div class="section-header">
                                <div class="section-icon orange"><i class="fas fa-layer-group"></i></div>
                                <div>
                                    <h3>Estadiaje</h3>
                                    <p>Evaluación médica que determina el tamaño y la propagación del tumor</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Estadio *</label>
                                <div class="radio-inline">
                                    <label class="radio-pill"><input type="radio" name="estadioTNM" value="T"><span>T</span></label>
                                    <label class="radio-pill"><input type="radio" name="estadioTNM" value="N"><span>N</span></label>
                                    <label class="radio-pill"><input type="radio" name="estadioTNM" value="M"><span>M</span></label>
                                </div>
                            </div>

                            <div class="form-grid grid-2" style="margin-top: 1.2rem;">
                                <div class="form-group">
                                    <label for="localizacionPrimaria">Localización Primaria (Topografía)</label>
                                    <textarea id="localizacionPrimaria" name="localizacionPrimaria" rows="3"
                                              placeholder="Describa la localización..."></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="tipoHistologia">Tipo de Histología</label>
                                    <textarea id="tipoHistologia" name="tipoHistologia" rows="3"
                                              placeholder="Describa el tipo de histología..."></textarea>
                                </div>
                            </div>

                            <div class="form-group" style="margin-top: 1.2rem;">
                                <label>Extensión Clínica de los Tumores Sólidos</label>
                                <div class="radio-list">
                                    <label class="radio-line">
                                        <input type="radio" name="extensionTumoresSolidos" value="CARCINOMA IN SITU">
                                        <span>Carcinoma</span>
                                    </label>
                                    <label class="radio-line">
                                        <input type="radio" name="extensionTumoresSolidos" value="LOCALIZADA">
                                        <span>Localizada</span>
                                    </label>
                                    <label class="radio-line">
                                        <input type="radio" name="extensionTumoresSolidos" value="EXTENSION DIRECTA">
                                        <span>Extensión Directa</span>
                                    </label>
                                    <label class="radio-line">
                                        <input type="radio" name="extensionTumoresSolidos" value="LINFATICOS REGIONALES COMPROMETIDOS">
                                        <span>Linfáticos Regionales Comprometidos</span>
                                    </label>
                                    <label class="radio-line">
                                        <input type="radio" name="extensionTumoresSolidos" value="EXTENSION DIRECTA Y LINFATICOS REGIONALES COMPROMETIDOS">
                                        <span>Extensión Directa y Linfáticos Regionales Comprometidos</span>
                                    </label>
                                    <label class="radio-line">
                                        <input type="radio" name="extensionTumoresSolidos" value="METASTASIS A DISTANCIA">
                                        <span>Metástasis a Distancia</span>
                                    </label>
                                    <label class="radio-line">
                                        <input type="radio" name="extensionTumoresSolidos" value="NO APLICABLE">
                                        <span>No Duplicable</span>
                                    </label>
                                    <label class="radio-line">
                                        <input type="radio" name="extensionTumoresSolidos" value="SIN ESPECIFICACIONES">
                                        <span>Sin Especificaciones</span>
                                    </label>
                                </div>
                            </div>
                        </section>

                        <section class="form-section">
                            <div class="section-header">
                                <div class="section-icon purple"><i class="fas fa-microscope"></i></div>
                                <div>
                                    <h3>Clasificación de los Tumores Hematológicos</h3>
                                    <p>Seleccione la clasificación que corresponda</p>
                                </div>
                            </div>

                            <div class="form-grid grid-2">
                                <div class="form-group">
                                    <label>Leucemia Linfoide Aguda (FAB)</label>
                                    <div class="radio-inline">
                                        <label class="radio-pill"><input type="radio" name="llaFab" value="L1"><span>L1</span></label>
                                        <label class="radio-pill"><input type="radio" name="llaFab" value="L2"><span>L2</span></label>
                                        <label class="radio-pill"><input type="radio" name="llaFab" value="L3"><span>L3</span></label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Leucemia Linfoide Crónica (RAI)</label>
                                    <div class="radio-inline">
                                        <label class="radio-pill"><input type="radio" name="llcRai" value="0"><span>0</span></label>
                                        <label class="radio-pill"><input type="radio" name="llcRai" value="1"><span>1</span></label>
                                        <label class="radio-pill"><input type="radio" name="llcRai" value="2"><span>2</span></label>
                                        <label class="radio-pill"><input type="radio" name="llcRai" value="3"><span>3</span></label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Leucemia Meloide Aguda (FAB)</label>
                                    <div class="radio-inline">
                                        <label class="radio-pill"><input type="radio" name="llcFab1" value="M0"><span>M0</span></label>
                                        <label class="radio-pill"><input type="radio" name="llcFab1" value="M1"><span>M1</span></label>
                                        <label class="radio-pill"><input type="radio" name="llcFab1" value="M2"><span>M2</span></label>
                                        <label class="radio-pill"><input type="radio" name="llcFab1" value="M3"><span>M3</span></label>
                                        <label class="radio-pill"><input type="radio" name="llcFab1" value="M4"><span>M4</span></label>
                                        <label class="radio-pill"><input type="radio" name="llcFab1" value="M5"><span>M5</span></label>
                                        <label class="radio-pill"><input type="radio" name="llcFab1" value="M6"><span>M6</span></label>
                                        <label class="radio-pill"><input type="radio" name="llcFab1" value="M7"><span>M7</span></label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Leucemia Mieloide Crónica</label>
                                    <div class="radio-inline">
                                        <label class="radio-pill"><input type="radio" name="lmcFase" value="CRONICA"><span>Crónica</span></label>
                                        <label class="radio-pill"><input type="radio" name="lmcFase" value="ACELERADA"><span>Acelerada</span></label>
                                        <label class="radio-pill"><input type="radio" name="lmcFase" value="CRISIS BLASTICA"><span>Crisis Blástica</span></label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Leucemia Linfoide Aguda (FBA)</label>
                                    <div class="radio-inline">
                                        <label class="radio-pill"><input type="radio" name="llaFba2" value="AR"><span>AR</span></label>
                                        <label class="radio-pill"><input type="radio" name="llaFba2" value="BR"><span>BR</span></label>
                                    </div>
                                </div>

                                <div class="form-group full-width">
                                    <label>Mieloma Múltiple (Durie-Salmon)</label>
                                    <div class="radio-inline">
                                        <label class="radio-pill"><input type="radio" name="mielomaDS" value="IA"><span>IA</span></label>
                                        <label class="radio-pill"><input type="radio" name="mielomaDS" value="IB"><span>IB</span></label>
                                        <label class="radio-pill"><input type="radio" name="mielomaDS" value="IIA"><span>IIA</span></label>
                                        <label class="radio-pill"><input type="radio" name="mielomaDS" value="IIB"><span>IIB</span></label>
                                        <label class="radio-pill"><input type="radio" name="mielomaDS" value="IIIA"><span>IIIA</span></label>
                                        <label class="radio-pill"><input type="radio" name="mielomaDS" value="IIIB"><span>IIIB</span></label>
                                    </div>
                                </div>

                                <div class="form-group full-width">
                                    <label>Linfomas</label>
                                    <div class="radio-inline">
                                        <label class="radio-pill"><input type="radio" name="linfomaEstadio" value="I"><span>I</span></label>
                                        <label class="radio-pill"><input type="radio" name="linfomaEstadio" value="II"><span>II</span></label>
                                        <label class="radio-pill"><input type="radio" name="linfomaEstadio" value="III"><span>III</span></label>
                                        <label class="radio-pill"><input type="radio" name="linfomaEstadio" value="IV"><span>IV</span></label>
                                        <label class="radio-pill"><input type="radio" name="linfomaEstadio" value="A"><span>A</span></label>
                                        <label class="radio-pill"><input type="radio" name="linfomaEstadio" value="B"><span>B</span></label>
                                        <label class="radio-pill"><input type="radio" name="linfomaEstadio" value="C"><span>C</span></label>
                                        <label class="radio-pill"><input type="radio" name="linfomaEstadio" value="E"><span>E</span></label>
                                        <label class="radio-pill"><input type="radio" name="linfomaEstadio" value="S"><span>S</span></label>
                                        <label class="radio-pill"><input type="radio" name="linfomaEstadio" value="MO"><span>MO</span></label>
                                        <label class="radio-pill"><input type="radio" name="linfomaEstadio" value="LCR"><span>LCR</span></label>
                                        <label class="radio-pill"><input type="radio" name="linfomaEstadio" value="OTROS"><span>Otro</span></label>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <div class="form-actions">
                            <button type="button" class="action-btn cancel-btn" onclick="continuarPaso('tab-buscar')">
                                <i class="fas fa-arrow-left"></i> Atrás
                            </button>
                            <button type="button" class="action-btn save-btn" onclick="continuarPaso('tab-clinica')">
                                Continuar <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>

                    </div>

                    <!-- ============================================= -->
                    <!-- TAB 3: CLÍNICA DEL PACIENTE (Enfermedad + antecedentes + Examen Físico) -->
                    <!-- ============================================= -->
                    <div id="tab-clinica" class="tab-content">

                        <!-- ENFERMEDAD ACTUAL -->
                        <section class="form-section">
                            <div class="section-header">
                                <div class="section-icon orange"><i class="fas fa-file-medical"></i></div>
                                <div>
                                    <h3>Enfermedad Actual</h3>
                                    <p>Describa la enfermedad que motiva la consulta</p>
                                </div>
                            </div>

                            <div class="form-grid grid-2" style="margin-top: 0.5rem;">
                                <div class="form-group full-width">
                                    <label for="Enfermedad_Actual">Enfermedad Actual</label>
                                    <textarea id="Enfermedad_Actual" name="enfermedadActual" rows="6"
                                              placeholder="Describa la enfermedad actual del paciente: inicio, evolución, síntomas, tratamientos previos..."></textarea>
                                </div>
                            </div>
                        </section>

                        <!-- ANTECEDENTES -->
                        <section class="form-section">
                            <div class="section-header">
                                <div class="section-icon red"><i class="fas fa-history"></i></div>
                                <div>
                                    <h3>Antecedentes Personales</h3>
                                    <p>Seleccione los antecedentes y especifique si es necesario</p>
                                </div>
                            </div>

                            <div class="form-group" style="margin-top: 0.5rem;">
                                <div class="antecedentes-tabla">

                                    <div class="ant-row">
                                        <label class="ant-item">
                                            <input type="checkbox" name="antecedentes[]" value="Cardiovascular">
                                            <span>Cardiovascular</span>
                                        </label>
                                        <div class="ant-extra">
                                            <input type="text" name="detalleCardiovascular" placeholder="Especifique..." class="ant-input-texto">
                                        </div>
                                    </div>

                                    <div class="ant-row">
                                        <label class="ant-item">
                                            <input type="checkbox" name="antecedentes[]" value="Respiratorios">
                                            <span>Respiratorio</span>
                                        </label>
                                        <div class="ant-extra">
                                            <input type="text" name="detalleRespiratorio" placeholder="Especifique..." class="ant-input-texto">
                                        </div>
                                    </div>

                                    <div class="ant-row">
                                        <label class="ant-item">
                                            <input type="checkbox" name="antecedentes[]" value="Gastrointestinal">
                                            <span>Gastrointestinal</span>
                                        </label>
                                        <div class="ant-extra">
                                            <input type="text" name="detalleGastrointestinal" placeholder="Especifique..." class="ant-input-texto">
                                        </div>
                                    </div>

                                    <div class="ant-row">
                                        <label class="ant-item">
                                            <input type="checkbox" name="antecedentes[]" value="Renal">
                                            <span>Renal</span>
                                        </label>
                                        <div class="ant-extra">
                                            <input type="text" name="detalleRenal" placeholder="Especifique..." class="ant-input-texto">
                                        </div>
                                    </div>

                                    <div class="ant-row">
                                        <label class="ant-item">
                                            <input type="checkbox" name="antecedentes[]" value="Tabaco y/o Licor">
                                            <span>Tabaco y/o Licor</span>
                                        </label>
                                        <div class="ant-extra">
                                            <input type="text" name="detalleTabaco" placeholder="Cantidad / frecuencia..." class="ant-input-texto">
                                        </div>
                                    </div>

                                    <div class="ant-row">
                                        <label class="ant-item">
                                            <input type="checkbox" name="antecedentes[]" value="ORL/CVC">
                                            <span>ORL/CVC</span>
                                        </label>
                                        <div class="ant-extra">
                                            <input type="text" name="detalleORL" placeholder="Especifique..." class="ant-input-texto">
                                        </div>
                                    </div>

                                    <div class="ant-row">
                                        <label class="ant-item">
                                            <input type="checkbox" name="antecedentes[]" value="Vascular">
                                            <span>Vascular</span>
                                        </label>
                                        <div class="ant-extra">
                                            <input type="text" name="detalleVascular" placeholder="Especifique..." class="ant-input-texto">
                                        </div>
                                    </div>

                                    <div class="ant-row">
                                        <label class="ant-item">
                                            <input type="checkbox" name="antecedentes[]" value="Hematológico">
                                            <span>Hematológico</span>
                                        </label>
                                        <div class="ant-extra">
                                            <input type="text" name="detalleHematologico" placeholder="Especifique..." class="ant-input-texto">
                                        </div>
                                    </div>

                                    <div class="ant-row">
                                        <label class="ant-item">
                                            <input type="checkbox" name="antecedentes[]" value="Menopáusica FUR'">
                                            <span>Menopáusica</span>
                                        </label>
                                        <div class="ant-extra">
                                            <span class="ant-extra-label">FUR</span>
                                            <input type="date" name="furMenopausica" class="ant-input-fecha">
                                        </div>
                                    </div>

                                    <div class="ant-row">
                                        <label class="ant-item">
                                            <input type="checkbox" name="antecedentes[]" value="Premenopáusica FUR">
                                            <span>Premenopáusica</span>
                                        </label>
                                        <div class="ant-extra">
                                            <span class="ant-extra-label">FUR</span>
                                            <input type="date" name="furPremenopausica" class="ant-input-fecha">
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </section>

                        <!-- EXAMEN FÍSICO -->
                        <section class="form-section">
                            <div class="section-header">
                                <div class="section-icon teal"><i class="fas fa-user-md"></i></div>
                                <div>
                                    <h3>Examen Físico</h3>
                                    <p>Registre los hallazgos del examen físico del paciente</p>
                                </div>
                            </div>
                            <div class="form-grid grid-2" style="margin-top: 1.2rem;">
                                <div class="form-group">
                                    <label for="localizacionPrimaria">Grupo Cooperativo Oncológico del Este (E.C.O.G)</label>
                                    <textarea id="ecog" name="ecog" rows="3"
                                              placeholder=" 0 al 5..."></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="tipoHistologia">KARNOFSKY</label>
                                    <textarea id="karnofsky" name="KARNOFSKY" rows="3"
                                              placeholder="0 al 100..."></textarea>
                                </div>
                            </div>

                            <!-- Signos Vitales -->
                            <div class="form-grid grid-3" style="margin-top: 0.6rem;">
                                <div class="form-group">
                                    <label for="ta">Tension (TA)</label>
                                    <input type="text" id="ta" name="ta" placeholder="Ej: 120/80 mmHg">
                                </div>
                                <div class="form-group">
                                    <label for="fc">Pulso </label>
                                    <input type="text" id="fc" name="fc" placeholder="Ej: 80 lpm">
                                </div>
                                <div class="form-group">
                                    <label for="fc">Peso </label>
                                    <input type="text" id="fp" name="fp" placeholder="Ej: 80 Kg">
                                </div>
                                <div class="form-group">
                                    <label for="fr">Temperatura </label>
                                    <input type="text" id="ft" name="ft" placeholder="Ej: 36.5 °">
                                </div>
                                <div class="form-group">
                                    <label for="fr">Frecuencia respiratoria </label>
                                    <input type="text" id="fr" name="fr" placeholder="Ej: 18 rpm">
                                </div>
                            </div>

                            <h4 class="sub-title" style="margin-top: 1.5rem;"> Descripción del Examen Fisíco</h4>
                            <div class="form-grid grid-2" style="margin-top: 0.6rem;">
                                <div class="form-group full-width">
                                    <textarea id="DescripcionEx" name="aspectoGeneral" rows="3"
                                              placeholder="Describa el aspecto general del paciente..."></textarea>
                                </div>
                            </div>
                        </section>

                        <!-- Botones -->
                        <div class="form-actions">
                            <button type="button" class="action-btn cancel-btn" onclick="continuarPaso('tab-consulta')">
                                <i class="fas fa-arrow-left"></i> Atrás
                            </button>
                            <button type="button" class="action-btn save-btn" onclick="continuarPaso('tab-tratamiento')">
                                Continuar <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>

                    </div>

                    <!-- ============================================= -->
                    <!-- TAB 4: PLAN DE TRATAMIENTO -->
                    <!-- ============================================= -->
                    <div id="tab-tratamiento" class="tab-content">

                        <section class="form-section">
                            <div class="section-header">
                                <div class="section-icon purple"><i class="fas fa-clipboard-list"></i></div>
                                <div>
                                    <h3>Plan de Tratamiento</h3>
                                    <p>Esquema y observaciones del tratamiento</p>
                                </div>
                            </div>

                            <div class="form-grid grid-2">
                                <div class="form-group">
                                    <label for="tipoTratamiento">Tipo de Tratamiento</label>
                                    <input list="trata" id="tipoTratamiento" name="tipoTratamiento" placeholder="Escribe el tratamiento...">
                                    <datalist id="trata">
                                        <option value="Quimioterapia">
                                        <option value="Radioterapia">
                                        <option value="Cirugía">
                                        <option value="Inmunoterapia">
                                        <option value="Hormonoterapia">
                                        <option value="Cuidados Paliativos">
                                    </datalist>
                                </div>
                                <div class="form-group">
                                    <label for="ciclos">Ciclos / Sesiones</label>
                                    <input type="number" id="ciclos" name="ciclos" min="0" placeholder="Ej: 6">
                                </div>
                                <div class="form-group full-width">
                                    <label for="descripcionTratamiento">Descripción del Tratamiento</label>
                                    <textarea id="descripcionTratamiento" name="descripcionTratamiento" rows="3"
                                              placeholder="Detalle del esquema, medicamentos, dosis..."></textarea>
                                </div>
                                <div class="form-group full-width">
                                    <label for="observacionesTratamiento">Observaciones</label>
                                    <textarea id="observacionesTratamiento" name="observacionesTratamiento" rows="3"
                                              placeholder="Notas adicionales del plan..."></textarea>
                                </div>
                            </div>
                        </section>

                        <!-- Botones finales -->
                        <div class="form-actions">
                            <button type="button" class="action-btn cancel-btn" onclick="continuarPaso('tab-clinica')">
                                <i class="fas fa-arrow-left"></i> Atrás
                            </button>
                            <button type="submit" class="action-btn save-btn" onclick="guardarPaciente()">
                                <i class="fas fa-save"></i> Guardar Historia
                            </button>
                        </div>

                    </div>

                </form>

            </main>
        </div>
    </div>

    <script src="../JS/Sesion.JS"></script>
    <script src="../js/hemato.js"></script>
    <script src="../JS/Bloqueodepestañas.js"></script>
</body>
</html>