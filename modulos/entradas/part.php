<?php
include("../../config/conexion.php");
$con = conectar();

// Verificar conexión
if (!$con) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Obtener y sanitizar parámetros
$id_tab = filter_var($_REQUEST['id_tab'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
$nj = filter_var($_REQUEST['nj'] ?? 0, FILTER_SANITIZE_NUMBER_INT);

// Consulta preparada para evitar inyecciones SQL
$revisar = "SELECT temporada.*, tab_clasf.* FROM temporada 
            INNER JOIN tab_clasf ON temporada.id_temp = tab_clasf.id_temp 
            WHERE tab_clasf.id_tab = ?";
$stmt = mysqli_prepare($con, $revisar);
mysqli_stmt_bind_param($stmt, "i", $id_tab);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_array($result);

if (!$data) {
    die("No se encontraron datos para el equipo especificado.");
}

$id_temp = $data['id_temp'];
$cat = $data['categoria'];
$varimpar = $data['nequipos'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Crear Partido - Sistema Baseball</title>
    
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
            max-width: 800px;
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
        }
        
        .btn-success:hover {
            background: linear-gradient(90deg, #146c43 0%, #0f5132 100%);
            transform: translateY(-2px);
        }
        
        .btn-info {
            background: linear-gradient(90deg, var(--info-color) 0%, #0aa2c0 100%);
            border: none;
        }
        
        .btn-info:hover {
            background: linear-gradient(90deg, #0aa2c0 0%, #08819c 100%);
            transform: translateY(-2px);
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
        
        .resultado-box {
            background-color: #f8f9fa;
            border-radius: 6px;
            padding: 0.75rem;
            text-align: center;
            font-weight: 500;
            min-height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .estado-ganando {
            color: #198754;
            font-weight: bold;
        }
        
        .estado-perdido {
            color: #dc3545;
            font-weight: bold;
        }
        
        .estado-empatado {
            color: #6c757d;
            font-weight: bold;
        }
        
        .radio-group {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        
        .form-check-input {
            width: 1.2em;
            height: 1.2em;
            margin-right: 0.5rem;
        }
        
        .form-check-label {
            display: flex;
            align-items: center;
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
                <h1 class="page-title">Crear Partido</h1>
                <h4 class="card-title"><?php echo htmlspecialchars($data['name_team']); ?> - Partido N° <?php echo $nj; ?></h4>
                
                <form method="POST" action="partcreate.php">
                    <input type="hidden" name="id_tab" value="<?php echo $id_tab; ?>">
                    <input type="hidden" name="id_temp" value="<?php echo $id_temp; ?>">
                    <input type="hidden" name="anfitrion" value="<?php echo $data['id_team']; ?>">
                    <input type="hidden" name="nj" value="<?php echo $nj; ?>">
                    <input type="hidden" name="cat" value="<?php echo htmlspecialchars($cat); ?>">

                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label text-end">JJ</label>
                        <div class="col-sm-2">
                            <input type="number" min="1" value="1" required class="form-control" name="jj">
                        </div>
                        <label class="col-sm-3 col-form-label text-end">Fecha</label>
                        <div class="col-sm-4">
                            <input type="date" required class="form-control" name="fech_part">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label text-end">Equipo N° 1</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control-plaintext" value="<?php echo htmlspecialchars($data['name_team']); ?>" name="team_one" readonly>
                        </div>
                        <label class="col-sm-1 col-form-label">CA</label>
                        <div class="col-sm-2">
                            <input type="number" class="form-control" id="ca" min="0" value="0" name="ca">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label text-end">Equipo N° 2</label>
                        <div class="col-sm-5">
                            <select class="form-select" required name="team_two">
                                <option disabled selected value="">Equipo Contrario</option>
                                <?php 
                                // Consulta preparada para obtener equipos disponibles
                                $team = "SELECT * FROM tab_clasf 
                                        WHERE NOT EXISTS ( 
                                            SELECT 1 FROM juegos
                                            WHERE tab_clasf.id_team = juegos.team_one
                                            AND tab_clasf.id_temp = juegos.id_temp
                                            AND juegos.nj = ?
                                        )
                                        AND tab_clasf.id_temp = ? 
                                        AND tab_clasf.id_tab != ?";
                                
                                $stmt_teams = mysqli_prepare($con, $team);
                                mysqli_stmt_bind_param($stmt_teams, "iii", $nj, $id_temp, $id_tab);
                                mysqli_stmt_execute($stmt_teams);
                                $obte = mysqli_stmt_get_result($stmt_teams);
                                
                                while ($dte = mysqli_fetch_array($obte)) {
                                    echo '<option value="' . $dte['id_team'] . '">' . htmlspecialchars($dte['name_team']) . '</option>';
                                }
                                
                                if ($varimpar % 2 != 0) {
                                    echo '<option value="0">LIBRE</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <label class="col-sm-1 col-form-label">CE</label>
                        <div class="col-sm-2">
                            <input type="number" class="form-control" id="ce" min="0" value="0" name="ce">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label text-end">Estado</label>
                        <div class="col-sm-5">
                            <div class="resultado-box" id="estado-container">
                                <input type="hidden" name="estado" id="estado-input" value="Empatado">
                                <span id="estado-texto" class="estado-empatado">Empatado</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label text-end">Válido</label>
                        <div class="col-sm-6">
                            <div class="radio-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="val" id="validoSi" value="1" checked>
                                    <label class="form-check-label" for="validoSi">Sí</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="val" id="validoNo" value="0">
                                    <label class="form-check-label" for="validoNo">No</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="Guardar partido">
                            <i class="fas fa-save me-2"></i>Guardar
                        </button>
                        <a href="datos.php?id_tab=<?php echo $id_tab; ?>&nj=<?php echo $nj; ?>" class="btn btn-info" data-bs-toggle="tooltip" data-bs-placement="top" title="Volver sin guardar">
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
        
        // Script para actualizar el estado del partido en tiempo real
        $(document).ready(function(){
            // Función para actualizar el estado
            function actualizarEstado() {
                const ca = $("#ca").val();
                const ce = $("#ce").val();
                
                // Si ambos campos están vacíos, mantener el estado por defecto
                if (ca === '' && ce === '') {
                    return;
                }
                
                // Hacer la petición AJAX a resultado.php
                $.get('resultado.php', 
                    { 
                        'ce': ce, 
                        'ca': ca
                    }, 
                    function(data) {
                        // Extraer solo el valor del estado del HTML devuelto
                        const estadoMatch = data.match(/value='([^']+)'/);
                        if (estadoMatch && estadoMatch[1]) {
                            const estado = estadoMatch[1];
                            $("#estado-input").val(estado);
                            
                            // Actualizar el texto visual con clases CSS apropiadas
                            const estadoTexto = $("#estado-texto");
                            estadoTexto.text(estado);
                            
                            // Aplicar clase según el estado
                            estadoTexto.removeClass("estado-ganando estado-perdido estado-empatado");
                            if (estado === "Ganando") {
                                estadoTexto.addClass("estado-ganando");
                            } else if (estado === "Perdido") {
                                estadoTexto.addClass("estado-perdido");
                            } else {
                                estadoTexto.addClass("estado-empatado");
                            }
                        }
                    }
                ).fail(function() {
                    console.error("Error al cargar resultado.php");
                });
            }
            
            // Escuchar cambios en los campos CA y CE
            $("#ca, #ce").on('input', actualizarEstado);
            
            // Inicializar tooltips de Bootstrap
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        });
    </script>
</body>
</html>