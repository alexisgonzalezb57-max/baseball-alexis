<?php
include("../../config/conexion.php");
$con=conectar();
$id = $_REQUEST['id'];
$val_hor   = "SELECT * FROM temporada WHERE id_temp = $id";
$query_hor = mysqli_query($con, $val_hor);
$obtenerho = mysqli_fetch_array($query_hor);
$id_temp   = $obtenerho['id_temp'];
$nm_temp   = $obtenerho['name_temp'];
$categoria = $obtenerho['categoria'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inicio</title>
    <link rel="stylesheet" href="../../css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../../css/styles/styleteams.css">
    <script src="../../js/jquery/jquery-3.2.1.js"></script>
    <script src="../../css/bootstrap/popper.min.js"></script>
    <script>
        $(function () {
          $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
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
	                    <h2 id="hour">00</h2>    <h2   id="dot">:</h2>
	                    <h2 id="minute">00</h2>  <h2   id="dot">:</h2>
	                    <h2 id="seconds">00</h2> <span id="ampm">AM</span>
	                </div>
	            </a>
			</div>
	</section>
</header>

		<!--  System Card -->
		  <div class="list">
            <div class="card">
                <h4 class="card-title">Ingresar la persona a honrarle en la temporada</h4>
                <h6 class="card-subtitle mb-2 text-muted">Completar los datos</h6>
                <form method="POST" action="create.php">
                <div class="card-body">

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Elije la Categoria</label>
                            <div class="col-sm-2">
                              <select class="form-control alte" id="categoria" name="categoria">
                                <option value="<?php echo $categoria ?>" ><?php echo $categoria ?></option>
                              </select>
                            </div>

                            <label class="col-sm-2 offset-1 col-form-label">Elije la Temporada</label>
                            <div class="col-sm-5">
                              <select class="form-control alte" id="tempo" name="temporada">
                                <option value="<?php echo $id_temp ?>"><?php echo $nm_temp ?></option>
                              </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Nombre de la(s) persona(s)</label>
                            <div class="col-sm-10">
                                <textarea class="form-control-plaintext" id="input" name="honor" rows="3" required></textarea>
                            </div>
                        </div>
                </div>
                <div class="card-footer">
                    <button style="color: #000;" type="submit" class="btn btn-success" value="search" data-toggle="tooltip" data-placement="top" title="Guardar">Guardar</button>
                    <a href="clasificacion.php" style="color: #000;"  class="btn btn-info" class="card-link" data-toggle="tooltip" data-placement="top" title="Volver">Volver</a>
                </div>
                </form>
            </div>
		  </div>

<script>
        function clock(){
            let hour = document.getElementById('hour');
            let minute = document.getElementById('minute');
            let seconds = document.getElementById('seconds');
            let ampm = document.getElementById('ampm');

            let h = new Date().getHours();
            let m = new Date().getMinutes();
            let s = new Date().getSeconds();
            var am = 'AM';

            //Convert 24 hour time to 12 hour format with AM PM Indicator

            if ( h > 12 ) {
                h = h - 12;
                var am = 'PM';
            }

            // Add 0 to the beginning of number if less than 10

            h = ( h < 10 ) ? '0' + h : h;
            m = ( m < 10 ) ? '0' + m : m;
            s = ( s < 10 ) ? '0' + s : s;

            hour.innerHTML = h;
            minute.innerHTML = m;
            seconds.innerHTML = s;
            ampm.innerHTML = am;
        }

        var interval = setInterval(clock, 1000);
</script>
<script src="../../js/bootstrap/bootstrap.bundle.min.js"></script>
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>