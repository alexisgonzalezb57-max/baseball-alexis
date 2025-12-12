<?php  
include("../../config/conexion.php");
$con = conectar();
$categoria  = $_POST['categoria'];
$temporada  = $_POST['temporada'];
$ncantidad  = $_POST['ncantidad'];
$activo     = isset($_POST['activo']) ? $_POST['activo'] : 1; // Por defecto activo

$prize_once   = $_POST['prize_once'];
$mond_once    = $_POST['mond_once'];
$cant_once    = $_POST['cant_once'];
$prize_second = $_POST['prize_second'];
$mond_second  = $_POST['mond_second'];
$cant_second  = $_POST['cant_second'];
$prize_third  = $_POST['prize_third'];
$mond_third   = $_POST['mond_third'];
$cant_third   = $_POST['cant_third'];
$prize_four   = $_POST['prize_four'];
$mond_four    = $_POST['mond_four'];
$cant_four    = $_POST['cant_four'];

echo $guardar = "INSERT INTO abonos SET id_temp    = '$temporada',
                                   categoria  = '$categoria',
                                   ncantidad  = '$ncantidad',
                                   activo     = '$activo',
                                   prize_four   = '$prize_four',
                                   cant_four    = '$cant_four',
                                   mond_four    = '$mond_four',
                                   prize_once   = '$prize_once',
                                   cant_once    = '$cant_once',
                                   mond_once    = '$mond_once',
                                   prize_second = '$prize_second',
                                   cant_second  = '$cant_second',
                                   mond_second  = '$mond_second',
                                   prize_third  = '$prize_third',
                                   cant_third   = '$cant_third',
                                   mond_third   = '$mond_third'; ";
$resaves = mysqli_query($con, $guardar);
Header("Location: ../abonos/");  
?>