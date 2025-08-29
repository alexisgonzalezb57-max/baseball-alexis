<?php
include("../../config/conexion.php");
$con=conectar();

$idtp = "SELECT * FROM temporada WHERE id_temp = $_GET[tempo]";
$vatp = mysqli_query($con, $idtp);
$dtpp = mysqli_fetch_array($vatp);

	$partidas = $dtpp['partidas'];
	$valor    = $dtpp['valor'];
	$champion  = $partidas * $valor;

	print "<option class='alte' value='$champion'>Partidas: $partidas x Valor: $valor = $champion </option>";


?>