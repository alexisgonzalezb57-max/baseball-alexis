<?php
include("../../config/conexion.php");
$con=conectar();

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
		    	<h4 class="titulo">Listado de Temporadas</h4><br>
                <a href="form.php">
                <button type="button" class="btn btn-primary">Agregar Homenaje</button>
                </a>
                <br><br>
				<div class="cardList">
					<table class="table table-hover table-sm table-bordered">
						<thead class="thead-dark">
                            <tr>
                                <th>Categoria</th>
                                <th>Nombre de la Temporada</th>
                                <th>Homenaje</th>
                                <th>¿Cuarto Premio?</th>
                                <th>Acción</th>
                            </tr>
						</thead>
                        <tbody>
                            <?php
                                $obtener = "SELECT homenaje.*, temporada.* FROM homenaje INNER JOIN temporada 
                                ON homenaje.id_temp = temporada.id_temp ORDER BY homenaje.categoria DESC, homenaje.id_hnr DESC";
                                $query   = mysqli_query($con, $obtener);
                                $num     = mysqli_num_rows($query);
                                if ($num >= 1) {
                                    for ($i = 1; $i <= $num; ++$i) {
                                    $data = mysqli_fetch_array($query);
                            ?>
                            <tr>
                                <td class="alte"><?php echo $data['categoria'] ?></td>
                                <td class="alte"><?php echo $data['name_temp'] ?></td>
                                <td class="alte"><?php echo $data['honor'] ?></td>
                                <td class="alte">
                                <?php if ( empty($data['prize_four']) ){echo 'NO';} else {echo 'SI';} ?>
                                </td>
                                <td>
                                    <a href="formedit.php?id=<?php echo $data['id_hnr'] ?>"><button type="button" class="buto btn btn-warning" data-toggle="tooltip" data-placement="top" title="Editar"><ion-icon name="create-outline"></ion-icon></button></a>
                                    <a href="delet.php?id=<?php echo $data['id_hnr'] ?>"><button type="button" class="buto btn btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar"><ion-icon name="trash-outline"></ion-icon></button></a>
                                </td>
                            </tr>
                            <?php } } ?>
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