<?php  
include("../../config/conexion.php");
$con      = conectar();
$id_tab   = $_REQUEST['id_tab'];
$id_tabt  = $_REQUEST['id_tabt'];
$id_team  = $_REQUEST['id_team'];
$id_teamt = $_REQUEST['id_teamt'];
$id_temp  = $_REQUEST['id_temp'];
$cat      = $_REQUEST['cat'];
$nj       = $_REQUEST['nj'];

$obtener  = "SELECT
COUNT(jj) AS tjj,
SUM(jg)   AS tjg,
SUM(jp)   AS tjp,
(SELECT SUM(je) FROM puntaje WHERE id_team = $id_team AND id_tab = $id_tab) AS tje,
SUM(ca)   AS tca,
SUM(ce)   AS tce
FROM puntaje WHERE val = 1 
AND   id_team = $id_team 
AND   id_tab  = $id_tab ";
$suma = mysqli_query($con, $obtener);
$data = mysqli_fetch_array($suma);


if ($id_teamt == 0) { } elseif ($id_teamt > 0) {
$eobtener  = "SELECT
COUNT(jj) AS tjj,
SUM(jg)   AS tjg,
SUM(jp)   AS tjp,
(SELECT SUM(je) FROM puntaje WHERE id_team = $id_teamt AND id_tab = $id_tabt) AS tje,
SUM(ca)   AS tca,
SUM(ce)   AS tce
FROM puntaje WHERE val = 1 
AND   id_team = $id_teamt 
AND   id_tab  = $id_tabt ";
$esuma = mysqli_query($con, $eobtener);
$edata = mysqli_fetch_array($esuma);
}

$tjj = $data['tjj'];
$tjg = $data['tjg'] + 0;
$tjp = $data['tjp'] + 0;
$tje = $data['tje'] + 0;
$tca = $data['tca'] + 0;
$tce = $data['tce'] + 0;

$dif = $tca - $tce;

if ($dif < 0) { $tdif = $dif; } 
else 
if ($dif >= 0) { $tdif = $dif; }

$avg_multi = ($tjg * 1000);

if ($avg_multi === 0) {
     $avg = 0;
     
} elseif ($avg_multi > 0) {
     $avg = $avg_multi / $tjj;
     
     
}

$tavg = round( $avg, 2 );  


if ($id_teamt == 0) { } elseif ($id_teamt > 0) {
$etjj = $edata['tjj'];
$etjg = $edata['tjg'] + 0;
$etjp = $edata['tjp'] + 0;
$etje = $edata['tje'] + 0;
$etca = $edata['tca'] + 0;
$etce = $edata['tce'] + 0;

$edif = $etca - $etce;

if ($edif < 0) { $etdif = $edif; } 
else 
if ($edif >= 0) { $etdif = $edif; }

$eavg_multi = ($etjg * 1000);

if ($eavg_multi === 0) {
     $eavg = 0;
     
} elseif ($eavg_multi > 0) {
     $eavg = $eavg_multi / $etjj;
     
     
}

$etavg = round( $eavg, 2 );  
}

echo $configurar = "UPDATE tab_clasf SET jj  = '$tjj',
                                         jg  = '$tjg',
                                         jp  = '$tjp',
                                         je  = '$tje',
                                         avg = '$tavg',
                                         ca  = '$tca',
                                         ce  = '$tce',
                                         dif = '$tdif' 
                                         WHERE id_temp = $id_temp 
                                         AND   id_team = $id_team 
                                         AND   id_tab  = $id_tab 
                                         AND   categoria LIKE '%$cat%'; ";
$resav = mysqli_query($con, $configurar);


if ($id_teamt == 0) { } elseif ($id_teamt > 0) {
echo $econfigurar = "UPDATE tab_clasf SET jj = '$etjj',
                                         jg  = '$etjg',
                                         jp  = '$etjp',
                                         je  = '$etje',
                                         avg = '$etavg',
                                         ca  = '$etca',
                                         ce  = '$etce',
                                         dif = '$etdif' 
                                         WHERE id_temp = $id_temp 
                                         AND   id_team = $id_teamt 
                                         AND   id_tab  = $id_tabt 
                                         AND   categoria LIKE '%$cat%'; ";
$eresav = mysqli_query($con, $econfigurar);
}

Header("Location: datos.php?id_tab=$id_tab&nj=$nj");
?>