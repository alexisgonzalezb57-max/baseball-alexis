<?php  include("../../config/conexion.php");
$con       = conectar();
$categoria = $_POST['categoria'];
$temporada = $_POST['temporada'];
$honor     = $_POST['honor'];

$guardar = "INSERT INTO homenaje  SET id_temp = '$temporada',
                                  categoria   = '$categoria',
                                  honor       = '$honor';";
$resaves = mysqli_query($con, $guardar);


Header("Location: clasificacion.php");


?>