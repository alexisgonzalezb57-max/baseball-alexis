<?php
// Configuración
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'baseball';
$backup_file = 'backup_' . $database . '_' . date('Y-m-d_H-i-s') . '.sql';

// Ruta a mysqldump (ajusta según tu instalación)
$mysqldump_path = "C:\\xampp\\mysql\\bin\\mysqldump.exe";

// Verificar si el archivo mysqldump existe
if (!file_exists($mysqldump_path)) {
    die("Error: No se encontró mysqldump.exe en la ruta especificada.");
}

// Opciones avanzadas de mysqldump que reducen tamaño:
$options = [
    '--opt',               // Activa optimizaciones
    '--skip-comments',     // Omite comentarios
    '--compact',           // Salida más compacta
    '--single-transaction', // Para tablas InnoDB
    '--no-create-db',      // No incluye CREATE DATABASE
];

$options_string = implode(' ', $options);

// Comando completo
$command = "\"{$mysqldump_path}\" {$options_string} --user={$user} --password={$password} --host={$host} {$database} 2>&1";

// Ejecutar
exec($command, $output, $return_var);

// Si estamos en modo preview (desde el botón)
if (isset($_POST['preview']) || isset($_GET['preview'])) {
    // Mostrar página con estilo
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Exportar Base de Datos - Baseball</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            
            .export-container {
                max-width: 900px;
                margin: 50px auto;
                background: white;
                border-radius: 15px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
                padding: 3rem;
                position: relative;
                overflow: hidden;
            }
            
            .export-container::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 5px;
                background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
            }
            
            .export-title {
                font-size: 2.2rem;
                font-weight: 700;
                color: var(--dark-color);
                margin-bottom: 1rem;
                text-align: center;
                position: relative;
                padding-bottom: 15px;
            }
            
            .export-title::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 50%;
                transform: translateX(-50%);
                width: 80px;
                height: 4px;
                background: #28a745;
                border-radius: 2px;
            }
            
            .result-box {
                background: #f8f9fa;
                border-radius: 10px;
                padding: 2rem;
                margin: 2rem 0;
                border-left: 5px solid #28a745;
            }
            
            .stats-box {
                background: #e7f6ea;
                border-radius: 10px;
                padding: 1.5rem;
                margin: 1.5rem 0;
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 15px;
            }
            
            .stat-item {
                text-align: center;
                padding: 15px;
                background: white;
                border-radius: 8px;
                border: 1px solid #d1e7dd;
            }
            
            .stat-label {
                font-weight: 600;
                color: #495057;
                font-size: 0.9rem;
                margin-bottom: 5px;
            }
            
            .stat-value {
                font-size: 1.5rem;
                font-weight: 700;
                color: #28a745;
            }
            
            .btn-download {
                background: #28a745;
                color: white;
                border: none;
                padding: 12px 30px;
                border-radius: 6px;
                font-size: 1.1rem;
                font-weight: 600;
                transition: all 0.3s;
                text-decoration: none;
                display: inline-block;
                margin: 10px;
            }
            
            .btn-download:hover {
                background: #218838;
                color: white;
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
            }
            
            .btn-back {
                background: #6c757d;
                color: white;
                border: none;
                padding: 12px 30px;
                border-radius: 6px;
                font-size: 1.1rem;
                font-weight: 600;
                transition: all 0.3s;
                text-decoration: none;
                display: inline-block;
                margin: 10px;
            }
            
            .btn-back:hover {
                background: #5a6268;
                color: white;
                transform: translateY(-2px);
            }
            
            .error-box {
                background: #f8d7da;
                border-radius: 10px;
                padding: 2rem;
                margin: 2rem 0;
                border-left: 5px solid #dc3545;
                color: #721c24;
            }
            
            .command-box {
                background: #e9ecef;
                border-radius: 8px;
                padding: 15px;
                font-family: monospace;
                font-size: 0.9rem;
                margin: 1rem 0;
                overflow-x: auto;
            }
            
            .text-center {
                text-align: center;
            }
            
            .mt-3 { margin-top: 1rem; }
            .mb-3 { margin-bottom: 1rem; }
        </style>
    </head>
    <body>
        <div class="export-container">
            <h1 class="export-title">EXPORTAR BASE DE DATOS</h1>
            
            <?php if ($return_var === 0): ?>
                <div class="result-box">
                    <div class="text-center mb-4">
                        <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                        <h3 class="text-success">¡Backup creado exitosamente!</h3>
                        <p class="mb-0">El archivo está listo para descargar</p>
                    </div>
                    
                    <div class="stats-box">
                        <div class="stat-item">
                            <div class="stat-label">Base de Datos</div>
                            <div class="stat-value"><?php echo htmlspecialchars($database); ?></div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Tamaño</div>
                            <div class="stat-value"><?php echo number_format(strlen(implode("\n", $output))); ?> bytes</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Fecha</div>
                            <div class="stat-value"><?php echo date('d/m/Y'); ?></div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Hora</div>
                            <div class="stat-value"><?php echo date('H:i:s'); ?></div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <p><strong>Archivo generado:</strong> <?php echo htmlspecialchars($backup_file); ?></p>
                        
                        <div class="mt-4">
                            <a href="exportar.php?download=1" class="btn-download">
                                <i class="fas fa-download me-2"></i>Descargar Backup
                            </a>
                            <a href="index.php" class="btn-back">
                                <i class="fas fa-arrow-left me-2"></i>Volver al Inicio
                            </a>
                        </div>
                        
                        <div class="mt-4">
                            <a href="exportar.php?download=1&compress=1" class="btn-download" style="background: #17a2b8;">
                                <i class="fas fa-file-archive me-2"></i>Descargar Comprimido (.gz)
                            </a>
                        </div>
                    </div>
                </div>
                
                <?php if (isset($_GET['debug'])): ?>
                    <div class="command-box">
                        <strong>Comando ejecutado:</strong><br>
                        <?php echo htmlspecialchars($command); ?>
                    </div>
                    
                    <details class="mt-3">
                        <summary>Vista previa del backup (primeras 20 líneas)</summary>
                        <pre class="command-box"><?php 
                        echo htmlspecialchars(implode("\n", array_slice($output, 0, 20)));
                        if (count($output) > 20) echo "\n[... " . (count($output) - 20) . " líneas más ...]";
                        ?></pre>
                    </details>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="error-box">
                    <div class="text-center mb-4">
                        <i class="fas fa-exclamation-triangle fa-4x text-danger mb-3"></i>
                        <h3 class="text-danger">Error al generar el backup</h3>
                        <p>Código de error: <?php echo $return_var; ?></p>
                    </div>
                    
                    <div class="command-box">
                        <strong>Comando ejecutado:</strong><br>
                        <?php echo htmlspecialchars($command); ?>
                    </div>
                    
                    <div class="command-box mt-3">
                        <strong>Salida del error:</strong><br>
                        <?php echo htmlspecialchars(implode("\n", $output)); ?>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="index.php" class="btn-back">
                            <i class="fas fa-arrow-left me-2"></i>Volver al Inicio
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// Modo descarga directa
if (isset($_GET['download'])) {
    if ($return_var === 0) {
        $backup_content = implode("\n", $output);
        
        // Comprimir si se solicita
        if(isset($_GET['compress']) && function_exists('gzencode')) {
            $backup_content = gzencode($backup_content, 9);
            $backup_file .= '.gz';
            header('Content-Type: application/x-gzip');
        } else {
            header('Content-Type: application/octet-stream');
        }
        
        // Headers para descarga
        header('Content-Disposition: attachment; filename="' . $backup_file . '"');
        header('Content-Length: ' . strlen($backup_content));
        
        echo $backup_content;
    } else {
        // Redirigir a la página de error con estilo
        header('Location: exportar.php?preview=1');
    }
    exit();
}

// Por defecto, redirigir al preview
header('Location: exportar.php?preview=1');
?>