<?php include("../../config/conexion.php");
$con = conectar();
$id_tab   = $_GET['id_tab'];
$id_temp  = $_GET['id_temp'];
$cat      = $_GET['cat'];

$sql="DELETE FROM tab_clasf WHERE id_tab = $id_tab ";
$query=mysqli_query($con,$sql);

$revisar = "SELECT count(name_team) as teams, id_temp, categoria FROM tab_clasf WHERE id_temp = $id_temp AND categoria LIKE '%$cat%'";
$query   = mysqli_query($con,$revisar);
$data    = mysqli_fetch_array($query);
$total_teams = $data['teams'];

$configurar = "UPDATE temporada SET nequipos = '$total_teams' WHERE id_temp = $id_temp AND categoria LIKE '%$cat%'; ";
$resav = mysqli_query($con, $configurar);

Header("Location: ../juego/formclasf.php?id=$id_temp&cat=$cat");


?>