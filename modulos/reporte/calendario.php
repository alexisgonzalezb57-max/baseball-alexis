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
        </header>
    </section>

        <!--  System Card -->
          <div class="list">
            <div class="seudo"></div>
                <h4 class="titulo">Reportes - Calendarios</h4>
                <a href="reporte.php">
                <button type="button" class="btn btn-success">Volver</button>
                </a><br><br>
                <div class="cardList">
                    <table class="table table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th class="alte" colspan="2">Fecha de Todos los Juegos (agrupado)</th>
                            </tr>
                        </thead>
                        <tbody>
            <?php

$val_hor   = "SELECT * FROM calendario GROUP BY fecha ORDER BY fecha DESC, categoria ASC";
$query_hor = mysqli_query($con, $val_hor);
$numbersho = mysqli_num_rows($query_hor);
if ($numbersho >= 1) {
    for ($i = 1; $i <= $numbersho ; ++$i) {
        $obtenerho = mysqli_fetch_array($query_hor);
        $id = $obtenerho['id_cal'];

$trg=$obtenerho['fecha'];
$entero_trg = strtotime($trg);
$ano_trg = date("Y", $entero_trg);
$mes_trg = date("m", $entero_trg);
$dia_trg = date("d", $entero_trg);
$desde_reorder=$mes_trg.'-'.$dia_trg.'-'.$ano_trg;
            ?>
<tr>
    <th style="border-bottom: 4px solid #000;" class="alte">
        <?php echo $desde_reorder ?>
    </th>

        <th style="border-bottom: 4px solid #000;">
        <a href="../PDF/calendario.php?fecha=<?php echo $trg ?>" target="_blank"><button type="button" class="btn btn-info" >Ingresar</button></a>
    </th>
</tr>
<?php } } ?>

                        </tbody>
                    </table>
                </div><br>
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