<?php
include("../../config/conexion.php");
$con=conectar();
$id_temp = $_REQUEST['temporada'];
$cat     = $_REQUEST['categoria'];
$ttmm    = $_REQUEST['equipone'];
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
                <li><a href="../depurar/depurar.php">depurar</a></li>
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
                <h4 class="titulo">Depurar Resumen Jugadores</h4>
                
                <div class="card">
                <div class="card-body">

                <table class="table table-bordered table-sm">
                <thead class="thead-dark">
                  <tr>
                    <td class="alte">Eliminar</td>
                    <td class="alte">N</td>
                    <td class="alte">cedula</td>
                    <td class="alte">jugador</td>
                    <td class="alte">nj</td>
                    <td class="alte">cat</td>
                    <td class="alte">vb</td>
                    <td class="alte">h</td>
                    <td class="alte">hr</td>
                    <td class="alte">2b</td>
                    <td class="alte">3b</td>
                    <td class="alte">ca</td>
                    <td class="alte">ci</td>
                    <td class="alte">k</td>
                    <td class="alte">b</td>
                    <td class="alte">a</td>
                  </tr>
                </thead>
                <tbody>
                <?php
                $tablast = "SELECT * FROM resumen_stats WHERE id_team = $ttmm AND id_temp = $id_temp AND categoria LIKE '%$cat%' ORDER BY id_temp DESC, id_team DESC, cedula DESC ";
                $quetast = mysqli_query($con, $tablast);
                $numtast = mysqli_num_rows($quetast);
                if ($numtast >= 1) {
                  for ($i=1; $i <= $numtast ; $i++) { 
                  $fettast = mysqli_fetch_array($quetast);
                  $idstats = $fettast['id_rsts'];
                ?>
                <tr>
                  <td class="alte"><a href="eliminar_stats.php?idstats=<?php echo $idstats ?>&temp=<?php echo $id_temp ?>&cat=<?php echo $cat ?>&equip=<?php echo $ttmm ?>">Eliminar</a></td>
                  <td class="alte"><?php echo $i ?></td>
                  <td class="alte"><?php echo $fettast['cedula']; ?></td>
                  <td class="alte"><?php echo $fettast['name_jgstats']; ?></td>
                  <td class="alte"><?php echo $fettast['tnj']; ?></td>
                  <td class="alte"><?php echo $fettast['categoria']; ?></td>
                  <td class="alte"><?php echo $fettast['vb']; ?></td>
                  <td class="alte"><?php echo $fettast['h']; ?></td>
                  <td class="alte"><?php echo $fettast['hr']; ?></td>
                  <td class="alte"><?php echo $fettast['2b']; ?></td>
                  <td class="alte"><?php echo $fettast['3b']; ?></td>
                  <td class="alte"><?php echo $fettast['ca']; ?></td>
                  <td class="alte"><?php echo $fettast['ci']; ?></td>
                  <td class="alte"><?php echo $fettast['k']; ?></td>
                  <td class="alte"><?php echo $fettast['b']; ?></td>
                  <td class="alte"><?php echo $fettast['a']; ?></td>
                </tr>
                <?php } } ?>
                </tbody>
                </table>
                </div>
                </div>



                <h4 class="titulo">Depurar Resumen Pichers</h4>
                
                <div class="card">
                <div class="card-body">

                <table class="table table-bordered table-sm">
                <thead class="thead-dark">
                  <tr>
                    <td class="alte">Eliminar</td>
                    <td class="alte">N</td>
                    <td class="alte">id_team</td>
                    <td class="alte">cedula</td>
                    <td class="alte">jugador</td>
                    <td class="alte">nj</td>
                    <td class="alte">cat</td>
                    <td class="alte">tjl</td>
                    <td class="alte">tjg</td>
                    <td class="alte">avg</td>
                    <td class="alte">til</td>
                    <td class="alte">tcpl</td>
                    <td class="alte">efect</td>
                  </tr>
                </thead>
                <tbody>
                <?php
                $tablast = "SELECT * FROM resumen_lanz WHERE id_team = $ttmm AND id_temp = $id_temp AND categoria LIKE '%$cat%' ORDER BY id_temp DESC, id_team DESC, cedula DESC ";
                $quetast = mysqli_query($con, $tablast);
                $numtast = mysqli_num_rows($quetast);
                if ($numtast >= 1) {
                  for ($i=1; $i <= $numtast ; $i++) { 
                  $fettast = mysqli_fetch_array($quetast);
                  $idstats = $fettast['id_rslz'];
                ?>
                <tr>
                  <td class="alte"><a href="eliminar_lz.php?idrslz=<?php echo $idstats ?>&temp=<?php echo $id_temp ?>&cat=<?php echo $cat ?>&equip=<?php echo $ttmm ?>">Eliminar <?php echo $ttmm ?></a></td>
                  <td class="alte"><?php echo $i ?></td>
                  <td class="alte"><?php echo $fettast['id_team']; ?></td>
                  <td class="alte"><?php echo $fettast['cedula']; ?></td>
                  <td class="alte"><?php echo $fettast['name_jglz']; ?></td>
                  <td class="alte"><?php echo $fettast['tnj']; ?></td>
                  <td class="alte"><?php echo $fettast['categoria']; ?></td>
                  <td class="alte"><?php echo $fettast['tjl']; ?></td>
                  <td class="alte"><?php echo $fettast['tjg']; ?></td>
                  <td class="alte"><?php echo $fettast['avg']; ?></td>
                  <td class="alte"><?php echo $fettast['til']; ?></td>
                  <td class="alte"><?php echo $fettast['tcpl']; ?></td>
                  <td class="alte"><?php echo $fettast['efec']; ?></td>
                </tr>
                <?php } } ?>
                </tbody>
                </table>
                </div>
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