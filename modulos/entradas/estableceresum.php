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
$cp        = 0;
$cpl       = 0;
$il        = 0;
$th        = 0;
$avg       = 0;
$efec      = 0;
$cb        = 0;
$va        = 0;
$sf        = 0;
$br        = 0;
$gp        = 0;
$ile       = 0;

$stats = "SELECT * FROM jugadores WHERE id_team = $id_team;";
$stquy = mysqli_query($con, $stats);
$numns = mysqli_num_rows($stquy);
      if ($numns >= 1) {
        for ($jg=1; $jg <= $numns ; $jg++) { 
          $player       = mysqli_fetch_array($stquy);
          $nombre       = $player['nombre'];
          $apellido     = $player['apellido'];
          $cedula       = $player['cedula'];
          $id_player    = $player['id_player'];

          $jugador      = $nombre." ".$apellido;
  
          $guardarstats = "INSERT INTO resumen_stats  SET id_tab   = '$id_tab',
                                                      id_temp      = '$id_temp',
                                                      id_team      = '$id_team',
                                                      id_player    = '$id_player',
                                                      cedula       = '$cedula',
                                                      name_jgstats = '$jugador',
                                                      tnj          = '$nj',
                                                      categoria    = '$categoria',
                                                      vb           = '$vb',
                                                      h            = '$h',
                                                      hr           = '$hr',
                                                      2b           = '$dosb',
                                                      3b           = '$tresb',
                                                      ca           = '$ca',
                                                      ci           = '$ci',
                                                      k            = '$k',
                                                      b            = '$b',
                                                      a            = '$a',
                                                      sf           = '$sf',
                                                      br           = '$br',
                                                      gp           = '$gp',
                                                      tvb          = '$tvb',
                                                      th           = '$th',
                                                      avg          = '$avg',
                                                      cb           = '$cb';";
          $resaves = mysqli_query($con, $guardarstats);
        }
      }


$lanza = "SELECT * FROM jugadores WHERE id_team = $id_team AND lanzador = 1;";
$lzquy = mysqli_query($con, $lanza);
$nomns = mysqli_num_rows($lzquy);
      if ($nomns >= 1) {
        for ($lz=1; $lz <= $nomns ; $lz++) { 
          $lzplay      = mysqli_fetch_array($lzquy);
          $nombrelz    = $lzplay['nombre'];
          $apellidolz  = $lzplay['apellido'];
          $cedula      = $lzplay['cedula'];
          $id_player   = $lzplay['id_player'];

          $name_lanz   = $nombrelz." ".$apellidolz;

          $guardarlanz = "INSERT INTO resumen_lanz    SET id_tab   = '$id_tab',
                                                      id_temp      = '$id_temp',
                                                      id_team      = '$id_team',
                                                      id_player    = '$id_player',
                                                      cedula       = '$cedula',
                                                      name_jglz    = '$name_lanz',
                                                      tnj          = '$nj',
                                                      categoria    = '$categoria',
                                                      tjl          = '$jl',
                                                      tjg          = '$jjg',
                                                      avg          = '$avg',
                                                      til          = '$il',
                                                      tcp          = '$cp',
                                                      tcpl         = '$cpl',
                                                      efec         = '$efec',
                                                      h            = '$h',
                                                      2b           = '$dosb',
                                                      3b           = '$tresb',
                                                      hr           = '$hr',
                                                      b            = '$b',
                                                      k            = '$k',
                                                      va           = '$va',
                                                      gp           = '$gp',
                                                      ile          = '$ile';";
          $lzsaves = mysqli_query($con, $guardarlanz);
        }
      }




Header("Location: datos.php?id_tab=$id_tab&nj=$nj");

?>