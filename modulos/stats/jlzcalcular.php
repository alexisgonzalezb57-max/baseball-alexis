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
SUM(k)    AS tk,
SUM(va)   AS tva,
SUM(gp)   AS tgp,
SUM(br)   AS tbr
FROM jugadores_lanz WHERE id_tab    = $id_tab 
                    AND   id_team   = $id_team 
                    AND   id_player = $id_player;";
$suma = mysqli_query($con, $obtener);
$data = mysqli_fetch_array($suma);


$tnj  = $data['tnj'];
$tjl  = $data['tjl']  + 0;
$tjg  = $data['tjg']  + 0;
$til  = $data['til']  + 0;
$tcp  = $data['tcp']  + 0;
$tcpl = $data['tcpl'] + 0;
$th   = $data['th']   + 0;
$t2b  = $data['t2b']  + 0;
$t3b  = $data['t3b']  + 0;
$thr  = $data['thr']  + 0;
$tb   = $data['tb']   + 0;
$tk   = $data['tk']   + 0;
$tva  = $data['tva']  + 0;
$tgp  = $data['tgp']  + 0;
$tbr  = $data['tbr']  + 0;


$avg_multi = ($tjg * 1000);

if ($avg_multi === 0) {
     $avg = 0;
     
} elseif ($avg_multi >= 1) {
     $avg = $avg_multi / $tjl;

}

/*Efectividad*/
$revisar = "SELECT temporada.*, tab_clasf.* FROM temporada INNER JOIN tab_clasf ON temporada.id_temp = tab_clasf.id_temp WHERE tab_clasf.id_tab = $id_tab";
$query   = mysqli_query($con,$revisar);
$data    = mysqli_fetch_array($query);

$innings = $data['innings'];

$efectividad = ($tcpl * $innings);
if ($efectividad === 0) {
    $efec = 0;
     
} elseif ($efectividad >= 1) {
    $efec = $efectividad / $til;

}

$defec = round($efec, 2);


$configurar = "UPDATE resumen_lanz  SET tnj  = '$tnj',
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
                                        k    = '$tk',
                                        va   = '$tva',
                                        gp   = '$tgp',
                                        br   = '$tbr'  
                                        WHERE id_tab    = $id_tab 
                                        AND   id_team   = $id_team 
                                        AND   id_player = $id_player; ";

$resav = mysqli_query($con, $configurar);

Header("Location: ../entradas/datos.php?id_tab=$id_tab&nj=$id_nj");

?>