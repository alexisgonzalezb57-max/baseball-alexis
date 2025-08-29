<?php
include("../../config/conexion.php");
$con = conectar();

// Verificar conexión
if (!$con) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Obtener y sanitizar parámetros
$id = filter_var($_REQUEST['id'] ?? 0, FILTER_SANITIZE_NUMBER_INT);

// Consulta preparada para obtener datos de clasificación
$revisar = "SELECT temporada.*, tab_clasf.* FROM temporada 
            INNER JOIN tab_clasf ON temporada.id_temp = tab_clasf.id_temp 
            WHERE tab_clasf.id_tab = ?";
$stmt = mysqli_prepare($con, $revisar);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_array($result);

if (!$data) {
    die("No se encontraron datos para la clasificación especificada.");
}

$id_team = $data['id_team'];
$id_tab = $data['id_tab'];
$cat = $data['categoria'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resumen de Estadísticas - Sistema Baseball</title>
    
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
        
        .card-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--dark-color);
            text-align: center;
            margin-bottom: 1.5rem;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary-color);
        }
        
        .btn {
            border-radius: 6px;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s;
            margin: 0 5px 10px 5px;
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
        
        .btn-danger {
            background: linear-gradient(90deg, var(--danger-color) 0%, #a71e2a 100%);
            border: none;
            color: white;
        }
        
        .btn-danger:hover {
            background: linear-gradient(90deg, #a71e2a 0%, #8a1a24 100%);
            transform: translateY(-2px);
            color: white;
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
        
        .table-container {
            overflow-x: auto;
            margin-bottom: 2rem;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
        }
        
        th {
            background: linear-gradient(90deg, var(--primary-color) 0%, #0a58ca 100%);
            color: white;
            padding: 12px;
            text-align: center;
            font-weight: 600;
            position: sticky;
            top: 0;
        }
        
        td {
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #dee2e6;
        }
        
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        tr:hover {
            background-color: #e9ecef;
        }
        
        .alte {
            font-weight: 500;
        }
        
        .total-row {
            background-color: #e7f1ff !important;
            font-weight: 600;
        }
        
        .total-row td {
            border-top: 2px solid var(--primary-color);
        }
        
        .action-buttons {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
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
            
            .card-title {
                font-size: 1.2rem;
            }
            
            th, td {
                padding: 8px 5px;
                font-size: 0.9rem;
            }
            
            .action-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn {
                width: 100%;
                margin-bottom: 10px;
            }
        }
        
        @media (max-width: 576px) {
            th, td {
                padding: 6px 3px;
                font-size: 0.8rem;
            }
            
            .clock-container {
                display: none;
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
                <h1 class="page-title">Resumen de Estadísticas</h1>
                <h4 class="card-title">Temporada: <?php echo htmlspecialchars($data['name_temp']); ?><br><?php echo htmlspecialchars($data['name_team']); ?></h4>
                
                <div class="action-buttons">
                    <a href="../entradas/entradas.php?id=<?php echo $id; ?>">
                        <button type="button" class="btn btn-info" data-bs-toggle="tooltip" data-bs-placement="top" title="Ver tabla de puntaje total">
                            <i class="fas fa-table me-2"></i>Tabla de Puntaje Total
                        </button>
                    </a>
                    <a href="../PDF/resumendos.php?id=<?php echo $id; ?>" target="_blank">
                        <button type="button" class="btn btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Generar reporte en PDF">
                            <i class="fas fa-file-pdf me-2"></i>Reportes
                        </button>
                    </a>
                    <a href="../juego/list.php?id=<?php echo $data['id_temp']; ?>&cat=<?php echo htmlspecialchars($cat); ?>">
                        <button type="button" class="btn btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="Volver a la lista de temporadas">
                            <i class="fas fa-arrow-left me-2"></i>Volver
                        </button>
                    </a>
                </div>

                <!-- Resumen de Jugadores -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">RESUMEN - Jugadores</h5>
                        <div class="table-container">
                            <table class="table table-hover table-sm table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Jugador</th>
                                        <th>VB</th>
                                        <th>H</th>
                                        <th>HR</th>
                                        <th>2B</th>
                                        <th>3B</th>
                                        <th>CA</th>
                                        <th>CI</th>
                                        <th>K</th>
                                        <th>B</th>
                                        <th>AS</th>
                                        <th>TVB</th>
                                        <th>TH</th>
                                        <th>AVG</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Consulta preparada para obtener estadísticas de jugadores
                                    $cons = "SELECT * FROM resumen_stats WHERE id_team = ? AND id_tab = ?";
                                    $stmt = mysqli_prepare($con, $cons);
                                    mysqli_stmt_bind_param($stmt, "ii", $id_team, $id_tab);
                                    mysqli_stmt_execute($stmt);
                                    $result = mysqli_stmt_get_result($stmt);
                                    
                                    $jg = 1;
                                    while ($player = mysqli_fetch_array($result)) {
                                    ?>
                                    <tr>
                                        <td class="alte"><?php echo $jg++; ?></td>
                                        <td class="alte" style="text-align: left; word-break: normal"><?php echo htmlspecialchars($player['name_jgstats']); ?></td>
                                        <td class="alte"><?php echo $player['vb']; ?></td>
                                        <td class="alte"><?php echo $player['h']; ?></td>
                                        <td class="alte"><?php echo $player['hr']; ?></td>
                                        <td class="alte"><?php echo $player['2b']; ?></td>
                                        <td class="alte"><?php echo $player['3b']; ?></td>
                                        <td class="alte"><?php echo $player['ca']; ?></td>
                                        <td class="alte"><?php echo $player['ci']; ?></td>
                                        <td class="alte"><?php echo $player['k']; ?></td>
                                        <td class="alte"><?php echo $player['b']; ?></td>
                                        <td class="alte"><?php echo $player['a']; ?></td>
                                        <td class="alte"><?php echo $player['tvb']; ?></td>
                                        <td class="alte"><?php echo $player['th']; ?></td>
                                        <td class="alte"><?php echo $player['avg']; ?></td>
                                    </tr>
                                    <?php } ?>
                                    
                                    <?php
                                    // Consulta para totales
                                    $ftor = "SELECT 
                                        SUM(vb) AS tvb,
                                        SUM(h) AS th,
                                        SUM(hr) AS thr,
                                        SUM(2b) AS t2b,
                                        SUM(3b) AS t3b,
                                        SUM(ca) AS tca,
                                        SUM(ci) AS tci,
                                        SUM(k) AS tk,
                                        SUM(b) AS tb,
                                        SUM(a) AS ta,
                                        SUM(tvb) AS ttvb,
                                        SUM(th) AS tthh 
                                        FROM resumen_stats 
                                        WHERE id_team = ? AND id_tab = ?";
                                    
                                    $stmt = mysqli_prepare($con, $ftor);
                                    mysqli_stmt_bind_param($stmt, "ii", $id_team, $id_tab);
                                    mysqli_stmt_execute($stmt);
                                    $result = mysqli_stmt_get_result($stmt);
                                    
                                    if ($gapa = mysqli_fetch_array($result)) {
                                        $on_tvb = $gapa['tvb'];
                                        $tw_ttvb = $gapa['ttvb'];
                                        $dtvb = ($on_tvb == $tw_ttvb) ? $on_tvb : 1;
                                        
                                        $th = $gapa['th'];
                                        $thr = $gapa['thr'];
                                        $t2b = $gapa['t2b'];
                                        $t3b = $gapa['t3b'];
                                        $tth = $gapa['tthh'];
                                        
                                        $on_th = $th + $thr + $t2b + $t3b;
                                        $dth = ($on_th == $tth) ? $on_th : 1;
                                        
                                        $avg = ($dth > 0 && $dtvb > 0) ? round(($dth * 1000) / $dtvb) : 0;
                                    ?>
                                    <tr class="total-row">
                                        <td colspan="2" class="alte"><strong>TOTAL</strong></td>
                                        <td class="alte"><strong><?php echo $gapa['tvb']; ?></strong></td>
                                        <td class="alte"><strong><?php echo $gapa['th']; ?></strong></td>
                                        <td class="alte"><strong><?php echo $gapa['thr']; ?></strong></td>
                                        <td class="alte"><strong><?php echo $gapa['t2b']; ?></strong></td>
                                        <td class="alte"><strong><?php echo $gapa['t3b']; ?></strong></td>
                                        <td class="alte"><strong><?php echo $gapa['tca']; ?></strong></td>
                                        <td class="alte"><strong><?php echo $gapa['tci']; ?></strong></td>
                                        <td class="alte"><strong><?php echo $gapa['tk']; ?></strong></td>
                                        <td class="alte"><strong><?php echo $gapa['tb']; ?></strong></td>
                                        <td class="alte"><strong><?php echo $gapa['ta']; ?></strong></td>
                                        <td class="alte"><strong><?php echo $dtvb; ?></strong></td>
                                        <td class="alte"><strong><?php echo $dth; ?></strong></td>
                                        <td class="alte"><strong><?php echo $avg; ?></strong></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Resumen de Lanzadores -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">RESUMEN - Lanzadores</h5>
                        <div class="table-container">
                            <table class="table table-hover table-sm table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Jugador</th>
                                        <th>TJL</th>
                                        <th>TJG</th>
                                        <th>AVG</th>
                                        <th>TIL</th>
                                        <th>TCPL</th>
                                        <th>EFEC</th>
                                        <th>H</th>
                                        <th>2B</th>
                                        <th>3B</th>
                                        <th>HR</th>
                                        <th>B</th>
                                        <th>K</th>
                                        <th>VA</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Consulta preparada para obtener estadísticas de lanzadores
                                    $cons = "SELECT * FROM resumen_lanz WHERE id_team = ? AND id_tab = ?";
                                    $stmt = mysqli_prepare($con, $cons);
                                    mysqli_stmt_bind_param($stmt, "ii", $id_team, $id_tab);
                                    mysqli_stmt_execute($stmt);
                                    $result = mysqli_stmt_get_result($stmt);
                                    
                                    $jg = 1;
                                    while ($player = mysqli_fetch_array($result)) {
                                    ?>
                                    <tr>
                                        <td class="alte"><?php echo $jg++; ?></td>
                                        <td class="alte" style="text-align: left;"><?php echo htmlspecialchars($player['name_jglz']); ?></td>
                                        <td class="alte"><?php echo $player['tjl']; ?></td>
                                        <td class="alte"><?php echo $player['tjg']; ?></td>
                                        <td class="alte"><?php echo $player['avg']; ?></td>
                                        <td class="alte"><?php echo $player['til']; ?></td>
                                        <td class="alte"><?php echo $player['tcpl']; ?></td>
                                        <td class="alte"><?php echo $player['efec']; ?></td>
                                        <td class="alte"><?php echo $player['h']; ?></td>
                                        <td class="alte"><?php echo $player['2b']; ?></td>
                                        <td class="alte"><?php echo $player['3b']; ?></td>
                                        <td class="alte"><?php echo $player['hr']; ?></td>
                                        <td class="alte"><?php echo $player['b']; ?></td>
                                        <td class="alte"><?php echo $player['k']; ?></td>
                                        <td class="alte"><?php echo $player['va']; ?></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
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
        
        // Inicializar tooltips de Bootstrap
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    </script>
</body>
</html>