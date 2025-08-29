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

// Obtener información de la temporada
$revisar = "SELECT * FROM temporada WHERE id_temp = ? AND categoria LIKE CONCAT('%', ?, '%')";
$stmt = mysqli_prepare($con, $revisar);
mysqli_stmt_bind_param($stmt, "is", $id, $cat);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_array($result);

// Procesar formulario de creación de equipo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eteam'])) {
    $id_team = filter_var($_POST['eteam'], FILTER_SANITIZE_NUMBER_INT);
    $name_team = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    
    // Insertar equipo en la clasificación
    $insert = "INSERT INTO tab_clasf (id_temp, id_team, name_team, categoria, jj, jg, jp, je, avg, ca, ce, dif) 
               VALUES (?, ?, ?, ?, 0, 0, 0, 0, 0, 0, 0, 0)";
    $stmt_insert = mysqli_prepare($con, $insert);
    mysqli_stmt_bind_param($stmt_insert, "iiss", $id, $id_team, $name_team, $cat);
    
    if (mysqli_stmt_execute($stmt_insert)) {
        $success_msg = "Equipo agregado exitosamente a la temporada.";
        // Redirigir para evitar reenvío del formulario
        header("Location: formclasf.php?id=$id&cat=$cat&success=1");
        exit();
    } else {
        $error_msg = "Error al agregar el equipo: " . mysqli_error($con);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestionar Equipos - Sistema Baseball</title>
    
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
        
        .form-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border-left: 4px solid var(--primary-color);
        }
        
        @media (max-width: 992px) {
            .navigation {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .action-buttons {
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
                <h1 class="page-title">Agregar Equipos: <?php echo htmlspecialchars($data['name_temp'] ?? 'Temporada'); ?></h1>
                
                <!-- Mostrar mensajes de éxito o error -->
                <?php if (isset($success_msg) || isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo $success_msg ?? 'Equipo agregado exitosamente a la temporada.'; ?>
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
                
                <div class="button-group mb-4">
                    <a href="../juego/list.php?id=<?php echo $id ?>&cat=<?php echo urlencode($cat) ?>" class="btn btn-info">
                        <i class="fas fa-arrow-left me-2"></i>Volver a Clasificación
                    </a>
                </div>
                
                <div class="form-card">
                    <h4 class="mb-3">Agregar Equipo a la Temporada</h4>
                    <form method="POST" action="">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <input type="hidden" name="cat" value="<?php echo htmlspecialchars($cat); ?>">
                        
                        <div class="row g-3 align-items-center">
                            <div class="col-md-5">
                                <label class="form-label">Seleccione el Equipo</label>
                                <select class="form-control" required name="eteam" id="team" aria-label="Seleccionar equipo">
                                    <option value="">Seleccione un equipo...</option>
                                    <?php
                                    $revisar = "SELECT * FROM equipos
                                                WHERE categoria LIKE CONCAT('%', ?, '%') AND id_team NOT IN 
                                                (SELECT id_team FROM tab_clasf WHERE id_temp = ?)
                                                ORDER BY nom_team ASC";
                                    $stmt = mysqli_prepare($con, $revisar);
                                    mysqli_stmt_bind_param($stmt, "si", $cat, $id);
                                    mysqli_stmt_execute($stmt);
                                    $result = mysqli_stmt_get_result($stmt);
                                    $num = mysqli_num_rows($result);
                                    
                                    if ($num >= 1) {
                                        while ($bdata = mysqli_fetch_array($result)) {
                                            echo '<option value="' . $bdata['id_team'] . '">' . htmlspecialchars($bdata['nom_team']) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            
                            <div class="col-md-5">
                                <label class="form-label">Nombre del Equipo (confirmación)</label>
                                <select class="form-control" id="name" name="name" required>
                                    <option value="">Seleccione un equipo primero</option>
                                </select>
                            </div>
                            
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-plus-circle me-2"></i>Agregar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <div class="table-container">
                    <h4 class="mb-3">Equipos en esta Temporada</h4>
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>Equipo</th>
                                <th>Categoría</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $revisar = "SELECT * FROM tab_clasf WHERE id_temp = ? AND categoria LIKE CONCAT('%', ?, '%') ORDER BY name_team ASC";
                            $stmt = mysqli_prepare($con, $revisar);
                            mysqli_stmt_bind_param($stmt, "is", $id, $cat);
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);
                            $num = mysqli_num_rows($result);
                            
                            if ($num >= 1) {
                                while ($bdata = mysqli_fetch_array($result)) {
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($bdata['name_team']); ?></td>
                                <td><?php echo htmlspecialchars($cat); ?></td>
                                <td class="text-center">
                                    <div class="action-buttons">
                                        <a href="clasfedit.php?id=<?php echo $bdata['id_tab'] ?>&cat=<?php echo urlencode($cat) ?>" class="btn btn-sm btn-warning action-btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="deletclasf.php?id_tab=<?php echo $bdata['id_tab'] ?>&id_temp=<?php echo $id ?>&cat=<?php echo urlencode($cat) ?>" class="btn btn-sm btn-danger action-btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Eliminar" onclick="return confirm('¿Está seguro de eliminar este equipo de la temporada?');">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php 
                                }
                            } else {
                            ?>
                            <tr>
                                <td colspan="3">
                                    <div class="empty-state">
                                        <i class="fas fa-users-slash"></i>
                                        <h4>No hay equipos registrados en esta temporada</h4>
                                        <p>Agrega equipos usando el formulario superior</p>
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
        
        // Cargar nombres de equipos cuando se selecciona uno
        document.addEventListener('DOMContentLoaded', function() {
            const teamSelect = document.getElementById('team');
            const nameSelect = document.getElementById('name');
            
            teamSelect.addEventListener('change', function() {
                const teamId = this.value;
                
                if (teamId) {
                    // Hacer una solicitud AJAX para obtener el nombre del equipo
                    fetch(`name.php?team=${teamId}`)
                        .then(response => response.text())
                        .then(data => {
                            nameSelect.innerHTML = data;
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            nameSelect.innerHTML = '<option value="">Error al cargar</option>';
                        });
                } else {
                    nameSelect.innerHTML = '<option value="">Seleccione un equipo primero</option>';
                }
            });
            
            // Inicializar tooltips de Bootstrap
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
</body>
</html>