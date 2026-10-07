<?php

require_once '../config/inic.php';
verificar_sesion();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ONCOPATH - Registro de Paciente</title>
    <link rel="stylesheet" href="../css/Base.css">
    <link rel="stylesheet" href="css/Regisp.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

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

            <aside class="sidebar">
                <nav>
                    <ul>
                        <li><a href="../Dashboard.php"><i class="fas fa-chart-pie"></i> Inicio</a></li>
                        <li><a href="../pacientes.php"><i class="fas fa-users"></i> Pacientes</a></li>

                        <li class="has-submenu open">
                            <a href="../HCHemato.php" class="active menu-toggle" onclick="toggleSubmenu(event)">
                                <i class="fas fa-edit"></i> Llenado de Historial
                                <i class="fas fa-chevron-down arrow"></i>
                            </a>
                            <ul class="submenu">
                                <li><a href="Registro_Paciente.php" class="active1"><i class="fas fa-user-plus"></i> REGISTRO DE PACIENTES</a></li>
                                <li><a href="HCHemato.php"><i class="fas fa-microscope"></i> HISTORIA CLÍNICA HEMATO</a></li>
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
                        <p class="subtitle">Complete todos los datos para registrar al paciente en el sistema</p>
                    </div>
                    <button type="button" class="btn-secondary" onclick="window.location.href='../pacientes.php'">
                        <i class="fas fa-arrow-left"></i> Volver
                    </button>
                </div>

                <div class="tabs-nav">
                    <button type="button" class="tab-btn active" onclick="mostrarTab(event, 'datos-personales')">
                        <i class="fas fa-id-card"></i> Datos Personales
                    </button>
                </div>

                <form id="formPaciente">

<!--TAB 1-->
                    <div id="datos-personales" class="tab-content active">

                        <section class="form-section">
                            <div class="section-header">
                                <div class="section-icon blue"><i class="fas fa-hospital"></i></div>
                                <div>
                                    <h3>Institución de Adscripción</h3>
                                    <p>Datos de la institución y referencia</p>
                                </div>
                            </div>

                            <div class="form-grid grid-3">
                                <div class="form-group prioridad">
                                    <label for="institucion">Institución de Adscripción *</label>
                                    <select id="institucion" name="institucion" required>
                                        <option value="">-- Seleccione --</option>
                                        <option value="IVSS">IVSS</option>
                                        <option value="MSDS">MSDS</option>
                                        <option value="MINISTERIO DE DEFENSA">Ministerio de Defensa</option>
                                        <option value="PRIVADO">Privado</option>
                                        <option value="OTRA">Otra</option>
                                    </select>
                                </div>
                                <div class="form-group prioridad">
                                    <label for="establecimiento">Nombre del Establecimiento *</label>
                                    <input type="text" id="establecimiento" name="establecimiento" placeholder="Ej: Hospital Central" required
                                    oninput="soloLetras(this)" data-solo-letras>
                                </div>
                                <div class="form-group prioridad">
                                    <label for="fechaReferencia">Fecha de la Referencia *</label>
                                    <input type="date" id="fechaReferencia" name="fechaReferencia" required>
                                </div>
                            </div>
                        </section>

                        <section class="form-section">
                            <div class="section-header">
                                <div class="section-icon green"><i class="fas fa-id-card"></i></div>
                                <div>
                                    <h3>Datos del Paciente</h3>
                                    <p>Información personal y de identificación</p>
                                </div>
                            </div>

                            <div class="form-grid grid-3">
                                <div class="form-group">
                                    <label for="fecha">Fecha del Registro *</label>
                                    <input type="date" id="fecha" name="fecha" value="<?php echo date('Y-m-d');?>" readonly required>
                                </div>
                                <div class="form-group prioridad">
                                    <label for="numeroHistoria">Número de Historia *</label>
                                    <input type="number" id="numeroHistoria" name="numeroHistoria" placeholder="Ej: 1001" min="1" required>
                                </div>
                                <div class="form-group prioridad">
                                    <label for="nombres">Nombres *</label>
                                    <input type="text" id="nombres" name="nombres" placeholder="Ej: Juan Carlos" required
                                     oninput="soloLetras(this)" data-solo-letras>
                                </div>
                                <div class="form-group prioridad">
                                    <label for="apellidos">Apellidos *</label>
                                    <input type="text" id="apellidos" name="apellidos" placeholder="Ej: Pérez Rodríguez" required
                                    oninput="soloLetras(this)" data-solo-letras>
                                </div>
                                <div class="form-group prioridad">
                                    <label>Tipo de Documento *</label>
                                    <div class="checkbox-group">
                                        <label class="check-item"><input type="radio" name="tipoDocumento" value="V" required><span>V</span></label>
                                        <label class="check-item"><input type="radio" name="tipoDocumento" value="E"><span>E</span></label>
                                        <label class="check-item"><input type="radio" name="tipoDocumento" value="J"><span>J</span></label>
                                    </div>
                                </div>
                                <div class="form-group prioridad">
                                    <label for="cedula">Documento de Identificación (Cédula) *</label>
                                    <input type="text" id="cedula" name="cedula" placeholder="Ej: 12345678"
                                           inputmode="numeric" pattern="[0-9]{7,8}" maxlength="8" minlength="7" title="Solo se permiten números" required
                                           oninput="soloNumeros(this)" data-solo-numeros>
                                </div>
                                <div class="form-group prioridad">
                                    <label for="fechaNacimiento">Fecha de Nacimiento *</label>
                                    <input type="date" id="fechaNacimiento" name="fechaNacimiento" required onchange="calcularEdad()">
                                </div>
                                <div class="form-group">
                                    <label for="edad">Edad *</label>
                                    <input type="number" id="edad" name="edad" placeholder="Se calcula automáticamente" min="0" max="120" step="1" required>
                                </div>
                                <div class="form-group prioridad">
                                    <label>Sexo *</label>
                                    <div class="radio-group">
                                        <label class="radio-item"><input type="radio" name="sexo" value="Masculino" required><span>Masculino</span></label>
                                        <label class="radio-item"><input type="radio" name="sexo" value="Femenino"><span>Femenino</span></label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="raza">Raza *</label>
                                    <input list="razas" id="raza" name="raza" placeholder="Escribe la raza..." required
                                    oninput="soloLetras(this)" data-solo-letras>
                                    <datalist id="razas">
                                        <option value="Caucásica">
                                        <option value="Negroide">
                                        <option value="Mestiza">
                                        <option value="Asiática">
                                        <option value="Etnia">
                                    </datalist>
                                </div>
                            </div>
                        </section>

                        <section class="form-section">
                            <div class="section-header">
                                <div class="section-icon orange"><i class="fas fa-map-marked-alt"></i></div>
                                <div>
                                    <h3>Lugar de Nacimiento y Procedencia</h3>
                                    <p>Ubicación geográfica del paciente</p>
                                </div>
                            </div>

                            <div class="form-grid grid-2">
                                <div class="sub-column">
                                    <h4 class="sub-title"><i class="fas fa-baby"></i> Lugar de Nacimiento</h4>
                                    <div class="form-group">
                                        <label for="nacEstado">Estado</label>
                                        <select id="nacEstado" name="nacEstado" onchange="cargarMunicipios('nac')">
                                            <option value="">-- Seleccione --</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="nacMunicipio">Municipio</label>
                                        <select id="nacMunicipio" name="nacMunicipio" onchange="cargarParroquias('nac')">
                                            <option value="">Seleccionar estado primero</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="nacParroquia">Parroquia</label>
                                        <select id="nacParroquia" name="nacParroquia">
                                            <option value="">Seleccionar municipio primero</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="nacPais">País</label>
                                        <input type="text" id="nacPais" name="nacPais" value="Venezuela">
                                    </div>
                                </div>

                                <div class="sub-column">
                                    <h4 class="sub-title"><i class="fas fa-map-pin"></i> Lugar de Procedencia</h4>

                                    <div class="form-group">
                                        <label for="proEstado">Estado</label>
                                        <select id="proEstado" name="proEstado" onchange="cargarMunicipios('pro')">
                                            <option value="">Seleccionar...</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="proMunicipio">Municipio</label>
                                        <select id="proMunicipio" name="proMunicipio" onchange="cargarParroquias('pro')">
                                            <option value="">Seleccionar estado primero</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="proParroquia">Parroquia</label>
                                        <select id="proParroquia" name="proParroquia">
                                            <option value="">Seleccionar municipio primero</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="proPais">País</label>
                                        <input type="text" id="proPais" name="proPais" value="Venezuela">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="direccionHabitacion">Dirección de Habitación*</label>
                                    <input type="text" id="direccionHabitacion" name="direccionHabitacion" placeholder="Ej: Av. Principal, Casa 12">
                                </div>
                                <div class="form-group">
                                    <label for="direccionContacto">Dirección de Contacto*</label>
                                    <input type="text" id="direccionContacto" name="direccionContacto" placeholder="Ej: Av. Secundaria, Apto 3B">
                                </div>
                            </div>
                        </section>

                        <section class="form-section">
                            <div class="section-header">
                                <div class="section-icon purple"><i class="fas fa-briefcase"></i></div>
                                <div>
                                    <h3>Ocupación y Profesión</h3>
                                    <p>Datos de actividad laboral</p>
                                </div>
                            </div>

                            <div class="form-grid grid-2">
                                <div class="form-group">
                                    <label for="ocupacion">Ocupación</label>
                                    <input type="text" id="ocupacion" name="ocupacion" placeholder="Ej: Comerciante" maxlength="45"
                                    oninput="soloLetras(this)" data-solo-letras>
                                </div>
                                <div class="form-group">
                                    <label for="aniosOcupacion">Años de Ocupación</label>
                                    <input type="number" id="aniosOcupacion" name="aniosOcupacion" placeholder="Ej: 5" min="0" max="80"
                                    oninput="soloNumeros(this)" data-solo-numeros>
                                </div>
                                <div class="form-group">
                                    <label for="profesion">Profesión</label>
                                    <input type="text" id="profesion" name="profesion" placeholder="Ej: Ingeniero" maxlength="45"
                                    oninput="soloLetras(this)" data-solo-letras>
                                </div>
                                <div class="form-group">
                                    <label for="aniosEjercidos">Años Ejercidos</label>
                                    <input type="text" id="aniosEjercidos" name="aniosEjercidos" placeholder="Ej: 10" maxlength="2"
                                    oninput="soloNumeros(this)" data-solo-numeros>
                                </div>
                            </div>
                        </section>

                        <section class="form-section">
                            <div class="section-header">
                                <div class="section-icon teal"><i class="fas fa-address-book"></i></div>
                                <div>
                                    <h3>Contacto</h3>
                                    <p>Información de comunicación del paciente</p>
                                </div>
                            </div>

                            <div class="form-grid grid-2">
                                <div class="form-group">
                                    <label for="telefonoHabitacion">Teléfono de Habitación</label>
                                    <input type="tel" id="telefonoHabitacion" name="telefonoHabitacion" placeholder="Ej: 02121234567" inputmode="numeric"
                                    oninput="soloNumeros(this)" data-solo-numeros>
                                </div>
                                <div class="form-group">
                                    <label for="telefono">Teléfono*</label>
                                    <input type="tel" id="telefono" name="telefono" placeholder="Ej: 04141234567" required inputmode="numeric"
                                    oninput="soloNumeros(this)" data-solo-numeros>
                                </div>
                                <div class="form-group">
                                    <label for="telefono2">Segundo Teléfono*</label>
                                    <input type="tel" id="telefono2" name="telefono2" placeholder="Ej: 04241234567" required inputmode="numeric"
                                    oninput="soloNumeros(this)" data-solo-numeros>
                                </div>
                                <div class="form-group">
                                    <label for="email">Correo Electrónico*</label>
                                    <input type="email" id="email" name="email" placeholder="Ej: correo@ejemplo.com" required
                                    oninput="validarEmail(this)">
                                </div>
                            </div>
                        </section>

                    </div>
                    <div class="form-actions">
                        <button type="button" class="action-btn cancel-btn" onclick="limpiarFormulario()">
                            <i class="fas fa-eraser"></i> Limpiar
                        </button>
                        <button type="submit" class="action-btn save-btn">
                            <i class="fas fa-save"></i> Guardar Paciente
                        </button>
                    </div>

                </form>

            </main>
        </div>
    </div>

    <script src="../JS/Sesion.JS"></script>
    <script src="../js/Validar.js"></script>
    <script src="../js/regis.js"></script>
    <script src="../js/ubicacion.js"></script>

</body>
</html>