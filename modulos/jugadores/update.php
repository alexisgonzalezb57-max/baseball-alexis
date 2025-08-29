<?php  include("../../config/conexion.php");
$con       = conectar();
$id        = $_POST['id'];
$team      = $_POST['team'];
$cedula    = $_POST['cedula'];
$nombre    = $_POST['nombre'];
$apellido  = $_POST['apellido'];
$lanzador  = $_POST['lanz'];
$cat       = $_POST['cat'];
$fecha     = $_POST['fecha'];
$edad      = $_POST['edad'];

    $guardar = "UPDATE jugadores SET cedula   = '$cedula',
                                     nombre   = '$nombre',
                                     apellido = '$apellido',
                                     fecha    = '$fecha',
                                     edad     = '$edad',
                                     lanzador = '$lanzador' 
                                     WHERE id_player = '$id' ";
    $resaves = mysqli_query($con, $guardar);
    Header("Location: list.php?id=$team&cat=$cat");

?>