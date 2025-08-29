<?php include("../../config/conexion.php");
$con = conectar();

$id_tab   = $_REQUEST['id_tab'];
$nj       = $_REQUEST['nj'];
$id_juego = $_REQUEST['id_juego'];




$teamss = "SELECT * FROM juegos WHERE id_juego = $id_juego;";
$obte = mysqli_query($con, $teamss);
$data = mysqli_fetch_array($obte);

$one  = $data['team_one'];   // necesario
$two  = $data['team_two'];   // necesario
$dnj  = $data['nj'];         // necesario
$temp = $data['id_temp'];    // necesario

$revisarth  = "SELECT temporada.*, tab_clasf.* FROM temporada INNER JOIN tab_clasf ON temporada.id_temp = tab_clasf.id_temp WHERE tab_clasf.id_tab = $id_tab";
$queryth    = mysqli_query($con,$revisarth);
$redt     = mysqli_fetch_array($queryth);
$cat      = $redt['categoria'];


// Tabla Juego
$equip_one = "SELECT * FROM juegos WHERE id_temp = $temp AND nj = $dnj AND team_one = $one";
$obten_one = mysqli_query($con, $equip_one);
$datan_one = mysqli_fetch_array($obten_one);

$tab_one  = $datan_one['id_tab'];  // necesario
$idj_one  = $datan_one['id_juego'];  // necesario

// Tabla Puntaje
$punta_one = "SELECT * FROM puntaje WHERE nj = $dnj AND id_tab = $tab_one AND id_team = $one";
$ntaje_one = mysqli_query($con, $punta_one);
$bodri_one = mysqli_fetch_array($ntaje_one);

$idp_one = $bodri_one['id_ttb'];


// Tabla Juego
$equip_two = "SELECT * FROM juegos WHERE id_temp = $temp AND nj = $dnj AND team_one = $two";
$obten_two = mysqli_query($con, $equip_two);
$datan_two = mysqli_fetch_array($obten_two);

$tab_two  = $datan_two['id_tab'];  // necesario
$idj_two  = $datan_two['id_juego'];  // necesario

// Tabla Puntaje
$punta_two = "SELECT * FROM puntaje WHERE nj = $dnj AND id_tab = $tab_two AND id_team = $two";
$ntaje_two = mysqli_query($con, $punta_two);
$bodri_two = mysqli_fetch_array($ntaje_two);

$idp_two = $bodri_two['id_ttb'];



$sql_juego_one="DELETE FROM juegos WHERE id_juego = $idj_one;"; 
$query_juego_one=mysqli_query($con,$sql_juego_one);

$sql_puntaje_one="DELETE FROM puntaje WHERE id_ttb = $idp_one;"; 
$query_puntaje_one=mysqli_query($con,$sql_puntaje_one);



$sql_juego_two="DELETE FROM juegos WHERE id_juego = $idj_two;"; 
$query_juego_two=mysqli_query($con,$sql_juego_two);

$sql_puntaje_two="DELETE FROM puntaje WHERE id_ttb = $idp_two;"; 
$query_puntaje_two=mysqli_query($con,$sql_puntaje_two);


//Calcular Tabla Clasificatoria 
$obtener  = "SELECT
COUNT(jj) AS tjj,
SUM(jg)   AS tjg,
SUM(jp)   AS tjp,
(SELECT SUM(je) FROM puntaje WHERE id_team = $one AND id_tab = $tab_one) AS tje,
SUM(ca)   AS tca,
SUM(ce)   AS tce
FROM puntaje WHERE val = 1 
AND   id_team = $one 
AND   id_tab  = $id_tab ";
$suma = mysqli_query($con, $obtener);
$data = mysqli_fetch_array($suma);

$tjj = $data['tjj'] + 0;
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
$configurar = "UPDATE tab_clasf SET jj  = '$tjj',
                                         jg  = '$tjg',
                                         jp  = '$tjp',
                                         je  = '$tje',
                                         avg = '$tavg',
                                         ca  = '$tca',
                                         ce  = '$tce',
                                         dif = '$tdif' 
                                         WHERE id_temp = $temp  
                                         AND   id_team = $one 
                                         AND   id_tab  = $id_tab 
                                         AND   categoria LIKE '%$cat%'; "; 
$resav = mysqli_query($con, $configurar);

//Calcular Tabla Clasificatoria

$eobtener  = "SELECT
COUNT(jj) AS tjj,
SUM(jg)   AS tjg,
SUM(jp)   AS tjp,
(SELECT SUM(je) FROM puntaje WHERE id_team = $two AND id_tab = $tab_two) AS tje,
SUM(ca)   AS tca,
SUM(ce)   AS tce
FROM puntaje WHERE val = 1 
AND   id_team = $two 
AND   id_tab  = $tab_two ";
$esuma = mysqli_query($con, $eobtener);
$edata = mysqli_fetch_array($esuma);

$etjj = $edata['tjj'] + 0;
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


echo "2".$econfigurar = "UPDATE tab_clasf SET jj = '$etjj',
                                         jg  = '$etjg',
                                         jp  = '$etjp',
                                         je  = '$etje',
                                         avg = '$etavg',
                                         ca  = '$etca',
                                         ce  = '$etce',
                                         dif = '$etdif' 
                                         WHERE id_temp = $temp  
                                         AND   id_team = $two 
                                         AND   id_tab  = $tab_two 
                                         AND   categoria LIKE '%$cat%'; "; ?> <br> <?php
$eresav = mysqli_query($con, $econfigurar);

Header("Location: entradas.php?id=$id_tab");


?>