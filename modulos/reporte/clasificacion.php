<?php
include("../../config/conexion.php");
$con = conectar();

// Verificar conexión
if (!$con) {
    die("Error de conexión: " . mysqli_connect_error());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reportes - Tabla de Clasificación</title>
    
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
        
        .badge-sin-estado {
            background: #e9ecef;
            color: #6c757d;
        }
        
        .temporada-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
            overflow: hidden;
        }
        
        .temporada-header {
            background: linear-gradient(90deg, var(--primary-color) 0%, #0a58ca 100%);
            color: white;
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .temporada-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
        }
        
        .temporada-info {
            font-size: 1rem;
            opacity: 0.9;
        }
        
        .temporada-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .honor-text {
            font-style: italic;
            margin-top: 0.5rem;
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        @media (max-width: 992px) {
            .navigation {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .temporada-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .temporada-actions {
                width: 100%;
                justify-content: center;
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
            
            .temporada-title {
                font-size: 1.3rem;
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
                <h1 class="page-title">Reportes - Tabla de Clasificación</h1>
                
                <div class="button-group mb-4">
                    <a href="reporte.php" class="btn btn-success">
                        <i class="fas fa-arrow-left me-2"></i>Volver a Reportes
                    </a>
                    
                    <a target="_blank" href="../PDF/tabla-clasf-avg-all.php" class="btn btn-warning">
                        <i class="fas fa-file-pdf me-2"></i>Todos en AVG
                    </a>
                </div>
                
                <?php
                // Consulta optimizada usando consultas preparadas
                $val_hor = "SELECT * FROM temporada WHERE activo = 1 ORDER BY categoria DESC";
                $query_hor = mysqli_query($con, $val_hor);
                
                if (mysqli_num_rows($query_hor) >= 1) {
                    $e = 1;
                    while ($obtenerho = mysqli_fetch_assoc($query_hor)) {
                        $id = $obtenerho['id_temp'];
                        $cat = $obtenerho['categoria'];
                        
                        // Consulta preparada para homenaje
                        $busc = "SELECT * FROM homenaje WHERE id_temp = ?";
                        $stmt = mysqli_prepare($con, $busc);
                        mysqli_stmt_bind_param($stmt, "i", $id);
                        mysqli_stmt_execute($stmt);
                        $result_homenaje = mysqli_stmt_get_result($stmt);
                        $ftdp = mysqli_fetch_assoc($result_homenaje);
                ?>
                
                <div class="temporada-card">
                    <div class="temporada-header">
                        <div>
                            <h3 class="temporada-title">Tabla N° <?php echo $e ?> - <?php echo htmlspecialchars($obtenerho['name_temp']); ?></h3>
                            <div class="temporada-info">Categoría "<?php echo htmlspecialchars($obtenerho['categoria']); ?>"</div>
                            <?php if (!empty($ftdp['honor'])): ?>
                            <div class="honor-text">Persona(s) de Honor: "<?php echo htmlspecialchars($ftdp['honor']); ?>"</div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="temporada-actions">
                            <?php if (empty($ftdp['id_hnr'])): ?>
                            <a href="creat.php?id=<?php echo $id ?>" class="btn btn-secondary btn-sm">
                                <i class="fas fa-plus me-1"></i>Crear Persona
                            </a>
                            <?php else: ?>
                            <a href="edit.php?id_hnr=<?php echo $ftdp['id_hnr'] ?>" class="btn btn-info btn-sm">
                                <i class="fas fa-edit me-1"></i>Modificar Persona
                            </a>
                            <?php endif; ?>
                            
                            <a target="_blank" href="../PDF/tabla-clasf-avg.php?id_temp=<?php echo $id ?>&id_tab=<?php echo '16' ?>&persona=<?php echo urlencode($ftdp['honor'] ?? '') ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-file-pdf me-1"></i>AVG personal
                            </a>
                        </div>
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
                                // Consulta optimizada con JOIN para obtener estados
                                $revisar = "SELECT t.*, e.estado 
                                            FROM tab_clasf t 
                                            LEFT JOIN equipo_estados e ON t.id_tab = e.id_tab AND t.id_temp = e.id_temp
                                            WHERE t.id_temp = ? AND t.categoria LIKE CONCAT('%', ?, '%')
                                            ORDER BY t.jg DESC, t.avg DESC";
                                $stmt = mysqli_prepare($con, $revisar);
                                mysqli_stmt_bind_param($stmt, "is", $id, $cat);
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);
                                
                                if (mysqli_num_rows($result) >= 1) {
                                    $i = 1;
                                    while ($bdata = mysqli_fetch_assoc($result)) {
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
                                            $estado_badge = '<span class="badge-estado badge-sin-estado">Sin estado</span>';
                                        }
                                ?>
                                <tr>
                                    <td class="text-center fw-bold"><?php echo $i; ?></td>
                                    <td><?php echo htmlspecialchars($bdata['name_team']); ?></td>
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
                                        $i++;
                                    }
                                } else {
                                ?>
                                <tr>
                                    <td colspan="11" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="fas fa-users-slash fa-2x text-muted mb-3"></i>
                                            <h5>No hay equipos registrados</h5>
                                            <p class="text-muted">Agrega equipos para comenzar a ver las estadísticas</p>
                                        </div>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <?php
                        $e++;
                    }
                } else {
                ?>
                <div class="empty-state text-center py-5">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <h3>No hay temporadas activas</h3>
                    <p class="text-muted">No se encontraron temporadas activas para mostrar.</p>
                </div>
                <?php } ?>
                
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
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>
</body>
</html>