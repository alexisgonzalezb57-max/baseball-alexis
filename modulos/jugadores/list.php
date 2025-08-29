<?php
include("../../config/conexion.php");
$con = conectar();

// Validar y sanitizar la entrada
$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
$cat = isset($_REQUEST['cat']) ? mysqli_real_escape_string($con, $_REQUEST['cat']) : '';

// Verificar conexión
if (!$con) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Obtener datos del equipo
$revisar = "SELECT * FROM equipos WHERE id_team = $id";
$query = mysqli_query($con, $revisar);

if (!$query) {
    die("Error en la consulta: " . mysqli_error($con));
}

$data = mysqli_fetch_array($query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jugadores - <?php echo htmlspecialchars($data['nom_team'] ?? ''); ?></title>
    
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
        
        .players-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }
        
        .players-container::before {
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
        
        .team-info {
            background: linear-gradient(90deg, rgba(13, 110, 253, 0.1) 0%, rgba(253, 126, 20, 0.1) 100%);
            border-radius: 10px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--primary-color);
        }
        
        .team-name {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }
        
        .team-category {
            font-size: 1.1rem;
            color: #6c757d;
        }
        
        .action-buttons .btn {
            margin-right: 10px;
            border-radius: 6px;
            font-weight: 500;
            padding: 10px 20px;
        }
        
        .table-container {
            margin-top: 2rem;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .table thead {
            background: linear-gradient(90deg, var(--primary-color) 0%, #0a58ca 100%);
            color: white;
        }
        
        .table th {
            padding: 1rem;
            font-weight: 600;
            vertical-align: middle;
        }
        
        .table td {
            padding: 1rem;
            vertical-align: middle;
        }
        
        .btn-action {
            padding: 0.5rem;
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            margin-right: 5px;
        }
        
        .badge-pitcher {
            background: var(--secondary-color);
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .badge-not-pitcher {
            background: #6c757d;
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #dee2e6;
        }
        
        @media (max-width: 992px) {
            .navigation {
                flex-wrap: wrap;
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
            
            .table-responsive {
                overflow-x: auto;
            }
            
            .team-name {
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
            <div class="players-container">
                <h1 class="page-title">Lista de Jugadores</h1>
                
                <div class="team-info">
                    <div class="team-name"><?php echo htmlspecialchars($data['nom_team'] ?? ''); ?></div>
                    <div class="team-category">Categoría: "<?php echo htmlspecialchars($cat); ?>"</div>
                </div>
                
                <div class="action-buttons mb-4">
                    <a href="form.php?id=<?php echo $id; ?>&cat=<?php echo urlencode($cat); ?>" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-2"></i>Nuevo Jugador
                    </a>
                    <a href="../equipos/nomina.php?cat=<?php echo urlencode($cat); ?>" class="btn btn-info">
                        <i class="fas fa-arrow-left me-2"></i>Volver
                    </a>
                </div>
                
                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Cédula</th>
                                    <th scope="col">Jugador</th>
                                    <th scope="col">Fecha Nac.</th>
                                    <th scope="col">Edad</th>
                                    <th scope="col">Lanzador</th>
                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $obtener = "SELECT * FROM jugadores WHERE id_team = $id";
                                $query = mysqli_query($con, $obtener);
                                
                                if (!$query) {
                                    echo "<tr><td colspan='7' class='text-center text-danger'>Error en la consulta: " . mysqli_error($con) . "</td></tr>";
                                } else {
                                    $num = mysqli_num_rows($query);
                                    
                                    if ($num > 0) {
                                        for ($i = 1; $i <= $num; ++$i) {
                                            $playerData = mysqli_fetch_array($query);
                                            $isPitcher = !empty($playerData['lanzador']);
                                ?>
                                            <tr>
                                                <th scope="row"><?php echo $i; ?></th>
                                                <td><?php echo htmlspecialchars($playerData['cedula']); ?></td>
                                                <td class="fw-bold"><?php echo htmlspecialchars($playerData['nombre'] . " " . $playerData['apellido']); ?></td>
                                                <td><?php echo htmlspecialchars($playerData['fecha']); ?></td>
                                                <td><?php echo htmlspecialchars($playerData['edad']); ?></td>
                                                <td>
                                                    <span class="<?php echo $isPitcher ? 'badge-pitcher' : 'badge-not-pitcher'; ?>">
                                                        <?php echo $isPitcher ? 'SI' : 'NO'; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="formedit.php?id=<?php echo $playerData['id_player']; ?>&cat=<?php echo urlencode($cat); ?>" class="btn btn-warning btn-action" data-bs-toggle="tooltip" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="deletplayer.php?id=<?php echo $playerData['id_player']; ?>&cat=<?php echo urlencode($cat); ?>" class="btn btn-danger btn-action" data-bs-toggle="tooltip" title="Eliminar" onclick="return confirm('¿Estás seguro de que deseas eliminar este jugador?');">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                <?php
                                        }
                                    } else {
                                        echo "<tr><td colspan='7' class='text-center'>
                                            <div class='empty-state'>
                                                <i class='fas fa-user-slash'></i>
                                                <h4>No hay jugadores en este equipo</h4>
                                                <p>Agrega un nuevo jugador para comenzar</p>
                                            </div>
                                        </td></tr>";
                                    }
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
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>
</body>
</html>