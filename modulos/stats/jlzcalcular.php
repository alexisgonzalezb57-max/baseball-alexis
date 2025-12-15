<?php  
include("../../config/conexion.php");
$con       = conectar();

$id_tab    = $_REQUEST['id_tab'];
$id_team   = $_REQUEST['id_team'];
$id_player = $_REQUEST['id_player'];
$cedula    = $_REQUEST['cedula'];
$temp      = $_REQUEST['temp'];
echo $cat       = $_REQUEST['cat'];
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
SUM(ile)   AS tbr
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


$constastar = "SELECT * FROM resumen_lanz WHERE id_player = $id_player";
$confkj = mysqli_query($con, $constastar);
$vjt = mysqli_num_rows($confkj);
if ($vjt >= 1) {


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
                                        ile   = '$tbr', 
                                        categoria  = '$cat',
                                        id_temp  = '$temp'   
                WHERE id_tab    = $id_tab 
                AND   id_team   = $id_team 
                AND   id_player = $id_player; ";
    
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


    $lanza = "SELECT * FROM jugadores WHERE id_player = $id_player;";
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
                                                      id_temp      = '$temp',
                                                      id_team      = '$id_team',
                                                      id_player    = '$id_player',
                                                      cedula       = '$cedula',
                                                      name_jglz    = '$name_lanz',
                                                      tnj          = '$nj',
                                                      categoria    = '$cat',
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

      
$configurar = "UPDATE resumen_lanz  SET tnj  = '$tnj',
                                        tjl  = '$tjl',
                                        tjg  = '$tjg',
                                        avg  = '$avg',
                                        til  = '$til',
                                        tcpl = '$tcpl',
                                        efec = '$defec',
                                        categoria    = '$cat',
                                        h    = '$th',
                                        2b   = '$t2b',
                                        3b   = '$t3b',
                                        hr   = '$thr',
                                        b    = '$tb',
                                        k    = '$tk',
                                        va   = '$tva',
                                        gp   = '$tgp',
                                        ile   = '$tbr'  
                                        WHERE id_tab    = $id_tab 
                                        AND   id_team   = $id_team 
                                        AND   id_player = $id_player; ";
    
    $resav = mysqli_query($con, $configurar);
    Header("Location: ../entradas/datos.php?id_tab=$id_tab&nj=$id_nj");

}

/**/

?>