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

$sdf = "SELECT id_team, SUM(monto) AS total_abonos FROM monto GROUP BY id_team";
$ryqgue   = mysqli_query($con,$sdf);
$yuyuy   = mysqli_num_rows($ryqgue);
if ($yuyuy >= 1) {
    for ($ppt=1; $ppt <= $yuyuy ; $ppt++) { 
        $dfgf   = mysqli_num_rows($ryqgue);
        $varteam[$ppt] = $dfgf['id_team'];
        $varmont[$ppt] = $dfgf['total_abonos'];
    }
}

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
            <div class="seudo"></div>
            <?php 
    $revisar = "SELECT * FROM temporada WHERE id_temp = $id";
    $ryque = mysqli_query($con, $revisar);
    $datatp = mysqli_fetch_array($ryque);
     ?>
		    	<h4 class="titulo">Temporada: <?php echo $datatp['name_temp']; ?> - Categoría <?php echo $cat ?></h4><br>
                <a href="abonar.php?idn=<?php echo $idn ?>&id=<?php echo $id ?>&cat=<?php echo $cat ?>">
                <button style="color: #000;" type="button" class="btn btn-info">Añadir Abonos</button>
                </a>
                
                <a href="../abonos/">
                <button style="color: #000;" type="button" class="btn btn-success">Volver</button>
                </a>
                <br><br>
				<div class="cardList">
                    <table class="table table-sm table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th class="alte">Equipo</th>
                                <?php for ($i = 1; $i <= $nabono; $i++) { ?>
                                    <th class="alte">AB-<?php echo $i ?></th>
                                <?php } ?>
                                <th class="alte">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $revisar = "SELECT * FROM tab_clasf WHERE id_temp = $id AND categoria LIKE '%$cat%'";
                            $ryque = mysqli_query($con, $revisar);
                            $nunum = mysqli_num_rows($ryque);

                            if ($nunum >= 1) {
                                for ($t = 1; $t <= $nunum; $t++) { 
                                    $bdata = mysqli_fetch_array($ryque);
                                    $nmmm = $bdata['name_team'];
                                    $nttm = $bdata['id_team'];
                            ?>
                                <tr>
                                    <td class="alte"><?php echo htmlspecialchars($nmmm); ?></td>
                                    <?php 
                                    $suma_montos = 0; // Inicializamos acumulador
                                    for ($j = 1; $j <= $nabono; $j++) { 
                                        $njn = "SELECT monto FROM monto WHERE id_abn = $idn AND id_team = $nttm AND numero = $j";
                                        $qtt = mysqli_query($con, $njn);
                                        $dop = mysqli_fetch_array($qtt);
                                        $monto_actual = isset($dop['monto']) ? $dop['monto'] : 0;
                                        $suma_montos += $monto_actual;
                                    ?>
                                        <td class="alte"><?php echo $monto_actual; ?></td>
                                    <?php } ?>
                                    <td class="alte"><?php echo $suma_montos; ?></td>
                                </tr>
                            <?php 
                                } 
                            } 
                            ?>

                            <?php 
                            // Opcional: fila para totales generales (puedes ajustarla o eliminarla)
                            $trrr = $nabono - 1;
                            ?>
                            <tr>
                                <td class="alte"></td>
                                <?php for ($k = 1; $k <= $trrr; $k++) { ?>
                                    <td class="alte"></td>
                                <?php } ?>
                                <td class="alte">Total</td>

                                <?php
                                $totalmente = "SELECT SUM(monto) AS total_final
                                FROM monto
                                WHERE id_temp = $id AND categoria LIKE '%$cat%' 
                                AND numero <= $nabono";
                                $trata = mysqli_query($con, $totalmente);
                                $datatol = mysqli_fetch_array($trata);
                                ?>
                                <?php if (empty($datatol['total_final'])): ?>
                                    <td class="alte">0</td>
                                <?php endif ?>
                                
                                <?php if (!empty($datatol['total_final'])): ?>
                                    <td class="alte"><?php echo $datatol['total_final'] ?></td>
                                <?php endif ?>
                            </tr>
                        </tbody>
                    </table>
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