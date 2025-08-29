<?php
include("../../config/conexion.php");
$con = conectar(); 

// Verificar conexión
if (!$con) {
    die("Error de conexión: " . mysqli_connect_error());
}

$id = $_REQUEST['id'];
$val_hor = "SELECT * FROM calendario WHERE id_cal = $id";
$query_hor = mysqli_query($con, $val_hor);
$obtenerho = mysqli_fetch_array($query_hor);
$cat = strtoupper($obtenerho['categoria']);
$temp = $obtenerho['id_temporada'];
$np = $obtenerho['partida'];
$team_one = $obtenerho['id_team_one'];
$team_two = $obtenerho['id_team_two'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Calendario - Sistema Baseball</title>
    
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
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
            overflow: hidden;
        }
        
        .card-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
            padding: 1.5rem 1.5rem 0;
        }
        
        .card-subtitle {
            padding: 0 1.5rem;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .card-footer {
            background-color: #f8f9fa;
            border-top: 1px solid #e9ecef;
            padding: 1rem 1.5rem;
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }
        
        .form-control-plaintext {
            background-color: #f8f9fa;
            border: 1px solid #ced4da;
            border-radius: 6px;
            padding: 0.75rem 1rem;
            transition: all 0.3s;
        }
        
        .form-control-plaintext:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
        
        .btn {
            border-radius: 6px;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s;
        }
        
        .btn-success {
            background: linear-gradient(90deg, var(--success-color) 0%, #146c43 100%);
            border: none;
            color: white;
        }
        
        .btn-success:hover {
            background: linear-gradient(90deg, #146c43 0%, #0f5132 100%);
            transform: translateY(-2px);
            color: white;
        }
        
        .btn-info {
            background: linear-gradient(90deg, var(--info-color) 0%, #0aa2c0 100%);
            border: none;
            color: white;
        }
        
        .btn-info:hover {
            background: linear-gradient(90deg, #0aa2c0 0%, #08869e 100%);
            transform: translateY(-2px);
            color: white;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .col-form-label {
            font-weight: 600;
            color: var(--dark-color);
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
            
            .card-footer {
                flex-direction: column;
            }
        }
        
        @media (max-width: 576px) {
            .clock-container {
                display: none;
            }
            
            .form-group.row {
                flex-direction: column;
            }
            
            .col-sm-2, .col-sm-4 {
                width: 100%;
                max-width: 100%;
            }
            
            .col-form-label {
                margin-bottom: 0.5rem;
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
                    <li><a href="../homenaje/homenaje.php"><i class="fas fa-award"></i> Homenaje</a></li>
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
                <h1 class="page-title">Editar Partido del Calendario</h1>
                
                <div class="card">
                    <h4 class="card-title">Editar el Calendario de la Semana</h4>
                    <h6 class="card-subtitle mb-2 text-muted">Cambia los datos</h6>
                    
                    <form method="POST" action="update.php">
                        <div class="card-body">
                            <input type="hidden" name="id" value="<?php echo $id ?>">
                            
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Fecha</label>
                                <div class="col-sm-4">
                                    <input type="date" class="form-control-plaintext alte" id="input" name="fecha" value="<?php echo $obtenerho['fecha'] ?>">
                                </div>
                                <label class="col-sm-2 col-form-label">Categoria</label>
                                <div class="col-sm-4">
                                    <select class="form-control-plaintext alte" required id="categoria" name="categoria">
                                        <?php 
                                        $select = "SELECT * FROM categorias"; 
                                        $querye = mysqli_query($con, $select); 
                                        $num = mysqli_num_rows($querye); 
                                        if ($num >= 1) {
                                            for ($i=1; $i <= $num ; $i++) { 
                                                $det = mysqli_fetch_array($querye);
                                        ?>
                                        <option <?php echo strpos($det['categoria'], $cat) !== false ? "selected='selected'" : ""; ?> class="alte" value="<?php echo $det['categoria'] ?>"><?php echo $det['categoria'] ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Temporada</label>
                                <div class="col-sm-4">
                                    <select class="form-control-plaintext alte" required id="tempo" name="temporada">
                                        <?php 
                                        $selectd = "SELECT * FROM temporada WHERE activo = 1 AND categoria LIKE '%$cat%' "; 
                                        $queryed = mysqli_query($con, $selectd); 
                                        $numd = mysqli_num_rows($queryed); 
                                        if ($numd >= 1) {
                                            for ($r=1; $r <= $numd ; $r++) { 
                                                $detd = mysqli_fetch_array($queryed);
                                        ?>
                                        <option <?php echo strpos($detd['id_temp'], $temp) !== false ? "selected='selected'" : ""; ?> class="alte" value="<?php echo $detd['id_temp'] ?>"><?php echo $detd['name_temp'] ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>

                                <label class="col-sm-2 col-form-label">N° de Partida</label>
                                <div class="col-sm-4">
                                    <select class="form-control-plaintext alte" required id="partida" name="partida">
                                        <?php 
                                        $selectp = "SELECT * FROM temporada WHERE activo = 1 AND categoria LIKE '%$cat%'"; 
                                        $queryep = mysqli_query($con, $selectp); 
                                        $nump = mysqli_num_rows($queryep); 
                                        $detp = mysqli_fetch_array($queryep);
                                        $vart = $detp['partidas'];
                                        if ($vart >= 1) {
                                            for ($p=1; $p <= $vart ; $p++) {                         ?>
                                        <option <?php echo $p == $np ? "selected='selected'" : ""; ?> class="alte" value="<?php echo $p ?>">Partida N° <?php echo $p ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Equipo 1</label>
                                <div class="col-sm-4">
                                    <select class="form-control-plaintext alte" required id="equipone" name="team_one">
                                        <?php 
                                        $selectu = "SELECT * FROM tab_clasf WHERE id_temp = $temp AND categoria LIKE '%$cat%' "; 
                                        $queryu = mysqli_query($con, $selectu); 
                                        $numu = mysqli_num_rows($queryu); 
                                        if ($numu >= 1) {
                                            for ($u=1; $u <= $numu ; $u++) { 
                                                $detu = mysqli_fetch_array($queryu);
                                        ?>
                                        <option <?php echo $detu['id_team'] == $team_one ? "selected='selected'" : ""; ?> class="alte" value="<?php echo $detu['id_team'] ?>"><?php echo $detu['name_team'] ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>

                                <label class="col-sm-2 col-form-label">Equipo 2</label>
                                <div class="col-sm-4">
                                    <select class="form-control-plaintext alte" required id="equipdos" name="team_two">
                                        <?php 
                                        $selecte = "SELECT * FROM tab_clasf WHERE id_temp = $temp AND categoria LIKE '%$cat%' AND id_team != $team_one "; 
                                        $queryek = mysqli_query($con, $selecte); 
                                        $numk = mysqli_num_rows($queryek); 
                                        if ($numk >= 1) {
                                            for ($k=1; $k <= $numk ; $k++) { 
                                                $detk = mysqli_fetch_array($queryek);
                                        ?>
                                        <option <?php echo $detk['id_team'] == $team_two ? "selected='selected'" : ""; ?> class="alte" value="<?php echo $detk['id_team'] ?>"><?php echo $detk['name_team'] ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Día</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control-plaintext alte" id="input" name="dia" value="Sábado" readonly>
                                </div>

                                <label class="col-sm-2 col-form-label">Hora</label>
                                <div class="col-sm-4">
                                    <select class="form-control-plaintext alte" required id="input" name="hora">
                                        <option class="alte" value="">...</option>
                                        <?php
                                        $id_hora = $obtenerho['id_hora'];
                                        $select = "SELECT * FROM tiempos";
                                        $query = mysqli_query($con, $select);
                                        $nums = mysqli_num_rows($query);
                                        if ($nums >= 1) {
                                            for ($i = 1; $i <= $nums ; ++$i) {
                                                $valor = mysqli_fetch_array($query);
                                        ?>
                                        <option class="alte" <?php echo $valor['id_tiempo'] == $id_hora ? "selected='selected'" : ""; ?> value="<?php echo $valor['id_tiempo'] ?>"><?php echo $valor['hora'] ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Campo</label>
                                <div class="col-sm-4">
                                    <select class="form-control-plaintext alte" required id="input" name="campo">
                                        <option class="alte" value="1" <?php echo $obtenerho['campo'] == 1 ? "selected" : ""; ?>>Campo 1</option>
                                        <option class="alte" value="2" <?php echo $obtenerho['campo'] == 2 ? "selected" : ""; ?>>Campo 2</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-footer">
                            <button type="submit" class="btn btn-success" value="search" data-bs-toggle="tooltip" data-bs-placement="top" title="Guardar">
                                <i class="fas fa-save me-2"></i>Guardar
                            </button>
                            <a href="../calendario/" class="btn btn-info" data-bs-toggle="tooltip" data-bs-placement="top" title="Volver">
                                <i class="fas fa-arrow-left me-2"></i>Volver
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Scripts AJAX -->
    <script type="text/javascript">
        $(document).ready(function(){
            $("#categoria").change(function(){
                $.get("categoria.php","categoria="+$("#categoria").val(), function(data){
                    $("#tempo").html(data);
                    console.log(data);
                });
            });
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function(){
            $("#tempo, #partida").change(function(){
                $.get('tempo.php', 
                    { 'partida': $("#partida").val(), 
                    'tempo': $("#tempo").val()}, function(data) {
                    $("#equipone").html(data);
                    console.log(data);
                });
            });
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function(){
            $("#tempo, #equipone, #partida").change(function(){
                $.get('equipone.php', 
                    { 'equipone': $("#equipone").val(),
                    'partida': $("#partida").val(), 
                    'tempo': $("#tempo").val()}, function(data) {
                    $("#equipdos").html(data);
                    console.log(data);
                });
            });
        });
    </script>

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