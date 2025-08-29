<?php
include("../../config/conexion.php");
$con=conectar();
$query=$con->query("SELECT * FROM equipos  
	WHERE id_team=$_GET[team]");
$depa = array();
while($r=$query->fetch_object()){ $depa[]=$r; }
if($depa>0){

foreach ($depa as $s) {
	print "<option value='$s->nom_team'>$s->nom_team</option>";
}
}else{
print "<option value=''>-- NO HAY DATOS --</option>";
}
?>