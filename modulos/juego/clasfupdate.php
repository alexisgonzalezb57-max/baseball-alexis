<?php  include("../../config/conexion.php");
$con       = conectar();
$id_tab    = $_POST['id_tab'];
$id_temp   = $_POST['id_temp'];
$team      = $_POST['eteam'];
$name      = $_POST['name'];
$cat       = $_POST['cat'];

    $guardar = "UPDATE tab_clasf SET id_team = '$team',
                                     name_team = '$name' 
                                     WHERE id_tab = '$id_tab' AND id_temp = '$id_temp';";
    $resaves = mysqli_query($con, $guardar);

$revisar = "SELECT count(name_team) as teams, id_temp, categoria FROM tab_clasf WHERE id_temp = $id_temp AND categoria LIKE '%$cat%'";
$query   = mysqli_query($con,$revisar);
$data    = mysqli_fetch_array($query);
$total_teams = $data['teams'];

$configurar = "UPDATE temporada SET nequipos = '$total_teams' WHERE id_temp = $id_temp AND categoria LIKE '%$cat%'; ";
$resav = mysqli_query($con, $configurar);

    Header("Location: ../juego/formclasf.php?id=$id_temp&cat=$cat"); 

?>