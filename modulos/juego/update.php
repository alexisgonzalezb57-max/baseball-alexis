<?php  include("../../config/conexion.php");
$con       = conectar();
$id        = $_POST['id'];
$natemp    = $_POST['n_temp'];
$game      = $_POST['n_games'];
$innings   = $_POST['n_innings'];
$categoria = $_POST['categoria'];
$juego     = $_POST['juego'];
$activo    = $_POST['activo'];

    $guardar = "UPDATE temporada SET name_temp = '$natemp',
                                     partidas  = '$game',
                                     innings   = '$innings', 
                                     categoria = '$categoria',
                                     valor     = '$juego',
                                     activo    = '$activo' 
                                     WHERE id_temp = '$id';";
    $resaves = mysqli_query($con, $guardar);

    Header("Location: ../juego/"); 

?>