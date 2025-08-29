<?php
include("../../config/conexion.php");
$con = conectar();

// Validar y sanitizar la entrada
$cat = isset($_REQUEST['cat']) ? mysqli_real_escape_string($con, $_REQUEST['cat']) : '';
$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

// Verificar conexión
if (!$con) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Obtener datos del equipo
$revisar = "SELECT * FROM equipos WHERE id_team = $id AND categoria LIKE '%$cat%'";
$query = mysqli_query($con, $revisar);
$data = mysqli_fetch_array($query);

// Verificar si se encontraron datos
if (!$data) {
    die("No se encontró el equipo solicitado.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Equipo - <?php echo htmlspecialchars($cat); ?></title>
    
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
        
        .form-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            padding: 2rem;
            position: relative;
            overflow: hidden;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .form-container::before {
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
        
        .category-info {
            background: linear-gradient(90deg, rgba(13, 110, 253, 0.1) 0%, rgba(253, 126, 20, 0.1) 100%);
            border-radius: 10px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--primary-color);
            text-align: center;
        }
        
        .category-name {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            border: 1px solid #ced4da;
            border-radius: 6px;
            padding: 0.75rem 1rem;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        
        .btn {
            border-radius: 6px;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s;
        }
        
        .btn-success {
            background: linear-gradient(90deg, #198754 0%, #146c43 100%);
            border: none;
        }
        
        .btn-success:hover {
            background: linear-gradient(90deg, #146c43 0%, #0f5132 100%);
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
            
            .category-name {
                font-size: 1.3rem;
            }
            
            .form-container {
                padding: 1.5rem;
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
            <div class="form-container">
                <h1 class="page-title">Editar Equipo</h1>
                
                <div class="category-info">
                    <div class="category-name">Categoría: "<?php echo htmlspecialchars($cat); ?>"</div>
                </div>
                
                <form method="POST" action="update.php">
                    <div class="mb-4">
                        <div class="mb-3">
                            <label for="n_team" class="form-label">Nombre del Equipo</label>
                            <input type="text" class="form-control" id="n_team" name="n_team" 
                                   value="<?php echo htmlspecialchars($data['nom_team']); ?>" 
                                   placeholder="Ingrese el nombre del equipo" required>
                        </div>
                    </div>
                    
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <input type="hidden" name="cat" value="<?php echo htmlspecialchars($cat); ?>">
                    
                    <div class="d-flex justify-content-end mt-4">
                        <a href="../equipos/nomina.php?cat=<?php echo urlencode($cat); ?>" class="btn btn-info me-3">
                            <i class="fas fa-arrow-left me-2"></i>Volver
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-2"></i>Guardar Cambios
                        </button>
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
    </script>
</body>
</html>