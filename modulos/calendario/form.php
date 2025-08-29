<?php
include("../../config/conexion.php");
$con = conectar();

// Verificar conexión
if (!$con) {
    die("Error de conexión: " . mysqli_connect_error());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Crear Horario - Sistema Baseball</title>
    
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
        
        .form-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
            overflow: hidden;
        }
        
        .form-header {
            background: linear-gradient(90deg, var(--primary-color) 0%, #0a58ca 100%);
            color: white;
            padding: 1rem;
            text-align: center;
        }
        
        .form-body {
            padding: 1.5rem;
        }
        
        .form-footer {
            background: #f8f9fa;
            padding: 1rem;
            text-align: center;
            border-top: 1px solid #dee2e6;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }
        
        .form-control, .form-select {
            border-radius: 6px;
            padding: 0.75rem 1rem;
            border: 1px solid #ced4da;
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
            margin: 0 0.5rem;
        }
        
        .btn-success {
            background: linear-gradient(90deg, var(--success-color) 0%, #146c43 100%);
            border: none;
            color: white;
        }
        
        .btn-success:hover {
            background: linear-gradient(90deg, #146c43 0%, #0f5132 100%);
            transform: translateY(-2px);
            color: white;
        }
        
        .btn-info {
            background: linear-gradient(90deg, var(--info-color) 0%, #0aa2c0 100%);
            border: none;
            color: white;
        }
        
        .btn-info:hover {
            background: linear-gradient(90deg, #0aa2c0 0%, #08869e 100%);
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
            
            .clock-container {
                display: none;
            }
        }
        
        @media (max-width: 576px) {
            .btn {
                width: 100%;
                margin-bottom: 0.5rem;
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
                <h1 class="page-title">Crear Horario de Juego</h1>
                
                <div class="form-card">
                    <div class="form-header">
                        <h4 class="mb-0"><i class="fas fa-calendar-plus me-2"></i>Definir el Calendario de la Semana</h4>
                        <p class="mb-0 opacity-75">Establece el Horario</p>
                    </div>
                    
                    <form method="POST" action="create.php">
                        <div class="form-body">
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Fecha</label>
                                    <input type="date" class="form-control" name="fecha" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Categoría</label>
                                    <select class="form-select" required id="categoria" name="categoria">
                                        <option value="">Seleccionar categoría...</option>
                                        <option value="b">B</option>
                                        <option value="c">C</option>
                                        <option value="d">D</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Temporada</label>
                                    <select class="form-select" required id="tempo" name="temporada">
                                        <option value="">Seleccionar temporada...</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Campo</label>
                                    <select class="form-select" required name="campo">
                                        <option value="1">Campo 1</option>
                                        <option value="2">Campo 2</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Equipo 1</label>
                                    <select class="form-select" required id="equipone" name="team_one">
                                        <option value="">Seleccionar equipo...</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Equipo 2</label>
                                    <select class="form-select" required id="equipdos" name="team_two">
                                        <option value="">Seleccionar equipo...</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Día</label>
                                    <input type="text" class="form-control" name="dia" value="Sábado" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Hora</label>
                                    <select class="form-select" required name="hora">
                                        <option value="">Seleccionar hora...</option>
                                        <?php
                                        $select = "SELECT * FROM tiempos";
                                        $query = mysqli_query($con, $select);
                                        if (mysqli_num_rows($query) >= 1) {
                                            while ($valor = mysqli_fetch_array($query)) {
                                                echo '<option value="' . $valor['id_tiempo'] . '">' . $valor['hora'] . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-footer">
                            <button type="submit" class="btn btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="Guardar">
                                <i class="fas fa-save me-2"></i>Guardar
                            </button>
                            <a href="../calendario/" class="btn btn-info" data-bs-toggle="tooltip" data-bs-placement="top" title="Volver al calendario">
                                <i class="fas fa-arrow-left me-2"></i>Volver
                            </a>
                        </div>
                    </form>
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
        
        // Inicializar tooltips de Bootstrap
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        
        // Funciones AJAX para cargar datos dinámicamente
        $(document).ready(function(){
            $("#categoria").change(function(){
                $.get("categoria.php", {"categoria": $("#categoria").val()}, function(data){
                    $("#tempo").html(data);
                });
            });
            
            $("#tempo").change(function(){
                $.get("tempo.php", {"tempo": $("#tempo").val()}, function(data){
                    $("#equipone").html(data);
                });
            });
            
            $("#tempo, #equipone").change(function(){
                $.get('equipone.php', 
                    { 
                        'equipone': $("#equipone").val(), 
                        'tempo': $("#tempo").val()
                    }, 
                    function(data) {
                        $("#equipdos").html(data);
                    }
                );
            });
        });
    </script>
</body>
</html>