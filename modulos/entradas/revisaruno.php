<?php  include("../../config/conexion.php");
$con       = conectar();


$id_tab    = $_REQUEST['id_tab'];
$nj        = $_REQUEST['nj'];

$revisar   = "SELECT temporada.*, tab_clasf.* 
            FROM temporada INNER JOIN tab_clasf 
            ON temporada.id_temp   = tab_clasf.id_temp 
            WHERE tab_clasf.id_tab = $id_tab";
$query     = mysqli_query($con,$revisar);
$data      = mysqli_fetch_array($query);

$id_temp   = $data['id_temp'];
$id_team   = $data['id_team'];
$categoria = $data['categoria'];

$vb        = 0;
$tvb       = 0;
$h         = 0;
$hr        = 0;
$dosb      = 0;
$tresb     = 0;
$ca        = 0;
$ci        = 0;
$k         = 0;
$b         = 0;
$a         = 0;
$jjg       = 0;
$tvb       = 0;
$cpl       = 0;
$il        = 0;
$th        = 0;
$avg       = 0;
$efec      = 0;
$cb        = 0;



echo $stadis = "SELECT * FROM jugadores WHERE id_team = $id_team AND id_player NOT IN (SELECT id_player FROM jugadores_stats WHERE nj = $nj AND id_temp = $id_temp);";

$stsquy = mysqli_query($con, $stadis);
$nomnsts = mysqli_num_rows($stsquy);
      if ($nomnsts >= 1) {
        for ($sts=1; $sts <= $nomnsts ; $sts++) { 
          $stsplay      = mysqli_fetch_array($stsquy);
          $nombrests    = $stsplay['nombre'];
          $apellidosts  = $stsplay['apellido'];
          $cedula       = $stsplay['cedula'];
          $id_player    = $stsplay['id_player'];

          $name_stats   = $nombrests." ".$apellidosts;

          $guardarstats = "INSERT INTO jugadores_stats  SET id_tab = '$id_tab',
                                                      id_temp      = '$id_temp',
                                                      id_team      = '$id_team',
                                                      id_player    = '$id_player',
                                                      cedula       = '$cedula',
                                                      name_jugador = '$name_stats',
                                                      nj           = '$nj',
                                                      categoria    = '$categoria',
                                                      vb           = '$vb',
                                                      h            = '$h',
                                                      hr           = '$hr',
                                                      2b           = '$dosb',
                                                      3b           = '$tresb',
                                                      ca           = '$ca',
                                                      ci           = '$ci',
                                                      b            = '$b',
                                                      k            = '$k',
                                                      a            = '$a';";
          $stssaves = mysqli_query($con, $guardarstats);

        }
      }

echo $stadis = "SELECT * FROM jugadores WHERE id_team = $id_team AND id_player IN (SELECT id_player FROM jugadores_stats WHERE nj = $nj AND id_temp = $id_temp);";

$stsquy = mysqli_query($con, $stadis);
$nomnsts = mysqli_num_rows($stsquy);
      if ($nomnsts >= 1) {
        for ($sts=1; $sts <= $nomnsts ; $sts++) { 
          $stsplay      = mysqli_fetch_array($stsquy);
          $nombrests    = $stsplay['nombre'];
          $apellidosts  = $stsplay['apellido'];
          $cedula       = $stsplay['cedula'];
          $id_player    = $stsplay['id_player'];

          $name_stats   = $nombrests." ".$apellidosts;

          $guardarstats = "UPDATE jugadores_stats SET cedula       = '$cedula',
                                                      name_jugador = '$name_stats' 
                                                      WHERE id_player = '$id_player' 
                                                      AND id_team = '$id_team'  
                                                      AND nj = '$nj';";
          $stssaves = mysqli_query($con, $guardarstats);
        }
      }


Header("Location: revisardos.php?id_tab=$id_tab&nj=$nj");

?>