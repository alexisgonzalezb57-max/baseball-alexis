<?php  
include("../../config/conexion.php");
$con = conectar();

// Obtener todos los datos del formulario
$id = $_POST['id'];
$categoria = $_POST['categoria'];
$temporada = $_POST['temporada'];
$ncantidad = $_POST['ncantidad'];
$activo = $_POST['activo']; // Esto será '0' o '1'

$prize_once = $_POST['prize_once'];
$cant_once = $_POST['cant_once'];
$prize_second = $_POST['prize_second'];
$cant_second = $_POST['cant_second'];
$prize_third = $_POST['prize_third'];
$cant_third = $_POST['cant_third'];
$prize_four = $_POST['prize_four'];
$cant_four = $_POST['cant_four'];
$mond_once    = $_POST['mond_once'];
$mond_second  = $_POST['mond_second'];
$mond_third   = $_POST['mond_third'];
$mond_four    = $_POST['mond_four'];

// Verificar qué valores estamos recibiendo (para debugging)
error_log("Valor de activo recibido: " . $activo);
error_log("Tipo de activo: " . gettype($activo));

// Asegurarnos que activo sea '0' o '1'
$activo = ($activo == '0') ? '0' : '1';

// Actualizar el abono - IMPORTANTE: usar comillas simples para el ENUM
$guardar = "UPDATE abonos SET 
    categoria    = '$categoria',
    id_temp      = '$temporada',
    ncantidad    = '$ncantidad',
    activo       = '$activo',  
    prize_once   = '$prize_once',
    cant_once    = '$cant_once',
    prize_second = '$prize_second',
    cant_second  = '$cant_second',
    prize_third  = '$prize_third',
    cant_third   = '$cant_third',
    prize_four   = '$prize_four',
    cant_four    = '$cant_four',
    mond_four    = '$mond_four',
    mond_once    = '$mond_once',
    mond_second  = '$mond_second',
    mond_third   = '$mond_third'
    WHERE id_abn = '$id'";

// Verificar la consulta SQL (para debugging)
error_log("Consulta SQL: " . $guardar);

$resaves = mysqli_query($con, $guardar);

if ($resaves) {
    // Verificar si realmente se actualizó
    $check_query = "SELECT activo FROM abonos WHERE id_abn = '$id'";
    $check_result = mysqli_query($con, $check_query);
    $row = mysqli_fetch_assoc($check_result);
    error_log("Valor de activo después de actualizar: " . $row['activo']);
    
    Header("Location: ../abonos/");
} else {
    error_log("Error MySQL: " . mysqli_error($con));
    die("Error al actualizar el abono: " . mysqli_error($con));
}
?>