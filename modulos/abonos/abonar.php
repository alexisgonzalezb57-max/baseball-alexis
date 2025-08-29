<?php
include("../../config/conexion.php");
$con = conectar();
$idn  = $_REQUEST['idn'];
$id  = $_REQUEST['id'];
$cat = $_REQUEST['cat'];
$revisar = "SELECT * FROM abonos WHERE id_abn = $idn AND categoria LIKE '%$cat%';";
$query   = mysqli_query($con,$revisar);
$data    = mysqli_fetch_array($query);
$nabono  = $data['ncantidad'];
$temp  = $data['id_temp'];
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
                <h4 class="card-title">Definir el Calendario de la Semana</h4>
                <h6 class="card-subtitle mb-2 text-muted">Establece el Horario</h6>
                <form method="POST" action="abnctr.php">
                <div class="card-body">

                    <input type="hidden" name="id" value="<?php echo $idn ?>">
                    <input type="hidden" name="cat" value="<?php echo $cat ?>">
                    <input type="hidden" name="temp" value="<?php echo $temp ?>">

                        <div class="form-group row">

                            <label class="col-sm-2 offset-1 col-form-label">Defina el Abono</label>
                            <div class="col-sm-3">
                              <select class="form-control-plaintext alte" required id="tempo" name="abono" required>
                                <option class="alte" value="">...</option>
                              <?php for ($i=1; $i <= $nabono ; $i++) { ?>
                                <option class="alte" value="<?php echo $i ?>">Abono N° <?php echo $i ?></option>
                              <?php } ?>
                              </select>
                            </div>
                        </div>

<?php 
                                $deftra  = "SELECT * FROM tab_clasf WHERE id_temp = $id AND categoria LIKE '%$cat%' ";
                                $ryque   = mysqli_query($con,$deftra );
                                $nunum   = mysqli_num_rows($ryque);
                                if ($nunum >= 1) {
                                for ($t=1; $t <= $nunum ; $t++) { 
                                $bdata   = mysqli_fetch_array($ryque); ?>
                        <div class="form-group row">
                            <label class="col-sm-1 offset-2 col-form-label">Equipo</label>
                            <div class="col-sm-3 ">
                              <input type="hidden" class="form-control-plaintext alte" id="input" name="idequipo[]" value="<?php echo $bdata['id_team'] ?>">
                              <input type="text" class="form-control-plaintext alte" id="input" name="equipo[]" readonly value="<?php echo $bdata['name_team'] ?>">
                            </div>

                            <label class="col-sm-1 offset-1 col-form-label">Monto</label>
                            <div class="col-sm-2">
                              <input type="number" class="form-control-plaintext alte" id="input" name="monto[]" min="0" step=".01" value="0.00">
                            </div>
                        </div>
<?php } } ?>


                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success" value="search" data-toggle="tooltip" data-placement="top" title="Guardar">Guardar</button>
                    <a href="list.php?id=<?php echo $id ?>&idn=<?php echo $idn ?>&cat=<?php echo $cat ?>" class="btn btn-info" class="card-link" data-toggle="tooltip" data-placement="top" title="Volver">Volver</a>
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