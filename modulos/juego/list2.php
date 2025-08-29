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
        estado ENUM('C', 'E', 'R') NOT NULL COMMENT 'C=Clasificado, E=Eliminado, R=Retirado',
        fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_tab) REFERENCES tab_clasf(id_tab),
        FOREIGN KEY (id_temp) REFERENCES temporada(id_temp)
    )";
    mysqli_query($con, $createTable);
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
                    <li><a href="../homenaje/"><i class="fas fa-trophy"></i> Homenaje</a></li>
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
                <h1 class="page-title">Clasificación: <?php echo htmlspecialchars($data['name_temp'] ?? 'Temporada'); ?></h1>
                
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

    <!-- Modal para gestión de estados -->
    <div class="modal fade" id="estadosModal" tabindex="-1" aria-labelledby="estadosModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="estadosModalLabel">Gestión de Estados de Equipos</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-pills mb-3" id="estadosTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="clasificar-tab" data-bs-toggle="pill" data-bs-target="#clasificar" type="button" role="tab" aria-controls="clasificar" aria-selected="true">Clasificar</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="eliminar-tab" data-bs-toggle="pill" data-bs-target="#eliminar" type="button" role="tab" aria-controls="eliminar" aria-selected="false">Eliminar</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="retirar-tab" data-bs-toggle="pill" data-bs-target="#retirar" type="button" role="tab" aria-controls="retirar" aria-selected="false">Retirar</button>
                        </li>
                    </ul>
                    
                    <div class="tab-content" id="estadosTabContent">
                        <!-- Pestaña de Clasificación -->
                        <div class="tab-pane fade show active" id="clasificar" role="tabpanel" aria-labelledby="clasificar-tab">
                            <p class="text-muted mb-3">Selecciona los equipos que han clasificado en esta temporada.</p>
                            
                            <form id="formClasificar" action="gestionar_estados.php" method="POST">
                                <input type="hidden" name="accion" value="clasificar">
                                <input type="hidden" name="id_temp" value="<?php echo $id; ?>">
                                <input type="hidden" name="categoria" value="<?php echo htmlspecialchars($cat); ?>">
                                
                                <div class="mb-3">
                                    <label class="form-label">Seleccionar equipos:</label>
                                    <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                        <?php
                                        $equipos = "SELECT * FROM tab_clasf WHERE id_temp = ? AND categoria LIKE CONCAT('%', ?, '%') ORDER BY name_team";
                                        $stmt = mysqli_prepare($con, $equipos);
                                        mysqli_stmt_bind_param($stmt, "is", $id, $cat);
                                        mysqli_stmt_execute($stmt);
                                        $result_equipos = mysqli_stmt_get_result($stmt);
                                        
                                        while ($equipo = mysqli_fetch_array($result_equipos)) {
                                            $checked = "";
                                            // Verificar si el equipo ya está clasificado
                                            $checkEstado = "SELECT estado FROM equipo_estados WHERE id_tab = ? AND id_temp = ?";
                                            $stmt_check = mysqli_prepare($con, $checkEstado);
                                            mysqli_stmt_bind_param($stmt_check, "ii", $equipo['id_tab'], $id);
                                            mysqli_stmt_execute($stmt_check);
                                            $result_check = mysqli_stmt_get_result($stmt_check);
                                            $estado_data = mysqli_fetch_array($result_check);
                                            
                                            if ($estado_data && $estado_data['estado'] == 'C') {
                                                $checked = "checked";
                                            }
                                        ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="equipos[]" value="<?php echo $equipo['id_tab']; ?>" id="clasificar_<?php echo $equipo['id_tab']; ?>" <?php echo $checked; ?>>
                                            <label class="form-check-label" for="clasificar_<?php echo $equipo['id_tab']; ?>">
                                                <?php echo htmlspecialchars($equipo['name_team']); ?>
                                            </label>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-success w-100">Guardar Clasificación</button>
                            </form>
                        </div>
                        
                        <!-- Pestaña de Eliminación -->
                        <div class="tab-pane fade" id="eliminar" role="tabpanel" aria-labelledby="eliminar-tab">
                            <p class="text-muted mb-3">Selecciona los equipos que han sido eliminados en esta temporada.</p>
                            
                            <form id="formEliminar" action="gestionar_estados.php" method="POST">
                                <input type="hidden" name="accion" value="eliminar">
                                <input type="hidden" name="id_temp" value="<?php echo $id; ?>">
                                <input type="hidden" name="categoria" value="<?php echo htmlspecialchars($cat); ?>">
                                
                                <div class="mb-3">
                                    <label class="form-label">Seleccionar equipos:</label>
                                    <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                        <?php
                                        mysqli_data_seek($result_equipos, 0);
                                        while ($equipo = mysqli_fetch_array($result_equipos)) {
                                            $checked = "";
                                            // Verificar si el equipo ya está eliminado
                                            $checkEstado = "SELECT estado FROM equipo_estados WHERE id_tab = ? AND id_temp = ?";
                                            $stmt_check = mysqli_prepare($con, $checkEstado);
                                            mysqli_stmt_bind_param($stmt_check, "ii", $equipo['id_tab'], $id);
                                            mysqli_stmt_execute($stmt_check);
                                            $result_check = mysqli_stmt_get_result($stmt_check);
                                            $estado_data = mysqli_fetch_array($result_check);
                                            
                                            if ($estado_data && $estado_data['estado'] == 'E') {
                                                $checked = "checked";
                                            }
                                        ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="equipos[]" value="<?php echo $equipo['id_tab']; ?>" id="eliminar_<?php echo $equipo['id_tab']; ?>" <?php echo $checked; ?>>
                                            <label class="form-check-label" for="eliminar_<?php echo $equipo['id_tab']; ?>">
                                                <?php echo htmlspecialchars($equipo['name_team']); ?>
                                            </label>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-danger w-100">Guardar Eliminados</button>
                            </form>
                        </div>
                        
                        <!-- Pestaña de Retiro -->
                        <div class="tab-pane fade" id="retirar" role="tabpanel" aria-labelledby="retirar-tab">
                            <p class="text-muted mb-3">Selecciona los equipos que se han retirado de la temporada. Los puntos de sus juegos ganados se redistribuirán entre los demás equipos.</p>
                            
                            <form id="formRetirar" action="gestionar_estados.php" method="POST">
                                <input type="hidden" name="accion" value="retirar">
                                <input type="hidden" name="id_temp" value="<?php echo $id; ?>">
                                <input type="hidden" name="categoria" value="<?php echo htmlspecialchars($cat); ?>">
                                
                                <div class="mb-3">
                                    <label class="form-label">Seleccionar equipos retirados:</label>
                                    <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                        <?php
                                        mysqli_data_seek($result_equipos, 0);
                                        while ($equipo = mysqli_fetch_array($result_equipos)) {
                                            $checked = "";
                                            // Verificar si el equipo ya está retirado
                                            $checkEstado = "SELECT estado FROM equipo_estados WHERE id_tab = ? AND id_temp = ?";
                                            $stmt_check = mysqli_prepare($con, $checkEstado);
                                            mysqli_stmt_bind_param($stmt_check, "ii", $equipo['id_tab'], $id);
                                            mysqli_stmt_execute($stmt_check);
                                            $result_check = mysqli_stmt_get_result($stmt_check);
                                            $estado_data = mysqli_fetch_array($result_check);
                                            
                                            if ($estado_data && $estado_data['estado'] == 'R') {
                                                $checked = "checked";
                                            }
                                        ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="equipos_retirados[]" value="<?php echo $equipo['id_tab']; ?>" id="retirar_<?php echo $equipo['id_tab']; ?>" <?php echo $checked; ?>>
                                            <label class="form-check-label" for="retirar_<?php echo $equipo['id_tab']; ?>">
                                                <?php echo htmlspecialchars($equipo['name_team']); ?>
                                            </label>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Distribuir puntos entre los equipos:</label>
                                    <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                        <?php
                                        mysqli_data_seek($result_equipos, 0);
                                        while ($equipo = mysqli_fetch_array($result_equipos)) {
                                        ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="equipos_beneficiados[]" value="<?php echo $equipo['id_tab']; ?>" id="beneficiar_<?php echo $equipo['id_tab']; ?>">
                                            <label class="form-check-label" for="beneficiar_<?php echo $equipo['id_tab']; ?>">
                                                <?php echo htmlspecialchars($equipo['name_team']); ?>
                                            </label>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-warning w-100">Procesar Retiros</button>
                            </form>
                        </div>
                    </div>
                </div>
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
        
        // Inicializar tooltips de Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
        
        // Manejar la selección de equipos retirados y beneficiados
        document.addEventListener('DOMContentLoaded', function() {
            const retiradosCheckboxes = document.querySelectorAll('input[name="equipos_retirados[]"]');
            const beneficiadosCheckboxes = document.querySelectorAll('input[name="equipos_beneficiados[]"]');
            
            // Deshabilitar la selección de un equipo como retirado y beneficiado al mismo tiempo
            retiradosCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        const correspondingBeneficiado = document.querySelector(`input[name="equipos_beneficiados[]"][value="${this.value}"]`);
                        if (correspondingBeneficiado) {
                            correspondingBeneficiado.disabled = true;
                        }
                    } else {
                        const correspondingBeneficiado = document.querySelector(`input[name="equipos_beneficiados[]"][value="${this.value}"]`);
                        if (correspondingBeneficiado) {
                            correspondingBeneficiado.disabled = false;
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>