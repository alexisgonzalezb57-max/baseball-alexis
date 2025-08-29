<?php
include("../../config/conexion.php");
$con = conectar();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema de Homenajes</title>
    <link rel="stylesheet" href="../../css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../../css/styles/styleteams.css">
    <style>
        body {
            background-color: #f5f8fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.08);
            border: none;
            margin: 20px 0;
        }
        .card-title {
            color: #2c3e50;
            font-weight: 700;
            padding-bottom: 15px;
            margin-bottom: 25px;
            border-bottom: 2px solid #eaecef;
        }
        .form-section {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
            border-left: 4px solid #3498db;
        }
        .form-section h5 {
            color: #2c3e50;
            margin-bottom: 25px;
            padding-bottom: 12px;
            border-bottom: 1px solid #dee2e6;
            font-weight: 600;
        }
        .premio-row {
            background-color: #fff;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 12px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .premio-label {
            font-weight: 600;
            color: #2c3e50;
            padding-top: 8px;
        }
        .btn-action {
            font-weight: 600;
            padding: 10px 25px;
            border-radius: 6px;
            min-width: 120px;
        }
        .btn-success {
            background-color: #2ecc71;
            border-color: #2ecc71;
        }
        .btn-secondary {
            background-color: #7f8c8d;
            border-color: #7f8c8d;
        }
        label {
            font-weight: 500;
            color: #34495e;
        }
        .nav-divider {
            margin: 0 12px;
            color: #dee2e6;
        }
        .header {
            background: linear-gradient(135deg, #2c3e50 0%, #4a6491 100%);
            padding: 15px 0;
            margin-bottom: 30px;
        }
        .logo {
            color: white;
            font-weight: 700;
            font-size: 28px;
        }
        .navigation a {
            color: rgba(255, 255, 255, 0.85) !important;
            font-weight: 500;
            transition: all 0.3s;
        }
        .navigation a:hover {
            color: white !important;
            transform: translateY(-2px);
        }
        .clock {
            border-radius: 8px;
            padding: 4px 20px;
        }
        .clock h2, .clock span {
            color: white;
            margin: 0;
        }
        input[type="text"], select, textarea {
            border-radius: 6px;
            padding: 10px 15px;
            border: 1px solid #dce4ec;
            transition: all 0.3s;
        }
        input[type="text"]:focus, select:focus, textarea:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        .required-field::after {
            content: "*";
            color: #e74c3c;
            margin-left: 4px;
        }
    </style>
</head>
<body>
    <section>
        <header>
            <a href="../../" class="logo">BASEBALL</a>
            <ul class="navigation">
                <li><a href="../equipos/">Equipos</a></li>
                <li><a href="../juego/">Temporada</a></li>
                <li><a href="../calendario/">Calendario</a></li>
                <li><a href="../homenaje/homenaje.php">Homenaje</a></li>
                <li><a href="../abonos/">Abono</a></li>
                <li><a href="../reporte/reporte.php">Reportes</a></li>
            </ul>
            <div class="second">
                <a class="content" href="#">
                    <div class="clock">
                        <h2 id="hour">00</h2><h2 id="dot">:</h2>
                        <h2 id="minute">00</h2><h2 id="dot">:</h2>
                        <h2 id="seconds">00</h2><span id="ampm">AM</span>
                    </div>
                </a>
            </div>
        </header>
    </section>

    <div class="container" style="margin-top: -750px;">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">🏆 Ingresar la persona a honrar en la temporada</h4>
                <h6 class="card-subtitle mb-4 text-muted">Complete todos los datos requeridos</h6>
                
                <form method="POST" action="create.php">
                    <!-- Sección de Información Básica -->
                    <div class="form-section">
                        <h5>📋 Información Básica</h5>
                        <div class="form-group row mb-4">
                            <label class="col-md-3 col-form-label required-field">Categoría</label>
                            <div class="col-md-4">
                                <select class="form-control" id="categoria" name="categoria" required>
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
                        </div>

                        <div class="form-group row mb-4">
                            <label class="col-md-3 col-form-label required-field">Temporada</label>
                            <div class="col-md-4">
                                <select class="form-control" id="tempo" name="temporada" required>
                                    <option value="">Primero seleccione una categoría</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label required-field">Nombre de la(s) persona(s)</label>
                            <div class="col-md-8">
                                <textarea class="form-control" name="honor" rows="3" required placeholder="Ingrese el nombre completo de la persona a homenajear"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de Premios Principales -->
                    <div class="form-section">
                        <h5>🏅 Premios Principales</h5>
                        
                        <div class="form-group row premio-row align-items-center">
                            <label class="col-md-3 col-form-label premio-label">Primer Premio</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="cant_once" value="0" placeholder="Ej: 1, Sí, No, 500$">
                            </div>
                        </div>

                        <div class="form-group row premio-row align-items-center">
                            <label class="col-md-3 col-form-label premio-label">Segundo Premio</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="cant_second" value="0" placeholder="Ej: 1, Sí, No, 500$">
                            </div>
                        </div>

                        <div class="form-group row premio-row align-items-center">
                            <label class="col-md-3 col-form-label premio-label">Tercer Premio</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="cant_third" value="0" placeholder="Ej: 1, Sí, No, 500$">
                            </div>
                        </div>

                        <div class="form-group row premio-row align-items-center">
                            <label class="col-md-3 col-form-label premio-label">Cuarto Premio</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="cant_four" value="0" placeholder="Ej: 1, Sí, No, 500$">
                            </div>
                        </div>
                    </div>

                    <!-- Sección de Premios Específicos -->
                    <div class="form-section">
                        <h5>⭐ Premios Específicos</h5>
                        
                        <div class="form-group row premio-row align-items-center">
                            <label class="col-md-3 col-form-label premio-label">Champion Picher Ganado</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="cant_pg" value="0" placeholder="Ej: 1, Sí, No, 500$">
                            </div>
                        </div>

                        <div class="form-group row premio-row align-items-center">
                            <label class="col-md-3 col-form-label premio-label">Champion Picher Efectividad</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="cant_pe" value="0" placeholder="Ej: 1, Sí, No, 500$">
                            </div>
                        </div>

                        <div class="form-group row premio-row align-items-center">
                            <label class="col-md-3 col-form-label premio-label">Champion Líder en Bateo</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="cant_lbt" value="0" placeholder="Ej: 1, Sí, No, 500$">
                            </div>
                        </div>

                        <div class="form-group row premio-row align-items-center">
                            <label class="col-md-3 col-form-label premio-label">Champion Líder en Jonrones</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="cant_lj" value="0" placeholder="Ej: 1, Sí, No, 500$">
                            </div>
                        </div>

                        <div class="form-group row premio-row align-items-center">
                            <label class="col-md-3 col-form-label premio-label">Champion Líder en Dobles</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="cant_ld" value="0" placeholder="Ej: 1, Sí, No, 500$">
                            </div>
                        </div>

                        <div class="form-group row premio-row align-items-center">
                            <label class="col-md-3 col-form-label premio-label">Champion Líder en Triples</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="cant_lt" value="0" placeholder="Ej: 1, Sí, No, 500$">
                            </div>
                        </div>

                        <div class="form-group row premio-row align-items-center">
                            <label class="col-md-3 col-form-label premio-label">Champion Líder en Carreras Anotadas</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="cant_lca" value="0" placeholder="Ej: 1, Sí, No, 500$">
                            </div>
                        </div>

                        <div class="form-group row premio-row align-items-center">
                            <label class="col-md-3 col-form-label premio-label">Champion Líder en Carreras Empujadas</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="cant_lce" value="0" placeholder="Ej: 1, Sí, No, 500$">
                            </div>
                        </div>

                        <div class="form-group row premio-row align-items-center">
                            <label class="col-md-3 col-form-label premio-label">Champion Líder en Ponches</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="cant_lp" value="0" placeholder="Ej: 1, Sí, No, 500$">
                            </div>
                        </div>

                        <div class="form-group row premio-row align-items-center">
                            <label class="col-md-3 col-form-label premio-label">Champion Líder en Boletos</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="cant_lb" value="0" placeholder="Ej: 1, Sí, No, 500$">
                            </div>
                        </div>
                    </div>

                    <div class="form-group row mt-4">
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-success btn-action mr-3">💾 Guardar</button>
                            <a href="homenaje.php" class="btn btn-secondary btn-action">↩ Volver</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../../js/jquery/jquery-3.2.1.js"></script>
    <script src="../../css/bootstrap/popper.min.js"></script>
    <script src="../../js/bootstrap/bootstrap.bundle.min.js"></script>
    
    <script>
        // Cargar temporadas según categoría seleccionada
        $(document).ready(function(){
            $("#categoria").change(function(){
                $.get("../calendario/categoria.php", "categoria=" + $("#categoria").val(), function(data){
                    $("#tempo").html(data);
                });
            });
        });

        // Reloj en tiempo real
        function updateClock() {
            const now = new Date();
            let hours = now.getHours();
            let minutes = now.getMinutes();
            let seconds = now.getSeconds();
            const ampm = hours >= 12 ? 'PM' : 'AM';
            
            hours = hours % 12;
            hours = hours ? hours : 12;
            
            document.getElementById('hour').textContent = hours.toString().padStart(2, '0');
            document.getElementById('minute').textContent = minutes.toString().padStart(2, '0');
            document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
            document.getElementById('ampm').textContent = ampm;
        }
        
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>
</html>