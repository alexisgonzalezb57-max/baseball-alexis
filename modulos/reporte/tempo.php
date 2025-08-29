<?php
include("../../config/conexion.php");
$con=conectar();

$query=$con->query("SELECT * FROM tab_clasf WHERE id_temp = $_GET[tempo]");
$depa = array();
while($r=$query->fetch_object()){ $depa[]=$r; }
if($depa>0){

	print "<option class='alte' value='0'> Todos </option>";
foreach ($depa as $s) {
	print "<option class='alte' value='$s->id_team'>$s->name_team</option>";
}
}else{
print "<option value=''>-- NO HAY DATOS --</option>";
}
?>