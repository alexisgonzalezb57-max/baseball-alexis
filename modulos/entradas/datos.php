<?php
include("../../config/conexion.php");

// Validar y sanitizar parámetros de entrada
$id_tab = isset($_REQUEST['id_tab']) ? intval($_REQUEST['id_tab']) : 0;
$nj = isset($_REQUEST['nj']) ? intval($_REQUEST['nj']) : 0;

// Verificar que los parámetros requeridos estén presentes
if ($id_tab <= 0 || $nj <= 0) {
    die("Parámetros inválidos");
}

$con = conectar();

// Verificar conexión
if (!$con) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Consulta preparada para prevenir inyecciones SQL
$revisar = "SELECT temporada.*, tab_clasf.* 
            FROM temporada 
            INNER JOIN tab_clasf ON temporada.id_temp = tab_clasf.id_temp 
            WHERE tab_clasf.id_tab = ?";
$stmt = mysqli_prepare($con, $revisar);
mysqli_stmt_bind_param($stmt, "i", $id_tab);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_array($result);

// Verificar que se obtuvieron datos
if (!$data) {
    die("No se encontraron datos para los parámetros proporcionados");
}

$idtemp = $data['id_temp'];
$idteam = $data['id_team'];
$cat = $data['categoria'];

// Consulta para obtener información del juego
$teamss = "SELECT * FROM juegos 
           WHERE nj = ? AND id_tab = ? AND id_temp = ? AND team_one = ?";
$stmt2 = mysqli_prepare($con, $teamss);
mysqli_stmt_bind_param($stmt2, "iiii", $nj, $id_tab, $idtemp, $idteam);
mysqli_stmt_execute($stmt2);
$result2 = mysqli_stmt_get_result($stmt2);
$dte = mysqli_fetch_array($result2);

$id_juego = isset($dte['id_juego']) ? $dte['id_juego'] : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detalles del Juego - Sistema Baseball</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 15px;
        }
        
        .page-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
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
        }
        
        .btn-primary {
            background: linear-gradient(90deg, var(--primary-color) 0%, #0a58ca 100%);
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(90deg, #0a58ca 0%, #084298 100%);
            transform: translateY(-2px);
        }
        
        .btn-info {
            background: linear-gradient(90deg, #0dcaf0 0%, #0aa2c0 100%);
            border: none;
        }
        
        .btn-info:hover {
            background: linear-gradient(90deg, #0aa2c0 0%, #08819c 100%);
            transform: translateY(-2px);
        }
        
        .btn-warning {
            background: linear-gradient(90deg, var(--warning-color) 0%, #ffb507 100%);
            border: none;
        }
        
        .btn-warning:hover {
            background: linear-gradient(90deg, #ffb507 0%, #e6a500 100%);
            transform: translateY(-2px);
        }
        
        .btn-danger {
            background: linear-gradient(90deg, var(--danger-color) 0%, #c82333 100%);
            border: none;
        }
        
        .btn-danger:hover {
            background: linear-gradient(90deg, #c82333 0%, #a71e2a 100%);
            transform: translateY(-2px);
        }
        
        .btn-success {
            background: linear-gradient(90deg, var(--success-color) 0%, #146c43 100%);
            border: none;
        }
        
        .btn-success:hover {
            background: linear-gradient(90deg, #146c43 0%, #0f5132 100%);
            transform: translateY(-2px);
        }
        
        .table-container {
            overflow-x: auto;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-top: 1.5rem;
        }
        
        .table th {
            background: linear-gradient(90deg, var(--primary-color) 0%, #0a58ca 100%);
            color: white;
            border: none;
            padding: 1rem;
            text-align: center;
            vertical-align: middle;
        }
        
        .table td {
            padding: 1rem;
            vertical-align: middle;
            border-color: #e9ecef;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
        }
        
        .action-btn {
            padding: 0.5rem;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #dee2e6;
        }
        
        .team-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .team-link:hover {
            color: #0a58ca;
            text-decoration: underline;
        }
        
        .button-group {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }
        
        .card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            padding: 2rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }
        
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        }
        
        .card-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 10px;
        }
        
        .card-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: var(--secondary-color);
            border-radius: 2px;
        }
        
        .thead-dark th {
            background: linear-gradient(90deg, var(--dark-color) 0%, #343a40 100%);
            color: white;
        }
        
        .alte {
            font-weight: 500;
        }
        
        @media (max-width: 992px) {
            .navigation {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .button-group {
                flex-direction: column;
            }
        }
        
        @media (max-width: 768px) {
            .page-title {
                font-size: 1.8rem;
            }
            
            .clock {
                font-size: 1rem;
            }
            
            .content-container {
                padding: 1.5rem;
            }
            
            .table th, .table td {
                padding: 0.75rem;
            }
            
            .button-group {
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
                <h1 class="page-title">Detalles del Juego: <?php echo htmlspecialchars($data['name_temp']); ?></h1>
                
                <div class="button-group">
                    <a href="entradas.php?id=<?php echo $id_tab; ?>" class="btn btn-info">
                        <i class="fas fa-arrow-left me-2"></i>Volver
                    </a>
                    
                    <?php if (empty($id_juego)): ?>
                        <a href="part.php?id_tab=<?php echo $id_tab; ?>&nj=<?php echo $nj; ?>" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Modificar
                        </a>
                    <?php else: ?>
                        <a href="partedit.php?id_tab=<?php echo $id_tab; ?>&nj=<?php echo $nj; ?>&id_juego=<?php echo $id_juego; ?>" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Modificar
                        </a>
                        <a href="deletpart.php?id_tab=<?php echo $id_tab; ?>&nj=<?php echo $nj; ?>&id_juego=<?php echo $id_juego; ?>" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i>Eliminar Juego
                        </a>
                    <?php endif; ?>
                    
                    <a href="eliminar.php?id_tab=<?php echo $id_tab; ?>&nj=<?php echo $nj; ?>&id_temp=<?php echo $idtemp; ?>&id_team=<?php echo $idteam; ?>&cat=<?php echo urlencode($cat); ?>" class="btn btn-danger">
                        <i class="fas fa-times-circle me-2"></i>Eliminar
                    </a>
                </div>
                
                <!-- Información del juego -->
                <div class="card">
                    <h4 class="titulo">Temporada: <?php echo htmlspecialchars($data['name_temp']); ?></h4>
                    <div class="row">
                        <div class="col-md-4">
                            <table class="table table-sm table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th class="alte" colspan="2">Información</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th class="alte">Juego</th>
                                        <th class="alte" style="text-align: center;">
                                            <?php echo !empty($dte['jj']) ? htmlspecialchars($dte['jj']) : 'N/A'; ?>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="alte">Estado</th>
                                        <th class="alte" style="text-align: center;">
                                            <?php echo !empty($dte['estado']) ? htmlspecialchars($dte['estado']) : 'N/A'; ?>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="alte">Valido</th>
                                        <th class="alte" style="text-align: center;">
                                            <?php echo !empty($dte['valido']) ? 'SI' : 'No'; ?>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="alte">Fecha</th>
                                        <th class="alte" style="text-align: center;">
                                            <?php echo !empty($dte['fech_part']) ? htmlspecialchars($dte['fech_part']) : 'N/A'; ?>
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-8">
                            <table class="table table-sm table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th class="alte" colspan="4"><?php echo htmlspecialchars($data['name_team']); ?> - Partido N° <?php echo $nj; ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th class="alte">Equipo 1</th>
                                        <th class="alte" style="text-align: center;">
                                            <?php
                                            if (empty($dte['team_one'])) {
                                                echo htmlspecialchars($data['name_team']);
                                            } else {
                                                $onet = $dte['team_one'];
                                                $otm = "SELECT * FROM equipos WHERE id_team = ?";
                                                $stmt3 = mysqli_prepare($con, $otm);
                                                mysqli_stmt_bind_param($stmt3, "i", $onet);
                                                mysqli_stmt_execute($stmt3);
                                                $result3 = mysqli_stmt_get_result($stmt3);
                                                $dtotm = mysqli_fetch_array($result3);
                                                echo htmlspecialchars($dtotm['nom_team']);
                                            }
                                            ?>
                                        </th>
                                        <th class="alte" style="text-align: center;">CA</th>
                                        <th class="alte" style="text-align: center;">
                                            <?php echo !empty($dte['ca']) ? htmlspecialchars($dte['ca']) : '0'; ?>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="alte">Equipo 2</th>
                                        <th class="alte" style="text-align: center;">
                                            <?php
                                            if (empty($dte['team_two'])) {
                                                echo 'LIBRE';
                                            } else {
                                                $twot = $dte['team_two'];
                                                $ttm = "SELECT * FROM equipos WHERE id_team = ?";
                                                $stmt4 = mysqli_prepare($con, $ttm);
                                                mysqli_stmt_bind_param($stmt4, "i", $twot);
                                                mysqli_stmt_execute($stmt4);
                                                $result4 = mysqli_stmt_get_result($stmt4);
                                                $dtttm = mysqli_fetch_array($result4);
                                                echo htmlspecialchars($dtttm['nom_team']);
                                            }
                                            ?>
                                        </th>
                                        <th class="alte" style="text-align: center;">CE</th>
                                        <th class="alte" style="text-align: center;">
                                            <?php echo !empty($dte['ce']) ? htmlspecialchars($dte['ce']) : '0'; ?>
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Jugadores -->
                <div class="card">
                    <div class="card-title">Jugadores</div>
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
                                    <th>A</th>
                                    <th>FL</th>
                                    <th>BR</th>
                                    <th>GP</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $cons = "SELECT * FROM jugadores_stats 
                                         WHERE id_team = ? AND id_tab = ? AND nj = ? AND categoria LIKE ?";
                                $stmt5 = mysqli_prepare($con, $cons);
                                $cat_like = "%$cat%";
                                mysqli_stmt_bind_param($stmt5, "iiis", $idteam, $id_tab, $nj, $cat_like);
                                mysqli_stmt_execute($stmt5);
                                $dteg = mysqli_stmt_get_result($stmt5);
                                
                                if (mysqli_num_rows($dteg) >= 1) {
                                    $jg = 1;
                                    while ($player = mysqli_fetch_array($dteg)) {
                                ?>
                                <tr>
                                    <td class="alte"><?php echo $jg; ?></td>
                                    <td class="alte"><a href="../stats/jg.php?id_js=<?php echo $player['id_js']; ?>&id_tab=<?php echo $player['id_tab']; ?>&nj=<?php echo $player['nj']; ?>" class="team-link"><?php echo htmlspecialchars($player['name_jugador']); ?></a></td>
                                    <td class="alte"><?php echo htmlspecialchars($player['vb']); ?></td>
                                    <td class="alte"><?php echo htmlspecialchars($player['h']); ?></td>
                                    <td class="alte"><?php echo htmlspecialchars($player['hr']); ?></td>
                                    <td class="alte"><?php echo htmlspecialchars($player['2b']); ?></td>
                                    <td class="alte"><?php echo htmlspecialchars($player['3b']); ?></td>
                                    <td class="alte"><?php echo htmlspecialchars($player['ca']); ?></td>
                                    <td class="alte"><?php echo htmlspecialchars($player['ci']); ?></td>
                                    <td class="alte"><?php echo htmlspecialchars($player['k']); ?></td>
                                    <td class="alte"><?php echo htmlspecialchars($player['b']); ?></td>
                                    <td class="alte"><?php echo htmlspecialchars($player['a']); ?></td>
                                    <td class="alte"><?php echo htmlspecialchars($player['sf']); ?></td>
                                    <td class="alte"><?php echo htmlspecialchars($player['br']); ?></td>
                                    <td class="alte"><?php echo htmlspecialchars($player['gp']); ?></td>
                                </tr>
                                <?php 
                                        $jg++;
                                    }
                                } else {
                                ?>
                                <tr>
                                    <td colspan="15">
                                        <div class="empty-state">
                                            <i class="fas fa-users-slash"></i>
                                            <h4>No hay jugadores registrados</h4>
                                            <p>No se encontraron estadísticas de jugadores para este juego</p>
                                        </div>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Lanzadores -->
                <div class="card">
                    <div class="card-title">Lanzadores</div>
                    <div class="table-container">
                        <table class="table table-hover table-sm table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Jugador</th>
                                    <th>JL</th>
                                    <th>JG</th>
                                    <th>IL</th>
                                    <th>CP</th>
                                    <th>CPL</th>
                                    <th>H</th>
                                    <th>2B</th>
                                    <th>3B</th>
                                    <th>HR</th>
                                    <th>B</th>
                                    <th>K</th>
                                    <th>VB</th>
                                    <th>GP</th>
                                    <th>BR</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $cons = "SELECT * FROM jugadores_lanz 
                                         WHERE id_team = ? AND id_tab = ? AND nj = ? AND categoria LIKE ? 
                                         ORDER BY id_player ASC";
                                $stmt6 = mysqli_prepare($con, $cons);
                                $cat_like = "%$cat%";
                                mysqli_stmt_bind_param($stmt6, "iiis", $idteam, $id_tab, $nj, $cat_like);
                                mysqli_stmt_execute($stmt6);
                                $dteg = mysqli_stmt_get_result($stmt6);
                                
                                if (mysqli_num_rows($dteg) >= 1) {
                                    $jg = 1;
                                    while ($player = mysqli_fetch_array($dteg)) {
                                ?>
                                <tr>
                                    <td class="alte"><?php echo $jg; ?></td>
                                    <td class="alte"><a href="../stats/jlz.php?id_lanz=<?php echo $player['id_lanz']; ?>&id_tab=<?php echo $player['id_tab']; ?>&nj=<?php echo $player['nj']; ?>" class="team-link"><?php echo htmlspecialchars($player['name_lanz']); ?></a></td>
                                    <td class="alte"><?php echo htmlspecialchars($player['jl']); ?></td>
                                    <td class="alte"><?php echo htmlspecialchars($player['jg']); ?></td>
                                    <td class="alte"><?php echo htmlspecialchars($player['il']); ?></td>
                                    <td class="alte"><?php echo htmlspecialchars($player['cp']); ?></td>
                                    <td class="alte"><?php echo htmlspecialchars($player['cpl']); ?></td>
                                    <td class="alte"><?php echo htmlspecialchars($player['h']); ?></td>
                                    <td class="alte"><?php echo htmlspecialchars($player['2b']); ?></td>
                                    <td class="alte"><?php echo htmlspecialchars($player['3b']); ?></td>
                                    <td class="alte"><?php echo htmlspecialchars($player['hr']); ?></td>
                                    <td class="alte"><?php echo htmlspecialchars($player['b']); ?></td>
                                    <td class="alte"><?php echo htmlspecialchars($player['k']); ?></td>
                                    <td class="alte"><?php echo htmlspecialchars($player['va']); ?></td>
                                    <td class="alte"><?php echo htmlspecialchars($player['gp']); ?></td>
                                    <td class="alte"><?php echo htmlspecialchars($player['ile']); ?></td>
                                </tr>
                                <?php 
                                        $jg++;
                                    }
                                } else {
                                ?>
                                <tr>
                                    <td colspan="16">
                                        <div class="empty-state">
                                            <i class="fas fa-users-slash"></i>
                                            <h4>No hay lanzadores registrados</h4>
                                            <p>No se encontraron estadísticas de lanzadores para este juego</p>
                                        </div>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
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
    </script>
</body>
</html>
<?php
// Cerrar todas las declaraciones preparadas y la conexión
if (isset($stmt)) mysqli_stmt_close($stmt);
if (isset($stmt2)) mysqli_stmt_close($stmt2);
if (isset($stmt3)) mysqli_stmt_close($stmt3);
if (isset($stmt4)) mysqli_stmt_close($stmt4);
if (isset($stmt5)) mysqli_stmt_close($stmt5);
if (isset($stmt6)) mysqli_stmt_close($stmt6);
mysqli_close($con);
?>