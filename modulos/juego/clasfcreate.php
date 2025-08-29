<?php  include("../../config/conexion.php");
$con  = conectar();
$id   = $_POST['id'];
$team = $_POST['eteam'];
$name = $_POST['name'];
$cat  = $_POST['cat'];
$jj   = 0;
$jg   = 0;
$jp   = 0;
$je   = 0;
$avg  = 0;
$ca   = 0;
$ce   = 0;
$dif  = 0;

    $guardar = "INSERT INTO tab_clasf SET id_temp   = '$id',
                                          id_team   = '$team',
                                          name_team = '$name',
                                          categoria = '$cat',
                                          jj        = '$jj',
                                          jg        = '$jg',
                                          jp        = '$jp',
                                          je        = '$je',
                                          avg       = '$avg',
                                          ca        = '$ca',
                                          ce        = '$ce',
                                          dif       = '$dif'; ";
    $resaves = mysqli_query($con, $guardar);


$revisar = "SELECT count(name_team) as teams, id_temp, categoria FROM tab_clasf WHERE id_temp = $id AND categoria LIKE '%$cat%'";
$query   = mysqli_query($con,$revisar);
$data    = mysqli_fetch_array($query);
$total_teams = $data['teams'];

$configurar = "UPDATE temporada SET nequipos = '$total_teams' WHERE id_temp = $id AND categoria LIKE '%$cat%'; ";
$resav = mysqli_query($con, $configurar);

   
    Header("Location: ../juego/formclasf.php?id=$id&cat=$cat"); 
    

?>