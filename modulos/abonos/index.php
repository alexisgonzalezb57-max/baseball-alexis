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
    <title>Abonos - Sistema Baseball</title>
    
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
        
        .btn-warning {
            background: linear-gradient(90deg, var(--warning-color) 0%, #e0a800 100%);
            border: none;
            color: var(--dark-color);
        }
        
        .btn-warning:hover {
            background: linear-gradient(90deg, #e0a800 0%, #c69500 100%);
            transform: translateY(-2px);
            color: var(--dark-color);
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
        
        .categoria-badge {
            display: inline-block;
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.5rem;
            text-transform: uppercase;
        }
        
        .categoria-a {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .categoria-b {
            background-color: #d4edda;
            color: #155724;
        }
        
        .categoria-c {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.5rem;
        }
        
        .status-yes {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-no {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
        }
        
        .buto {
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
            transition: all 0.3s;
        }
        
        .buto:hover {
            transform: translateY(-2px);
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
        
        .count-badge {
            display: inline-block;
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 50rem;
            background-color: var(--primary-color);
            color: white;
            min-width: 30px;
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
                gap: 0.25rem;
            }
            
            .buto {
                width: 100%;
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
                <h1 class="page-title">💰 Listado de Temporadas para Abonar</h1>
                
                <div class="text-center">
                    <a href="form.php">
                        <button type="button" class="btn btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Definir nuevos abonos para temporadas">
                            <i class="fas fa-plus-circle me-2"></i>Definir Abono de la Temporada
                        </button>
                    </a>
                </div>

                <div class="table-container">
                    <div class="table-header">
                        <h5 class="mb-0"><i class="fas fa-ticket-alt me-2"></i>Temporadas con Abonos Definidos</h5>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Nombre de la Temporada</th>
                                    <th>Categoría</th>
                                    <th>N° de Abonos</th>
                                    <th>¿Cuarto Premio?</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $obtener = "SELECT temporada.*, abonos.* FROM temporada INNER JOIN abonos 
                                ON temporada.id_temp = abonos.id_temp ORDER BY abonos.categoria DESC";
                                $query = mysqli_query($con, $obtener);
                                $num = mysqli_num_rows($query);
                                
                                if ($num >= 1) {
                                    while ($data = mysqli_fetch_array($query)) {
                                        $categoria_class = 'categoria-' . strtolower($data['categoria'][0]);
                                        $status_class = empty($data['prize_four']) ? 'status-no' : 'status-yes';
                                        $status_text = empty($data['prize_four']) ? 'NO' : 'SI';
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($data['name_temp']); ?></td>
                                    <td>
                                        <span class="categoria-badge <?php echo $categoria_class; ?>">
                                            <?php echo $data['categoria']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="count-badge">
                                            <?php echo $data['ncantidad']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge <?php echo $status_class; ?>">
                                            <?php echo $status_text; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="list.php?idn=<?php echo $data['id_abn']; ?>&id=<?php echo $data['id_temp']; ?>&cat=<?php echo $data['categoria']; ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Ingresar Abonos">
                                                <button type="button" class="buto btn btn-info">
                                                    <i class="fas fa-cash-register"></i>
                                                </button>
                                            </a>

                                            <a href="formedit.php?id=<?php echo $data['id_abn']; ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Editar">
                                                <button type="button" class="buto btn btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </a>
                                            <a href="delet.php?id=<?php echo $data['id_abn']; ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Eliminar" onclick="return confirm('¿Estás seguro de eliminar este abono?')">
                                                <button type="button" class="buto btn btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php 
                                    } 
                                } else {
                                    echo '<tr><td colspan="5" class="empty-state"><i class="fas fa-receipt"></i><br>No hay abonos definidos para temporadas</td></tr>';
                                }
                                ?>
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
        
        // Inicializar tooltips de Bootstrap
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    </script>
</body>
</html>