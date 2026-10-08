<?php 
    require_once 'config/inic.php';
    verificar_sesion();

try {

    $conn = conexion::getConnection();

    $stats = [

        'pacientes_activos' => $conn->query("SELECT COUNT(*) FROM pacientes WHERE status = TRUE")->fetchcolumn(),
        'pacientes_inactivos' => $conn->query("SELECT COUNT(*) FROM pacientes WHERE status = FALSE")->fetchcolumn(),

        ];

    $stmt = $conn->query("
    SELECT
        id_paciente,
        no_historia,
        cedula,
        TRIM(
                COALESCE(primer_nombre, '') || ' ' || 
                COALESCE(segundo_nombre, '') || ' ' || 
                COALESCE(primer_apellido, '') || ' ' || 
                COALESCE(segundo_apellido, '')
            ) AS nombre_completo,
        edad,
        sexo,
        fecha_ingreso_sistema,
        status
    FROM pacientes
    ORDER BY id_paciente DESC
    LIMIT 100
    ");
    $pacientes = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error al cargar pacientes: " . $e->getMessage());
}
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ONCOPATH - Pacientes</title>
    <link rel="stylesheet" href="css/Base.css">
    <link rel="stylesheet" href="css/Pacientes.css">
    <link rel="stylesheet" href="css/VentanaDatos.css">
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

            <!-- ===== MENÚ LATERAL ===== -->
            <aside class="sidebar">
                <nav>
                    <ul>
                        <li><a href="Dashboard.php"><i class="fas fa-chart-pie"></i> Inicio</a></li>
                        <li><a href="pacientes.php" class="active"><i class="fas fa-users"></i> Pacientes</a></li>

                        <li class="has-submenu">
                            <a href="HCHemato.php" class="menu-toggle" onclick="toggleSubmenu(event)">
                                <i class="fas fa-edit"></i> Llenado de Historial
                                <i class="fas fa-chevron-down arrow"></i>
                            </a>
                            <ul class="submenu">
                                <li><a href="historias/Registro_Paciente.php"><i class="fas fa-user-plus"></i> REGISTRO DE PACIENTES</a></li>
                                <li><a href="historias/HCHemato.php"><i class="fas fa-microscope"></i> HISTORIA CLÍNICA HEMATO</a></li>
                                <li><a href="historias/HCCirugia.php"><i class="fas fa-notes-medical"></i> HISTORIA CLÍNICA CIRUGÍA</a></li>
                                <li><a href="historias/HCMamografia.php"><i class="fas fa-clipboard-list"></i> HISTORIA CLÍNICA MAMOGRAFÍA</a></li>
                            </ul>
                        </li>

                        <li><a href="diagnostico.php"><i class="fas fa-stethoscope"></i> Diagnóstico</a></li>
                        <li><a href="consulta.php"><i class="fas fa-comments"></i> Consulta</a></li>
                        <li><a href="cirugia.php"><i class="fas fa-syringe"></i> Cirugía</a></li>
                        <li><a href="Historiales.php"><i class="fas fa-file-medical"></i> Historiales</a></li>
                    </ul>
                </nav>
            </aside>

            <!-- ===== CONTENIDO ===== -->
            <main class="content">

                <div class="content-header">
                    <div>
                        <h2>Lista de Pacientes</h2>
                        <p class="subtitle">Gestiona todos los pacientes del sistema</p>
                    </div>
                        <button class="btn-primary" onclick="agregarPaciente()">
                         <i class="fas fa-plus"></i> Agregar Paciente
                        </button>
                </div>

                <section class="filters-bar">
                    <div class="filter-group">
                        <label><i class="fas fa-search"></i> Buscar</label>
                        <input type="text" id="buscador" placeholder="Nombre o cédula..." oninput="filtrarTabla()">
                    </div>

                    <div class="filter-group">
                        <label><i class="fas fa-filter"></i> Estado</label>
                        <select id="filtroEstado" onchange="filtrarTabla()">
                            <option value="Activo" selected>Activo</option>
                            <option value="Inactivo">Inactivo</option>
                            <option value="">Todos</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label><i class="fas fa-calendar"></i> Ingreso</label>
                        <input type="date" id="filtroFecha" onchange="filtrarTabla()">
                    </div>

                    <button class="clear-filters" onclick="limpiarFiltros()">
                        <i class="fas fa-times"></i> Limpiar
                    </button>
                </section>

                <section class="table-container">
                    <div class="table-header">
                        <h3>Lista de Pacientes <span class="count-badge" id="contador"> <?php echo $stats['pacientes_activos']; ?> </span></h3>
                        <span class="results-info" id="resultados">Mostrando 5 pacientes</span>
                    </div>

                    <table id="tablaPacientes">
                        <thead>
                            <tr >
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Edad</th>
                                <th>N° de Historia</th>
                                <th>Fecha Ingreso</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>

                            <tbody>
                    <?php if (empty($pacientes)): ?>
                        <tr>
                            <td colspan="7" style="text-align:center; padding:2rem; color:#94a3b8;">
                                No hay pacientes registrados.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pacientes as $p): ?>

                            <tr data-status="<?= $p['status'] ? 'activo' : 'inactivo' ?>"
                                class="fila-paciente <?= $p['status'] ? '' : 'fila-inactiva' ?>">

                    <td>#<?= htmlspecialchars($p['id_paciente']) ?></td>
                    <td><?= htmlspecialchars($p['nombre_completo']) ?></td>
                    <td><?= htmlspecialchars($p['edad']) ?></td>
                    <td><?= htmlspecialchars($p['no_historia']) ?></td>
                    <td><?= date('d/m/Y', strtotime($p['fecha_ingreso_sistema'])) ?></td>
                    <td>
                        <span class="badge <?= $p['status'] ? 'active' : 'inactive' ?>">
                            <?= $p['status'] ? 'Activo' : 'Inactivo' ?>
                        </span>
                    </td>
                    <td>
                        <button class="action-btn view" onclick="abrirModalPaciente(<?= $p['id_paciente'] ?>)">
                        <i class="fas fa-eye"></i>
                        </button>
                        <button class="action-btn edit" onclick="mostrarEditarPaciente(<?= $p['id_paciente'] ?>)">
                            <i class="fas fa-edit"></i>
                        </button>


                        <?php if ($p['status']): ?>
                            <button class="action-btn delete"
                                    onclick="eliminarPaciente(this, <?= $p['id_paciente'] ?>)"
                                    title="Eliminar paciente">
                                <i class="fas fa-trash"></i>
                            </button>
                        <?php else: ?>
                            <button class="action-btn reactivate"
                                    onclick="reactivarPaciente(this, <?= $p['id_paciente'] ?>)"
                                    title="Reactivar paciente">
                                <i class="fas fa-undo"></i>
                            </button>
                        <?php endif; ?>


                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>

                    </table>

                    <div class="empty-state" id="emptyState" style="display:none;">
                        <i class="fas fa-user-slash"></i>
                        <p>No se encontraron pacientes con esos filtros.</p>
                    </div>
                </section>

                <div class="pagination">
                    <button class="page-btn" disabled><i class="fas fa-chevron-left"></i></button>
                    <button class="page-btn active">1</button>
                    <button class="page-btn">2</button>
                    <button class="page-btn">3</button>
                    <button class="page-btn"><i class="fas fa-chevron-right"></i></button>
                </div>






                <!-- ===== MODAL: DATOS DEL PACIENTE ===== -->
<div class="modal-overlay" id="modalVerPaciente">
    <div class="modal-card">

        <!-- Encabezado -->
        <div class="modal-header">
            <div class="modal-header-left">
                <div class="modal-icon">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div>
                    <h2 id="modalNombre">Cargando...</h2>
                    <p id="modalCedula">—</p>
                </div>
            </div>
            <button class="modal-close" onclick="cerrarModalPaciente()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Cuerpo con secciones -->
        <div class="modal-body">

            <!-- Datos personales -->
            <div class="modal-section">
                <h3><i class="fas fa-id-card"></i> Datos Personales</h3>
                <div class="modal-grid">
                    <div class="modal-field">
                        <label>N° Historia</label>
                        <span id="modalHistoria">—</span>
                    </div>
                    <div class="modal-field">
                        <label>Cédula</label>
                        <span id="modalCedulaField">—</span>
                    </div>
                    <div class="modal-field">
                        <label>Edad</label>
                        <span id="modalEdad">—</span>
                    </div>
                    <div class="modal-field">
                        <label>Sexo</label>
                        <span id="modalSexo">—</span>
                    </div>
                    <div class="modal-field">
                        <label>Fecha de Nacimiento</label>
                        <span id="modalFechaNac">—</span>
                    </div>
                    <div class="modal-field">
                        <label>Raza / Grupo Étnico</label>
                        <span id="modalRaza">—</span>
                    </div>

                </div>
            </div>

            <!-- Ubicación -->
            <div class="modal-section">
                <h3><i class="fas fa-map-marked-alt"></i> Ubicación</h3>
                <div class="modal-grid">
                    <div class="modal-field">
                        <label>Estado (Nacimiento)</label>
                        <span id="modalEstadoNac">—</span>
                    </div>
                    <div class="modal-field">
                        <label>Municipio (Nacimiento)</label>
                        <span id="modalMunicipioNac">—</span>
                    </div>
                    <div class="modal-field">
                        <label>Estado (Procedencia)</label>
                        <span id="modalEstadoProc">—</span>
                    </div>
                    <div class="modal-field">
                        <label>Municipio (Procedencia)</label>
                        <span id="modalMunicipioProc">—</span>
                    </div>
                </div>
            </div>

            <!-- Contacto -->
            <div class="modal-section">
                <h3><i class="fas fa-address-book"></i> Contacto</h3>
                <div class="modal-grid">
                    <div class="modal-field full">
                        <label>Dirección</label>
                        <span id="modalDireccion">—</span>
                    </div>
                    <div class="modal-field">
                        <label>Teléfono</label>
                        <span id="modalTelefono">—</span>
                    </div>
                    <div class="modal-field">
                        <label>Email</label>
                        <span id="modalEmail">—</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Pie -->
        <div class="modal-footer">
            <button class="action-btn cancel-btn" onclick="cerrarModalPaciente()">
                <i class="fas fa-times"></i> Cerrar
            </button>
            <a id="btnDescargarPDF" href="#" target="_blank" class="action-btn save-btn">
                <i class="fas fa-file-pdf"></i> Descargar PDF
            </a>
        </div>

    </div>
</div>




    
    <!-- ===== MODAL: EDITAR PACIENTE ===== -->
<div class="modal-overlay" id="modalEditarPaciente">
    <div class="modal-card">

        <div class="modal-header">
            <div class="modal-header-left">
                <div class="modal-icon">
                    <i class="fas fa-user-edit"></i>
                </div>
                <div>
                    <h2>Editar Paciente</h2>
                    <p id="editarCedula">Cédula: —</p>
                </div>
            </div>
            <button class="modal-close" onclick="cerrarModalEditarPaciente()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="formEditarPaciente" onsubmit="event.preventDefault(); guardarEdicionPaciente();">

            <input type="hidden" id="editarIdPaciente" name="id_paciente">

            <div class="modal-body">

                <!-- ===== DATOS PERSONALES ===== -->
                <div class="modal-section">
                    <h3><i class="fas fa-id-card"></i> Datos Personales</h3>
                    <div class="modal-grid">

                        <div class="form-group">
                            <label for="edit_nombres">Primer Nombre *</label>
                            <input type="text" id="edit_nombres" name="primer_nombre" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_segundo_nombre">Segundo Nombre</label>
                            <input type="text" id="edit_segundo_nombre" name="segundo_nombre">
                        </div>

                        <div class="form-group">
                            <label for="edit_apellidos">Primer Apellido *</label>
                            <input type="text" id="edit_apellidos" name="primer_apellido" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_segundo_apellido">Segundo Apellido</label>
                            <input type="text" id="edit_segundo_apellido" name="segundo_apellido">
                        </div>

                        <div class="form-group">
                            <label for="edit_cedula">Cédula *</label>
                            <input type="text" id="edit_cedula" name="cedula" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_no_historia">N° Historia *</label>
                            <input type="text" id="edit_no_historia" name="no_historia" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_fecha_nacimiento">Fecha de Nacimiento *</label>
                            <input type="date" id="edit_fecha_nacimiento" name="fecha_nacimiento" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_edad">Edad *</label>
                            <input type="number" id="edit_edad" name="edad" min="0" max="120" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_sexo">Sexo *</label>
                            <select id="edit_sexo" name="sexo" required>
                                <option value="">Seleccionar...</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="edit_raza">Raza</label>
                            <input type="text" id="edit_raza" name="raza_grupo_etnico">
                        </div>

                    </div>
                </div>

                <!-- ===== CONTACTO ===== -->
                <div class="modal-section">
                    <h3><i class="fas fa-address-book"></i> Contacto</h3>
                    <div class="modal-grid">

                        <div class="form-group full">
                            <label for="edit_direccion">Dirección de Habitación</label>
                            <input type="text" id="edit_direccion" name="direccion_habitacion">
                        </div>

                        <div class="form-group">
                            <label for="edit_telefono">Teléfono</label>
                            <input type="tel" id="edit_telefono" name="telefono_contac">
                        </div>

                        <div class="form-group">
                            <label for="edit_email">Correo Electrónico</label>
                            <input type="email" id="edit_email" name="email">
                        </div>

                    </div>
                </div>

            </div>

            <!-- Pie -->
            <div class="modal-footer">
                <button type="button" class="action-btn cancel-btn" onclick="cerrarModalEditarPaciente()">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="submit" class="action-btn save-btn">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
            </div>

        </form>

    </div>
</div>

    





            </main>
        </div>
    </div>
                <script src="js/pacientes.js"></script>
                <script src="JS/Sesion.JS"></script>
    </body>
</html>