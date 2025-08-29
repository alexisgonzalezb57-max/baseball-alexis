<?php include("../../config/conexion.php");
$con = conectar();
$id  = $_GET['id'];

$sql="DELETE FROM calendario WHERE id_cal = $id ";
$query=mysqli_query($con,$sql);
Header("Location: ../calendario/");


?>