<?php  
include("../../config/conexion.php");
$con      = conectar();
$id_tab   = $_REQUEST['id_tab'];
$id_team  = $_REQUEST['id_team'];
$id_temp  = $_REQUEST['id_temp'];

$cantidad = "SELECT SUM(jj) AS tjj FROM puntaje";
$valor    = mysqli_query($con, $cantidad);
$number   = mysqli_fetch_array($valor);

$obtener  = "SELECT
SUM(jg) AS tjg,
SUM(jp) AS tjp,
SUM(je) AS tje,
SUM(ca) AS tca,
SUM(ce) AS tce
FROM puntaje WHERE val = 1";
$suma = mysqli_query($con, $obtener);
$data = mysqli_fetch_array($suma);



echo $tjj = $number['tjj'];
echo $tjg = $data['tjg'];
echo $tjp = $data['tjp'];
echo $tje = $data['tje'];
echo $tca = $data['tca'];
echo $tce = $data['tce'];



?>