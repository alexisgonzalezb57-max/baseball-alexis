<?php  include("../../config/conexion.php");
$con       = conectar();
$name      = $_POST['n_team'];
$categoria = $_POST['categoria'];
$jugadores = 0;

    $guardar = "INSERT INTO equipos SET nom_team    = '$name', 
                                        n_jugadores = '$jugadores',
                                        categoria   = '$categoria'";
    $resaves = mysqli_query($con, $guardar);
    Header("Location: ../equipos/form.php?cat=$categoria");

?>