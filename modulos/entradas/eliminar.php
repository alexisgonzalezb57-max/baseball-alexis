<?php  include("../../config/conexion.php");
$con       = conectar();
$id_tab    = $_REQUEST['id_tab'];
$nj        = $_REQUEST['nj'];
$id_temp   = $_REQUEST['id_temp'];
$id_team   = $_REQUEST['id_team'];
$cat       = $_REQUEST['cat'];

$configurar = "DELETE FROM jugadores_stats WHERE id_tab    = $id_tab 
                                           AND   id_temp   = $id_temp 
                                           AND   id_team   = $id_team 
                                           AND   nj        = $nj 
                                           AND   categoria LIKE '%$cat%';";
$resav = mysqli_query($con, $configurar);



$econfigurar = "DELETE FROM jugadores_lanz  WHERE id_tab    = $id_tab 
                                           AND   id_temp   = $id_temp 
                                           AND   id_team   = $id_team 
                                           AND   nj        = $nj 
                                           AND   categoria LIKE '%$cat%';";
$eresav = mysqli_query($con, $econfigurar);



$stats = "SELECT * FROM jugadores WHERE id_team = $id_team;";
$stquy = mysqli_query($con, $stats);
$numns = mysqli_num_rows($stquy);
      if ($numns >= 1) {
        for ($jg=1; $jg <= $numns ; $jg++) {
          $player       = mysqli_fetch_array($stquy);
          $cedula       = $player['cedula'];

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
    id_temp 
    FROM jugadores_stats WHERE id_tab    = $id_tab 
                         AND   id_team   = $id_team 
                         AND   cedula    LIKE '%$cedula%';"; ?> <br> <?php
    $suma = mysqli_query($con, $obtener);
    $data = mysqli_fetch_array($suma);


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

    $qconfigurar = "UPDATE resumen_stats SET tnj = '$tnj',
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
                                        tvb = '$dtvb',
                                        th  = '$dth',
                                        avg = '$avg',
                                        cb  = $cb 
                                        WHERE id_tab    = $id_tab 
                                        AND   id_team   = $id_team 
                                        AND   cedula    LIKE '%$cedula%'; ";  ?> <br> <?php
    $qresav = mysqli_query($con, $qconfigurar);

}
}


$lanz = "SELECT * FROM jugadores WHERE id_team = $id_team;";
$lzquy = mysqli_query($con, $lanz);
$numlz = mysqli_num_rows($lzquy);
      if ($numlz >= 1) {
        for ($lz=1; $lz <= $numlz ; $lz++) {
          $playerlz       = mysqli_fetch_array($lzquy);
          $cedulalz       = $playerlz['cedula'];


$obtenerlz  = "SELECT
COUNT(nj) AS tnj,
SUM(jl)   AS tjl,
SUM(jg)   AS tjg,
SUM(il)   AS til,
SUM(cp)   AS tcp,
SUM(cpl)  AS tcpl,
SUM(h)    AS th,
SUM(2b)   AS t2b,
SUM(3b)   AS t3b,
SUM(hr)   AS thr,
SUM(b)    AS tb,
SUM(k)    AS tk
FROM jugadores_lanz WHERE id_tab    = $id_tab 
                    AND   id_team   = $id_team 
                    AND   cedula    LIKE '%$cedulalz%';";
$sumalz = mysqli_query($con, $obtenerlz);
$datalz = mysqli_fetch_array($sumalz);


$tnj  = $datalz['tnj'];
$tjl  = $datalz['tjl']  + 0;
$tjg  = $datalz['tjg']  + 0;
$til  = $datalz['til']  + 0;
$tcp  = $datalz['tcp']  + 0;
$tcpl = $datalz['tcpl'] + 0;
$th   = $datalz['th']   + 0;
$t2b  = $datalz['t2b']  + 0;
$t3b  = $datalz['t3b']  + 0;
$thr  = $datalz['thr']  + 0;
$tb   = $datalz['tb']   + 0;
$tk   = $datalz['tk']   + 0;


$avg_multi = ($tjg * 1000);

if ($avg_multi === 0) {
     $avg = 0;
     
} elseif ($avg_multi >= 1) {
     $avg = $avg_multi / $tjl;

}

/*Efectividad*/
$revisar = "SELECT temporada.*, tab_clasf.* FROM temporada INNER JOIN tab_clasf ON temporada.id_temp = tab_clasf.id_temp WHERE tab_clasf.id_tab = $id_tab";
$query   = mysqli_query($con,$revisar);
$datah    = mysqli_fetch_array($query);

$innings = $datah['innings'];

$efectividad = ($tcpl * $innings);
if ($efectividad === 0) {
    $efec = 0;
     
} elseif ($efectividad >= 1) {
    $efec = $efectividad / $til;

}

$defec = round($efec, 2);


$hconfigurar = "UPDATE resumen_lanz  SET tnj  = '$tnj',
                                        tjl  = '$tjl',
                                        tjg  = '$tjg',
                                        avg  = '$avg',
                                        til  = '$til',
                                        tcpl = '$tcpl',
                                        efec = '$defec',
                                        h    = '$th',
                                        2b   = '$t2b',
                                        3b   = '$t3b',
                                        hr   = '$thr',
                                        b    = '$tb',
                                        k    = '$tk'  
                                        WHERE id_tab    = $id_tab 
                                        AND   id_team   = $id_team 
                                        AND   cedula    LIKE '%$cedulalz%'; ";

$hresav = mysqli_query($con, $hconfigurar);

}
}
Header("Location: entradas.php?id=$id_tab");
?>