<?php  include("../../config/conexion.php");
$con       = conectar();
$name      = $_POST['n_team'];
$categoria = $_POST['cat'];
$id        = $_POST['id'];

    $guardar = "UPDATE equipos SET nom_team = '$name' WHERE id_team = '$id' AND categoria LIKE '%$categoria%' ";
    $resaves = mysqli_query($con, $guardar);
    Header("Location: nomina.php?cat=$categoria");

?>