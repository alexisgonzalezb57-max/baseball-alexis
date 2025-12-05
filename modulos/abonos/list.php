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
    <title>Abonos por Equipo - Sistema Baseball</title>
    
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
        
        .btn {
            border-radius: 6px;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s;
            margin-bottom: 1rem;
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
        
        .table-container {
            margin-top: 2rem;
            overflow: hidden;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
        
        .table-header {
            background: linear-gradient(90deg, var(--primary-color) 0%, #0a58ca 100%);
            color: white;
            padding: 1rem;
            text-align: center;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }
        
        th {
            background: #f8f9fa;
            color: var(--dark-color);
            padding: 12px;
            text-align: center;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        td {
            padding: 12px;
            text-align: center;
            vertical-align: middle;
            border-bottom: 1px solid #dee2e6;
        }
        
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        tr:hover {
            background-color: #e9ecef;
        }
        
        .team-name {
            font-weight: 600;
            color: var(--dark-color);
            text-align: left;
        }
        
        .abono-cell {
            min-width: 80px;
            font-weight: 500;
        }
        
        .total-cell {
            font-weight: 700;
            background-color: #e9ecef;
            color: var(--dark-color);
        }
        
        .grand-total {
            font-weight: 800;
            background: linear-gradient(90deg, var(--secondary-color) 0%, #fd9843 100%);
            color: white;
        }
        
        .empty-abono {
            color: #6c757d;
            font-style: italic;
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-bottom: 1.5rem;
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
            
            th, td {
                padding: 8px 5px;
                font-size: 0.9rem;
            }
            
            .action-buttons {
                flex-direction: column;
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
            
            table {
                display: block;
                overflow-x: auto;
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
                <h1 class="page-title">💰 Abonos por Equipo</h1>

                <!-- Filtros por Categoría -->
                <div class="filter-buttons mb-4 justify-content-center">
                    <a href="list.php?idn=<?php echo $idn; ?>&id=<?php echo $id; ?>&cat=B" class="btn btn-sm filter-btn <?php echo ($cat == 'B') ? 'btn-primary active' : 'btn-outline-primary'; ?>">
                        Categoría B
                    </a>
                    <a href="list.php?idn=<?php echo $idn; ?>&id=<?php echo $id; ?>&cat=C" class="btn btn-sm filter-btn <?php echo ($cat == 'C') ? 'btn-primary active' : 'btn-outline-primary'; ?>">
                        Categoría C
                    </a>
                    <a href="list.php?idn=<?php echo $idn; ?>&id=<?php echo $id; ?>&cat=D" class="btn btn-sm filter-btn <?php echo ($cat == 'D') ? 'btn-primary active' : 'btn-outline-primary'; ?>">
                        Categoría D
                    </a>
                </div>
                
                <div class="text-center mb-4">
                    <div class="info-badge mb-2">
                        <i class="fas fa-info-circle me-1"></i>
                        Temporada: <?php echo htmlspecialchars($datatp['name_temp']); ?> 
                        | Categoría: <?php echo htmlspecialchars($cat); ?>
                        | Abonos: <?php echo $nabono; ?>
                    </div>
                </div>

                <div class="action-buttons">
                    <a href="abonar.php?idn=<?php echo $idn; ?>&id=<?php echo $id; ?>&cat=<?php echo urlencode($cat); ?>" class="btn btn-info" data-bs-toggle="tooltip" data-bs-placement="top" title="Añadir nuevos abonos">
                        <i class="fas fa-plus-circle me-2"></i>Añadir Abonos
                    </a>
                    
                    <a href="../abonos/" class="btn btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="Volver al listado de abonos">
                        <i class="fas fa-arrow-left me-2"></i>Volver
                    </a>
                </div>

                <div class="table-container">
                    <div class="table-header">
                        <h5 class="mb-0"><i class="fas fa-table me-2"></i>Detalle de Abonos por Equipo</h5>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Equipo</th>
                                    <?php for ($i = 1; $i <= $nabono; $i++) { ?>
                                        <th>AB-<?php echo $i; ?></th>
                                    <?php } ?>
                                    <th class="total-cell">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $revisar = "SELECT * FROM tab_clasf WHERE id_temp = $id AND categoria LIKE '%$cat%'";
                                $ryque = mysqli_query($con, $revisar);
                                $nunum = mysqli_num_rows($ryque);

                                if ($nunum >= 1) {
                                    while ($bdata = mysqli_fetch_array($ryque)) {
                                        $nmmm = $bdata['name_team'];
                                        $nttm = $bdata['id_team'];
                                ?>
                                <tr>
                                    <td class="team-name"><?php echo htmlspecialchars($nmmm); ?></td>
                                    <?php 
                                    $suma_montos = 0;
                                    for ($j = 1; $j <= $nabono; $j++) { 
                                        $njn = "SELECT monto FROM monto WHERE id_abn = $idn AND id_team = $nttm AND numero = $j";
                                        $qtt = mysqli_query($con, $njn);
                                        $dop = mysqli_fetch_array($qtt);
                                        $monto_actual = isset($dop['monto']) ? $dop['monto'] : 0;
                                        $suma_montos += $monto_actual;
                                    ?>
                                    <td class="abono-cell">
                                        <?php if ($monto_actual > 0): ?>
                                            <?php echo number_format($monto_actual, 2); ?>
                                        <?php else: ?>
                                            <span class="empty-abono">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <?php } ?>
                                    <td class="total-cell"><?php echo number_format($suma_montos, 2); ?></td>
                                </tr>
                                <?php 
                                    }
                                } else {
                                    echo '<tr><td colspan="' . ($nabono + 2) . '" class="text-center py-4"><i class="fas fa-exclamation-circle me-2"></i>No hay equipos registrados para esta temporada y categoría</td></tr>';
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="total-cell"><strong>Total General</strong></td>
                                    <?php 
                                    // Calcular totales por columna
                                    for ($j = 1; $j <= $nabono; $j++) {
                                        $total_columna = 0;
                                        $revisar_equipos = "SELECT id_team FROM tab_clasf WHERE id_temp = $id AND categoria LIKE '%$cat%'";
                                        $query_equipos = mysqli_query($con, $revisar_equipos);
                                        
                                        while ($equipo = mysqli_fetch_array($query_equipos)) {
                                            $nttm = $equipo['id_team'];
                                            $njn = "SELECT monto FROM monto WHERE id_abn = $idn AND id_team = $nttm AND numero = $j";
                                            $qtt = mysqli_query($con, $njn);
                                            $dop = mysqli_fetch_array($qtt);
                                            $monto_actual = isset($dop['monto']) ? $dop['monto'] : 0;
                                            $total_columna += $monto_actual;
                                        }
                                    ?>
                                    <td class="total-cell"><?php echo number_format($total_columna, 2); ?></td>
                                    <?php } ?>
                                    
                                    <?php
                                    $totalmente = "SELECT SUM(monto) AS total_final
                                    FROM monto
                                    WHERE id_abn = $idn AND id_temp = $id AND categoria LIKE '%$cat%'";
                                    $trata = mysqli_query($con, $totalmente);
                                    $datatol = mysqli_fetch_array($trata);
                                    $total_general = isset($datatol['total_final']) ? $datatol['total_final'] : 0;
                                    ?>
                                    <td class="grand-total"><?php echo number_format($total_general, 2); ?></td>
                                </tr>
                            </tfoot>
                        </table>
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