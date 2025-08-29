<?php  include("../../config/conexion.php");
$con       = conectar();
$id        = $_POST['id'];
$cedula    = $_POST['cedula'];
$nombre    = $_POST['nombre'];
$apellido  = $_POST['apellido'];
$lanzador  = $_POST['lanz'];
$categoria = $_POST['cat'];
$fecha     = $_POST['fecha'];
$edad      = $_POST['edad'];

    $guardar = "INSERT INTO jugadores SET id_team   = '$id',
                                          cedula    = '$cedula',
                                          nombre    = '$nombre',
                                          apellido  = '$apellido',
                                          fecha     = '$fecha',
                                          edad      = '$edad',
                                          lanzador  = '$lanzador',
                                          categoria = '$categoria' ";
    $resaves = mysqli_query($con, $guardar);

if ($resaves) {

$obtener = "SELECT * FROM jugadores WHERE id_team = $id";
$query   = mysqli_query($con, $obtener);
$nums    = mysqli_num_rows($query);

    $update  = "UPDATE equipos SET n_jugadores = '$nums' WHERE id_team = '$id' ";
    $upgrade = mysqli_query($con, $update);
    Header("Location: form.php?id=$id&cat=$categoria");
  
} else {
  echo "ERROR";
}


?>