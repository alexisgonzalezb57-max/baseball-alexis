<?php
// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$database = "baseball";

$mensaje = '';
$tipo_mensaje = '';
$import_mode = isset($_POST['import_mode']) ? $_POST['import_mode'] : 'complete';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['sql_file'])) {
    try {
        // Conectar a MySQL
        $conn = new mysqli($servername, $username, $password);
        
        // Verificar conexión
        if ($conn->connect_error) {
            throw new Exception("Conexión fallida: " . $conn->connect_error);
        }
        
        // Crear base de datos si no existe
        $sql = "CREATE DATABASE IF NOT EXISTS $database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        if (!$conn->query($sql)) {
            throw new Exception("Error creando base de datos: " . $conn->error);
        }
        
        // Seleccionar la base de datos
        $conn->select_db($database);
        
        // DESACTIVAR restricciones de claves foráneas temporalmente
        $conn->query("SET FOREIGN_KEY_CHECKS = 0");
        
        // Procesar archivo SQL
        $sqlFile = $_FILES['sql_file']['tmp_name'];
        
        // Verificar si se subió correctamente
        if (!is_uploaded_file($sqlFile)) {
            throw new Exception("Error al subir el archivo");
        }
        
        // Leer el contenido del archivo
        $sqlContent = file_get_contents($sqlFile);
        
        if ($sqlContent === false) {
            throw new Exception("No se pudo leer el archivo SQL");
        }
        
        // Si el archivo está comprimido con gzip, descomprimir
        if (strpos($sqlContent, "\x1f\x8b\x08") === 0) {
            $sqlContent = gzdecode($sqlContent);
            if ($sqlContent === false) {
                throw new Exception("Error al descomprimir el archivo GZIP");
            }
        }
        
        // Eliminar BOM si existe
        $sqlContent = preg_replace('/^\xEF\xBB\xBF/', '', $sqlContent);
        
        // Modo de importación
        $consultas_ejecutadas = 0;
        $consultas_omitidas = 0;
        $errores = [];
        
        if ($import_mode === 'data_only') {
            // Solo ejecutar INSERT, UPDATE, REPLACE (sin DROP, CREATE, ALTER, TRUNCATE)
            // Dividir en consultas individuales
            $queries = [];
            $current_query = '';
            $in_string = false;
            $string_char = '';
            $in_comment = false;
            
            // Procesar caracter por caracter para manejar strings correctamente
            for ($i = 0; $i < strlen($sqlContent); $i++) {
                $char = $sqlContent[$i];
                $prev_char = ($i > 0) ? $sqlContent[$i-1] : '';
                $next_char = ($i < strlen($sqlContent) - 1) ? $sqlContent[$i+1] : '';
                
                // Manejar comentarios
                if (!$in_string && !$in_comment && $char === '/' && $next_char === '*') {
                    $in_comment = true;
                    $i++;
                    continue;
                }
                
                if ($in_comment && $char === '*' && $next_char === '/') {
                    $in_comment = false;
                    $i++;
                    continue;
                }
                
                if ($in_comment) {
                    continue;
                }
                
                // Manejar comentarios de línea
                if (!$in_string && $char === '-' && $next_char === '-') {
                    // Saltar hasta el final de la línea
                    while ($i < strlen($sqlContent) && $sqlContent[$i] !== "\n") {
                        $i++;
                    }
                    continue;
                }
                
                // Manejar comillas
                if (!$in_comment && ($char === "'" || $char === '"' || $char === '`') && $prev_char !== '\\') {
                    if (!$in_string) {
                        $in_string = true;
                        $string_char = $char;
                    } elseif ($in_string && $string_char === $char) {
                        $in_string = false;
                    }
                }
                
                // Si encontramos punto y coma y no estamos dentro de un string o comentario
                if ($char === ';' && !$in_string && !$in_comment) {
                    $current_query = trim($current_query);
                    if (!empty($current_query)) {
                        $queries[] = $current_query;
                    }
                    $current_query = '';
                } else {
                    $current_query .= $char;
                }
            }
            
            // Agregar la última consulta si existe
            $current_query = trim($current_query);
            if (!empty($current_query)) {
                $queries[] = $current_query;
            }
            
            // Ejecutar solo consultas de datos
            foreach ($queries as $query) {
                $query = trim($query);
                if (empty($query)) continue;
                
                $query_upper = strtoupper($query);
                
                // Verificar si es una consulta de datos permitida
                if (strpos($query_upper, 'INSERT') === 0 || 
                    strpos($query_upper, 'UPDATE') === 0 || 
                    strpos($query_upper, 'REPLACE') === 0) {
                    
                    if ($conn->query($query)) {
                        $consultas_ejecutadas++;
                    } else {
                        $errores[] = "Error en consulta: " . substr($query, 0, 200) . "... Error: " . $conn->error;
                    }
                } else {
                    $consultas_omitidas++;
                }
            }
            
            if (empty($errores)) {
                $mensaje = "Importación de datos completada. Se ejecutaron $consultas_ejecutadas consultas de datos ($consultas_omitidas omitidas).";
                $tipo_mensaje = 'success';
            } else {
                $mensaje = "Se ejecutaron $consultas_ejecutadas consultas con éxito, pero hubo " . count($errores) . " errores.";
                $tipo_mensaje = 'error';
            }
            
        } else {
            // Modo completo - dividir consultas y ejecutar en orden correcto
            $queries = [];
            $current_query = '';
            $in_string = false;
            $string_char = '';
            $in_comment = false;
            
            // Primero, separar todas las consultas
            for ($i = 0; $i < strlen($sqlContent); $i++) {
                $char = $sqlContent[$i];
                $prev_char = ($i > 0) ? $sqlContent[$i-1] : '';
                $next_char = ($i < strlen($sqlContent) - 1) ? $sqlContent[$i+1] : '';
                
                // Manejar comentarios
                if (!$in_string && !$in_comment && $char === '/' && $next_char === '*') {
                    $in_comment = true;
                    $i++;
                    continue;
                }
                
                if ($in_comment && $char === '*' && $next_char === '/') {
                    $in_comment = false;
                    $i++;
                    continue;
                }
                
                if ($in_comment) {
                    continue;
                }
                
                // Manejar comentarios de línea
                if (!$in_string && $char === '-' && $next_char === '-') {
                    // Saltar hasta el final de la línea
                    while ($i < strlen($sqlContent) && $sqlContent[$i] !== "\n") {
                        $i++;
                    }
                    continue;
                }
                
                // Manejar comillas
                if (!$in_comment && ($char === "'" || $char === '"' || $char === '`') && $prev_char !== '\\') {
                    if (!$in_string) {
                        $in_string = true;
                        $string_char = $char;
                    } elseif ($in_string && $string_char === $char) {
                        $in_string = false;
                    }
                }
                
                // Si encontramos punto y coma y no estamos dentro de un string o comentario
                if ($char === ';' && !$in_string && !$in_comment) {
                    $current_query = trim($current_query);
                    if (!empty($current_query)) {
                        $queries[] = $current_query;
                    }
                    $current_query = '';
                } else {
                    $current_query .= $char;
                }
            }
            
            // Agregar la última consulta si existe
            $current_query = trim($current_query);
            if (!empty($current_query)) {
                $queries[] = $current_query;
            }
            
            // Separar consultas de estructura (CREATE, DROP, ALTER) y datos (INSERT)
            $structure_queries = [];
            $data_queries = [];
            $fk_queries = [];
            
            foreach ($queries as $query) {
                $query_upper = strtoupper($query);
                
                if (strpos($query_upper, 'CREATE TABLE') === 0) {
                    // Para CREATE TABLE, necesitamos separar las FOREIGN KEY
                    if (strpos($query_upper, 'FOREIGN KEY') !== false) {
                        // Extraer FOREIGN KEY constraints para ejecutar después
                        preg_match_all('/CONSTRAINT\s+[^ ]+\s+FOREIGN\s+KEY[^)]+\)[^)]+\)/i', $query, $matches, PREG_OFFSET_CAPTURE);
                        
                        if (!empty($matches[0])) {
                            // Quitar las FOREIGN KEY de la creación de tabla
                            $table_query = $query;
                            foreach ($matches[0] as $match) {
                                $fk_queries[] = $match[0];
                                $table_query = str_replace($match[0], '', $table_query);
                            }
                            // Limpiar comas extra
                            $table_query = preg_replace('/,\s*,/', ',', $table_query);
                            $table_query = preg_replace('/,\s*\)/', ')', $table_query);
                            $structure_queries[] = $table_query;
                        } else {
                            $structure_queries[] = $query;
                        }
                    } else {
                        $structure_queries[] = $query;
                    }
                } elseif (strpos($query_upper, 'ALTER TABLE') === 0 && strpos($query_upper, 'ADD CONSTRAINT') !== false) {
                    // Almacenar ALTER TABLE que añaden FOREIGN KEY
                    $fk_queries[] = $query;
                } elseif (strpos($query_upper, 'INSERT') === 0 || 
                          strpos($query_upper, 'UPDATE') === 0 || 
                          strpos($query_upper, 'REPLACE') === 0) {
                    $data_queries[] = $query;
                } else {
                    $structure_queries[] = $query;
                }
            }
            
            // Paso 1: Ejecutar consultas de estructura (sin FOREIGN KEY)
            foreach ($structure_queries as $query) {
                if ($conn->query($query)) {
                    $consultas_ejecutadas++;
                } else {
                    $errores[] = "Error en estructura: " . substr($query, 0, 200) . "... Error: " . $conn->error;
                }
            }
            
            // Paso 2: Ejecutar consultas de datos
            foreach ($data_queries as $query) {
                if ($conn->query($query)) {
                    $consultas_ejecutadas++;
                } else {
                    $errores[] = "Error en datos: " . substr($query, 0, 200) . "... Error: " . $conn->error;
                }
            }
            
            // Paso 3: Ejecutar consultas de FOREIGN KEY (ahora que todas las tablas existen)
            foreach ($fk_queries as $query) {
                if ($conn->query($query)) {
                    $consultas_ejecutadas++;
                } else {
                    $errores[] = "Error en FOREIGN KEY: " . substr($query, 0, 200) . "... Error: " . $conn->error;
                }
            }
            
            if (empty($errores)) {
                $mensaje = "Importación completa exitosa! Se ejecutaron $consultas_ejecutadas consultas.";
                $tipo_mensaje = 'success';
            } else {
                $mensaje = "Importación parcial. Se ejecutaron $consultas_ejecutadas consultas con " . count($errores) . " errores.";
                $tipo_mensaje = 'warning';
            }
        }
        
        // REACTIVAR restricciones de claves foráneas
        $conn->query("SET FOREIGN_KEY_CHECKS = 1");
        
        // Mostrar errores si los hay
        if (!empty($errores) && count($errores) > 0) {
            $mensaje .= "<br><br><strong>Errores encontrados:</strong><br>";
            foreach (array_slice($errores, 0, 5) as $error) { // Mostrar solo primeros 5 errores
                $mensaje .= "- " . htmlspecialchars($error) . "<br>";
            }
            if (count($errores) > 5) {
                $mensaje .= "... y " . (count($errores) - 5) . " errores más";
            }
        }
        
    } catch (Exception $e) {
        // Asegurarse de reactivar las FOREIGN KEY checks si hay error
        if (isset($conn)) {
            $conn->query("SET FOREIGN_KEY_CHECKS = 1");
        }
        $mensaje = "Error: " . $e->getMessage();
        $tipo_mensaje = 'error';
    } finally {
        if (isset($conn)) {
            $conn->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importar Base de Datos - Baseball</title>
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
        
        .import-container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            padding: 3rem;
            position: relative;
            overflow: hidden;
        }
        
        .import-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-color) 0%, #0a58ca 100%);
        }
        
        .import-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 1rem;
            text-align: center;
            position: relative;
            padding-bottom: 15px;
        }
        
        .import-title::after {
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
        
        .mode-selector {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin: 2rem 0;
        }
        
        .mode-option {
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            cursor: pointer;
            border: 2px solid #dee2e6;
            transition: all 0.3s;
        }
        
        .mode-option:hover {
            border-color: var(--primary-color);
            background: #f0f7ff;
        }
        
        .mode-option.selected {
            border-color: var(--primary-color);
            background: #e7f1ff;
        }
        
        .mode-option h5 {
            color: var(--dark-color);
            margin-bottom: 5px;
        }
        
        .mode-option.complete {
            border-left: 4px solid var(--primary-color);
        }
        
        .mode-option.data-only {
            border-left: 4px solid #28a745;
        }
        
        .upload-area {
            border: 3px dashed #ddd;
            border-radius: 12px;
            padding: 3rem 2rem;
            text-align: center;
            margin: 2rem 0;
            background: #fafafa;
            transition: all 0.3s;
        }
        
        .upload-area:hover {
            border-color: var(--primary-color);
            background: #f0f7ff;
        }
        
        .upload-icon {
            font-size: 4rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .file-input {
            display: none;
        }
        
        .file-label {
            background: var(--primary-color);
            color: white;
            padding: 12px 25px;
            border-radius: 6px;
            cursor: pointer;
            display: inline-block;
            margin-top: 1rem;
            transition: all 0.3s;
            font-weight: 500;
        }
        
        .file-label:hover {
            background: #0a58ca;
            transform: translateY(-2px);
        }
        
        .selected-file {
            margin-top: 1rem;
            color: #6c757d;
            font-style: italic;
        }
        
        .btn-submit {
            background: var(--secondary-color);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 6px;
            font-size: 1.1rem;
            font-weight: 600;
            transition: all 0.3s;
            display: block;
            margin: 2rem auto 0;
            width: 250px;
        }
        
        .btn-submit:hover {
            background: #e2660d;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(253, 126, 20, 0.3);
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
            transform: translateY(-2px);
        }
        
        .alert-custom {
            border-radius: 10px;
            padding: 1.5rem;
            margin: 2rem 0;
            border: none;
            font-size: 1.1rem;
        }
        
        .alert-success {
            background: linear-gradient(90deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            border-left: 5px solid #28a745;
        }
        
        .alert-error {
            background: linear-gradient(90deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
            border-left: 5px solid #dc3545;
        }
        
        .alert-warning {
            background: linear-gradient(90deg, #fff3cd 0%, #ffeaa7 100%);
            color: #856404;
            border-left: 5px solid #ffc107;
        }
        
        .info-box {
            background: #e7f1ff;
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 2rem;
            border-left: 5px solid var(--primary-color);
        }
        
        .info-box h4 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .text-center {
            text-align: center;
        }
        
        .file-types {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }
        
        .file-type-badge {
            background: #e9ecef;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            color: #495057;
        }
        
        .processing-steps {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 2rem;
            border-left: 5px solid #6c757d;
        }
        
        .step-item {
            display: flex;
            align-items: center;
            margin: 10px 0;
            padding: 10px;
            background: white;
            border-radius: 6px;
        }
        
        .step-number {
            background: var(--primary-color);
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="import-container">
        <h1 class="import-title">IMPORTAR BASE DE DATOS</h1>
        
        <?php if ($mensaje): ?>
            <div class="alert-custom <?php 
                if ($tipo_mensaje === 'success') echo 'alert-success';
                elseif ($tipo_mensaje === 'error') echo 'alert-error';
                else echo 'alert-warning';
            ?>">
                <i class="fas <?php 
                    if ($tipo_mensaje === 'success') echo 'fa-check-circle';
                    elseif ($tipo_mensaje === 'error') echo 'fa-exclamation-circle';
                    else echo 'fa-exclamation-triangle';
                ?> me-2"></i>
                <?php echo $mensaje; ?>
            </div>
            
            <div class="text-center">
                <a href="index.php" class="btn-back">
                    <i class="fas fa-home me-2"></i>Volver al Inicio
                </a>
                <?php if ($tipo_mensaje !== 'error'): ?>
                <a href="importar.php" class="btn-submit" style="display: inline-block; width: auto;">
                    <i class="fas fa-redo me-2"></i>Importar Otro Archivo
                </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!$mensaje || $tipo_mensaje === 'error'): ?>
        <form method="POST" enctype="multipart/form-data" id="uploadForm">
            <div class="mode-selector">
                <h4><i class="fas fa-cog me-2"></i>Modo de Importación</h4>
                
                <div class="mode-option complete <?php echo $import_mode === 'complete' ? 'selected' : ''; ?>" 
                     onclick="selectMode('complete')">
                    <input type="radio" name="import_mode" value="complete" 
                           <?php echo $import_mode === 'complete' ? 'checked' : ''; ?> hidden>
                    <h5><i class="fas fa-sync-alt me-2"></i>Importación Completa</h5>
                    <p class="mb-0">Reemplaza toda la base de datos. Incluye creación de tablas, índices y datos.</p>
                    <small class="text-muted">(Recomendado para archivos exportados con mysqldump)</small>
                </div>
                
                <div class="mode-option data-only <?php echo $import_mode === 'data_only' ? 'selected' : ''; ?>" 
                     onclick="selectMode('data_only')">
                    <input type="radio" name="import_mode" value="data_only" 
                           <?php echo $import_mode === 'data_only' ? 'checked' : ''; ?> hidden>
                    <h5><i class="fas fa-database me-2"></i>Solo Datos Nuevos</h5>
                    <p class="mb-0">Importa solo datos sin modificar la estructura. Mantiene las tablas existentes.</p>
                    <small class="text-muted">(Solo para archivos que contengan principalmente INSERT, UPDATE, REPLACE)</small>
                </div>
            </div>
            
            <div class="upload-area">
                <div class="upload-icon">
                    <i class="fas fa-database"></i>
                </div>
                <h3>Selecciona tu archivo SQL</h3>
                <p>Arrastra y suelta o haz clic para buscar</p>
                
                <div class="file-types">
                    <span class="file-type-badge">.sql</span>
                    <span class="file-type-badge">.sql.gz</span>
                    <span class="file-type-badge">.gz</span>
                </div>
                
                <input type="file" name="sql_file" id="sql_file" class="file-input" 
                       accept=".sql,.sql.gz,.gz" required>
                <label for="sql_file" class="file-label">
                    <i class="fas fa-folder-open me-2"></i>Buscar Archivo
                </label>
                
                <div class="selected-file" id="file-name">
                    Ningún archivo seleccionado
                </div>
            </div>
            
            <?php if ($import_mode === 'complete'): ?>
            <div class="processing-steps">
                <h5><i class="fas fa-list-ol me-2"></i>Proceso de Importación Completa:</h5>
                <div class="step-item">
                    <div class="step-number">1</div>
                    <div>Desactivar restricciones de claves foráneas</div>
                </div>
                <div class="step-item">
                    <div class="step-number">2</div>
                    <div>Crear todas las tablas (sin FOREIGN KEY)</div>
                </div>
                <div class="step-item">
                    <div class="step-number">3</div>
                    <div>Insertar datos en las tablas</div>
                </div>
                <div class="step-item">
                    <div class="step-number">4</div>
                    <div>Añadir restricciones FOREIGN KEY</div>
                </div>
                <div class="step-item">
                    <div class="step-number">5</div>
                    <div>Reactivar restricciones de claves foráneas</div>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="text-center">
                <button type="submit" class="btn-submit" id="submitBtn">
                    <i class="fas fa-upload me-2"></i>Importar Base de Datos
                </button>
                <a href="index.php" class="btn-back">
                    <i class="fas fa-times me-2"></i>Cancelar
                </a>
            </div>
        </form>
        
        <div class="info-box">
            <h4><i class="fas fa-info-circle me-2"></i>Información Importante</h4>
            <ul>
                <li>Acepta archivos: <strong>.sql</strong>, <strong>.sql.gz</strong>, <strong>.gz</strong></li>
                <li><strong>Importación Completa:</strong> Maneja automáticamente problemas de FOREIGN KEY</li>
                <li><strong>Solo Datos:</strong> Mantiene la estructura existente</li>
                <li>Base de datos: <strong><?php echo $database; ?></strong></li>
                <li>Tamaño máximo recomendado: 50MB</li>
                <li>Para archivos grandes, la importación puede tardar varios minutos</li>
                <li>Se recomienda hacer backup antes de importar</li>
            </ul>
        </div>
        <?php endif; ?>
    </div>

    <script>
        // Seleccionar modo
        function selectMode(mode) {
            document.querySelectorAll('.mode-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            
            document.querySelector(`.mode-option.${mode.replace('_', '-')}`).classList.add('selected');
            document.querySelector(`input[value="${mode}"]`).checked = true;
            
            // Mostrar/ocultar pasos de procesamiento
            const stepsDiv = document.querySelector('.processing-steps');
            if (stepsDiv) {
                stepsDiv.style.display = mode === 'complete' ? 'block' : 'none';
            }
        }
        
        // Mostrar nombre del archivo seleccionado
        document.getElementById('sql_file').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) {
                document.getElementById('file-name').textContent = 'Ningún archivo seleccionado';
                return;
            }
            
            const fileName = file.name;
            const fileSize = (file.size / 1024 / 1024).toFixed(2); // MB
            document.getElementById('file-name').innerHTML = 
                `<strong>${fileName}</strong> (${fileSize} MB)`;
        });
        
        // Validar antes de enviar
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            const fileInput = document.getElementById('sql_file');
            const submitBtn = document.getElementById('submitBtn');
            
            if (!fileInput.files[0]) {
                e.preventDefault();
                alert('Por favor, selecciona un archivo SQL');
                return false;
            }
            
            const file = fileInput.files[0];
            const fileName = file.name.toLowerCase();
            const validExtensions = ['.sql', '.sql.gz', '.gz'];
            
            // Verificar extensión
            const hasValidExtension = validExtensions.some(ext => fileName.endsWith(ext));
            if (!hasValidExtension) {
                e.preventDefault();
                alert('Por favor, selecciona un archivo con extensión .sql, .sql.gz o .gz');
                return false;
            }
            
            // Verificar tamaño (50MB máximo recomendado)
            const maxSizeMB = 50;
            const fileSizeMB = file.size / 1024 / 1024;
            if (fileSizeMB > maxSizeMB) {
                if (!confirm(`El archivo es grande (${fileSizeMB.toFixed(2)} MB).\n\nLa importación puede tardar varios minutos y consumir recursos.\n\n¿Continuar de todos modos?`)) {
                    e.preventDefault();
                    return false;
                }
            }
            
            const importMode = document.querySelector('input[name="import_mode"]:checked').value;
            let message = '';
            
            if (importMode === 'complete') {
                message = '⚠️ ¿Estás seguro de que deseas realizar una IMPORTACIÓN COMPLETA?\n\n' +
                         '✅ Se creará/reemplazará la base de datos completa\n' +
                         '❌ Se perderán los datos actuales si existen\n' +
                         '⚙️  Se manejarán automáticamente las FOREIGN KEY\n' +
                         '📋 Se ejecutarán todas las consultas en orden correcto\n\n' +
                         'Recomendado para archivos exportados desde esta aplicación\n\n' +
                         '¿Continuar?';
            } else {
                message = '📥 ¿Estás seguro de que deseas importar SOLO DATOS NUEVOS?\n\n' +
                         '✅ Se mantendrá la estructura actual\n' +
                         '✅ Se agregarán nuevos datos\n' +
                         '📋 Solo se ejecutarán INSERT/UPDATE/REPLACE\n' +
                         '⚠️ Asegúrate de que el archivo SQL contenga principalmente INSERT\n\n' +
                         '¿Continuar?';
            }
            
            if (!confirm(message)) {
                e.preventDefault();
                return false;
            }
            
            // Mostrar indicador de carga
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Importando...';
            submitBtn.disabled = true;
            
            return true;
        });
        
        // Inicializar mostrando pasos si el modo es completo
        document.addEventListener('DOMContentLoaded', function() {
            const importMode = document.querySelector('input[name="import_mode"]:checked');
            if (importMode && importMode.value === 'complete') {
                selectMode('complete');
            }
        });
    </script>
</body>
</html>