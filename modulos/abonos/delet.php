<?php include("../../config/conexion.php");
$con = conectar();
$id  = $_GET['id'];

$sql="DELETE FROM abonos WHERE id_abn = $id ";
$query=mysqli_query($con,$sql);
Header("Location: ../abonos/");


?>