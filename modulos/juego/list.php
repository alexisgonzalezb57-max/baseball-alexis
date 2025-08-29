<?php
include("../../config/conexion.php");
$con = conectar();

// Verificar conexión
if (!$con) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Obtener parámetros y sanitizarlos
$id = filter_var($_REQUEST['id'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
$cat = filter_var($_REQUEST['cat'] ?? '', FILTER_SANITIZE_STRING);

// Consulta preparada para evitar inyecciones SQL
$revisar = "SELECT temporada.*, tab_clasf.* FROM temporada 
            INNER JOIN tab_clasf ON temporada.id_temp = tab_clasf.id_temp 
            WHERE tab_clasf.id_temp = ? AND temporada.categoria LIKE CONCAT('%', ?, '%')";
$stmt = mysqli_prepare($con, $revisar);
mysqli_stmt_bind_param($stmt, "is", $id, $cat);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_array($result);

// Verificar si existe la tabla de estados, si no, crearla
$checkTable = "SHOW TABLES LIKE 'equipo_estados'";
$tableExists = mysqli_query($con, $checkTable);

if (mysqli_num_rows($tableExists) == 0) {
    $createTable = "CREATE TABLE equipo_estados (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_tab INT NOT NULL,
        id_temp INT NOT NULL,
        estado ENUM('C', 'E', 'G', 'R') NOT NULL COMMENT 'C=Clasificado, E=Eliminado, G=Ganador, R=Retirado',
        fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_tab) REFERENCES tab_clasf(id_tab),
        FOREIGN KEY (id_temp) REFERENCES temporada(id_temp)
    )";
    mysqli_query($con, $createTable);
}

// Verificar si existe la tabla de retiros, si no, crearla
$checkTableRetiros = "SHOW TABLES LIKE 'equipo_retiros'";
$tableExistsRetiros = mysqli_query($con, $checkTableRetiros);

if (mysqli_num_rows($tableExistsRetiros) == 0) {
    $createTableRetiros = "CREATE TABLE equipo_retiros (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_equipo_retirado INT NOT NULL,
        id_temp INT NOT NULL,
        puntos_jj INT NOT NULL DEFAULT 1 COMMENT 'Puntos a sumar en JJ por equipo activo',
        puntos_jg INT NOT NULL DEFAULT 1 COMMENT 'Puntos a sumar en JG por equipo activo',
        fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_equipo_retirado) REFERENCES tab_clasf(id_tab),
        FOREIGN KEY (id_temp) REFERENCES temporada(id_temp)
    )";
    mysqli_query($con, $createTableRetiros);
}

// Procesar formulario de gestión de estados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['estado']) && isset($_POST['equipos'])) {
        $estado = $_POST['estado'];
        $equipos = $_POST['equipos'];
        
        // Para estado Retirado, procesar de manera especial
        if ($estado === 'R') {
            // Procesar un equipo retirado a la vez
            if (count($equipos) > 1) {
                $error_msg = "Solo puede seleccionar un equipo para retirar a la vez.";
            } else {
                $id_equipo_retirado = $equipos[0];
                
                // Obtener puntos de retiro para cada equipo activo
                $puntos_retiro = array();
                foreach ($_POST as $key => $value) {
                    if (strpos($key, 'puntos_jj_') === 0) {
                        $id_equipo_activo = str_replace('puntos_jj_', '', $key);
                        $puntos_retiro[$id_equipo_activo]['jj'] = (int)$value;
                    } elseif (strpos($key, 'puntos_jg_') === 0) {
                        $id_equipo_activo = str_replace('puntos_jg_', '', $key);
                        $puntos_retiro[$id_equipo_activo]['jg'] = (int)$value;
                    }
                }
                
                // Obtener información del equipo retirado
                $query_retirado = "SELECT * FROM tab_clasf WHERE id_tab = ? AND id_temp = ?";
                $stmt_retirado = mysqli_prepare($con, $query_retirado);
                mysqli_stmt_bind_param($stmt_retirado, "ii", $id_equipo_retirado, $id);
                mysqli_stmt_execute($stmt_retirado);
                $equipo_retirado = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_retirado));
                
                // Obtener todos los equipos activos de la misma categoría y temporada
                $query_activos = "SELECT * FROM tab_clasf 
                                 WHERE id_temp = ? AND categoria = ? AND id_tab != ?";
                $stmt_activos = mysqli_prepare($con, $query_activos);
                mysqli_stmt_bind_param($stmt_activos, "isi", $id, $cat, $id_equipo_retirado);
                mysqli_stmt_execute($stmt_activos);
                $equipos_activos = mysqli_stmt_get_result($stmt_activos);
                
                // Calcular juegos a sumar (JJ y JG para activos, JJ y JP para retirado)
                $total_jj_retirado = 0;
                $total_jg_retirado = 0;
                
                while ($activo = mysqli_fetch_assoc($equipos_activos)) {
                    // Obtener puntos para este equipo activo
                    $puntos_jj = isset($puntos_retiro[$activo['id_tab']]['jj']) ? $puntos_retiro[$activo['id_tab']]['jj'] : 1;
                    $puntos_jg = isset($puntos_retiro[$activo['id_tab']]['jg']) ? $puntos_retiro[$activo['id_tab']]['jg'] : 1;
                    
                    // Sumar puntos a equipos activos
                    $nuevo_jj = $activo['jj'] + $puntos_jj;
                    $nuevo_jg = $activo['jg'] + $puntos_jg;
                    
                    $update_activo = "UPDATE tab_clasf SET jj = ?, jg = ? WHERE id_tab = ?";
                    $stmt_update = mysqli_prepare($con, $update_activo);
                    mysqli_stmt_bind_param($stmt_update, "iii", $nuevo_jj, $nuevo_jg, $activo['id_tab']);
                    mysqli_stmt_execute($stmt_update);
                    
                    $total_jj_retirado += $puntos_jj;
                    $total_jg_retirado += $puntos_jg;
                    
                    // Guardar en tabla de retiros
                    $insert_retiro = "INSERT INTO equipo_retiros (id_equipo_retirado, id_temp, puntos_jj, puntos_jg) 
                                     VALUES (?, ?, ?, ?)";
                    $stmt_retiro = mysqli_prepare($con, $insert_retiro);
                    mysqli_stmt_bind_param($stmt_retiro, "iiii", $id_equipo_retirado, $id, $puntos_jj, $puntos_jg);
                    mysqli_stmt_execute($stmt_retiro);
                }
                
                // Actualizar equipo retirado
                $nuevo_jj_retirado = $equipo_retirado['jj'] + $total_jj_retirado;
                $nuevo_jp_retirado = $equipo_retirado['jp'] + $total_jg_retirado;
                
                $update_retirado = "UPDATE tab_clasf SET jj = ?, jp = ? WHERE id_tab = ?";
                $stmt_update_ret = mysqli_prepare($con, $update_retirado);
                mysqli_stmt_bind_param($stmt_update_ret, "iii", $nuevo_jj_retirado, $nuevo_jp_retirado, $id_equipo_retirado);
                mysqli_stmt_execute($stmt_update_ret);
                
                // Registrar estado retirado
                $insert_estado = "INSERT INTO equipo_estados (id_tab, id_temp, estado) 
                                 VALUES (?, ?, ?)";
                $stmt_estado = mysqli_prepare($con, $insert_estado);
                mysqli_stmt_bind_param($stmt_estado, "iis", $id_equipo_retirado, $id, $estado);
                mysqli_stmt_execute($stmt_estado);
                
                $success_msg = "Equipo retirado exitosamente. Se han sumado puntos a todos los equipos activos.";
            }
        } else {
            // Para otros estados (C, E, G)
            foreach ($equipos as $id_equipo) {
                // Verificar si ya existe un registro para este equipo
                $check_exist = "SELECT id FROM equipo_estados WHERE id_tab = ? AND id_temp = ?";
                $stmt_check = mysqli_prepare($con, $check_exist);
                mysqli_stmt_bind_param($stmt_check, "ii", $id_equipo, $id);
                mysqli_stmt_execute($stmt_check);
                $exists = mysqli_num_rows(mysqli_stmt_get_result($stmt_check));
                
                if ($exists) {
                    // Actualizar estado existente
                    $update_estado = "UPDATE equipo_estados SET estado = ? WHERE id_tab = ? AND id_temp = ?";
                    $stmt_update = mysqli_prepare($con, $update_estado);
                    mysqli_stmt_bind_param($stmt_update, "sii", $estado, $id_equipo, $id);
                    mysqli_stmt_execute($stmt_update);
                } else {
                    // Insertar nuevo estado
                    $insert_estado = "INSERT INTO equipo_estados (id_tab, id_temp, estado) VALUES (?, ?, ?)";
                    $stmt_insert = mysqli_prepare($con, $insert_estado);
                    mysqli_stmt_bind_param($stmt_insert, "iis", $id_equipo, $id, $estado);
                    mysqli_stmt_execute($stmt_insert);
                }
            }
            
            $success_msg = "Estados actualizados exitosamente.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Clasificación - Sistema Baseball</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Estilos personalizados -->
    <style>
        /* Estilos anteriores (se mantienen igual) */
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #fd7e14;
            --dark-color: #212529;
            --light-color: #f8f9fa;
            --success-color: #198754;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #0dcaf0;
            --ganador-color: #6f42c1;
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
        
        .btn-ganador {
            background: linear-gradient(90deg, var(--ganador-color) 0%, #5a3596 100%);
            color: white;
            border: none;
        }
        
        .btn-ganador:hover {
            background: linear-gradient(90deg, #5a3596 0%, #4a2a7a 100%);
            color: white;
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
        
        .badge-estado {
            padding: 0.5rem 0.75rem;
            border-radius: 50rem;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .badge-clasificado {
            background: linear-gradient(90deg, var(--success-color) 0%, #146c43 100%);
            color: white;
        }
        
        .badge-eliminado {
            background: linear-gradient(90deg, var(--danger-color) 0%, #c82333 100%);
            color: white;
        }
        
        .badge-retirado {
            background: linear-gradient(90deg, var(--warning-color) 0%, #ffb507 100%);
            color: black;
        }
        
        .badge-ganador {
            background: linear-gradient(90deg, var(--ganador-color) 0%, #5a3596 100%);
            color: white;
        }
        
        .modal-content {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        
        .modal-header {
            background: linear-gradient(90deg, var(--primary-color) 0%, #0a58ca 100%);
            color: white;
            border-bottom: none;
        }
        
        .btn-close-white {
            filter: invert(1) grayscale(100%) brightness(200%);
        }
        
        .estado-option {
            border: 2px solid #dee2e6;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .estado-option:hover {
            border-color: var(--primary-color);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .estado-option.selected {
            border-color: var(--primary-color);
            background-color: rgba(13, 110, 253, 0.05);
        }
        
        .puntos-retiro-container {
            display: none;
            margin-top: 1rem;
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        
        .puntos-equipo {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            background: white;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        
        .puntos-input {
            width: 70px;
            text-align: center;
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
            
            .puntos-equipo {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .puntos-input {
                width: 100%;
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
                <h1 class="page-title">Clasificación: <?php echo htmlspecialchars($data['name_temp'] ?? 'Temporada'); ?></h1>
                
                <!-- Mostrar mensajes de éxito o error -->
                <?php if (isset($success_msg)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo $success_msg; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <?php if (isset($error_msg)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo $error_msg; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <div class="button-group">
                    <a href="formclasf.php?id=<?php echo $id ?>&cat=<?php echo urlencode($cat) ?>" class="btn btn-info">
                        <i class="fas fa-users me-2"></i>Gestionar Equipos
                    </a>
                    
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#estadosModal">
                        <i class="fas fa-cogs me-2"></i>Gestionar Estados
                    </button>
                    
                    <a href="../juego/" class="btn btn-success">
                        <i class="fas fa-arrow-left me-2"></i>Volver
                    </a>
                </div>
                
                <div class="table-container">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Equipo</th>
                                <th>JJ</th>
                                <th>JG</th>
                                <th>JP</th>
                                <th>JE</th>
                                <th>AVG</th>
                                <th>CA</th>
                                <th>CE</th>
                                <th>DIF</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $revisar = "SELECT t.*, e.estado 
                                        FROM tab_clasf t 
                                        LEFT JOIN equipo_estados e ON t.id_tab = e.id_tab AND t.id_temp = e.id_temp
                                        WHERE t.id_temp = ? AND t.categoria LIKE CONCAT('%', ?, '%') 
                                        ORDER BY t.jg DESC, t.avg DESC";
                            $stmt = mysqli_prepare($con, $revisar);
                            mysqli_stmt_bind_param($stmt, "is", $id, $cat);
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);
                            $num = mysqli_num_rows($result);
                            
                            if ($num >= 1) {
                                $counter = 1;
                                while ($bdata = mysqli_fetch_array($result)) {
                                    // Determinar clase CSS según el estado
                                    $estado_badge = "";
                                    if ($bdata['estado'] == 'C') {
                                        $estado_badge = '<span class="badge-estado badge-clasificado">Clasificado</span>';
                                    } elseif ($bdata['estado'] == 'E') {
                                        $estado_badge = '<span class="badge-estado badge-eliminado">Eliminado</span>';
                                    } elseif ($bdata['estado'] == 'R') {
                                        $estado_badge = '<span class="badge-estado badge-retirado">Retirado</span>';
                                    } elseif ($bdata['estado'] == 'G') {
                                        $estado_badge = '<span class="badge-estado badge-ganador">Ganador</span>';
                                    } else {
                                        $estado_badge = '<span class="badge-estado" style="background:#e9ecef; color:#6c757d;">Sin estado</span>';
                                    }
                            ?>
                            <tr>
                                <td class="text-center fw-bold"><?php echo $counter; ?></td>
                                <td>
                                    <a href="../entradas/entradas.php?id=<?php echo $bdata['id_tab'] ?>" class="team-link">
                                        <?php echo htmlspecialchars($bdata['name_team']); ?>
                                    </a>
                                </td>
                                <td class="text-center"><?php echo $bdata['jj']; ?></td>
                                <td class="text-center"><?php echo $bdata['jg']; ?></td>
                                <td class="text-center"><?php echo $bdata['jp']; ?></td>
                                <td class="text-center"><?php echo $bdata['je']; ?></td>
                                <td class="text-center"><?php echo $bdata['avg']; ?></td>
                                <td class="text-center"><?php echo $bdata['ca']; ?></td>
                                <td class="text-center"><?php echo $bdata['ce']; ?></td>
                                <td class="text-center"><?php echo $bdata['dif']; ?></td>
                                <td class="text-center"><?php echo $estado_badge; ?></td>
                            </tr>
                            <?php 
                                    $counter++;
                                }
                            } else {
                            ?>
                            <tr>
                                <td colspan="11">
                                    <div class="empty-state">
                                        <i class="fas fa-users-slash"></i>
                                        <h4>No hay equipos registrados</h4>
                                        <p>Agrega equipos para comenzar a ver las estadísticas</p>
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal para gestionar estados -->
    <div class="modal fade" id="estadosModal" tabindex="-1" aria-labelledby="estadosModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="estadosModalLabel">Gestionar Estados de Equipos</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Seleccionar Estado:</label>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <div class="estado-option" data-estado="C">
                                        <h5><i class="fas fa-check-circle text-success me-2"></i> Clasificado</h5>
                                        <p class="mb-0">Equipos que avanzan a la siguiente fase</p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="estado-option" data-estado="E">
                                        <h5><i class="fas fa-times-circle text-danger me-2"></i> Eliminado</h5>
                                        <p class="mb-0">Equipos que no continúan en la competencia</p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="estado-option" data-estado="G">
                                        <h5><i class="fas fa-trophy text-ganador me-2"></i> Ganador</h5>
                                        <p class="mb-0">Equipo campeón de la temporada</p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="estado-option" data-estado="R">
                                        <h5><i class="fas fa-walking text-warning me-2"></i> Retirado</h5>
                                        <p class="mb-0">Equipos que se retiran de la competencia</p>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="estado" id="inputEstado" value="">
                        </div>
                        
                        <div id="puntosRetiroContainer" class="puntos-retiro-container">
                            <h6>Puntos a sumar por equipo activo:</h6>
                            <p class="text-muted">Seleccione cuántos puntos (1 o 2) se sumarán a cada equipo activo en JJ y JG.</p>
                            
                            <div id="puntosEquiposContainer">
                                <!-- Aquí se cargarán dinámicamente los equipos activos -->
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Seleccionar Equipos:</label>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th width="50px"><input type="checkbox" id="selectAll"></th>
                                            <th>Equipo</th>
                                            <th>Estado Actual</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query_equipos = "SELECT t.*, e.estado 
                                                         FROM tab_clasf t 
                                                         LEFT JOIN equipo_estados e ON t.id_tab = e.id_tab AND t.id_temp = e.id_temp
                                                         WHERE t.id_temp = ? AND t.categoria LIKE CONCAT('%', ?, '%') 
                                                         ORDER BY t.name_team";
                                        $stmt_equipos = mysqli_prepare($con, $query_equipos);
                                        mysqli_stmt_bind_param($stmt_equipos, "is", $id, $cat);
                                        mysqli_stmt_execute($stmt_equipos);
                                        $equipos = mysqli_stmt_get_result($stmt_equipos);
                                        
                                        while ($equipo = mysqli_fetch_assoc($equipos)) {
                                            $estado_actual = "";
                                            if ($equipo['estado'] == 'C') {
                                                $estado_actual = '<span class="badge bg-success">Clasificado</span>';
                                            } elseif ($equipo['estado'] == 'E') {
                                                $estado_actual = '<span class="badge bg-danger">Eliminado</span>';
                                            } elseif ($equipo['estado'] == 'R') {
                                                $estado_actual = '<span class="badge bg-warning">Retirado</span>';
                                            } elseif ($equipo['estado'] == 'G') {
                                                $estado_actual = '<span class="badge bg-purple">Ganador</span>';
                                            } else {
                                                $estado_actual = '<span class="badge bg-secondary">Sin estado</span>';
                                            }
                                        ?>
                                        <tr>
                                            <td><input type="checkbox" name="equipos[]" value="<?php echo $equipo['id_tab']; ?>" class="equipo-checkbox"></td>
                                            <td><?php echo htmlspecialchars($equipo['name_team']); ?></td>
                                            <td><?php echo $estado_actual; ?></td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Aplicar Estados</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
        
        // Script para el modal de estados
        document.addEventListener('DOMContentLoaded', function() {
            // Seleccionar/deseleccionar todos los equipos
            document.getElementById('selectAll').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.equipo-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });
            
            // Manejar selección de estado
            const estadoOptions = document.querySelectorAll('.estado-option');
            const inputEstado = document.getElementById('inputEstado');
            const puntosRetiroContainer = document.getElementById('puntosRetiroContainer');
            const puntosEquiposContainer = document.getElementById('puntosEquiposContainer');
            
            estadoOptions.forEach(option => {
                option.addEventListener('click', function() {
                    // Remover selección anterior
                    estadoOptions.forEach(opt => opt.classList.remove('selected'));
                    
                    // Agregar selección actual
                    this.classList.add('selected');
                    
                    // Actualizar valor del input
                    const estado = this.getAttribute('data-estado');
                    inputEstado.value = estado;
                    
                    // Mostrar u ocultar campo de puntos para retiro
                    if (estado === 'R') {
                        puntosRetiroContainer.style.display = 'block';
                        
                        // Cargar equipos activos para selección de puntos
                        cargarEquiposActivos();
                    } else {
                        puntosRetiroContainer.style.display = 'none';
                    }
                });
            });
            
            // Función para cargar equipos activos
            function cargarEquiposActivos() {
                // Obtener todos los equipos de la tabla
                const equipos = [];
                document.querySelectorAll('.equipo-checkbox').forEach(checkbox => {
                    const row = checkbox.closest('tr');
                    const nombre = row.querySelector('td:nth-child(2)').textContent;
                    equipos.push({
                        id: checkbox.value,
                        nombre: nombre.trim()
                    });
                });
                
                // Generar HTML para selección de puntos
                let html = '';
                equipos.forEach(equipo => {
                    html += `
                    <div class="puntos-equipo">
                        <div class="flex-grow-1">
                            <label class="form-label">${equipo.nombre}</label>
                        </div>
                        <div>
                            <label class="form-label">JJ:</label>
                            <input type="number" class="form-control puntos-input" name="puntos_jj_${equipo.id}" min="1" max="2" value="1">
                        </div>
                        <div>
                            <label class="form-label">JG:</label>
                            <input type="number" class="form-control puntos-input" name="puntos_jg_${equipo.id}" min="1" max="2" value="1">
                        </div>
                    </div>
                    `;
                });
                
                puntosEquiposContainer.innerHTML = html;
            }
            
            // Validar formulario antes de enviar
            const form = document.querySelector('#estadosModal form');
            form.addEventListener('submit', function(e) {
                if (!inputEstado.value) {
                    e.preventDefault();
                    alert('Por favor, seleccione un estado.');
                    return;
                }
                
                const equiposSeleccionados = document.querySelectorAll('.equipo-checkbox:checked');
                if (equiposSeleccionados.length === 0) {
                    e.preventDefault();
                    alert('Por favor, seleccione al menos un equipo.');
                    return;
                }
                
                // Validación especial para estado Retirado
                if (inputEstado.value === 'R') {
                    if (equiposSeleccionados.length > 1) {
                        e.preventDefault();
                        alert('Para el estado "Retirado", solo puede seleccionar un equipo a la vez.');
                        return;
                    }
                    
                    // Validar que todos los puntos estén entre 1 y 2
                    const puntosInputs = document.querySelectorAll('.puntos-input');
                    let puntosValidos = true;
                    
                    puntosInputs.forEach(input => {
                        const valor = parseInt(input.value);
                        if (isNaN(valor) || valor < 1 || valor > 2) {
                            puntosValidos = false;
                            input.classList.add('is-invalid');
                        } else {
                            input.classList.remove('is-invalid');
                        }
                    });
                    
                    if (!puntosValidos) {
                        e.preventDefault();
                        alert('Los puntos de retiro deben ser 1 o 2.');
                        return;
                    }
                }
                
                // Validación especial para estado Ganador
                if (inputEstado.value === 'G' && equiposSeleccionados.length > 1) {
                    e.preventDefault();
                    alert('Solo puede haber un equipo Ganador. Seleccione solo un equipo.');
                    return;
                }
            });
        });
    </script>
</body>
</html>