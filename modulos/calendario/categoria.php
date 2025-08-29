<?php
include("../../config/conexion.php");
$con=conectar();
$categoria = $_REQUEST['categoria'];
$query=$con->query("SELECT * FROM temporada WHERE activo = 1 AND categoria LIKE '%$categoria%' ");
$depa = array();
while($r=$query->fetch_object()){ $depa[]=$r; }
if($depa>0){

	print "<option class='alte'  value=''> ... </option>";
foreach ($depa as $s) {
	print "<option class='alte' value='$s->id_temp'>$s->name_temp</option>";
}
}else{
print "<option value=''>-- NO HAY DATOS --</option>";
}
?>