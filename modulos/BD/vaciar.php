<?php
// Configuración
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'baseball';

$mensaje = '';
$tipo_mensaje = '';
$tablas_vaciadas = [];

// Procesar si se confirma la acción
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar'])) {
    try {
        // Conectar a la base de datos
        $conn = new mysqli($host, $user, $password, $database);
        
        // Verificar conexión
        if ($conn->connect_error) {
            throw new Exception("Conexión fallida: " . $conn->connect_error);
        }
        
        // Obtener todas las tablas
        $result = $conn->query("SHOW TABLES");
        
        if ($result->num_rows > 0) {
            // Desactivar restricciones de clave foránea temporalmente
            $conn->query("SET FOREIGN_KEY_CHECKS = 0");
            
            while ($row = $result->fetch_array()) {
                $table = $row[0];
                
                // Vaciar la tabla (TRUNCATE es más rápido que DELETE)
                if ($conn->query("TRUNCATE TABLE `$table`")) {
                    $tablas_vaciadas[] = $table;
                } else {
                    // Si TRUNCATE falla, intentar con DELETE
                    if ($conn->query("DELETE FROM `$table`")) {
                        $tablas_vaciadas[] = $table . " (con DELETE)";
                    } else {
                        throw new Exception("Error vaciando tabla $table: " . $conn->error);
                    }
                }
            }
            
            // Reactivar restricciones de clave foránea
            $conn->query("SET FOREIGN_KEY_CHECKS = 1");
            
            $mensaje = "Base de datos vaciada exitosamente. Se vaciaron " . count($tablas_vaciadas) . " tablas.";
            $tipo_mensaje = 'success';
            
        } else {
            $mensaje = "La base de datos no contiene tablas.";
            $tipo_mensaje = 'info';
        }
        
        $conn->close();
        
    } catch (Exception $e) {
        $mensaje = "Error: " . $e->getMessage();
        $tipo_mensaje = 'error';
        
        // Asegurarse de reactivar las restricciones si hubo error
        if (isset($conn)) {
            $conn->query("SET FOREIGN_KEY_CHECKS = 1");
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
    <title>Vaciar Base de Datos - Baseball</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #fd7e14;
            --danger-color: #dc3545;
            --dark-color: #212529;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
            color: #333;
        }
        
        .vaciar-container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            padding: 3rem;
            position: relative;
            overflow: hidden;
        }
        
        .vaciar-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--danger-color) 0%, #c82333 100%);
        }
        
        .vaciar-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 1rem;
            text-align: center;
            position: relative;
            padding-bottom: 15px;
        }
        
        .vaciar-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--danger-color);
            border-radius: 2px;
        }
        
        .warning-box {
            background: #fff3cd;
            border-radius: 10px;
            padding: 2rem;
            margin: 2rem 0;
            border-left: 5px solid #ffc107;
            color: #856404;
        }
        
        .info-box {
            background: #d1ecf1;
            border-radius: 10px;
            padding: 2rem;
            margin: 2rem 0;
            border-left: 5px solid #17a2b8;
            color: #0c5460;
        }
        
        .success-box {
            background: #d4edda;
            border-radius: 10px;
            padding: 2rem;
            margin: 2rem 0;
            border-left: 5px solid #28a745;
            color: #155724;
        }
        
        .error-box {
            background: #f8d7da;
            border-radius: 10px;
            padding: 2rem;
            margin: 2rem 0;
            border-left: 5px solid #dc3545;
            color: #721c24;
        }
        
        .tables-list {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            max-height: 300px;
            overflow-y: auto;
        }
        
        .table-item {
            padding: 10px 15px;
            margin: 5px 0;
            background: white;
            border-radius: 6px;
            border: 1px solid #dee2e6;
            display: flex;
            align-items: center;
        }
        
        .table-item i {
            margin-right: 10px;
            color: var(--danger-color);
        }
        
        .btn-vaciar {
            background: var(--danger-color);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 6px;
            font-size: 1.1rem;
            font-weight: 600;
            transition: all 0.3s;
            margin: 10px;
        }
        
        .btn-vaciar:hover {
            background: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
        }
        
        .btn-cancelar {
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
        
        .btn-cancelar:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        .text-center {
            text-align: center;
        }
        
        .mt-4 { margin-top: 1.5rem; }
        .mb-4 { margin-bottom: 1.5rem; }
    </style>
</head>
<body>
    <div class="vaciar-container">
        <h1 class="vaciar-title">VACIAR BASE DE DATOS</h1>
        
        <?php if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['confirmar'])): ?>
        
        <div class="warning-box">
            <div class="text-center mb-3">
                <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                <h3 class="text-warning">¡ADVERTENCIA CRÍTICA!</h3>
            </div>
            
            <p><strong>Estás a punto de realizar una acción IRREVERSIBLE:</strong></p>
            
            <ul>
                <li>Se eliminarán <strong>TODOS LOS DATOS</strong> de la base de datos</li>
                <li>Se mantendrán las tablas (estructura)</li>
                <li>No se podrán recuperar los datos eliminados</li>
                <li>Esta acción afectará a todo el sistema</li>
            </ul>
            
            <div class="mt-3">
                <p><strong>Base de datos:</strong> <?php echo htmlspecialchars($database); ?></p>
                <p><strong>Servidor:</strong> <?php echo htmlspecialchars($host); ?></p>
            </div>
            
            <div class="mt-4 text-center">
                <p class="mb-3"><strong>¿Estás completamente seguro de continuar?</strong></p>
                
                <form method="POST">
                    <button type="submit" name="confirmar" class="btn-vaciar">
                        <i class="fas fa-trash-alt me-2"></i>SÍ, VACIAR BASE DE DATOS
                    </button>
                    <a href="index.php" class="btn-cancelar">
                        <i class="fas fa-times me-2"></i>NO, CANCELAR
                    </a>
                </form>
            </div>
        </div>
        
        <div class="info-box">
            <h5><i class="fas fa-lightbulb me-2"></i>Recomendación de seguridad</h5>
            <p class="mb-0">Antes de vaciar la base de datos, se recomienda:</p>
            <ol class="mb-0 mt-2">
                <li>Exportar un respaldo completo (usa la opción "Exportar")</li>
                <li>Verificar que el respaldo se haya creado correctamente</li>
                <li>Notificar a otros usuarios del sistema</li>
                <li>Realizar esta acción en horas de menor actividad</li>
            </ol>
        </div>
        
        <?php else: ?>
        
        <?php if ($tipo_mensaje === 'success'): ?>
        <div class="success-box">
            <div class="text-center mb-3">
                <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                <h3 class="text-success">¡Operación completada!</h3>
            </div>
            
            <p><?php echo $mensaje; ?></p>
            
            <?php if (!empty($tablas_vaciadas)): ?>
            <div class="tables-list">
                <h5>Tablas vaciadas:</h5>
                <?php foreach ($tablas_vaciadas as $tabla): ?>
                <div class="table-item">
                    <i class="fas fa-table"></i>
                    <?php echo htmlspecialchars($tabla); ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <div class="text-center mt-4">
                <a href="index.php" class="btn-cancelar">
                    <i class="fas fa-home me-2"></i>Volver al Inicio
                </a>
                <a href="exportar.php" class="btn-vaciar" style="background: #28a745;">
                    <i class="fas fa-download me-2"></i>Crear Respaldo Ahora
                </a>
            </div>
        </div>
        <?php else: ?>
        <div class="error-box">
            <div class="text-center mb-3">
                <i class="fas fa-times-circle fa-3x mb-3 text-danger"></i>
                <h3 class="text-danger">¡Error en la operación!</h3>
            </div>
            
            <p><?php echo $mensaje; ?></p>
            
            <div class="text-center mt-4">
                <a href="index.php" class="btn-cancelar">
                    <i class="fas fa-arrow-left me-2"></i>Volver al Inicio
                </a>
                <a href="vaciar.php" class="btn-vaciar">
                    <i class="fas fa-redo me-2"></i>Reintentar
                </a>
            </div>
        </div>
        <?php endif; ?>
        
        <?php endif; ?>
    </div>
</body>
</html>