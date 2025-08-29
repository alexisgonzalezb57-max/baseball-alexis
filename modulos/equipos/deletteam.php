<?php include("../../config/conexion.php");
$con = conectar();
$id  = $_GET['id'];
$cat = $_REQUEST['cat'];

$sql="DELETE FROM equipos WHERE id_team = $id ";
$query=mysqli_query($con,$sql);
Header("Location: ../equipos/nomina.php?cat=$cat");


?>