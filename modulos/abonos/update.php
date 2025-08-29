<?php  include("../../config/conexion.php");
$con       = conectar();
$categoria  = $_POST['categoria'];
$temporada  = $_POST['temporada'];
$ncantidad  = $_POST['ncantidad'];
$prize_once   = $_POST['prize_once'];
$cant_once    = $_POST['cant_once'];
$prize_second = $_POST['prize_second'];
$cant_second  = $_POST['cant_second'];
$prize_third  = $_POST['prize_third'];
$cant_third   = $_POST['cant_third'];
$prize_four   = $_POST['prize_four'];
$cant_four    = $_POST['cant_four'];
$id         = $_POST['id'];

$guardar = "UPDATE abonos   SET id_temp      = '$temporada',
                                categoria    = '$categoria',
                                ncantidad    = '$ncantidad',
                                  prize_four   = '$prize_four',
                                  cant_four    = '$cant_four',
                                  prize_once   = '$prize_once',
                                  cant_once    = '$cant_once',
                                  prize_second = '$prize_second',
                                  cant_second  = '$cant_second',
                                  prize_third  = '$prize_third',
                                  cant_third   = '$cant_third'
                                WHERE id_abn = '$id';";
$resaves = mysqli_query($con, $guardar);


Header("Location: ../abonos/");

?>