<?php include("../../config/conexion.php");
$con = conectar();
$id  = $_GET['id'];

$sql="DELETE FROM temporada WHERE id_temp = $id ";
$query=mysqli_query($con,$sql);
Header("Location: ../juego/");


?>