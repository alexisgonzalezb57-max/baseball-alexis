<?php
include("../../config/conexion.php");
$con = conectar();

// Verificar conexión
if (!$con) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Obtener parámetros con validación
$idn = isset($_REQUEST['idn']) ? intval($_REQUEST['idn']) : 0;
$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
$cat = isset($_REQUEST['cat']) ? mysqli_real_escape_string($con, $_REQUEST['cat']) : '';

if ($idn <= 0 || $id <= 0 || empty($cat)) {
    die("Parámetros no válidos");
}

// Consultar información del abono
$revisar = "SELECT * FROM abonos WHERE id_abn = $idn AND categoria LIKE '%$cat%'";
$query = mysqli_query($con, $revisar);
$data = mysqli_fetch_array($query);

if (!$data) {
    die("Abono no encontrado");
}

$nabono = $data['ncantidad'];
$temp = $data['id_temp'];

// Consultar información de la temporada
$revisar_temp = "SELECT * FROM temporada WHERE id_temp = $id";
$ryque_temp = mysqli_query($con, $revisar_temp);
$datatp = mysqli_fetch_array($ryque_temp);

if (!$datatp) {
    die("Temporada no encontrada");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrar Abonos - Sistema Baseball</title>
    
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
        
        .team-row {
            background-color: #fff;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            border-left: 3px solid var(--primary-color);
            transition: all 0.3s;
        }
        
        .team-row:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
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
            color: var(--primary-color);
        }
        
        .info-badge {
            display: inline-block;
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.5rem;
            background-color: var(--info-color);
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
            
            .btn-container {
                flex-direction: column;
            }
            
            .team-row {
                padding: 0.75rem;
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
                <h1 class="page-title">💰 Registrar Abonos</h1>
                
                <div class="text-center mb-4">
                    <div class="info-badge mb-2">
                        <i class="fas fa-info-circle me-1"></i>
                        Temporada: <?php echo htmlspecialchars($datatp['name_temp']); ?> 
                        | Categoría: <?php echo htmlspecialchars($cat); ?>
                        | Total de Abonos: <?php echo $nabono; ?>
                    </div>
                </div>

                <form method="POST" action="abnctr.php">
                    <input type="hidden" name="id" value="<?php echo $idn; ?>">
                    <input type="hidden" name="cat" value="<?php echo htmlspecialchars($cat); ?>">
                    <input type="hidden" name="temp" value="<?php echo $temp; ?>">

                    <!-- Sección de Selección de Abono -->
                    <div class="form-section">
                        <h5 class="section-title"><i class="fas fa-ticket-alt"></i> Selección de Abono</h5>
                        
                        <div class="row">
                            <div class="col-md-8 mx-auto">
                                <label class="form-label required-field">Seleccione el Abono a Registrar</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                    <select class="form-select" id="abono" name="abono" required>
                                        <option value="">Seleccione un abono...</option>
                                        <?php for ($i = 1; $i <= $nabono; $i++) { ?>
                                            <option value="<?php echo $i; ?>">Abono N° <?php echo $i; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-text">Seleccione el número de abono que desea registrar para todos los equipos</div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de Equipos y Montos -->
                    <div class="form-section">
                        <h5 class="section-title"><i class="fas fa-users"></i> Montos por Equipo</h5>
                        
                        <?php 
                        $deftra = "SELECT * FROM tab_clasf WHERE id_temp = $id AND categoria LIKE '%$cat%'";
                        $ryque = mysqli_query($con, $deftra);
                        $nunum = mysqli_num_rows($ryque);
                        
                        if ($nunum >= 1) {
                            while ($bdata = mysqli_fetch_array($ryque)) { 
                        ?>
                        <div class="team-row">
                            <div class="team-header">
                                <i class="fas fa-baseball-ball"></i> Equipo: <?php echo htmlspecialchars($bdata['name_team']); ?>
                            </div>
                            
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <input type="hidden" name="idequipo[]" value="<?php echo $bdata['id_team']; ?>">
                                    <input type="hidden" name="equipo[]" value="<?php echo htmlspecialchars($bdata['name_team']); ?>">
                                    
                                    <label class="form-label">Nombre del Equipo</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($bdata['name_team']); ?>" readonly>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label required-field">Monto del Abono ($)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                        <input type="number" class="form-control" name="monto[]" min="0" step="0.01" value="0.00" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php 
                            }
                        } else {
                            echo '<div class="alert alert-info text-center"><i class="fas fa-exclamation-circle me-2"></i>No hay equipos registrados para esta temporada y categoría</div>';
                        }
                        ?>
                    </div>

                    <div class="btn-container">
                        <button type="submit" class="btn btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="Guardar todos los abonos">
                            <i class="fas fa-save me-2"></i>Guardar Abonos
                        </button>
                        <a href="list.php?id=<?php echo $id; ?>&idn=<?php echo $idn; ?>&cat=<?php echo urlencode($cat); ?>" class="btn btn-info" data-bs-toggle="tooltip" data-bs-placement="top" title="Volver al listado de abonos">
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
        
        // Validación del formulario
        document.querySelector('form').addEventListener('submit', function(e) {
            const abonoSelect = document.getElementById('abono');
            if (abonoSelect.value === '') {
                e.preventDefault();
                alert('Por favor, seleccione un número de abono');
                abonoSelect.focus();
            }
        });
    </script>
</body>
</html>