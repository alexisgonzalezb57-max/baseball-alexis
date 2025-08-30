<?php
include("../../config/conexion.php");
$con = conectar();

// Verificar conexión
if (!$con) {
    die("Error de conexión a la base de datos: " . mysqli_connect_error());
}

// Consulta para obtener la fecha
$query = "SELECT timeday FROM report LIMIT 1";
$result = mysqli_query($con, $query);

if (!$result) {
    die("Error en la consulta: " . mysqli_error($con));
}

$ddtt = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reportes - Sistema Baseball</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- jQuery (necesario para la funcionalidad AJAX) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Estilos personalizados -->
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #fd7e14;
            --dark-color: #212529;
            --light-color: #f8f9fa;
            --success-color: #198754;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #0dcaf0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
            color: #333;
        }
        
        .header {
            background: linear-gradient(90deg, var(--primary-color) 0%, #0a58ca 100%);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .logo {
            font-size: 2rem;
            font-weight: 700;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        
        .logo i {
            margin-right: 10px;
            color: var(--secondary-color);
            transition: transform 0.3s;
        }
        
        .logo:hover i {
            transform: rotate(360deg);
        }
        
        .navigation {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        
        .navigation li {
            margin: 0 5px;
        }
        
        .navigation a {
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 6px;
            transition: all 0.3s;
            font-weight: 500;
        }
        
        .navigation a:hover {
            color: white;
            background: rgba(255, 255, 255, 0.15);
        }
        
        .clock-container {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 10px 15px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .clock {
            display: flex;
            align-items: center;
            color: white;
            font-size: 1.2rem;
            font-weight: 600;
        }
        
        .clock h2 {
            margin: 0;
            min-width: 28px;
            text-align: center;
        }
        
        .dot {
            margin: 0 5px;
        }
        
        .main-content {
            padding: 2rem 0;
        }
        
        .content-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            padding: 2rem;
            position: relative;
            overflow: hidden;
            margin: 0 auto;
        }
        
        .content-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        }
        
        .page-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 15px;
            text-align: center;
        }
        
        .page-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--secondary-color);
            border-radius: 2px;
        }
        
        .date-form-container {
            background: linear-gradient(90deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border-left: 4px solid var(--primary-color);
        }
        
        .date-form-title {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }
        
        .date-form-title i {
            margin-right: 10px;
            color: var(--primary-color);
        }
        
        .report-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        
        .report-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s;
            border-left: 4px solid var(--info-color);
            height: 100%;
        }
        
        .report-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }
        
        .report-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #e9ecef;
        }
        
        .report-icon {
            font-size: 1.5rem;
            color: var(--primary-color);
            margin-right: 12px;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border-radius: 10px;
        }
        
        .report-title {
            font-weight: 700;
            color: var(--dark-color);
            margin: 0;
            flex: 1;
        }
        
        .report-description {
            color: #6c757d;
            margin-bottom: 1.5rem;
            line-height: 1.5;
            font-size: 0.95rem;
        }
        
        .report-action {
            text-align: center;
        }
        
        .btn-report {
            background: linear-gradient(90deg, var(--info-color) 0%, #0bacbf 100%);
            border: none;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-report:hover {
            background: linear-gradient(90deg, #0bacbf 0%, #098a9c 100%);
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }
        
        .form-control {
            border-radius: 6px;
            padding: 0.75rem 1rem;
            border: 1px solid #dce4ec;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        
        .btn-success {
            background: linear-gradient(90deg, var(--success-color) 0%, #157347 100%);
            border: none;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-success:hover {
            background: linear-gradient(90deg, #157347 0%, #0f5132 100%);
            transform: translateY(-2px);
            color: white;
        }
        
        @media (max-width: 768px) {
            .navigation {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .content-container {
                padding: 1.5rem;
            }
            
            .page-title {
                font-size: 1.5rem;
            }
            
            .report-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 576px) {
            .clock-container {
                display: none;
            }
            
            .date-form-container {
                padding: 1rem;
            }
            
            .report-card {
                padding: 1.25rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <a href="../../" class="logo">
                    <i class="fas fa-baseball-ball"></i>BASEBALL
                </a>
                
                <ul class="navigation">
                    <li><a href="../equipos/"><i class="fas fa-users"></i> Equipos</a></li>
                    <li><a href="../juego/"><i class="fas fa-calendar-alt"></i> Temporada</a></li>
                    <li><a href="../calendario/"><i class="fas fa-calendar-day"></i> Calendario</a></li>
                    <li><a href="../homenaje/homenaje.php"><i class="fas fa-trophy"></i> Homenaje</a></li>
                    <li><a href="../abonos/"><i class="fas fa-ticket-alt"></i> Abono</a></li>
                    <li><a href="../reporte/reporte.php"><i class="fas fa-chart-bar"></i> Reportes</a></li>
                </ul>
                
                <div class="clock-container">
                    <div class="clock">
                        <h2 id="hour">00</h2><h2 class="dot">:</h2>
                        <h2 id="minute">00</h2><h2 class="dot">:</h2>
                        <h2 id="seconds">00</h2><span id="ampm">AM</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div class="content-container">
                <h1 class="page-title">📊 Panel de Reportes</h1>
                
                <!-- Formulario de Fecha -->
                <div class="date-form-container">
                    <h5 class="date-form-title"><i class="fas fa-calendar-alt"></i> Configuración de Fecha para Reportes</h5>
                    <form id="fechaForm" class="row g-3 align-items-center">
                        <div class="col-md-8">
                            <label class="form-label">Fecha para todos los Reportes:</label>
                            <input type="date" name="tempo" class="form-control" value="<?php echo htmlspecialchars($ddtt['timeday'], ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-save me-2"></i>Guardar Fecha
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Grid de Reportes -->
                <div class="report-grid">
                    <!-- Reporte Calendario -->
                    <div class="report-card">
                        <div class="report-header">
                            <div class="report-icon">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <h3 class="report-title">Calendario</h3>
                        </div>
                        <p class="report-description">
                            Horario de todas las temporadas activas por fecha para generar la tabla de calendario.
                        </p>
                        <div class="report-action">
                            <a href="calendario.php" class="btn-report">
                                <i class="fas fa-arrow-right me-2"></i>Generar Reporte
                            </a>
                        </div>
                    </div>

                    <!-- Reporte Nómina -->
                    <div class="report-card">
                        <div class="report-header">
                            <div class="report-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <h3 class="report-title">Nómina</h3>
                        </div>
                        <p class="report-description">
                            Elige el listado del equipo para generar el reporte de nómina de jugadores.
                        </p>
                        <div class="report-action">
                            <a href="nomina.php" class="btn-report">
                                <i class="fas fa-arrow-right me-2"></i>Generar Reporte
                            </a>
                        </div>
                    </div>

                    <!-- Reporte Abonos -->
                    <div class="report-card">
                        <div class="report-header">
                            <div class="report-icon">
                                <i class="fas fa-ticket-alt"></i>
                            </div>
                            <h3 class="report-title">Abonos</h3>
                        </div>
                        <p class="report-description">
                            Elige la temporada para generar el reporte de abonos y estados de pago.
                        </p>
                        <div class="report-action">
                            <a href="../PDF/abono.php" target="_blank" class="btn-report">
                                <i class="fas fa-arrow-right me-2"></i>Generar Reporte
                            </a>
                        </div>
                    </div>

                    <!-- Reporte Clasificación -->
                    <div class="report-card">
                        <div class="report-header">
                            <div class="report-icon">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <h3 class="report-title">Tabla de Clasificación</h3>
                        </div>
                        <p class="report-description">
                            Muestra todas las tablas de clasificación de temporadas activas, por AVG o por diferencia de carreras.
                        </p>
                        <div class="report-action">
                            <a href="clasificacion.php" class="btn-report">
                                <i class="fas fa-arrow-right me-2"></i>Generar Reporte
                            </a>
                        </div>
                    </div>

                    <!-- Reporte Resumen -->
                    <div class="report-card">
                        <div class="report-header">
                            <div class="report-icon">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <h3 class="report-title">Resumen - Jugadores | Pichers</h3>
                        </div>
                        <p class="report-description">
                            Elige categoría y temporada activa para generar resumen de jugadores y pitchers.
                        </p>
                        <div class="report-action">
                            <a href="resumen.php" class="btn-report">
                                <i class="fas fa-arrow-right me-2"></i>Generar Reporte
                            </a>
                        </div>
                    </div>

                    <!-- Reporte Asistencia -->
                    <div class="report-card">
                        <div class="report-header">
                            <div class="report-icon">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
                            <h3 class="report-title">Asistencia</h3>
                        </div>
                        <p class="report-description">
                            Elige categoría y temporada activa para generar reportes de asistencia básica o por valor.
                        </p>
                        <div class="report-action">
                            <a href="asistencia.php" class="btn-report">
                                <i class="fas fa-arrow-right me-2"></i>Generar Reporte
                            </a>
                        </div>
                    </div>

                    <!-- Reporte Asistencia Manual -->
                    <div class="report-card">
                        <div class="report-header">
                            <div class="report-icon">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <h3 class="report-title">Asistencia Manual</h3>
                        </div>
                        <p class="report-description">
                            Elige categoría y temporada activa para generar reportes de asistencia manual.
                        </p>
                        <div class="report-action">
                            <a href="asistencia_manual.php" class="btn-report">
                                <i class="fas fa-arrow-right me-2"></i>Generar Reporte
                            </a>
                        </div>
                    </div>

                    <!-- Reporte Pichers -->
                    <div class="report-card">
                        <div class="report-header">
                            <div class="report-icon">
                                <i class="fas fa-baseball-ball"></i>
                            </div>
                            <h3 class="report-title">Pichers - Ganados | Efectividad</h3>
                        </div>
                        <p class="report-description">
                            Reportes de pitchers por ganados y efectividad, por equipo o todos los equipos.
                        </p>
                        <div class="report-action">
                            <a href="pichers.php" class="btn-report">
                                <i class="fas fa-arrow-right me-2"></i>Generar Reporte
                            </a>
                        </div>
                    </div>

                    <!-- Reporte Líderes Bateo -->
                    <div class="report-card">
                        <div class="report-header">
                            <div class="report-icon">
                                <i class="fas fa-baseball-bat-ball"></i>
                            </div>
                            <h3 class="report-title">Líderes - Bateo | Jonrones</h3>
                        </div>
                        <p class="report-description">
                            Reportes de líderes en bateo y jonrones, por equipo o todos los equipos.
                        </p>
                        <div class="report-action">
                            <a href="lidvbhr.php" class="btn-report">
                                <i class="fas fa-arrow-right me-2"></i>Generar Reporte
                            </a>
                        </div>
                    </div>

                    <!-- Reporte Líderes Dobles/Triples -->
                    <div class="report-card">
                        <div class="report-header">
                            <div class="report-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <h3 class="report-title">Líderes - Dobles | Triples</h3>
                        </div>
                        <p class="report-description">
                            Reportes de líderes en dobles y triples, por equipo o todos los equipos.
                        </p>
                        <div class="report-action">
                            <a href="lid2b3b.php" class="btn-report">
                                <i class="fas fa-arrow-right me-2"></i>Generar Reporte
                            </a>
                        </div>
                    </div>

                    <!-- Reporte Líderes Carreras -->
                    <div class="report-card">
                        <div class="report-header">
                            <div class="report-icon">
                                <i class="fas fa-running"></i>
                            </div>
                            <h3 class="report-title">Líderes en Carreras CA | CI</h3>
                        </div>
                        <p class="report-description">
                            Reportes de líderes en carreras anotadas y empujadas, por equipo o todos los equipos.
                        </p>
                        <div class="report-action">
                            <a href="lidcaci.php" class="btn-report">
                                <i class="fas fa-arrow-right me-2"></i>Generar Reporte
                            </a>
                        </div>
                    </div>

                    <!-- Reporte Líderes Ponches/Boletos -->
                    <div class="report-card">
                        <div class="report-header">
                            <div class="report-icon">
                                <i class="fas fa-baseball"></i>
                            </div>
                            <h3 class="report-title">Líderes - Ponches | Boletos</h3>
                        </div>
                        <p class="report-description">
                            Reportes de líderes en ponches y boletos, por equipo o todos los equipos.
                        </p>
                        <div class="report-action">
                            <a href="lidkobo.php" class="btn-report">
                                <i class="fas fa-arrow-right me-2"></i>Generar Reporte
                            </a>
                        </div>
                    </div>

                    <!-- Reporte Historial -->
                    <div class="report-card">
                        <div class="report-header">
                            <div class="report-icon">
                                <i class="fas fa-history"></i>
                            </div>
                            <h3 class="report-title">Historial</h3>
                        </div>
                        <p class="report-description">
                            Reporte histórico de temporadas y categorías con estadísticas completas.
                        </p>
                        <div class="report-action">
                            <a href="historial.php" class="btn-report">
                                <i class="fas fa-arrow-right me-2"></i>Generar Reporte
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Función para actualizar el reloj
        function updateClock() {
            const now = new Date();
            let hours = now.getHours();
            let minutes = now.getMinutes();
            let seconds = now.getSeconds();
            const ampm = hours >= 12 ? 'PM' : 'AM';
            
            // Convertir a formato 12 horas
            hours = hours % 12;
            hours = hours ? hours : 12; // La hora '0' debe ser '12'
            
            // Añadir ceros iniciales si es necesario
            hours = hours.toString().padStart(2, '0');
            minutes = minutes.toString().padStart(2, '0');
            seconds = seconds.toString().padStart(2, '0');
            
            // Actualizar el DOM
            document.getElementById('hour').textContent = hours;
            document.getElementById('minute').textContent = minutes;
            document.getElementById('seconds').textContent = seconds;
            document.getElementById('ampm').textContent = ampm;
        }
        
        // Actualizar el reloj inmediatamente y luego cada segundo
        updateClock();
        setInterval(updateClock, 1000);
        
        // Manejo del formulario de fecha
        document.getElementById('fechaForm').addEventListener('submit', function(event) {
            event.preventDefault();
            
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            
            // Mostrar estado de carga
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';
            submitButton.disabled = true;
            
            fetch('save.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    // Mostrar mensaje de éxito
                    submitButton.innerHTML = '<i class="fas fa-check me-2"></i>¡Guardado!';
                    submitButton.classList.add('btn-success');
                    
                    // Restaurar después de 2 segundos
                    setTimeout(() => {
                        submitButton.innerHTML = originalText;
                        submitButton.classList.remove('btn-success');
                        submitButton.disabled = false;
                    }, 2000);
                } else {
                    alert('Error al guardar la fecha: ' + (result.message || 'Error desconocido'));
                    submitButton.innerHTML = originalText;
                    submitButton.disabled = false;
                }
            })
            .catch(error => {
                alert('Ocurrió un error: ' + error.message);
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            });
        });
    </script>
</body>
</html>