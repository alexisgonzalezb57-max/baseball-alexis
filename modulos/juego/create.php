<?php  include("../../config/conexion.php");
$con = conectar();
$natemp    = $_POST['n_temp'];
$game      = $_POST['n_games'];
$innings   = $_POST['n_innings'];
$categoria = $_POST['categoria'];
$juego     = $_POST['juego'];
$numteam   = 0;
$activo    = 1;

$guardar = "INSERT INTO temporada SET name_temp = '$natemp',
                                          categoria = '$categoria',
                                          partidas  = '$game',
                                          innings   = '$innings',
                                          valor     = '$juego',
                                          nequipos  = '$numteam',
                                          activo    = '$activo'; ";
$resaves = mysqli_query($con, $guardar);
Header("Location: ../juego/");  

?>