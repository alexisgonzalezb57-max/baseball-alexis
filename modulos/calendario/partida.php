<?php
include("../../config/conexion.php");
$con=conectar();

$query  = "SELECT * FROM temporada WHERE id_temp = $_GET[tempo]";
$select = mysqli_query($con, $query);
$nums   = mysqli_num_rows($select);
$blah   = mysqli_fetch_array($select);
$valors = $blah['partidas'];
if($valors > 0){
print "<option value=''>...</option>";
for ($i=1; $i <= $valors ; $i++) { 
	print "<option class='alte' value='$i'>Partida N° $i</option>";
}
}else{
print "<option value=''>-- NO HAY DATOS --</option>";
}
?>