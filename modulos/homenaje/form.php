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
    <title>Crear Homenaje - Sistema Baseball</title>
    
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
        
        .form-section {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--primary-color);
            transition: all 0.3s;
        }
        
        .form-section:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .section-title {
            color: var(--dark-color);
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #dee2e6;
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        
        .section-title i {
            margin-right: 10px;
            color: var(--primary-color);
        }
        
        .form-label {
            font-weight: 500;
            color: #34495e;
            margin-bottom: 0.5rem;
        }
        
        .required-field::after {
            content: "*";
            color: var(--danger-color);
            margin-left: 4px;
        }
        
        .form-control, .form-select {
            border-radius: 6px;
            padding: 0.75rem 1rem;
            border: 1px solid #dce4ec;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        
        .premio-row {
            background-color: #fff;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
        }
        
        .premio-row:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
        
        .premio-label {
            font-weight: 600;
            color: var(--dark-color);
            padding-top: 0.5rem;
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
            color: white;
        }
        
        .btn-primary:hover {
            background: linear-gradient(90deg, #0a58ca 0%, #084298 100%);
            transform: translateY(-2px);
            color: white;
        }
        
        .btn-secondary {
            background: linear-gradient(90deg, #6c757d 0%, #5a6268 100%);
            border: none;
            color: white;
        }
        
        .btn-secondary:hover {
            background: linear-gradient(90deg, #5a6268 0%, #495057 100%);
            transform: translateY(-2px);
            color: white;
        }
        
        .btn-container {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
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
            
            .btn-container {
                flex-direction: column;
            }
            
            .premio-label {
                padding-top: 0;
                margin-bottom: 0.5rem;
            }
        }
        
        @media (max-width: 576px) {
            .clock-container {
                display: none;
            }
            
            .form-section {
                padding: 1rem;
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
                <h1 class="page-title">🏆 Crear Nuevo Homenaje</h1>
                <p class="text-center text-muted mb-4">Complete todos los campos requeridos para registrar un nuevo homenaje</p>
                
                <form method="POST" action="create.php">
                    <!-- Sección de Información Básica -->
                    <div class="form-section">
                        <h5 class="section-title"><i class="fas fa-info-circle"></i> Información Básica</h5>
                        
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required-field">Categoría</label>
                                <select class="form-select" id="categoria" name="categoria" required>
                                    <option value="">Seleccione una categoría...</option>
                                    <?php 
                                    $select = "SELECT * FROM categorias"; 
                                    $querye = mysqli_query($con, $select); 
                                    while($det = mysqli_fetch_array($querye)) {
                                    ?>
                                    <option value="<?php echo $det['categoria'] ?>"><?php echo $det['categoria'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label required-field">Temporada</label>
                                <select class="form-select" id="tempo" name="temporada" required>
                                    <option value="">Primero seleccione una categoría</option>
                                </select>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label required-field">Nombre de la(s) persona(s)</label>
                                <textarea class="form-control" name="honor" rows="3" required placeholder="Ingrese el nombre completo de la persona a homenajear"></textarea>
                                <div class="form-text">Puede ingresar más de una persona separándolas con comas</div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de Premios Principales -->
                    <div class="form-section">
                        <h5 class="section-title"><i class="fas fa-medal"></i> Premios Principales</h5>
                        
                        <div class="premio-row">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <label class="form-label premio-label">Primer Premio</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="cant_once" value="0" placeholder="Ej: 1, Sí, No, 500$">
                                </div>
                            </div>
                        </div>

                        <div class="premio-row">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <label class="form-label premio-label">Segundo Premio</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="cant_second" value="0" placeholder="Ej: 1, Sí, No, 500$">
                                </div>
                            </div>
                        </div>

                        <div class="premio-row">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <label class="form-label premio-label">Tercer Premio</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="cant_third" value="0" placeholder="Ej: 1, Sí, No, 500$">
                                </div>
                            </div>
                        </div>

                        <div class="premio-row">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <label class="form-label premio-label">Cuarto Premio</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="cant_four" value="0" placeholder="Ej: 1, Sí, No, 500$">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de Premios Específicos -->
                    <div class="form-section">
                        <h5 class="section-title"><i class="fas fa-star"></i> Premios Específicos</h5>
                        
                        <div class="premio-row">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <label class="form-label premio-label">Champion Picher Ganado</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="cant_pg" value="0" placeholder="Ej: 1, Sí, No, 500$">
                                </div>
                            </div>
                        </div>

                        <div class="premio-row">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <label class="form-label premio-label">Champion Picher Efectividad</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="cant_pe" value="0" placeholder="Ej: 1, Sí, No, 500$">
                                </div>
                            </div>
                        </div>

                        <div class="premio-row">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <label class="form-label premio-label">Champion Líder en Bateo</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="cant_lbt" value="0" placeholder="Ej: 1, Sí, No, 500$">
                                </div>
                            </div>
                        </div>

                        <div class="premio-row">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <label class="form-label premio-label">Champion Líder en Jonrones</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="cant_lj" value="0" placeholder="Ej: 1, Sí, No, 500$">
                                </div>
                            </div>
                        </div>

                        <div class="premio-row">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <label class="form-label premio-label">Champion Líder en Dobles</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="cant_ld" value="0" placeholder="Ej: 1, Sí, No, 500$">
                                </div>
                            </div>
                        </div>

                        <div class="premio-row">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <label class="form-label premio-label">Champion Líder en Triples</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="cant_lt" value="0" placeholder="Ej: 1, Sí, No, 500$">
                                </div>
                            </div>
                        </div>

                        <div class="premio-row">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <label class="form-label premio-label">Champion Líder en Carreras Anotadas</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="cant_lca" value="0" placeholder="Ej: 1, Sí, No, 500$">
                                </div>
                            </div>
                        </div>

                        <div class="premio-row">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <label class="form-label premio-label">Champion Líder en Carreras Empujadas</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="cant_lce" value="0" placeholder="Ej: 1, Sí, No, 500$">
                                </div>
                            </div>
                        </div>

                        <div class="premio-row">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <label class="form-label premio-label">Champion Líder en Ponches</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="cant_lp" value="0" placeholder="Ej: 1, Sí, No, 500$">
                                </div>
                            </div>
                        </div>

                        <div class="premio-row">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <label class="form-label premio-label">Champion Líder en Boletos</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="cant_lb" value="0" placeholder="Ej: 1, Sí, No, 500$">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="btn-container">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Guardar Homenaje
                        </button>
                        <a href="homenaje.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver al Listado
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Cargar temporadas según categoría seleccionada
        $(document).ready(function(){
            $("#categoria").change(function(){
                $.get("../calendario/categoria.php", "categoria=" + $("#categoria").val(), function(data){
                    $("#tempo").html(data);
                });
            });
        });

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