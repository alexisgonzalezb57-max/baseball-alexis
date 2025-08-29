<?php  include("../../config/conexion.php");
$con       = conectar();
$categoria = $_POST['categoria'];
$temporada = $_POST['temporada'];
$honor     = $_POST['honor'];
$id        = $_POST['id'];

$guardar = "UPDATE homenaje SET id_temp   = '$temporada',
                                categoria = '$categoria',
                                honor     = '$honor' 
                                WHERE id_hnr = '$id';";
$resaves = mysqli_query($con, $guardar);


Header("Location: clasificacion.php");


?>