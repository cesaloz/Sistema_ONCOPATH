<?php

require_once 'config/inic.php';
verificar_sesion();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ONCOPATH - Diagnóstico</title>
    <link rel="stylesheet" href="css/Base.css">
    <link rel="stylesheet" href="css/Proximamente.css">
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
                        <li><a href="Dashboard.php"><i class="fas fa-chart-pie"></i> Inicio</a></li>
                        <li><a href="pacientes.php"><i class="fas fa-users"></i> Pacientes</a></li>

                        <li class="has-submenu">
                            <a href="HCHemato.php" class="menu-toggle" onclick="toggleSubmenu(event)">
                                <i class="fas fa-edit""></i> Llenado de Historial
                                <i class="fas fa-chevron-down arrow"></i>
                            </a>
                            <ul class="submenu">
                                <li><a href="historias/Registro_Paciente.php"><i class="fas fa-user-plus"></i> REGISTRO DE PACIENTES</a></li>
                                <li><a href="historias/HCHemato.php"><i class="fas fa-microscope"></i> HISTORIA CLÍNICA HEMATO</a></li>
                                <li><a href="historias/HCCirugia.php"><i class="fas fa-notes-medical"></i> HISTORIA CLÍNICA CIRUGÍA</a></li>
                                <li><a href="historias/HCMamografia.php"><i class="fas fa-clipboard-list"></i> HISTORIA CLÍNICA MAMOGRAFÍA</a></li>
                            </ul>
                        </li>

                        <li><a href="diagnostico.php" class="active"><i class="fas fa-stethoscope"></i> Diagnóstico</a></li>
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
                        <h2>Diagnóstico</h2>
                        <p class="subtitle">Módulo en desarrollo</p>
                    </div>
                </div>

                <!-- ===== TARJETA PRÓXIMAMENTE ===== -->
                <section class="coming-soon-card">
                    <h3>Módulo de Diagnóstico</h3>
                    <p>
                        Estamos trabajando en esta sección. Aquí podrás registrar,
                        consultar y dar seguimiento a los diagnósticos oncológicos
                        de los pacientes del sistema.
                    </p>
                    <span class="coming-soon-badge">
                        <i class="fas fa-hourglass-half"></i> Próximamente
                    </span>
                </section>

            </main>
        </div>
    </div>
            <script src="JS/Sesion.JS"></script>
</body>
</html>