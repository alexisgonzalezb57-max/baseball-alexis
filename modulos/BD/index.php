<?php
// Configuración de la base de datos (para información en la página)
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'baseball';

// Conectar para obtener información de la base de datos
$conn = new mysqli($host, $user, $password, $database);
$tables_count = 0;
$db_size = 'N/A';
$db_exists = false;

if ($conn->connect_error) {
    $db_status = '<span class="text-danger">No conectada</span>';
} else {
    $db_status = '<span class="text-success">Conectada</span>';
    $db_exists = true;
    
    // Contar tablas
    $result = $conn->query("SHOW TABLES");
    $tables_count = $result->num_rows;
    
    // Obtener tamaño de la base de datos
    $result = $conn->query("
        SELECT SUM(data_length + index_length) as size
        FROM information_schema.tables 
        WHERE table_schema = '$database'
    ");
    
    if ($row = $result->fetch_assoc()) {
        $size = $row['size'];
        if ($size > 0) {
            if ($size < 1024) {
                $db_size = $size . ' B';
            } elseif ($size < 1048576) {
                $db_size = round($size / 1024, 2) . ' KB';
            } else {
                $db_size = round($size / 1048576, 2) . ' MB';
            }
        }
    }
    
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador de Base de Datos - Baseball</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #fd7e14;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --success-color: #28a745;
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
            padding: 3rem 0;
        }
        
        .admin-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            padding: 3rem;
            position: relative;
            overflow: hidden;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .admin-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        }
        
        .admin-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 1rem;
            text-align: center;
            position: relative;
            padding-bottom: 15px;
        }
        
        .admin-title::after {
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
        
        .admin-subtitle {
            text-align: center;
            color: #6c757d;
            margin-bottom: 3rem;
            font-size: 1.1rem;
        }
        
        .options-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 2rem;
        }
        
        .option-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
            height: 100%;
            text-align: center;
            padding: 2.5rem 2rem;
        }
        
        .option-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
        }
        
        .option-card-import {
            border-top: 5px solid var(--primary-color);
        }
        
        .option-card-export {
            border-top: 5px solid var(--success-color);
        }
        
        .option-card-vaciar {
            border-top: 5px solid var(--danger-color);
        }
        
        .option-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            display: inline-block;
        }
        
        .icon-import {
            color: var(--primary-color);
        }
        
        .icon-export {
            color: var(--success-color);
        }
        
        .icon-vaciar {
            color: var(--danger-color);
        }
        
        .option-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 1rem;
        }
        
        .option-desc {
            color: #6c757d;
            margin-bottom: 2rem;
            font-size: 1rem;
            line-height: 1.6;
        }
        
        .btn-option {
            padding: 12px 30px;
            border-radius: 6px;
            font-size: 1.1rem;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            border: none;
            cursor: pointer;
            width: 200px;
        }
        
        .btn-import {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-import:hover {
            background: #0a58ca;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(13, 110, 253, 0.3);
        }
        
        .btn-export {
            background: var(--success-color);
            color: white;
        }
        
        .btn-export:hover {
            background: #218838;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(40, 167, 69, 0.3);
        }
        
        .btn-vaciar {
            background: var(--danger-color);
            color: white;
        }
        
        .btn-vaciar:hover {
            background: #c82333;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(220, 53, 69, 0.3);
        }
        
        .info-panel {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 3rem;
            border-left: 5px solid #6c757d;
        }
        
        .info-panel h4 {
            color: var(--dark-color);
            margin-bottom: 1rem;
        }
        
        .database-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 1rem;
        }
        
        .info-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        
        .info-label {
            font-weight: 600;
            color: #495057;
            font-size: 0.9rem;
        }
        
        .info-value {
            font-size: 1.1rem;
            font-weight: 600;
            margin-top: 5px;
        }
        
        .status-connected {
            color: var(--success-color);
        }
        
        .status-disconnected {
            color: var(--danger-color);
        }
        
        .warning-box {
            background: #fff3cd;
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 2rem;
            border-left: 5px solid var(--warning-color);
            color: #856404;
        }
        
        .footer {
            background: var(--dark-color);
            color: white;
            padding: 2rem 0;
            margin-top: 3rem;
        }
        
        @media (max-width: 768px) {
            .admin-container {
                padding: 2rem 1.5rem;
            }
            
            .admin-title {
                font-size: 1.8rem;
            }
            
            .options-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .option-card {
                padding: 2rem 1.5rem;
            }
            
            .navigation {
                flex-wrap: wrap;
                justify-content: center;
                margin-top: 1rem;
            }
            
            .clock {
                font-size: 1rem;
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
            <div class="admin-container">
                <h1 class="admin-title">ADMINISTRADOR DE BASE DE DATOS</h1>
                <p class="admin-subtitle">Gestiona respaldos, restauraciones y mantenimiento de la base de datos</p>
                
                <div class="options-grid">
                    <!-- Opción Importar -->
                    <div class="option-card option-card-import">
                        <div class="option-icon icon-import">
                            <i class="fas fa-database"></i>
                        </div>
                        <h3 class="option-title">Importar Base de Datos</h3>
                        <p class="option-desc">
                            Restaura una base de datos desde un archivo SQL. Importa datos nuevos 
                            manteniendo la estructura actual o realiza una importación completa.
                        </p>
                        <a href="importar.php" class="btn-option btn-import">
                            <i class="fas fa-upload me-2"></i>Importar SQL
                        </a>
                    </div>
                    
                    <!-- Opción Exportar -->
                    <div class="option-card option-card-export">
                        <div class="option-icon icon-export">
                            <i class="fas fa-file-export"></i>
                        </div>
                        <h3 class="option-title">Exportar Base de Datos</h3>
                        <p class="option-desc">
                            Crea un respaldo completo de tu base de datos en formato SQL. 
                            Descarga un archivo que contiene toda la estructura y datos actuales.
                        </p>
                        <a href="exportar.php" class="btn-option btn-export" onclick="return confirmExport()">
                            <i class="fas fa-download me-2"></i>Exportar SQL
                        </a>
                    </div>
                    
                    <!-- Opción Vaciar -->
                    <div class="option-card option-card-vaciar">
                        <div class="option-icon icon-vaciar">
                            <i class="fas fa-trash-alt"></i>
                        </div>
                        <h3 class="option-title">Vaciar Base de Datos</h3>
                        <p class="option-desc">
                            Elimina todos los datos de la base de datos manteniendo la estructura. 
                            Esta acción es irreversible - realiza un backup antes de continuar.
                        </p>
                        <a href="vaciar.php" class="btn-option btn-vaciar" onclick="return confirmVaciar()">
                            <i class="fas fa-broom me-2"></i>Vaciar Datos
                        </a>
                    </div>
                </div>
                
                <div class="info-panel">
                    <h4><i class="fas fa-info-circle me-2"></i>Información de la Base de Datos</h4>
                    <div class="database-info">
                        <div class="info-item">
                            <div class="info-label">Base de Datos</div>
                            <div class="info-value"><?php echo htmlspecialchars($database); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Estado</div>
                            <div class="info-value"><?php echo $db_status; ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Tablas</div>
                            <div class="info-value"><?php echo $tables_count; ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Tamaño</div>
                            <div class="info-value"><?php echo $db_size; ?></div>
                        </div>
                    </div>
                </div>
                
                <?php if ($db_exists): ?>
                <div class="warning-box">
                    <h5><i class="fas fa-exclamation-triangle me-2"></i>Recomendaciones de Seguridad</h5>
                    <ul class="mb-0 mt-2">
                        <li>Realiza un <strong>exportar</strong> antes de cualquier operación crítica</li>
                        <li>El <strong>vaciar datos</strong> elimina toda la información pero mantiene las tablas</li>
                        <li>Verifica siempre el contenido de los archivos SQL antes de importar</li>
                        <li>Mantén respaldos regulares en diferentes ubicaciones</li>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container text-center">
            <p>&copy; <?php echo date('Y'); ?> Sistema de Gestión de Baseball. Todos los derechos reservados.</p>
            <div class="mt-2">
                <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                <a href="#" class="text-white"><i class="fab fa-youtube"></i></a>
            </div>
        </div>
    </footer>

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
        
        // Confirmación para exportar
        function confirmExport() {
            const ahora = new Date();
            const fecha = ahora.toLocaleDateString('es-ES');
            const hora = ahora.toLocaleTimeString('es-ES');
            
            return confirm(`¿Estás seguro de que deseas exportar la base de datos?\n\nSe generará un archivo SQL con fecha: ${fecha} ${hora}`);
        }
        
        // Confirmación para vaciar
        function confirmVaciar() {
            return confirm('⚠️ ¡ADVERTENCIA! ⚠️\n\n¿Estás completamente seguro de que deseas VACIAR la base de datos?\n\n✅ Se mantendrán las tablas (estructura)\n❌ Se eliminarán TODOS los datos\n\nEsta acción es IRREVERSIBLE.\n\n¿Continuar?');
        }
    </script>
</body>
</html>