<?php  
include("../../config/conexion.php");
$con       = conectar();

$id_tab    = $_REQUEST['id_tab'];
$id_team   = $_REQUEST['id_team'];
$id_player = $_REQUEST['id_player'];
$cedula    = $_REQUEST['cedula'];
$temp      = $_REQUEST['temp'];
$cat       = $_REQUEST['cat'];
$id_nj     = $_REQUEST['nj'];


$obtener  = "SELECT
COUNT(nj) AS tnj,
SUM(vb)   AS tvb,
SUM(h)    AS th,
SUM(hr)   AS thr,
SUM(2b)   AS t2b,
SUM(3b)   AS t3b,
SUM(ca)   AS tca,
SUM(ci)   AS tci,
SUM(k)    AS tk,
SUM(b)    AS tb,
SUM(a)    AS ta,
SUM(sf)   AS tfl,
SUM(br)   AS tbr,
SUM(gp)   AS tgp,
id_temp 
FROM jugadores_stats WHERE id_tab    = $id_tab 
                     AND   id_team   = $id_team 
                     AND   id_player = $id_player 
                     AND   cedula    LIKE '%$cedula%';";
$suma = mysqli_query($con, $obtener);
$data = mysqli_fetch_array($suma);

$id_temp = $data['id_temp'];

$tnj = $data['tnj'];
$tvb = $data['tvb'] + 0;
$th  = $data['th']  + 0;
$thr = $data['thr'] + 0;
$t2b = $data['t2b'] + 0;
$t3b = $data['t3b'] + 0;
$tca = $data['tca'] + 0;
$tci = $data['tci'] + 0;
$tk  = $data['tk']  + 0;
$tb  = $data['tb']  + 0;
$ta  = $data['ta']  + 0;
$tfl = $data['tfl'] + 0;
$tbr = $data['tbr'] + 0;
$tgp = $data['tgp'] + 0;

$champion = "SELECT * FROM temporada WHERE id_temp = $id_temp";
$bate = mysqli_query($con, $champion);
$puntaje = mysqli_fetch_array($bate);
$valores = $puntaje['valor'];

$cb = $tvb * $valores;
$dtvb      = $tvb;
$dth       = $th + $thr + $t2b + $t3b ;
$avg_multi = ($dth * 1000);

if ($avg_multi === 0) {
     $avg = 0;
     
} elseif ($avg_multi >= 1) {
     $avg = $avg_multi / $dtvb;

}





$constastar = "SELECT * FROM resumen_stats WHERE id_player = $id_player";
$confkj = mysqli_query($con, $constastar);
$vjt = mysqli_num_rows($confkj);
if ($vjt >= 1) {


$configurar = "UPDATE resumen_stats SET tnj = '$tnj',
                                        vb  = '$tvb',
                                        h   = '$th',
                                        hr  = '$thr',
                                        2b  = '$t2b',
                                        3b  = '$t3b',
                                        ca  = '$tca',
                                        ci  = '$tci',
                                        k   = '$tk',
                                        b   = '$tb',
                                        a   = '$ta',
                                        sf  = '$tfl',
                                        br  = '$tbr',
                                        gp  = '$tgp',
                                        tvb = '$dtvb',
                                        th  = '$dth',
                                        avg = '$avg',
                                        cb  = $cb, 
                                        categoria  = '$cat',
                                        id_temp  = '$temp' 
                WHERE id_tab    = $id_tab 
                AND   id_team   = $id_team 
                AND   id_player = $id_player 
                AND   cedula    LIKE '%$cedula%'; ";
$resav = mysqli_query($con, $configurar);

Header("Location: ../entradas/datos.php?id_tab=$id_tab&nj=$id_nj");

} elseif ($vjt < 1) {
    
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


$stats = "SELECT * FROM jugadores WHERE id_player = $id_player;";
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


$configurar = "UPDATE resumen_stats SET tnj = '$tnj',
                                        vb  = '$tvb',
                                        h   = '$th',
                                        hr  = '$thr',
                                        2b  = '$t2b',
                                        3b  = '$t3b',
                                        ca  = '$tca',
                                        ci  = '$tci',
                                        k   = '$tk',
                                        b   = '$tb',
                                        a   = '$ta',
                                        sf  = '$tfl',
                                        br  = '$tbr',
                                        gp  = '$tgp',
                                        tvb = '$dtvb',
                                        th  = '$dth',
                                        avg = '$avg',
                                        cb  = $cb 
                                        WHERE id_tab    = $id_tab 
                                        AND   id_team   = $id_team 
                                        AND   id_player = $id_player 
                                        AND   cedula    LIKE '%$cedula%'; ";
$resav = mysqli_query($con, $configurar);

Header("Location: ../entradas/datos.php?id_tab=$id_tab&nj=$id_nj");

}
















?>