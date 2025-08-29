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
$va        = 0;
 
$lanza = "SELECT * FROM jugadores WHERE id_team = $id_team AND lanzador = 1 AND id_player NOT IN (SELECT id_player FROM jugadores_lanz WHERE nj = $nj AND id_temp = $id_temp);";
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

          $guardarlanz = "INSERT INTO jugadores_lanz  SET id_tab   = '$id_tab',
                                                      id_temp      = '$id_temp',
                                                      id_team      = '$id_team',
                                                      id_player    = '$id_player',
                                                      cedula       = '$cedula',
                                                      name_lanz    = '$name_lanz',
                                                      nj           = '$nj',
                                                      categoria    = '$categoria',
                                                      jl           = '$jl',
                                                      jg           = '$jjg',
                                                      il           = '$il',
                                                      cp           = '$cp',
                                                      cpl          = '$cpl',
                                                      h            = '$h',
                                                      2b           = '$dosb',
                                                      3b           = '$tresb',
                                                      hr           = '$hr',
                                                      b            = '$b',
                                                      k            = '$k',
                                                      va           = '$va';";
          $lzsaves = mysqli_query($con, $guardarlanz);

        }
      }

$lanzas = "SELECT * FROM jugadores WHERE id_team = $id_team AND lanzador = 1 AND id_player IN (SELECT id_player FROM jugadores_lanz WHERE nj = $nj AND id_temp = $id_temp);";
$lzquys = mysqli_query($con, $lanzas);
$nomnss = mysqli_num_rows($lzquys);
      if ($nomnss >= 1) {
        for ($lzs=1; $lzs <= $nomnss ; $lzs++) { 
          $lzsplay      = mysqli_fetch_array($lzquys);
          $nombrelzs    = $lzsplay['nombre'];
          $apellidolzs  = $lzsplay['apellido'];
          $cedulas      = $lzsplay['cedula'];
          $id_players   = $lzsplay['id_player'];

          $name_lanzs   = $nombrelzs." ".$apellidolzs;

          $guardarlanzs = "UPDATE jugadores_lanz SET cedula    = '$cedulas',
                                                    name_lanz = '$name_lanzs' 
                                                    WHERE id_player = '$id_players' 
                                                    AND id_team = '$id_team'  
                                                    AND nj = '$nj';";
          $lzsavess = mysqli_query($con, $guardarlanzs);
        }
      }



Header("Location: revisaruno.php?id_tab=$id_tab&nj=$nj");

?>