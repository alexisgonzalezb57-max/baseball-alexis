<?php include("../../config/conexion.php");
$con = conectar();
$id  = $_GET['id'];
$cat = $_REQUEST['cat'];

$obtener = "SELECT * FROM jugadores WHERE id_player = $id";
$query   = mysqli_query($con, $obtener);
$data    = mysqli_fetch_array($query);
$team    = $data['id_team'];

$sql="DELETE FROM jugadores WHERE id_player = $id ";
$query=mysqli_query($con,$sql);

if ($query) {

$obtener = "SELECT * FROM jugadores WHERE id_team = $team";
$query   = mysqli_query($con, $obtener);
$nums    = mysqli_num_rows($query);

    $update  = "UPDATE equipos SET n_jugadores = '$nums' WHERE id_team = '$team' ";
    $upgrade = mysqli_query($con, $update);
    Header("Location: ../jugadores/list.php?id=$team&cat=$cat");
  
} else {
  echo "ERROR";
}

 
?>