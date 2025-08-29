<?php
include("../../config/conexion.php");
$con       = conectar();
$id        = $_REQUEST['id'];

$revisar   = "SELECT * FROM abonos WHERE id_abn = $id";
$query     = mysqli_query($con,$revisar);
$data      = mysqli_fetch_array($query);
$cat       = $data['categoria'];
$temp      = $data['id_temp'];
$four     = $data['prize_four'];
$cfour    = $data['cant_four'];
$once     = $data['prize_once'];
$conce    = $data['cant_once'];
$second   = $data['prize_second'];
$csecond  = $data['cant_second'];
$third    = $data['prize_third'];
$cthird   = $data['cant_third'];

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
                <h4 class="card-title">Editar los Abonos</h4>
                <h6 class="card-subtitle mb-2 text-muted">Edite la temporada, la cantidad de muestra y el cuarto premio</h6>
                <form method="POST" action="update.php">
                <div class="card-body">

                    <input type="hidden" name="id" value="<?php echo $id ?>">

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Categoria</label>
                            <div class="col-sm-2">
                              <select class="form-control-plaintext alte" required id="categoria" name="categoria">
                                  <?php 
                                    $select = "SELECT * FROM categorias"; 
                                    $querye  = mysqli_query($con, $select); 
                                    $num    = mysqli_num_rows($querye); 
                                    if ($num >= 1) {
                                        for ($i=1; $i <= $num ; $i++) { 
                                            $det = mysqli_fetch_array($querye);
                                    ?>
                                           <option <?php echo strpos($det['categoria'], $cat) !== false ? "selected='selected'" : ""; ?> class="alte" value="<?php echo $det['categoria'] ?>"><?php echo $det['categoria'] ?>
                                    <?php } } ?>
                              </select>
                            </div>

                            <label class="col-sm-2 offset-1 col-form-label">Temporada</label>
                            <div class="col-sm-4">
                              <select class="form-control-plaintext alte" required id="tempo" name="temporada">
                                  <?php 
                                    $selectd = "SELECT * FROM temporada WHERE activo = 1 AND categoria LIKE '%$cat%' "; 
                                    $queryed  = mysqli_query($con, $selectd); 
                                    $numd    = mysqli_num_rows($queryed); 
                                    if ($numd >= 1) {
                                        for ($r=1; $r <= $numd ; $r++) { 
                                            $detd = mysqli_fetch_array($queryed);
                                    ?>
                                        <option <?php echo strpos($detd['id_temp'], $temp) !== false ? "selected='selected'" : ""; ?> class="alte" value="<?php echo $detd['id_temp'] ?>"><?php echo $detd['name_temp'] ?></option>
                                    <?php } } ?>
                              </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Cantidad de Abono</label>
                            <div class="col-sm-2">
                              <input type="number" name="ncantidad" class="form-control-plaintext" id="input" required min="0" value="<?php echo $data['ncantidad'] ?>">
                            </div>
                        </div>


                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">¿Primer Lugar?</label>
                            <div class="col-sm-2">
                              <select class="form-control-plaintext alte" required id="input" name="prize_once">
                                  <option class="alte" <?php echo strpos(1, $once) !== false ? "selected='selected'" : ""; ?> value="1">SI</option>
                                  <option class="alte" <?php echo strpos(0, $once) !== false ? "selected='selected'" : ""; ?> value="0">NO</option>
                              </select>
                            </div>
                            <label class="col-sm-1 col-form-label">Cantidad</label>
                            <div class="col-sm-1">
                              <input type="text" class="form-control-plaintext alte" required id="input" name="cant_once" value="<?php echo $conce ?>" min="0" required>
                            </div>  

                            <label class="col-sm-2 col-form-label">| ¿Segundo Lugar?</label>
                            <div class="col-sm-2">
                              <select class="form-control-plaintext alte" required id="input" name="prize_second">
                                  <option class="alte" <?php echo strpos(1, $second) !== false ? "selected='selected'" : ""; ?> value="1">SI</option>
                                  <option class="alte" <?php echo strpos(0, $second) !== false ? "selected='selected'" : ""; ?> value="0">NO</option>
                              </select>
                            </div>
                            <label class="col-sm-1 col-form-label">Cantidad</label>
                            <div class="col-sm-1">
                              <input type="text" class="form-control-plaintext alte" required id="input" name="cant_second" value="<?php echo $csecond ?>" min="0" required>
                            </div>
                        </div> 

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">¿Tercer Lugar?</label>
                            <div class="col-sm-2">
                              <select class="form-control-plaintext alte" required id="input" name="prize_third">
                                  <option class="alte" <?php echo strpos(1, $third) !== false ? "selected='selected'" : ""; ?> value="1">SI</option>
                                  <option class="alte" <?php echo strpos(0, $third) !== false ? "selected='selected'" : ""; ?> value="0">NO</option>
                              </select>
                            </div>
                            <label class="col-sm-1 col-form-label">Cantidad</label>
                            <div class="col-sm-1">
                              <input type="text" class="form-control-plaintext alte" required id="input" name="cant_third" value="<?php echo $cthird ?>" min="0" required>
                            </div>

                            <label class="col-sm-2 col-form-label">| ¿Cuarto Lugar?</label>
                            <div class="col-sm-2">
                              <select class="form-control-plaintext alte" required id="input" name="prize_four">
                                  <option class="alte" <?php echo strpos(1, $four) !== false ? "selected='selected'" : ""; ?> value="1">SI</option>
                                  <option class="alte" <?php echo strpos(0, $four) !== false ? "selected='selected'" : ""; ?> value="0">NO</option>
                              </select>
                            </div>
                            <label class="col-sm-1 col-form-label">Cantidad</label>
                            <div class="col-sm-1">
                              <input type="text" class="form-control-plaintext alte" required id="input" name="cant_four" value="<?php echo $cfour ?>" min="0" required>
                            </div>
                        </div> 
                </div>
                <div class="card-footer">
                    <button style="color: #000;" type="submit" class="btn btn-success" value="search" data-toggle="tooltip" data-placement="top" title="Guardar">Guardar</button>
                    <a style="color: #000;" href="../abonos/" class="btn btn-info" class="card-link" data-toggle="tooltip" data-placement="top" title="Volver">Volver</a>
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