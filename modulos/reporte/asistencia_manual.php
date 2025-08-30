<?php
include("../../config/conexion.php");
$con = conectar();

// Verificar conexión
if (!$con) {
    die("Error de conexión a la base de datos: " . mysqli_connect_error());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Asistencia Manual - Sistema Baseball</title>
    
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
            gap: 2px;
        }
        
        .clock h2 {
            margin: 0;
            min-width: 28px;
            text-align: center;
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
        
        .form-section {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--primary-color);
            transition: all 0.3s;
        }
        
        .form-section:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .section-title {
            color: var(--dark-color);
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #dee2e6;
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        
        .section-title i {
            margin-right: 10px;
            color: var(--primary-color);
        }
        
        .form-label {
            font-weight: 500;
            color: #34495e;
            margin-bottom: 0.5rem;
        }
        
        .required-field::after {
            content: "*";
            color: var(--danger-color);
            margin-left: 4px;
        }
        
        .form-control, .form-select {
            border-radius: 6px;
            padding: 0.75rem 1rem;
            border: 1px solid #dce4ec;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        
        .btn {
            border-radius: 6px;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: linear-gradient(90deg, var(--primary-color) 0%, #0a58ca 100%);
            border: none;
            color: white;
        }
        
        .btn-primary:hover {
            background: linear-gradient(90deg, #0a58ca 0%, #084298 100%);
            transform: translateY(-2px);
            color: white;
        }
        
        .btn-success {
            background: linear-gradient(90deg, var(--success-color) 0%, #157347 100%);
            border: none;
            color: white;
        }
        
        .btn-success:hover {
            background: linear-gradient(90deg, #157347 0%, #0f5132 100%);
            transform: translateY(-2px);
            color: white;
        }
        
        .btn-info {
            background: linear-gradient(90deg, var(--info-color) 0%, #0bacbf 100%);
            border: none;
            color: white;
        }
        
        .btn-info:hover {
            background: linear-gradient(90deg, #0bacbf 0%, #098a9c 100%);
            transform: translateY(-2px);
            color: white;
        }
        
        .btn-container {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }
        
        .input-group {
            margin-bottom: 1rem;
        }
        
        .input-group-text {
            background: linear-gradient(90deg, #f8f9fa 0%, #e9ecef 100%);
            border: 1px solid #dce4ec;
            font-weight: 500;
        }
        
        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 8px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .players-container {
            max-height: 500px;
            overflow-y: auto;
            padding: 1rem;
            background: white;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        
        .team-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid var(--info-color);
        }
        
        .team-header {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }
        
        .team-header i {
            margin-right: 10px;
            color: var(--info-color);
        }
        
        .player-item {
            display: flex;
            align-items: center;
            padding: 0.5rem;
            border-bottom: 1px solid #e9ecef;
        }
        
        .player-item:last-child {
            border-bottom: none;
        }
        
        .player-item:hover {
            background-color: #e9ecef;
        }
        
        .select-all {
            background: #e3f2fd;
            padding: 0.75rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
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
            
            .btn-container {
                flex-direction: column;
            }
            
            .header-actions {
                flex-direction: column;
                align-items: stretch;
            }
        }
        
        @media (max-width: 576px) {
            .clock-container {
                display: none;
            }
            
            .form-section {
                padding: 1rem;
            }
            
            .players-container {
                max-height: 300px;
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
                        <h2 id="hour">00</h2>:
                        <h2 id="minute">00</h2>:
                        <h2 id="seconds">00</h2>
                        <span id="ampm">AM</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div class="content-container">
                <div class="header-actions">
                    <h1 class="page-title">✅ Asistencia Manual</h1>
                    <a href="reporte.php" class="btn btn-info">
                        <i class="fas fa-arrow-left me-2"></i>Volver a Reportes
                    </a>
                </div>
                
                <p class="text-center text-muted mb-4">Seleccione los jugadores que asistieron para generar el reporte correspondiente</p>

                <form id="formAsistencia" method="POST" target="_blank" action="../PDF/generar_asistencia_manual.php">
                    <!-- Sección de Filtros -->
                    <div class="form-section">
                        <h5 class="section-title"><i class="fas fa-filter"></i> Filtros de Búsqueda</h5>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label required-field">Categoría</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                    <select class="form-select" id="categoria" name="categoria" required>
                                        <option value="">Seleccione una categoría...</option>
                                        <option value="B">Categoría B</option>
                                        <option value="C">Categoría C</option>
                                        <option value="D">Categoría D</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label required-field">Temporada</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                    <select class="form-select" id="temporada" name="temporada" required disabled>
                                        <option value="">Primero seleccione una categoría</option>
                                    </select>
                                    <div class="loading-spinner" id="tempoLoading"></div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3 d-flex align-items-end">
                                <button type="button" id="btnCargar" class="btn btn-primary w-100" disabled>
                                    <i class="fas fa-users me-2"></i>Cargar Jugadores
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de Jugadores -->
                    <div class="form-section" id="seccionJugadores" style="display: none;">
                        <h5 class="section-title"><i class="fas fa-user-check"></i> Selección de Jugadores</h5>
                        
                        <div class="select-all">
                            <input type="checkbox" id="selectAll" class="form-check-input">
                            <label for="selectAll" class="form-check-label fw-bold">Seleccionar/Deseleccionar Todos</label>
                        </div>
                        
                        <div class="players-container" id="listaEquiposJugadores">
                            <!-- Aquí se cargarán los equipos y jugadores -->
                        </div>
                    </div>

                    <div class="btn-container">
                        <button type="submit" class="btn btn-success" id="btnEnviar" disabled>
                            <i class="fas fa-file-pdf me-2"></i>Generar Reporte PDF
                        </button>
                    </div>
                </form>
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
        
        $(document).ready(function() {
            // Deshabilitar botones al inicio
            $('#btnCargar').prop('disabled', true);
            $('#btnEnviar').prop('disabled', true);

            // Al cambiar categoría, cargar temporadas relacionadas
            $('#categoria').change(function() {
                let categoria = $(this).val();
                $('#temporada').prop('disabled', true).html('<option>Cargando...</option>');
                $('#seccionJugadores').hide();
                $('#btnCargar').prop('disabled', true);
                $('#btnEnviar').prop('disabled', true);

                if(categoria) {
                    $('#tempoLoading').show();
                    $.ajax({
                        url: 'get_temporadas.php',
                        type: 'GET',
                        data: { categoria: categoria },
                        success: function(data) {
                            $('#tempoLoading').hide();
                            $('#temporada').html(data).prop('disabled', false);
                        },
                        error: function() {
                            $('#tempoLoading').hide();
                            $('#temporada').html('<option>Error cargando temporadas</option>').prop('disabled', true);
                        }
                    });
                } else {
                    $('#temporada').html('<option>Seleccione categoría primero</option>').prop('disabled', true);
                }
            });

            // Al cambiar temporada, habilitar botón de cargar
            $('#temporada').change(function() {
                $('#btnCargar').prop('disabled', !$(this).val());
                $('#seccionJugadores').hide();
                $('#btnEnviar').prop('disabled', true);
            });

            // Cargar equipos y jugadores
            $('#btnCargar').click(function() {
                let categoria = $('#categoria').val();
                let temporada = $('#temporada').val();

                if (!categoria || !temporada) {
                    showAlert('Seleccione categoría y temporada primero.', 'warning');
                    return;
                }

                $('#listaEquiposJugadores').html('<div class="text-center py-4"><div class="loading-spinner" style="margin: 0 auto;"></div><p class="mt-2">Cargando jugadores...</p></div>');
                $('#seccionJugadores').show();
                $('#btnEnviar').prop('disabled', true);

                $.ajax({
                    url: 'get_equipos_jugadores.php',
                    type: 'GET',
                    data: { categoria: categoria, temporada: temporada },
                    success: function(data) {
                        $('#listaEquiposJugadores').html(data);
                        $('#btnEnviar').prop('disabled', false);
                    },
                    error: function() {
                        $('#listaEquiposJugadores').html('<div class="alert alert-danger">Error cargando jugadores. Intente nuevamente.</div>');
                        $('#btnEnviar').prop('disabled', true);
                    }
                });
            });

            // Seleccionar/Deseleccionar todos
            $('#selectAll').change(function() {
                $('input[name="asistencia[]"]').prop('checked', this.checked);
            });

            // Validar envío del formulario
            $('#formAsistencia').submit(function(e) {
                if ($('input[name="asistencia[]"]:checked').length === 0) {
                    e.preventDefault();
                    showAlert('Debe seleccionar al menos un jugador.', 'warning');
                }
            });
        });

        function showAlert(message, type) {
            // Crear alerta temporal
            const alert = document.createElement('div');
            alert.className = `alert alert-${type} alert-dismissible fade show`;
            alert.style.position = 'fixed';
            alert.style.top = '20px';
            alert.style.right = '20px';
            alert.style.zIndex = '1050';
            alert.style.minWidth = '300px';
            alert.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(alert);
            
            // Auto-eliminar después de 5 segundos
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.parentNode.removeChild(alert);
                }
            }, 5000);
            
            // Inicializar funcionalidad de Bootstrap
            new bootstrap.Alert(alert);
        }
    </script>
</body>
</html>