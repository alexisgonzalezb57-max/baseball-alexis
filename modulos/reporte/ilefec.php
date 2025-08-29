<?php
include("../../config/conexion.php");
$con=conectar();

$idtp = "SELECT temporada.*, tab_clasf.* FROM temporada INNER JOIN tab_clasf 
ON temporada.id_temp = tab_clasf.id_temp 
WHERE temporada.id_temp = $_GET[tempo] AND tab_clasf.jj = (SELECT MAX(tab_clasf.jj) FROM tab_clasf)";
$vatp = mysqli_query($con, $idtp);
$dtpp = mysqli_fetch_array($vatp);

	$partidas = $dtpp['jj'];

	print "<option class='alte' value='$partidas'>$partidas</option>";


?>