<?php
include("../../config/conexion.php");
$con = conectar();

// Verificar conexión
if (!$con) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Obtener y sanitizar parámetros
$id_lanz = filter_var($_REQUEST['id_lanz'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
$id_tab = filter_var($_REQUEST['id_tab'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
$nj = filter_var($_REQUEST['nj'] ?? 0, FILTER_SANITIZE_NUMBER_INT);

// Consulta preparada para obtener datos de clasificación
$revisar = "SELECT temporada.*, tab_clasf.* FROM temporada 
            INNER JOIN tab_clasf ON temporada.id_temp = tab_clasf.id_temp 
            WHERE tab_clasf.id_tab = ?";
$stmt = mysqli_prepare($con, $revisar);
mysqli_stmt_bind_param($stmt, "i", $id_tab);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_array($result);

if (!$data) {
    die("No se encontraron datos para la clasificación especificada.");
}

$id_temp = $data['id_temp'];

// Consulta preparada para obtener estadísticas del lanzador
$cons = "SELECT * FROM jugadores_lanz WHERE id_lanz = ? AND id_tab = ? AND nj = ?";
$stmt = mysqli_prepare($con, $cons);
mysqli_stmt_bind_param($stmt, "iii", $id_lanz, $id_tab, $nj);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$dteg = mysqli_fetch_array($result);

if (!$dteg) {
    die("No se encontraron estadísticas para el lanzador especificado.");
}

$id_player = $dteg['id_player'];
$id_team = $dteg['id_team'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Estadísticas de Lanzador - Sistema Baseball</title>
    
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
            max-width: 900px;
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
        
        .card-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--dark-color);
            text-align: center;
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            font-weight: 500;
            color: var(--dark-color);
        }
        
        .form-control, .form-control-plaintext, .form-select {
            border-radius: 6px;
            padding: 0.75rem;
            border: 1px solid #ced4da;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        
        .form-control-plaintext {
            background-color: #f8f9fa;
            text-align: center;
            font-weight: 500;
        }
        
        .btn {
            border-radius: 6px;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s;
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
            background: linear-gradient(90deg, #0aa2c0 0%, #08819c 100%);
            transform: translateY(-2px);
            color: white;
        }
        
        .card-footer {
            background: transparent;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: center;
            gap: 1rem;
            padding: 1.5rem 0 0;
            margin-top: 1.5rem;
        }
        
        .input-number {
            text-align: center;
        }
        
        .stat-group {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .stat-group-title {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 1rem;
            text-align: center;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 0.5rem;
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
            
            .card-title {
                font-size: 1.2rem;
            }
            
            .card-footer {
                flex-direction: column;
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
                    <li><a href="../homenaje/homenaje.php"><i class="fas fa-award"></i> Homenaje</a></li>
                    <li><a href="../abonos/"><i class="fas fa-ticket-alt"></i> Abono</a></li>
                    <li><a href="../reporte/"><i class="fas fa-chart-bar"></i> Reportes</a></li>
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
                <h1 class="page-title">Editar Estadísticas de Lanzador</h1>
                <h4 class="card-title"><?php echo htmlspecialchars($data['name_team']); ?> - Partido N° <?php echo $nj; ?></h4>
                
                <form method="POST" action="jlzupdate.php">
                    <input type="hidden" name="id_player" value="<?php echo $id_player; ?>">
                    <input type="hidden" name="id_team" value="<?php echo $id_team; ?>">
                    <input type="hidden" name="id_lanz" value="<?php echo $id_lanz; ?>">
                    <input type="hidden" name="id_tab" value="<?php echo $id_tab; ?>">
                    <input type="hidden" name="nj" value="<?php echo $nj; ?>">

                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label text-end">Lanzador</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control-plaintext" readonly name="jugador" value="<?php echo htmlspecialchars($dteg['name_lanz']); ?>">
                            <input type="hidden" name="cedula" value="<?php echo htmlspecialchars($dteg['cedula']); ?>">
                        </div>
                    </div>

                    <div class="stat-group">
                        <div class="stat-group-title">Estadísticas de Juego</div>
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label text-end">JL</label>
                            <div class="col-sm-3">
                                <input type="number" class="form-control input-number" name="jl" min="0" max="1" value="<?php echo $dteg['jl']; ?>">
                            </div>
                            <label class="col-sm-2 col-form-label text-end">JG</label>
                            <div class="col-sm-3">
                                <input type="number" class="form-control input-number" name="jg" min="0" max="1" value="<?php echo $dteg['jg']; ?>">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label text-end">IL (usar .)</label>
                            <div class="col-sm-3">
                                <input type="number" class="form-control input-number" name="il" min="0" step="0.1" value="<?php echo $dteg['il']; ?>">
                            </div>
                            <label class="col-sm-2 col-form-label text-end">CP</label>
                            <div class="col-sm-3">
                                <input type="number" class="form-control input-number" name="cp" min="0" value="<?php echo $dteg['cp']; ?>">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label text-end">CPL</label>
                            <div class="col-sm-3">
                                <input type="number" class="form-control input-number" name="cpl" min="0" value="<?php echo $dteg['cpl']; ?>">
                            </div>
                            <label class="col-sm-2 col-form-label text-end">VA</label>
                            <div class="col-sm-3">
                                <input type="number" class="form-control input-number" name="va" min="0" value="<?php echo $dteg['va']; ?>">
                            </div>
                        </div>
                    </div>

                    <div class="stat-group">
                        <div class="stat-group-title">Estadísticas de Bateo Permitido</div>
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label text-end">H</label>
                            <div class="col-sm-3">
                                <input type="number" class="form-control input-number" name="h" min="0" value="<?php echo $dteg['h']; ?>">
                            </div>
                            <label class="col-sm-2 col-form-label text-end">2B</label>
                            <div class="col-sm-3">
                                <input type="number" class="form-control input-number" name="2b" min="0" value="<?php echo $dteg['2b']; ?>">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label text-end">3B</label>
                            <div class="col-sm-3">
                                <input type="number" class="form-control input-number" name="3b" min="0" value="<?php echo $dteg['3b']; ?>">
                            </div>
                            <label class="col-sm-2 col-form-label text-end">HR</label>
                            <div class="col-sm-3">
                                <input type="number" class="form-control input-number" name="hr" min="0" value="<?php echo $dteg['hr']; ?>">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label text-end">K</label>
                            <div class="col-sm-3">
                                <input type="number" class="form-control input-number" name="k" min="0" value="<?php echo $dteg['k']; ?>">
                            </div>
                            <label class="col-sm-2 col-form-label text-end">B</label>
                            <div class="col-sm-3">
                                <input type="number" class="form-control input-number" name="b" min="0" value="<?php echo $dteg['b']; ?>">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label text-end">GP</label>
                            <div class="col-sm-3">
                                <input type="number" class="form-control input-number" name="gp" min="0" value="<?php echo $dteg['gp']; ?>">
                            </div>
                            <label class="col-sm-2 col-form-label text-end">BR</label>
                            <div class="col-sm-3">
                                <input type="number" class="form-control input-number" name="br" min="0" value="<?php echo $dteg['br']; ?>">
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="Guardar cambios">
                            <i class="fas fa-save me-2"></i>Guardar
                        </button>
                        <a href="../entradas/datos.php?id_tab=<?php echo $id_tab; ?>&nj=<?php echo $nj; ?>" class="btn btn-info" data-bs-toggle="tooltip" data-bs-placement="top" title="Volver sin guardar">
                            <i class="fas fa-arrow-left me-2"></i>Volver
                        </a>
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
        
        // Validación para JL y JG (solo pueden ser 0 o 1)
        $(document).ready(function(){
            $('input[name="jl"], input[name="jg"]').on('change', function() {
                const value = $(this).val();
                if (value > 1) {
                    $(this).val(1);
                    alert('El valor máximo para este campo es 1');
                } else if (value < 0) {
                    $(this).val(0);
                    alert('El valor mínimo para este campo es 0');
                }
            });
            
            // Inicializar tooltips de Bootstrap
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        });
    </script>
</body>
</html>