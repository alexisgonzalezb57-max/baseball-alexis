<?php
include("../../config/conexion.php");
$con=conectar();
session_start();
$cedu=$_SESSION['cedula'];


//Comprobamos si esta definida la sesión 'tiempo'.
if(isset($_SESSION['tiempo']) ) {

    //Tiempo en segundos para dar vida a la sesión.
    $inactivo = 1800;//30min en este caso.

    //Calculamos tiempo de vida inactivo.
    $vida_session = time() - $_SESSION['tiempo'];

        //Compraración para redirigir página, si la vida de sesión sea mayor a el tiempo insertado en inactivo.
        if($vida_session > $inactivo)
        {
            //Removemos sesión.
            session_unset();
            //Destruimos sesión.
            session_destroy();              
            //Redirigimos pagina.
            header("Location: ../start/cerrarsession.php");

            exit();
        } else {  // si no ha caducado la sesion, actualizamos
            $_SESSION['tiempo'] = time();
        }


} else {
    //Activamos sesion tiempo.
    $_SESSION['tiempo'] = time();
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
            <a href="../start/" class="logo">BASEBALL</a>
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
                <h4 class="titulo">Temporada: <?php echo $data['name_temp']; ?></h4><br>
            <div class="row" style="margin: 20px;">
                <div class="card-body col-md-4">
                    <table class="table table-sm table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th class="alte" colspan="2">Información</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th class="alte">Juego</th>
                                <th class="alte" style="text-align: center;">-</th>
                            </tr>
                            <tr>
                                <th class="alte">Estado</th>
                                <th class="alte" style="text-align: center;">-</th>
                            </tr>
                            <tr>
                                <th class="alte">Valido</th>
                                <th class="alte" style="text-align: center;">-</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-body col-md-8">
                    <table class="table table-sm table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th class="alte" colspan="4">R - Partido N° </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th class="alte">Equipo 1</th>
                                <th class="alte" style="text-align: center;">Renegado</th>
                                <th class="alte" style="text-align: center;">Carrera Anotada</th>
                                <th class="alte" style="text-align: center;">-</th>
                            </tr>
                            <tr>
                                <th class="alte">Equipo 2</th>
                                <th class="alte" style="text-align: center;">-</th>
                                <th class="alte" style="text-align: center;">Carrera En Contra</th>
                                <th class="alte" style="text-align: center;">-</th>
                            </tr>
                            <tr>
                                <th class="alte" colspan="4" style="text-align: center;"><a href="part.php" class="btn btn-warning">Modificar</a></th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
            <br>
            <div class="card">
                <div class="card-title">Jugadores</div>
                <div class="card-body">

                    <table class="table table-hover table-sm table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 10px;">#</th>
                                <th style="width: 100px;">Jugador</th>
                                <th style="width: 10px;">VB</th>
                                <th style="width: 10px;">H</th>
                                <th style="width: 10px;">HR</th>
                                <th style="width: 10px;">2B</th>
                                <th style="width: 10px;">3B</th>
                                <th style="width: 10px;">CA</th>
                                <th style="width: 10px;">CI</th>
                                <th style="width: 10px;">K</th>
                                <th style="width: 10px;">B</th>
                                <th style="width: 10px;">A</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $cons = "SELECT * FROM jugadores WHERE id_team = 1;";
                                $dteg = mysqli_query($con, $cons);
                                $nums = mysqli_num_rows($dteg);
                                if ($nums >= 1) {
                                    for ($jg=1; $jg <= $nums ; $jg++) { 
                                        $player = mysqli_fetch_array($dteg);
                            ?>
                            <tr>
                                <td class="alte"><?php echo $jg ?></td>
                                <td class="alte"><?php echo $player['nombre']." ".$player['apellido'] ?></td>
                                <td>X</td>
                                <td>X</td>
                                <td>X</td>
                                <td>X</td>
                                <td>X</td>
                                <td>X</td>
                                <td>X</td>
                                <td>X</td>
                                <td>X</td>
                                <td>X</td>
                            </tr>
                        <?php } } ?>
                        </tbody>
                    </table>

                </div>                
            </div>
            <br>
            <div class="card">
                <div class="card-title">Lanzadores</div>
                <div class="card-body">
                    <table class="table table-hover table-sm table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Jugador</th>
                                <th>JJ</th>
                                <th>JG</th>
                                <th>IL</th>
                                <th>CP</th>
                                <th>CPL</th>
                                <th>H</th>
                                <th>2B</th>
                                <th>3B</th>
                                <th>HR</th>
                                <th>B</th>
                                <th>K</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $cons = "SELECT * FROM jugadores WHERE id_team = 1 AND lanzador = 1;";
                                $dteg = mysqli_query($con, $cons);
                                $nums = mysqli_num_rows($dteg);
                                if ($nums >= 1) {
                                    for ($jg=1; $jg <= $nums ; $jg++) { 
                                        $player = mysqli_fetch_array($dteg);
                            ?>
                            <tr>
                                <td class="alte"><?php echo $jg ?></td>
                                <td class="alte"><?php echo $player['nombre']." ".$player['apellido'] ?></td>
                                <td>X</td>
                                <td>X</td>
                                <td>X</td>
                                <td>X</td>
                                <td>X</td>
                                <td>X</td>
                                <td>X</td>
                                <td>X</td>
                                <td>X</td>
                                <td>X</td>
                                <td>X</td>
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