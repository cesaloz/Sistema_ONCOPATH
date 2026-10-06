<?php 
    require_once 'config/inic.php';
    verificar_sesion();

try {

    $conn = conexion::getConnection();

    $stats = [

        'pacientes' => $conn->query("SELECT COUNT(*) FROM pacientes")->fetchcolumn(),
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
        'Activo' AS estado
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
                            <option value="">Todos</option>
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                            <option value="En Revisión">En Revisión</option>
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
                        <h3>Lista de Pacientes <span class="count-badge" id="contador"> <?php echo $stats['pacientes']; ?> </span></h3>
                        <span class="results-info" id="resultados">Mostrando 5 pacientes</span>
                    </div>

                    <table id="tablaPacientes">
                        <thead>
                            <tr>
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
                            <tr>
                    <td>#<?= htmlspecialchars($p['id_paciente']) ?></td>
                    <td><?= htmlspecialchars($p['nombre_completo']) ?></td>
                    <td><?= htmlspecialchars($p['edad']) ?></td>
                    <td><?= htmlspecialchars($p['no_historia']) ?></td>
                    <td><?= date('d/m/Y', strtotime($p['fecha_ingreso_sistema'])) ?></td>
                    <td><span class="badge active"><?= htmlspecialchars($p['estado']) ?></span></td>
                    <td>
                        <button class="action-btn view" onclick="abrirModalPaciente(<?= $p['id_paciente'] ?>)">
                        <i class="fas fa-eye"></i>
                        </button>
                        <button class="action-btn edit" onclick="editarPaciente(<?= $p['id_paciente'] ?>)">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="action-btn delete" onclick="eliminarPaciente(this, <?= $p['id_paciente'] ?>)">
                            <i class="fas fa-trash"></i>
                        </button>
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

            </main>
        </div>
    </div>
                <script src="js/pacientes.js"></script>
                <script src="JS/Sesion.JS"></script>
    </body>
</html>