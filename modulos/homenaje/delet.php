<?php include("../../config/conexion.php");
$con = conectar();
$id  = $_GET['id'];

$sql="DELETE FROM homenaje WHERE id_hnr = $id ";
$query=mysqli_query($con,$sql);
Header("Location: homenaje.php");


?>