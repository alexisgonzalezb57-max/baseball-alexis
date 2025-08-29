<?php  include("../../config/conexion.php");
$con        = conectar();
$categoria  = $_POST['categoria'];
$temporada  = $_POST['temporada'];
$honor      = $_POST['honor'];
$prize_four = $_POST['prize_four'];
$id         = $_POST['id'];

$guardar = "UPDATE homenaje SET id_temp    = '$temporada',
                                categoria  = '$categoria',
                                honor      = '$honor', 
                                prize_four = '$prize_four ' 
                                WHERE id_hnr = '$id';";
$resaves = mysqli_query($con, $guardar);


Header("Location: homenaje.php");


?>