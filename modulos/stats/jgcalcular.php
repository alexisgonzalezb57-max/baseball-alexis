<?php  
include("../../config/conexion.php");
$con       = conectar();

$id_tab    = $_REQUEST['id_tab'];
$id_team   = $_REQUEST['id_team'];
$id_player = $_REQUEST['id_player'];
$cedula    = $_REQUEST['cedula'];
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

?>