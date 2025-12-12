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

// Obtener la moneda del abono (por defecto $)
$moneda_abono = isset($data['tipo_moneda']) && !empty($data['tipo_moneda']) ? $data['tipo_moneda'] : '$';

// Consultar información de la temporada
$revisar_temp = "SELECT * FROM temporada WHERE id_temp = $id";
$ryque_temp = mysqli_query($con, $revisar_temp);
$datatp = mysqli_fetch_array($ryque_temp);

if (!$datatp) {
    die("Temporada no encontrada");
}

// Obtener el número de abono si viene por GET (para pre-seleccionar)
$abono_seleccionado = isset($_GET['abono']) ? intval($_GET['abono']) : 0;
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
        
        .btn-warning {
            background: linear-gradient(90deg, var(--warning-color) 0%, #ffca2c 100%);
            border: none;
            color: var(--dark-color);
        }
        
        .btn-warning:hover {
            background: linear-gradient(90deg, #ffca2c 0%, #b08900 100%);
            transform: translateY(-2px);
            color: var(--dark-color);
        }
        
        .btn-secondary {
            background: linear-gradient(90deg, #6c757d 0%, #5a6268 100%);
            border: none;
            color: white;
        }
        
        .btn-secondary:hover {
            background: linear-gradient(90deg, #5a6268 0%, #484e53 100%);
            transform: translateY(-2px);
            color: white;
        }
        
        .btn-container {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
            flex-wrap: wrap;
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
            justify-content: space-between;
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
        
        .moneda-badge {
            display: inline-block;
            padding: 0.25em 0.5em;
            font-size: 0.7em;
            font-weight: 600;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            border-radius: 0.25rem;
            background-color: #17a2b8;
            color: white;
            margin-left: 5px;
        }
        
        .moneda-dolares {
            background-color: #28a745 !important;
        }
        
        .moneda-bolivares {
            background-color: #dc3545 !important;
        }
        
        .abono-badge {
            display: inline-block;
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.5rem;
            background-color: var(--success-color);
            color: white;
        }
        
        .loading-spinner {
            display: none;
            text-align: center;
            padding: 20px;
        }
        
        .monto-input-group {
            position: relative;
        }
        
        .simbolo-moneda {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-weight: bold;
            color: #495057;
            z-index: 3;
        }
        
        .monto-input {
            padding-left: 30px !important;
        }
        
        .team-status {
            display: none;
        }
        
        .team-status-badge {
            font-size: 0.7em;
            padding: 0.25em 0.5em;
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
            
            .simbolo-moneda {
                left: 10px;
                font-size: 0.9em;
            }
            
            .monto-input {
                padding-left: 25px !important;
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
                <h1 class="page-title">💰 Registrar / Modificar Abonos</h1>
                
                <div class="text-center mb-4">
                    <div class="info-badge mb-2">
                        <i class="fas fa-info-circle me-1"></i>
                        Temporada: <?php echo htmlspecialchars($datatp['name_temp']); ?> 
                        | Categoría: <?php echo htmlspecialchars($cat); ?>
                        | Total de Abonos: <?php echo $nabono; ?>
                        | Moneda: 
                        <span class="moneda-badge <?php echo $moneda_abono == '$' ? 'moneda-dolares' : 'moneda-bolivares'; ?>">
                            <?php echo $moneda_abono == '$' ? 'Dólares ($)' : 'Bolívares (Bs)'; ?>
                        </span>
                    </div>
                </div>

                <form method="POST" action="abnctr.php" id="abonoForm">
                    <input type="hidden" name="id" value="<?php echo $idn; ?>">
                    <input type="hidden" name="cat" value="<?php echo htmlspecialchars($cat); ?>">
                    <input type="hidden" name="temp" value="<?php echo $temp; ?>">

                    <!-- Sección de Selección de Abono -->
                    <div class="form-section">
                        <h5 class="section-title"><i class="fas fa-ticket-alt"></i> Selección de Abono</h5>
                        
                        <div class="row">
                            <div class="col-md-8 mx-auto">
                                <label class="form-label required-field">Seleccione el Abono a Registrar/Modificar</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                    <select class="form-select" id="abono" name="abono" required onchange="cargarMontosAbono()">
                                        <option value="">Seleccione un abono...</option>
                                        <?php for ($i = 1; $i <= $nabono; $i++) { 
                                            $selected = ($i == $abono_seleccionado) ? 'selected' : '';
                                        ?>
                                            <option value="<?php echo $i; ?>" <?php echo $selected; ?>>Abono N° <?php echo $i; ?></option>
                                        <?php } ?>
                                    </select>
                                    <button type="button" class="btn btn-secondary" onclick="limpiarMontos()" id="btnLimpiar" disabled>
                                        <i class="fas fa-eraser me-2"></i>Limpiar Montos
                                    </button>
                                </div>
                                <div class="form-text">Seleccione el número de abono que desea registrar o modificar</div>
                                <div id="estadoAbono" class="mt-2" style="display: none;">
                                    <span class="abono-badge" id="badgeEstado">
                                        <i class="fas fa-info-circle me-1"></i>
                                        <span id="textoEstado">Cargando...</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Loading Spinner -->
                    <div id="loadingSpinner" class="loading-spinner">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2">Cargando montos del abono...</p>
                    </div>

                    <!-- Sección de Equipos y Montos -->
                    <div class="form-section" id="seccionMontos">
                        <h5 class="section-title">
                            <i class="fas fa-users"></i> Montos por Equipo
                            <span class="ms-2 moneda-badge <?php echo $moneda_abono == '$' ? 'moneda-dolares' : 'moneda-bolivares'; ?>">
                                <?php echo $moneda_abono; ?>
                            </span>
                        </h5>
                        
                        <?php 
                        $deftra = "SELECT * FROM tab_clasf WHERE id_temp = $id AND categoria LIKE '%$cat%' ORDER BY name_team ASC";
                        $ryque = mysqli_query($con, $deftra);
                        $nunum = mysqli_num_rows($ryque);
                        
                        if ($nunum >= 1) {
                            while ($bdata = mysqli_fetch_array($ryque)) { 
                        ?>
                        <div class="team-row" id="team-<?php echo $bdata['id_team']; ?>">
                            <div class="team-header">
                                <div>
                                    <i class="fas fa-baseball-ball"></i> Equipo: <?php echo htmlspecialchars($bdata['name_team']); ?>
                                </div>
                                <div class="team-status" id="status-<?php echo $bdata['id_team']; ?>">
                                    <span class="badge bg-success team-status-badge" id="badge-<?php echo $bdata['id_team']; ?>">
                                        <i class="fas fa-check me-1"></i>Guardado
                                    </span>
                                </div>
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
                                    <label class="form-label required-field">Monto del Abono</label>
                                    <div class="monto-input-group">
                                        <span class="simbolo-moneda"><?php echo $moneda_abono; ?></span>
                                        <input type="number" class="form-control monto-input" 
                                               name="monto[]" 
                                               id="monto-<?php echo $bdata['id_team']; ?>" 
                                               data-team="<?php echo $bdata['id_team']; ?>"
                                               min="0" 
                                               step="0.01" 
                                               value="0.00" 
                                               required>
                                    </div>
                                    <div class="form-text" id="info-<?php echo $bdata['id_team']; ?>">
                                        Ingrese el monto para este equipo
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
                        <button type="submit" class="btn btn-success" id="btnGuardar" data-bs-toggle="tooltip" data-bs-placement="top" title="Guardar todos los abonos">
                            <i class="fas fa-save me-2"></i>Guardar Abonos
                        </button>
                        <a href="list.php?id=<?php echo $id; ?>&idn=<?php echo $idn; ?>&cat=<?php echo urlencode($cat); ?>" class="btn btn-info" data-bs-toggle="tooltip" data-bs-placement="top" title="Volver al listado de abonos">
                            <i class="fas fa-arrow-left me-2"></i>Volver al Listado
                        </a>
                        <button type="button" class="btn btn-warning" onclick="limpiarMontos()" id="btnLimpiar2">
                            <i class="fas fa-eraser me-2"></i>Limpiar Montos
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
            hours = hours ? hours : 12;
            
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
        
        // Variable para almacenar los montos cargados
        let montosCargados = false;
        
        // Función para cargar los montos del abono seleccionado
        function cargarMontosAbono() {
            const abonoSelect = document.getElementById('abono');
            const abonoNumero = abonoSelect.value;
            const btnGuardar = document.getElementById('btnGuardar');
            const btnLimpiar = document.getElementById('btnLimpiar');
            const btnLimpiar2 = document.getElementById('btnLimpiar2');
            const estadoAbono = document.getElementById('estadoAbono');
            const badgeEstado = document.getElementById('badgeEstado');
            const textoEstado = document.getElementById('textoEstado');
            const loadingSpinner = document.getElementById('loadingSpinner');
            const seccionMontos = document.getElementById('seccionMontos');
            
            if (abonoNumero === '') {
                estadoAbono.style.display = 'none';
                btnLimpiar.disabled = true;
                btnLimpiar2.disabled = true;
                limpiarMontos();
                return;
            }
            
            // Habilitar botones de limpiar
            btnLimpiar.disabled = false;
            btnLimpiar2.disabled = false;
            
            // Mostrar loading
            loadingSpinner.style.display = 'block';
            seccionMontos.style.opacity = '0.5';
            btnGuardar.disabled = true;
            
            // Ocultar todos los estados previos
            document.querySelectorAll('.team-status').forEach(el => {
                el.style.display = 'none';
            });
            
            // Configurar AJAX
            $.ajax({
                url: 'cargar_montos.php',
                type: 'GET',
                data: {
                    id_abn: <?php echo $idn; ?>,
                    id_temp: <?php echo $id; ?>,
                    categoria: '<?php echo $cat; ?>',
                    abono: abonoNumero
                },
                dataType: 'json',
                success: function(response) {
                    // Ocultar loading
                    loadingSpinner.style.display = 'none';
                    seccionMontos.style.opacity = '1';
                    btnGuardar.disabled = false;
                    
                    if (response.success) {
                        // Actualizar estado del abono
                        estadoAbono.style.display = 'block';
                        textoEstado.textContent = 'Abono ' + abonoNumero + ' - ' + 
                            (response.existe ? 'Modificando datos existentes' : 'Nuevo abono');
                        badgeEstado.className = response.existe ? 
                            'abono-badge bg-warning' : 'abono-badge bg-primary';
                        
                        // Limpiar todos los campos primero
                        limpiarCamposMontos();
                        
                        // Llenar los campos con los datos cargados
                        if (response.existe && response.montos) {
                            montosCargados = true;
                            response.montos.forEach(function(monto) {
                                const inputMonto = document.getElementById('monto-' + monto.id_team);
                                const statusDiv = document.getElementById('status-' + monto.id_team);
                                const infoDiv = document.getElementById('info-' + monto.id_team);
                                
                                if (inputMonto) {
                                    inputMonto.value = parseFloat(monto.monto).toFixed(2);
                                    
                                    // Mostrar estado si ya tiene datos
                                    if (parseFloat(monto.monto) > 0) {
                                        statusDiv.style.display = 'block';
                                        infoDiv.innerHTML = '<i class="fas fa-info-circle me-1"></i>Valor previamente guardado';
                                        infoDiv.className = 'form-text text-success';
                                    } else {
                                        infoDiv.innerHTML = '<i class="fas fa-info-circle me-1"></i>Ingrese el monto para este equipo';
                                        infoDiv.className = 'form-text';
                                    }
                                }
                            });
                        } else {
                            montosCargados = false;
                            textoEstado.textContent = 'Abono ' + abonoNumero + ' - Nuevo abono';
                            badgeEstado.className = 'abono-badge bg-primary';
                        }
                        
                        // Actualizar texto del botón
                        btnGuardar.innerHTML = response.existe ? 
                            '<i class="fas fa-sync-alt me-2"></i>Actualizar Abonos' : 
                            '<i class="fas fa-save me-2"></i>Guardar Abonos';
                    } else {
                        alert('Error al cargar los montos: ' + (response.error || 'Error desconocido'));
                        limpiarMontos();
                    }
                },
                error: function() {
                    // Ocultar loading
                    loadingSpinner.style.display = 'none';
                    seccionMontos.style.opacity = '1';
                    btnGuardar.disabled = false;
                    
                    alert('Error de conexión al cargar los montos');
                    limpiarMontos();
                }
            });
        }
        
        // Función para limpiar solo los campos de montos (NO afecta el select)
        function limpiarCamposMontos() {
            document.querySelectorAll('.monto-input').forEach(function(input) {
                input.value = '0.00';
                const teamId = input.getAttribute('data-team');
                const infoDiv = document.getElementById('info-' + teamId);
                const statusDiv = document.getElementById('status-' + teamId);
                
                if (infoDiv) {
                    infoDiv.innerHTML = '<i class="fas fa-info-circle me-1"></i>Ingrese el monto para este equipo';
                    infoDiv.className = 'form-text';
                }
                if (statusDiv) {
                    statusDiv.style.display = 'none';
                }
            });
        }
        
        // Función para limpiar solo los montos (mantiene el select seleccionado)
        function limpiarMontos() {
            const abonoSelect = document.getElementById('abono');
            const abonoNumero = abonoSelect.value;
            const estadoAbono = document.getElementById('estadoAbono');
            const btnGuardar = document.getElementById('btnGuardar');
            
            // Solo limpiar si hay un abono seleccionado
            if (abonoNumero !== '') {
                if (confirm('¿Está seguro de que desea limpiar todos los montos del abono ' + abonoNumero + '? Los datos no se perderán hasta que guarde los cambios.')) {
                    limpiarCamposMontos();
                    montosCargados = false;
                    
                    // Actualizar estado
                    estadoAbono.style.display = 'block';
                    document.getElementById('textoEstado').textContent = 'Abono ' + abonoNumero + ' - Montos limpiados';
                    document.getElementById('badgeEstado').className = 'abono-badge bg-secondary';
                    
                    // Actualizar botón
                    btnGuardar.innerHTML = '<i class="fas fa-save me-2"></i>Guardar Abonos';
                }
            } else {
                alert('Por favor, seleccione un abono primero');
            }
        }
        
        // Función para validar el formulario
        document.getElementById('abonoForm').addEventListener('submit', function(e) {
            const abonoSelect = document.getElementById('abono');
            if (abonoSelect.value === '') {
                e.preventDefault();
                alert('Por favor, seleccione un número de abono');
                abonoSelect.focus();
            }
        });
        
        // Si hay un abono pre-seleccionado, cargarlo automáticamente
        <?php if ($abono_seleccionado > 0): ?>
        $(document).ready(function() {
            // Esperar un momento para que la página cargue completamente
            setTimeout(function() {
                cargarMontosAbono();
            }, 500);
        });
        <?php endif; ?>
        
        // Habilitar/deshabilitar botones de limpiar según si hay abono seleccionado
        document.getElementById('abono').addEventListener('change', function() {
            const btnLimpiar = document.getElementById('btnLimpiar');
            const btnLimpiar2 = document.getElementById('btnLimpiar2');
            const abonoNumero = this.value;
            
            if (abonoNumero === '') {
                btnLimpiar.disabled = true;
                btnLimpiar2.disabled = true;
            } else {
                btnLimpiar.disabled = false;
                btnLimpiar2.disabled = false;
            }
        });
    </script>
</body>
</html>