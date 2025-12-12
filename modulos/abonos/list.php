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

// Función para determinar la moneda del abono
function determinarMonedaAbono($data_abono) {
    // Si no hay datos, retornar $ por defecto
    if (!$data_abono) {
        return '$';
    }
    
    // Primero, verificar si existe el campo tipo_moneda y tiene valor
    if (isset($data_abono['tipo_moneda']) && !empty($data_abono['tipo_moneda'])) {
        return $data_abono['tipo_moneda'] == 'Bs' ? 'Bs' : '$';
    }
    
    // Si no existe tipo_moneda, usar la lógica anterior basada en mond_*
    $monedas = array(
        isset($data_abono['mond_once']) ? $data_abono['mond_once'] : '$',
        isset($data_abono['mond_second']) ? $data_abono['mond_second'] : '$',
        isset($data_abono['mond_third']) ? $data_abono['mond_third'] : '$',
        isset($data_abono['mond_four']) ? $data_abono['mond_four'] : '$'
    );
    
    $premios_activos = array(
        isset($data_abono['prize_once']) ? $data_abono['prize_once'] : '0',
        isset($data_abono['prize_second']) ? $data_abono['prize_second'] : '0',
        isset($data_abono['prize_third']) ? $data_abono['prize_third'] : '0',
        isset($data_abono['prize_four']) ? $data_abono['prize_four'] : '0'
    );
    
    // 1. Buscar la moneda del primer premio activo
    for ($i = 0; $i < 4; $i++) {
        if ($premios_activos[$i] == '1') {
            return $monedas[$i] == 'Bs' ? 'Bs' : '$';
        }
    }
    
    // 2. Si no hay premios activos, contar monedas
    $count_dolares = 0;
    $count_bolivares = 0;
    
    foreach ($monedas as $moneda) {
        if ($moneda == 'Bs') {
            $count_bolivares++;
        } else {
            $count_dolares++;
        }
    }
    
    // 3. Usar la mayoría, o $ si hay empate o todos están vacíos
    if ($count_bolivares > 0 || $count_dolares > 0) {
        return ($count_bolivares > $count_dolares) ? 'Bs' : '$';
    }
    
    // 4. Si todo está vacío, usar $ por defecto
    return '$';
}

// Consultar información del abono
$revisar = "SELECT * FROM abonos WHERE id_abn = $idn AND categoria LIKE '%$cat%'";
$query = mysqli_query($con, $revisar);
$data = mysqli_fetch_array($query);

if (!$data) {
    die("Abono no encontrado");
}

$nabono = $data['ncantidad'];

// Determinar la moneda usando la función
$moneda_abono = determinarMonedaAbono($data);

// Obtener el valor actual de tipo_moneda si existe
$tipo_moneda_actual = isset($data['tipo_moneda']) ? $data['tipo_moneda'] : $moneda_abono;

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
            flex-wrap: wrap;
            align-items: center;
        }
        
        .moneda-select-container {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: #f8f9fa;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            border: 1px solid #dee2e6;
        }
        
        .moneda-select-label {
            font-weight: 500;
            color: #495057;
            white-space: nowrap;
            margin: 0;
        }
        
        .moneda-select {
            width: 120px;
            border-radius: 4px;
            border: 1px solid #ced4da;
            padding: 0.375rem 0.75rem;
            font-size: 0.9rem;
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
            padding: 0.25em 0.5em;
            font-size: 0.65em;
            font-weight: 600;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
            color: white;
        }
        
        .abono-actions {
            display: flex;
            gap: 5px;
            justify-content: center;
            margin-top: 3px;
        }
        
        .edit-link {
            color: var(--warning-color);
            text-decoration: none;
            font-size: 0.85em;
            padding: 2px 5px;
            border-radius: 3px;
            transition: all 0.2s;
        }
        
        .edit-link:hover {
            background-color: rgba(255, 193, 7, 0.1);
            color: #e0a800;
        }
        
        .monto-con-moneda {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 3px;
        }
        
        .simbolo-moneda {
            font-weight: bold;
            font-size: 0.9em;
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
            
            .abono-actions {
                flex-direction: column;
                gap: 2px;
            }
            
            .monto-con-moneda {
                flex-direction: column;
                gap: 1px;
            }
            
            .moneda-select-container {
                width: 100%;
                justify-content: center;
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
            
            .moneda-select {
                width: 100px;
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

                <!-- Información de la temporada -->
                <div class="text-center mb-4">
                    <div class="info-badge mb-2">
                        <i class="fas fa-info-circle me-1"></i>
                        Temporada: <?php echo htmlspecialchars($datatp['name_temp']); ?> 
                        | Categoría: <?php echo htmlspecialchars($cat); ?>
                        | Abonos: <?php echo $nabono; ?>
                        | Moneda: 
                        <span class="moneda-badge <?php echo $moneda_abono == '$' ? 'moneda-dolares' : 'moneda-bolivares'; ?>">
                            <?php echo $moneda_abono == '$' ? 'Dólares ($)' : 'Bolívares (Bs)'; ?>
                        </span>
                    </div>
                </div>

                <div class="action-buttons">
                    <a href="abonar.php?idn=<?php echo $idn; ?>&id=<?php echo $id; ?>&cat=<?php echo urlencode($cat); ?>" class="btn btn-info" data-bs-toggle="tooltip" data-bs-placement="top" title="Añadir nuevos abonos">
                        <i class="fas fa-plus-circle me-2"></i>Añadir Abonos
                    </a>
                    
                    <a href="../abonos/" class="btn btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="Volver al listado de abonos">
                        <i class="fas fa-arrow-left me-2"></i>Volver
                    </a>
                    
                    <button type="button" class="btn btn-warning" onclick="window.print()" data-bs-toggle="tooltip" data-bs-placement="top" title="Imprimir esta tabla">
                        <i class="fas fa-print me-2"></i>Imprimir
                    </button>
                </div>

                <div class="action-buttons">                    
                    <!-- Select de moneda al lado del botón imprimir -->
                    <div class="moneda-select-container">
                        <label class="moneda-select-label">
                            <i class="fas fa-coins me-1"></i>Moneda:
                        </label>
                        <select class="form-select moneda-select" id="selectMoneda" onchange="actualizarMoneda()">
                            <option value="$" <?php echo $tipo_moneda_actual == '$' ? 'selected' : ''; ?>>Dólares ($)</option>
                            <option value="Bs" <?php echo $tipo_moneda_actual == 'Bs' ? 'selected' : ''; ?>>Bolívares (Bs)</option>
                        </select>
                        <button style="margin-top: 15px;" type="button" class="btn btn-primary btn-sm" onclick="guardarMoneda()" id="btnGuardarMoneda">
                            <i class="fas fa-save"></i>
                        </button>
                    </div>
                </div>

                <div class="table-container">
                    <div class="table-header">
                        <h5 class="mb-0"><i class="fas fa-table me-2"></i>Detalle de Abonos por Equipo 
                            <span class="moneda-badge <?php echo $moneda_abono == '$' ? 'moneda-dolares' : 'moneda-bolivares'; ?>" id="badgeMonedaActual">
                                <?php echo $moneda_abono == '$' ? '$ Dólares' : 'Bs Bolívares'; ?>
                            </span>
                        </h5>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Equipo</th>
                                    <?php for ($i = 1; $i <= $nabono; $i++) { ?>
                                        <th>
                                            AB-<?php echo $i; ?>
                                            <?php 
                                            // Verificar si este abono tiene datos
                                            $verificar_abono = "SELECT COUNT(*) as total FROM monto 
                                                              WHERE id_abn = $idn 
                                                              AND id_temp = $id 
                                                              AND categoria LIKE '%$cat%'
                                                              AND numero = $i
                                                              AND monto > 0";
                                            $result_abono = mysqli_query($con, $verificar_abono);
                                            $row_abono = mysqli_fetch_assoc($result_abono);
                                            if ($row_abono['total'] > 0) {
                                                echo '<span class="abono-badge bg-success ms-1" style="font-size: 0.6em;">✓</span>';
                                            }
                                            ?>
                                        </th>
                                    <?php } ?>
                                    <th class="total-cell">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $revisar = "SELECT * FROM tab_clasf WHERE id_temp = $id AND categoria LIKE '%$cat%' ORDER BY name_team ASC";
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
                                            <div class="monto-con-moneda">
                                                <span class="simbolo-moneda" id="simbolo-<?php echo $nttm; ?>-<?php echo $j; ?>"><?php echo $moneda_abono; ?></span>
                                                <span class="monto-value"><?php echo number_format($monto_actual, 2); ?></span>
                                            </div>
                                            <div class="abono-actions">
                                                <a href="abonar.php?idn=<?php echo $idn; ?>&id=<?php echo $id; ?>&cat=<?php echo urlencode($cat); ?>&abono=<?php echo $j; ?>" 
                                                   class="edit-link" 
                                                   title="Modificar este abono">
                                                    <i class="fas fa-edit"></i> Editar
                                                </a>
                                            </div>
                                        <?php else: ?>
                                            <span class="empty-abono">-</span>
                                            <div class="abono-actions">
                                                <a href="abonar.php?idn=<?php echo $idn; ?>&id=<?php echo $id; ?>&cat=<?php echo urlencode($cat); ?>&abono=<?php echo $j; ?>" 
                                                   class="edit-link" 
                                                   title="Agregar monto para este abono">
                                                    <i class="fas fa-plus"></i> Agregar
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <?php } ?>
                                    <td class="total-cell">
                                        <div class="monto-con-moneda">
                                            <span class="simbolo-moneda" id="simbolo-total-<?php echo $nttm; ?>"><?php echo $moneda_abono; ?></span>
                                            <strong><?php echo number_format($suma_montos, 2); ?></strong>
                                        </div>
                                        <?php if ($suma_montos > 0): ?>
                                            <div class="abono-actions">
                                                <a href="abonar.php?idn=<?php echo $idn; ?>&id=<?php echo $id; ?>&cat=<?php echo urlencode($cat); ?>" 
                                                   class="edit-link" 
                                                   title="Ver/Editar todos los abonos de este equipo">
                                                    <i class="fas fa-list"></i> Ver todos
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </td>
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
                                    <td class="total-cell">
                                        <strong>Total General</strong>
                                    </td>
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
                                    <td class="total-cell">
                                        <div class="monto-con-moneda">
                                            <span class="simbolo-moneda" id="simbolo-columna-<?php echo $j; ?>"><?php echo $moneda_abono; ?></span>
                                            <strong><?php echo number_format($total_columna, 2); ?></strong>
                                        </div>
                                        <div class="abono-actions">
                                            <a href="abonar.php?idn=<?php echo $idn; ?>&id=<?php echo $id; ?>&cat=<?php echo urlencode($cat); ?>&abono=<?php echo $j; ?>" 
                                               class="edit-link" 
                                               title="Editar todos los montos de este abono">
                                                <i class="fas fa-edit"></i> Editar columna
                                            </a>
                                        </div>
                                    </td>
                                    <?php } ?>
                                    
                                    <?php
                                    $totalmente = "SELECT SUM(monto) AS total_final
                                    FROM monto
                                    WHERE id_abn = $idn AND id_temp = $id AND categoria LIKE '%$cat%'";
                                    $trata = mysqli_query($con, $totalmente);
                                    $datatol = mysqli_fetch_array($trata);
                                    $total_general = isset($datatol['total_final']) ? $datatol['total_final'] : 0;
                                    ?>
                                    <td class="grand-total">
                                        <div class="monto-con-moneda">
                                            <span class="simbolo-moneda" id="simbolo-total-general"><?php echo $moneda_abono; ?></span>
                                            <strong><?php echo number_format($total_general, 2); ?></strong>
                                        </div>
                                        <div class="abono-actions">
                                            <span style="color: white; font-size: 0.8em;">
                                                <i class="fas fa-chart-line"></i> Total general
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Resumen de abonos -->
                <div class="mt-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Resumen de Abonos</h6>
                                </div>
                                <div class="card-body">
                                    <?php 
                                    // Contar abonos con datos
                                    $abonos_con_datos = 0;
                                    $total_abonos = 0;
                                    for ($j = 1; $j <= $nabono; $j++) {
                                        $verificar_abono = "SELECT COUNT(*) as total FROM monto 
                                                          WHERE id_abn = $idn 
                                                          AND id_temp = $id 
                                                          AND categoria LIKE '%$cat%'
                                                          AND numero = $j
                                                          AND monto > 0";
                                        $result_abono = mysqli_query($con, $verificar_abono);
                                        $row_abono = mysqli_fetch_assoc($result_abono);
                                        if ($row_abono['total'] > 0) {
                                            $abonos_con_datos++;
                                        }
                                        $total_abonos++;
                                    }
                                    
                                    // Contar equipos con al menos un abono
                                    $verificar_equipos = "SELECT COUNT(DISTINCT id_team) as total FROM monto 
                                                        WHERE id_abn = $idn 
                                                        AND id_temp = $id 
                                                        AND categoria LIKE '%$cat%'
                                                        AND monto > 0";
                                    $result_equipos = mysqli_query($con, $verificar_equipos);
                                    $row_equipos = mysqli_fetch_assoc($result_equipos);
                                    $equipos_con_abono = $row_equipos['total'];
                                    ?>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Abonos configurados
                                            <span class="badge bg-primary rounded-pill"><?php echo $nabono; ?></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Abonos con datos
                                            <span class="badge bg-success rounded-pill"><?php echo $abonos_con_datos; ?></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Abonos pendientes
                                            <span class="badge bg-warning rounded-pill"><?php echo $nabono - $abonos_con_datos; ?></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Equipos con abono
                                            <span class="badge bg-info rounded-pill"><?php echo $equipos_con_abono; ?> / <?php echo $nunum; ?></span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="fas fa-dollar-sign me-2"></i>Resumen Financiero 
                                        <span class="moneda-badge <?php echo $moneda_abono == '$' ? 'moneda-dolares' : 'moneda-bolivares'; ?>" id="badgeMonedaResumen" style="font-size: 0.7em;">
                                            <?php echo $moneda_abono; ?>
                                        </span>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <?php
                                    // Obtener promedio por equipo
                                    $promedio_equipo = $nunum > 0 ? $total_general / $nunum : 0;
                                    
                                    // Obtener promedio por abono
                                    $promedio_abono = $nabono > 0 ? $total_general / $nabono : 0;
                                    
                                    // Obtener monto máximo y mínimo por equipo
                                    $max_min_query = "SELECT 
                                        MAX(total_equipo) as max_equipo,
                                        MIN(total_equipo) as min_equipo
                                        FROM (
                                            SELECT id_team, SUM(monto) as total_equipo
                                            FROM monto
                                            WHERE id_abn = $idn 
                                            AND id_temp = $id 
                                            AND categoria LIKE '%$cat%'
                                            GROUP BY id_team
                                        ) as equipo_totals";
                                    $result_max_min = mysqli_query($con, $max_min_query);
                                    $row_max_min = mysqli_fetch_assoc($result_max_min);
                                    $max_equipo = $row_max_min['max_equipo'] ?? 0;
                                    $min_equipo = $row_max_min['min_equipo'] ?? 0;
                                    ?>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Total recaudado
                                            <span class="badge bg-success rounded-pill" id="total-recaudado">
                                                <?php echo $moneda_abono; ?><?php echo number_format($total_general, 2); ?>
                                            </span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Promedio por equipo
                                            <span class="badge bg-info rounded-pill" id="promedio-equipo">
                                                <?php echo $moneda_abono; ?><?php echo number_format($promedio_equipo, 2); ?>
                                            </span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Promedio por abono
                                            <span class="badge bg-primary rounded-pill" id="promedio-abono">
                                                <?php echo $moneda_abono; ?><?php echo number_format($promedio_abono, 2); ?>
                                            </span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Mayor aporte (equipo)
                                            <span class="badge bg-warning rounded-pill" id="mayor-aporte">
                                                <?php echo $moneda_abono; ?><?php echo number_format($max_equipo, 2); ?>
                                            </span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Menor aporte (equipo)
                                            <span class="badge bg-secondary rounded-pill" id="menor-aporte">
                                                <?php echo $moneda_abono; ?><?php echo number_format($min_equipo, 2); ?>
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
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
        
        // Variable para moneda seleccionada
        let monedaSeleccionada = '<?php echo $tipo_moneda_actual; ?>';
        
        // Función para actualizar visualmente la moneda en la página
        function actualizarMoneda() {
            const selectMoneda = document.getElementById('selectMoneda');
            const nuevaMoneda = selectMoneda.value;
            const monedaTexto = nuevaMoneda == '$' ? 'Dólares ($)' : 'Bolívares (Bs)';
            const badgeClase = nuevaMoneda == '$' ? 'moneda-dolares' : 'moneda-bolivares';
            
            // Actualizar badge en el encabezado de la tabla
            const badgeMonedaActual = document.getElementById('badgeMonedaActual');
            badgeMonedaActual.textContent = nuevaMoneda == '$' ? '$ Dólares' : 'Bs Bolívares';
            badgeMonedaActual.className = 'moneda-badge ' + badgeClase;
            
            // Actualizar badge en el resumen
            const badgeMonedaResumen = document.getElementById('badgeMonedaResumen');
            badgeMonedaResumen.textContent = nuevaMoneda;
            badgeMonedaResumen.className = 'moneda-badge ' + badgeClase;
            
            // Actualizar símbolos de moneda en toda la tabla
            document.querySelectorAll('.simbolo-moneda').forEach(function(element) {
                element.textContent = nuevaMoneda;
            });
            
            // Actualizar símbolos en los badges del resumen financiero
            document.querySelectorAll('#total-recaudado, #promedio-equipo, #promedio-abono, #mayor-aporte, #menor-aporte').forEach(function(element) {
                const textoActual = element.textContent;
                const nuevoTexto = nuevaMoneda + textoActual.substring(1);
                element.textContent = nuevoTexto;
            });
            
            // Habilitar botón de guardar si la moneda cambió
            const btnGuardarMoneda = document.getElementById('btnGuardarMoneda');
            if (nuevaMoneda !== monedaSeleccionada) {
                btnGuardarMoneda.disabled = false;
                btnGuardarMoneda.classList.remove('btn-primary');
                btnGuardarMoneda.classList.add('btn-success');
            } else {
                btnGuardarMoneda.disabled = true;
                btnGuardarMoneda.classList.remove('btn-success');
                btnGuardarMoneda.classList.add('btn-primary');
            }
        }
        
        // Función para guardar la moneda en la base de datos
        function guardarMoneda() {
            const selectMoneda = document.getElementById('selectMoneda');
            const nuevaMoneda = selectMoneda.value;
            const btnGuardarMoneda = document.getElementById('btnGuardarMoneda');
            
            // Mostrar indicador de carga
            const textoOriginal = btnGuardarMoneda.innerHTML;
            btnGuardarMoneda.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            btnGuardarMoneda.disabled = true;
            
            // Enviar petición AJAX
            $.ajax({
                url: 'guardar_moneda.php',
                type: 'POST',
                data: {
                    id_abn: <?php echo $idn; ?>,
                    tipo_moneda: nuevaMoneda
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Actualizar variable local
                        monedaSeleccionada = nuevaMoneda;
                        
                        // Restaurar botón
                        btnGuardarMoneda.innerHTML = '<i class="fas fa-check"></i>';
                        btnGuardarMoneda.classList.remove('btn-success');
                        btnGuardarMoneda.classList.add('btn-primary');
                        
                        // Mostrar mensaje de éxito
                        alert('Moneda actualizada correctamente a ' + (nuevaMoneda == '$' ? 'Dólares ($)' : 'Bolívares (Bs)'));
                        
                        // Después de 2 segundos, restaurar ícono original
                        setTimeout(function() {
                            btnGuardarMoneda.innerHTML = '<i class="fas fa-save"></i>';
                        }, 2000);
                    } else {
                        alert('Error al guardar la moneda: ' + response.error);
                        btnGuardarMoneda.innerHTML = textoOriginal;
                        btnGuardarMoneda.disabled = false;
                    }
                },
                error: function() {
                    alert('Error de conexión al guardar la moneda');
                    btnGuardarMoneda.innerHTML = textoOriginal;
                    btnGuardarMoneda.disabled = false;
                }
            });
        }
        
        // Verificar si el campo tipo_moneda existe en la base de datos
        function verificarCampoTipoMoneda() {
            $.ajax({
                url: 'verificar_moneda.php',
                type: 'GET',
                data: {
                    id_abn: <?php echo $idn; ?>
                },
                dataType: 'json',
                success: function(response) {
                    if (!response.campo_existe) {
                        // Si el campo no existe, mostrar advertencia
                        console.warn('El campo tipo_moneda no existe en la tabla abonos. Debes agregarlo manualmente.');
                        // Opcional: mostrar mensaje al usuario
                        // alert('Nota: El campo tipo_moneda no existe en la base de datos. Contacta al administrador.');
                    }
                }
            });
        }
        
        // Llamar a la verificación al cargar la página
        $(document).ready(function() {
            verificarCampoTipoMoneda();
        });
    </script>
</body>
</html>