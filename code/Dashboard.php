<?php
 
require_once 'config/inic.php';
verificar_sesion();

try {
    $con = conexion::getConnection();

    $stats = [
        'pacientes' => $con->query("SELECT COUNT(*) FROM pacientes")->fetchColumn(),
        'ciudades'  => $con->query("SELECT COUNT(*) FROM ciudades")->fetchColumn(),
        'estados'   => $con->query("SELECT COUNT(*) FROM estados")->fetchColumn(),
        'no_historia'=> $con->query("SELECT COUNT(*) FROM pacientes")->fetchColumn(),
    ];

    $stmt = $con->query("
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
        LIMIT 5
    ");
    $ultimosPacientes = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

registrar_actividad('Acceso al dashboard', 'sistema');

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ONCOPATH - Inicio</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="css/Base.css">
    <link rel="stylesheet" href="css/Pacientes.css">
    <link rel="stylesheet" href="css/Dash.css">
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
                        <li><a href="Dashboard.php" class="active" ><i class="fas fa-chart-pie"></i> Inicio</a></li>
                        <li><a href="pacientes.php"><i class="fas fa-users"></i> Pacientes</a></li>

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

            <main class="content">


                <div class="content-header">
                    <h2>Estadísticas Regionales</h2>
                    <p class="subtitle">Distribución de pacientes por ubicación geográfica</p>
                </div>

                <section class="stats-regionales">
                    <div class="chart-card">
                        <div class="chart-header">
                            <div class="chart-icon blue"><i class="fas fa-map-marked-alt"></i></div>
                            <div>
                                <h3>Pacientes por Estado</h3>
                                <p>Distribución por estado de procedencia</p>
                            </div>
                        </div>
                        <div class="chart-body">
                            <canvas id="graficoEstados"></canvas>
                        </div>
                    </div>

                    <div class="chart-card">
                        <div class="chart-header">
                            <div class="chart-icon purple"><i class="fas fa-map-pin"></i></div>
                            <div>
                                <h3>Pacientes por Municipio</h3>
                                <p>Del estado con más registros</p>
                            </div>
                        </div>
                        <div class="chart-body">
                            <canvas id="graficoMunicipios"></canvas>
                        </div>
                    </div>
                </section>

                <div class="content-header">
                    <h2>Resumen de Pacientes</h2>
                </div>

                <section class="stats-cards">
                    <div class="card">
                        <div class="card-icon blue"><i class="fas fa-users"></i></div>
                        <div class="card-info">
                            <h3><?php echo $stats['pacientes']; ?></h3>
                            <p>Pacientes Totales</p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-icon green"><i class="fas fa-calendar-day"></i></div>
                        <div class="card-info">
                            <h3>18</h3>
                            <p>Citas Hoy</p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-icon orange"><i class="fas fa-notes-medical"></i></div>
                        <div class="card-info">
                            <h3><?php echo $stats ['no_historia']; ?></h3>
                            <p>Nuevos Registros</p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-icon red"><i class="fas fa-exclamation-triangle"></i></div>
                        <div class="card-info">
                            <h3><?php echo $stats ['ciudades']; ?></h3>
                            <p>Casos Pendientes</p>
                        </div>
                    </div>
                </section>

                <section class="table-container">
                <div class="table-header">
                    <h3>Últimos Pacientes Registrados</h3>
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                        <input type="text" 
                            id="buscador" 
                            placeholder="Buscar paciente..." 
                            class="search-input"
                            oninput="filtrarTabla()">
                        <input type="date" 
                            id="filtroFecha" 
                            class="search-input"
                            onchange="filtrarTabla()"
                            title="Filtrar por fecha de ingreso">
                        <button type="button" 
                                class="clear-filters" 
                                onclick="limpiarFiltros()">
                            <i class="fas fa-times"></i> Limpiar
                        </button>
                    </div>
                </div>

                    <table  id="tablaPacientes">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Edad</th>
                                <th>Diagnóstico</th>
                                <th>Fecha Ingreso</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($ultimosPacientes)): ?>
                                <tr>
                                    <td colspan="7" style="text-align:center; padding: 2rem; color: #94a3b8;">
                                        <i class="fas fa-user-slash" style="font-size: 1.5rem; display: block; margin-bottom: 0.5rem;"></i>
                                        No hay pacientes registrados aún.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($ultimosPacientes as $p): ?>
                                    <tr>
                                        <td>#<?= htmlspecialchars($p['id_paciente']) ?></td>
                                        <td><?= htmlspecialchars($p['nombre_completo']) ?></td>
                                        <td><?= htmlspecialchars($p['edad']) ?></td>
                                        <td><?= htmlspecialchars($p['diagnostico'] ?? '—') ?></td>
                                        <td><?= date('d/m/Y', strtotime($p['fecha_ingreso_sistema'])) ?></td>
                                        <td><span class="badge active"><?= htmlspecialchars($p['estado']) ?></span></td>
                                        
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </section>

            </main>
        </div>
    </div>

    <script src="js/DashboardFiltro.JS"></script>
    <script src="JS/Sesion.JS"></script>
    <script src="JS/estadisticas.js"></script>
    <script>
        function verPaciente(id) {
            window.location.href = "pacientes.php?ver=" + id;
        }
        function editarPaciente(id) {
            window.location.href = "pacientes.php?editar=" + id;
        }
    </script>
</body>
</html>