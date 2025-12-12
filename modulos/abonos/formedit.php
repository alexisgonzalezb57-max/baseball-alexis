<?php
include("../../config/conexion.php");
$con = conectar();

// Verificar conexión
if (!$con) {
    die("Error de conexión: " . mysqli_connect_error());
}

$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

if ($id <= 0) {
    die("ID de abono no válido");
}

// CONSULTA CORREGIDA: Obtener datos del abono específico
$revisar = "SELECT * FROM abonos WHERE id_abn = $id";
$query = mysqli_query($con, $revisar);

if (!$query) {
    die("Error en la consulta: " . mysqli_error($con));
}

$data = mysqli_fetch_array($query);

if (!$data) {
    die("Abono no encontrado");
}

// Obtener los valores del abono
$cat = isset($data['categoria']) ? $data['categoria'] : '';
$temp = isset($data['id_temp']) ? $data['id_temp'] : 0;
$four = isset($data['prize_four']) ? $data['prize_four'] : 0;
$cfour = isset($data['cant_four']) ? $data['cant_four'] : 0;
$mfour = isset($data['mond_four']) ? $data['mond_four'] : '$';
$once = isset($data['prize_once']) ? $data['prize_once'] : 0;
$conce = isset($data['cant_once']) ? $data['cant_once'] : 0;
$monce = isset($data['mond_once']) ? $data['mond_once'] : '$';
$second = isset($data['prize_second']) ? $data['prize_second'] : 0;
$csecond = isset($data['cant_second']) ? $data['cant_second'] : 0;
$msecond = isset($data['mond_second']) ? $data['mond_second'] : '$';
$third = isset($data['prize_third']) ? $data['prize_third'] : 0;
$cthird = isset($data['cant_third']) ? $data['cant_third'] : 0;
$mthird = isset($data['mond_third']) ? $data['mond_third'] : '$';
$activo = isset($data['activo']) ? $data['activo'] : 1;
$ncantidad = isset($data['ncantidad']) ? $data['ncantidad'] : 0;

// Obtener información de la temporada asociada
$nombre_temporada = 'No encontrada';
if ($temp > 0) {
    $revisar_temp = "SELECT name_temp FROM temporada WHERE id_temp = $temp";
    $query_temp = mysqli_query($con, $revisar_temp);
    if ($query_temp && mysqli_num_rows($query_temp) > 0) {
        $temp_data = mysqli_fetch_array($query_temp);
        $nombre_temporada = $temp_data['name_temp'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Abonos - Sistema Baseball</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- jQuery (necesario para la funcionalidad AJAX) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Estilos personalizados -->
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
        
        .premio-row {
            background-color: #fff;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
        }
        
        .premio-row:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
        
        .premio-label {
            font-weight: 600;
            color: var(--dark-color);
            padding-top: 0.5rem;
        }
        
        .btn {
            border-radius: 6px;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s;
        }
        
        .btn-warning {
            background: linear-gradient(90deg, var(--warning-color) 0%, #e0a800 100%);
            border: none;
            color: var(--dark-color);
        }
        
        .btn-warning:hover {
            background: linear-gradient(90deg, #e0a800 0%, #c69500 100%);
            transform: translateY(-2px);
            color: var(--dark-color);
        }
        
        .btn-secondary {
            background: linear-gradient(90deg, #6c757d 0%, #5a6268 100%);
            border: none;
            color: white;
        }
        
        .btn-secondary:hover {
            background: linear-gradient(90deg, #5a6268 0%, #495057 100%);
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
        
        .premio-card {
            background: white;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            border-left: 3px solid var(--primary-color);
        }
        
        .premio-header {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }
        
        .premio-header i {
            margin-right: 10px;
            color: var(--primary-color);
        }
        
        .alert-info {
            background: linear-gradient(90deg, #d1ecf1 0%, #bee5eb 100%);
            border: 1px solid #bee5eb;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
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
            
            .premio-label {
                padding-top: 0;
                margin-bottom: 0.5rem;
            }
        }
        
        @media (max-width: 576px) {
            .clock-container {
                display: none;
            }
            
            .form-section {
                padding: 1rem;
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
                <h1 class="page-title">✏️ Editar Abonos</h1>
                <p class="text-center text-muted mb-4">Modifique la configuración de abonos para la temporada</p>
                
                <div class="alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Editando abono ID:</strong> <?php echo $data['id_abn']; ?> 
                    | <strong>Temporada actual:</strong> <?php echo htmlspecialchars($nombre_temporada); ?>
                    | <strong>Categoría actual:</strong> <?php echo htmlspecialchars($cat); ?>
                    | <strong>N° de Abonos:</strong> <?php echo $ncantidad; ?>
                    | <strong>Estado actual:</strong> 
                    <?php if ($activo == 1): ?>
                        <span class="badge bg-success">Activo</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Inactivo</span>
                    <?php endif; ?>
                </div>
                
                <form method="POST" action="update.php">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    
                    <!-- Sección de Información Básica -->
                    <div class="form-section">
                        <h5 class="section-title"><i class="fas fa-info-circle"></i> Información Básica</h5>
                        
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required-field">Categoría</label>
                                <input type="text" class="form-control" required readonly value="<?php echo $cat ?>" name="categoria">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label required-field">Temporada</label>
                                <input type="text" class="form-control" required readonly name="temp" value="<?php echo $nombre_temporada; ?>">
                                <input type="hidden" class="form-control" required readonly name="temporada" value="<?php echo $temp; ?>">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label required-field">Cantidad de Abonos</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-ticket-alt"></i></span>
                                    <input type="number" name="ncantidad" class="form-control" required min="1" value="<?php echo htmlspecialchars($ncantidad); ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label required-field">Estado del Abono</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-toggle-on"></i></span>
                                    <select class="form-select" name="activo" required>
                                        <option value="1" <?php echo ($activo == 1) ? 'selected' : ''; ?>>Activo</option>
                                        <option value="0" <?php echo ($activo == 0) ? 'selected' : ''; ?>>Inactivo</option>
                                    </select>
                                </div>
                                <div class="form-text">Los abonos inactivos se moverán a la sección inactiva</div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de Premios -->
                    <div class="form-section">
                        <h5 class="section-title"><i class="fas fa-medal"></i> Configuración de Premios</h5>
                        
                        <div class="premio-card">
                            <div class="premio-header">
                                <i class="fas fa-trophy"></i> Primer Lugar
                            </div>
                            <div class="row align-items-center">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">¿Habilitar premio?</label>
                                    <select class="form-select" name="prize_once" required>
                                        <option value="1" <?php echo ($once == 1) ? 'selected' : ''; ?>>SI</option>
                                        <option value="0" <?php echo ($once == 0) ? 'selected' : ''; ?>>NO</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Tipo de Moneda</label>
                                    <div class="input-group">
                                    <select class="form-select" name="mond_once" required>
                                        <option value="$" <?php echo ($monce == '$') ? 'selected' : ''; ?>>($) Dólares</option>
                                        <option value="Bs" <?php echo ($monce == 'Bs') ? 'selected' : ''; ?>>(Bs) Bolívares</option>
                                    </select>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Cantidad ($)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                        <input type="text" class="form-control" name="cant_once" value="<?php echo htmlspecialchars($conce); ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="premio-card">
                            <div class="premio-header">
                                <i class="fas fa-medal"></i> Segundo Lugar
                            </div>
                            <div class="row align-items-center">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">¿Habilitar premio?</label>
                                    <select class="form-select" name="prize_second" required>
                                        <option value="1" <?php echo ($second == 1) ? 'selected' : ''; ?>>SI</option>
                                        <option value="0" <?php echo ($second == 0) ? 'selected' : ''; ?>>NO</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Tipo de Moneda</label>
                                    <div class="input-group">
                                    <select class="form-select" name="mond_second" required>
                                        <option value="$" <?php echo ($msecond == '$') ? 'selected' : ''; ?>>($) Dólares</option>
                                        <option value="Bs" <?php echo ($msecond == 'Bs') ? 'selected' : ''; ?>>(Bs) Bolívares</option>
                                    </select>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Cantidad ($)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                        <input type="text" class="form-control" name="cant_second" value="<?php echo htmlspecialchars($csecond); ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="premio-card">
                            <div class="premio-header">
                                <i class="fas fa-award"></i> Tercer Lugar
                            </div>
                            <div class="row align-items-center">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">¿Habilitar premio?</label>
                                    <select class="form-select" name="prize_third" required>
                                        <option value="1" <?php echo ($third == 1) ? 'selected' : ''; ?>>SI</option>
                                        <option value="0" <?php echo ($third == 0) ? 'selected' : ''; ?>>NO</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Tipo de Moneda</label>
                                    <div class="input-group">
                                    <select class="form-select" name="mond_third" required>
                                        <option value="$" <?php echo ($mthird == '$') ? 'selected' : ''; ?>>($) Dólares</option>
                                        <option value="Bs" <?php echo ($mthird == 'Bs') ? 'selected' : ''; ?>>(Bs) Bolívares</option>
                                    </select>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Cantidad ($)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                        <input type="text" class="form-control" name="cant_third" value="<?php echo htmlspecialchars($cthird); ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="premio-card">
                            <div class="premio-header">
                                <i class="fas fa-star"></i> Cuarto Lugar
                            </div>
                            <div class="row align-items-center">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">¿Habilitar premio?</label>
                                    <select class="form-select" name="prize_four" required>
                                        <option value="1" <?php echo ($four == 1) ? 'selected' : ''; ?>>SI</option>
                                        <option value="0" <?php echo ($four == 0) ? 'selected' : ''; ?>>NO</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Tipo de Moneda</label>
                                    <div class="input-group">
                                    <select class="form-select" name="mond_four" required>
                                        <option value="$" <?php echo ($mfour == '$') ? 'selected' : ''; ?>>($) Dólares</option>
                                        <option value="Bs" <?php echo ($mfour == 'Bs') ? 'selected' : ''; ?>>(Bs) Bolívares</option>
                                    </select>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Cantidad ($)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                        <input type="text" class="form-control" name="cant_four" value="<?php echo htmlspecialchars($cfour); ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="btn-container">
                        <button type="submit" class="btn btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="Guardar cambios">
                            <i class="fas fa-save me-2"></i>Actualizar
                        </button>
                        <a href="../abonos/" class="btn btn-info" data-bs-toggle="tooltip" data-bs-placement="top" title="Volver al listado">
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
        
        // Inicializar tooltips de Bootstrap
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        
        // Cargar temporadas según categoría seleccionada
        $(document).ready(function(){
            var categoriaActual = "<?php echo $cat; ?>";
            var tempActual = "<?php echo $temp; ?>";
            
            // Establecer la categoría
            $("#categoria").val(categoriaActual);
            
            // Cargar temporadas para esta categoría (opcional)
            $("#categoria").change(function(){
                var categoria = $(this).val();
                if(categoria) {
                    $.ajax({
                        url: "../calendario/categoria.php",
                        type: "GET",
                        data: {categoria: categoria},
                        success: function(data){
                            // Solo actualizar si se cambia la categoría
                            if(categoria != categoriaActual) {
                                $("#tempo").html('<option value="">Seleccione temporada...</option>' + data);
                            }
                        },
                        error: function(){
                            $("#tempo").html('<option value="">Error al cargar temporadas</option>');
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>