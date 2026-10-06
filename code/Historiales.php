<?php
require_once __DIR__ . '../config/inic.php';
verificar_sesion();

try {
    $con = conexion::getConnection();

    /* ===== TRAER PACIENTES ===== */
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
    <title>ONCOPATH - Historiales</title>
    <link rel="stylesheet" href="css/Base.css">
    <link rel="stylesheet" href="css/Historiales.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

    <div class="dashboard-container">

        <!-- ===== BARRA SUPERIOR ===== -->
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
                        <li><a href="<?= BASE_URL ?>/Dashboard.php"><i class="fas fa-chart-pie"></i> Inicio</a></li>
                        <li><a href="<?= BASE_URL ?>/pacientes.php"><i class="fas fa-users"></i> Pacientes</a></li>

                        <li class="has-submenu">
                            <a href="<?= BASE_URL ?>/historias/HCHemato.php" class="menu-toggle" onclick="toggleSubmenu(event)">
                                <i class="fas fa-edit"></i> Llenado de Historial
                                <i class="fas fa-chevron-down arrow"></i>
                            </a>
                            <ul class="submenu">
                                <li><a href="<?= BASE_URL ?>/historias/Registro_Paciente.php"><i class="fas fa-user-plus"></i> REGISTRO DE PACIENTES</a></li>
                                <li><a href="<?= BASE_URL ?>/historias/HCHemato.php"><i class="fas fa-microscope"></i> HISTORIA CLÍNICA HEMATO</a></li>
                                <li><a href="<?= BASE_URL ?>/historias/HCCirugia.php"><i class="fas fa-notes-medical"></i> HISTORIA CLÍNICA CIRUGÍA</a></li>
                                <li><a href="<?= BASE_URL ?>/historias/HCMamografia.php"><i class="fas fa-clipboard-list"></i> HISTORIA CLÍNICA MAMOGRAFÍA</a></li>
                            </ul>
                        </li>

                        <li><a href="<?= BASE_URL ?>/diagnostico.php"><i class="fas fa-stethoscope"></i> Diagnóstico</a></li>
                        <li><a href="<?= BASE_URL ?>/consulta.php"><i class="fas fa-comments"></i> Consulta</a></li>
                        <li><a href="<?= BASE_URL ?>/cirugia.php"><i class="fas fa-syringe"></i> Cirugía</a></li>
                        <li><a href="<?= BASE_URL ?>/Historiales.php" class="active"><i class="fas fa-file-medical"></i> Historiales</a></li>
                    </ul>
                </nav>
            </aside>

            <!-- ===== CONTENIDO ===== -->
            <main class="content">

                <div class="content-header">
                    <div>
                        <h2>Historiales Clínicos</h2>
                        <p class="subtitle">Consulte los historiales de los pacientes</p>
                    </div>
                </div>

                <form id="formPaciente" onsubmit="event.preventDefault();">

                    <!-- ============================================= -->
                    <!-- SUBMÓDULO 1: SELECCIONAR PACIENTE -->
                    <!-- ============================================= -->
                    <div id="tab-pacientes" class="tab-content active">

                        <section class="table-container">

                            <div class="table-header">
                                <h3>
                                    Lista de Pacientes
                                    <span class="count-badge"><?php echo count($pacientes ?? []); ?></span>
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

                            <div class="empty-state" id="emptyState" style="display:none;">
                                <i class="fas fa-user-slash"></i>
                                <p>No se encontraron pacientes con esos filtros.</p>
                            </div>

                            <div id="pacienteSeleccionado" class="paciente-seleccionado" style="display:none;">
                                <i class="fas fa-user-check"></i>
                                <div>
                                    <strong>Paciente seleccionado:</strong>
                                    <span id="nombreSeleccionado"></span>
                                </div>
                            </div>

                        </section>

                        <div class="form-actions">
                            <button type="button" class="action-btn save-btn" onclick="continuarPaso('tab-historial')">
                                Continuar <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>

                    </div>

                    <!-- ============================================= -->
                    <!-- SUBMÓDULO 2: VER HISTORIAL DEL PACIENTE -->
                    <!-- ============================================= -->
                    <div id="tab-historial" class="tab-content">

                        <!-- Info del paciente seleccionado -->
                        <section class="paciente-info-card" id="infoPacienteCard">
                            <div class="paciente-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="paciente-detalles">
                                <h3 id="infoNombrePaciente">—</h3>
                                <p>
                                    <span><i class="fas fa-id-card"></i> <strong id="infoCedula">—</strong></span>
                                </p>
                            </div>
                        </section>

                        <!-- Filtros -->
                        <section class="filters-bar">
                            <div class="filter-group">
                                <label><i class="fas fa-search"></i> Buscar</label>
                                <input type="text" id="buscadorHistorial" placeholder="Buscar por tipo o diagnóstico..." oninput="filtrarHistorial()">
                            </div>

                            <div class="filter-group">
                                <label><i class="fas fa-file-medical"></i> Tipo</label>
                                <select id="filtroTipo" onchange="filtrarHistorial()">
                                    <option value="">Todos</option>
                                    <option value="Hemato">Hemato</option>
                                    <option value="Cirugía">Cirugía</option>
                                    <option value="Mamografía">Mamografía</option>
                                </select>
                            </div>

                            <div class="filter-group">
                                <label><i class="fas fa-calendar"></i> Desde</label>
                                <input type="date" id="filtroFecha" onchange="filtrarHistorial()">
                            </div>

                            <button class="clear-filters" onclick="limpiarFiltrosHistorial()">
                                <i class="fas fa-times"></i> Limpiar
                            </button>
                        </section>

                        <!-- Lista de historiales -->
                        <section class="historiales-lista" id="listaHistoriales">
                            <div class="historiales-vacio">
                                <i class="fas fa-spinner fa-spin"></i>
                                <p>Cargando historiales...</p>
                            </div>
                        </section>

                        <!-- Botones -->
                        <div class="form-actions">
                            <button type="button" class="action-btn cancel-btn" onclick="continuarPaso('tab-pacientes')">
                                <i class="fas fa-arrow-left"></i> Atrás
                            </button>
                        </div>

                    </div>

                </form>

            </main>
        </div>
    </div>

    <!-- JS -->
    <script>
        /* ===== BASE_URL para JS ===== */
        const BASE_URL = "<?= BASE_URL ?>";
    </script>
    <script src="js/historiales.js"></script>
    <script src="JS/Sesion.JS"></script>
</body>
</html>