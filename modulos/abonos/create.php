<?php  
include("../../config/conexion.php");
$con = conectar();

// Obtener datos del formulario
$categoria  = $_POST['categoria'];
$temporada  = $_POST['temporada'];
$ncantidad  = $_POST['ncantidad'];
$activo     = isset($_POST['activo']) ? $_POST['activo'] : '1'; // Por defecto activo

// Obtener datos de premios y monedas
$prize_once   = isset($_POST['prize_once']) ? $_POST['prize_once'] : '0';
$mond_once    = isset($_POST['mond_once']) ? $_POST['mond_once'] : '$';
$cant_once    = isset($_POST['cant_once']) ? $_POST['cant_once'] : 0;

$prize_second = isset($_POST['prize_second']) ? $_POST['prize_second'] : '0';
$mond_second  = isset($_POST['mond_second']) ? $_POST['mond_second'] : '$';
$cant_second  = isset($_POST['cant_second']) ? $_POST['cant_second'] : 0;

$prize_third  = isset($_POST['prize_third']) ? $_POST['prize_third'] : '0';
$mond_third   = isset($_POST['mond_third']) ? $_POST['mond_third'] : '$';
$cant_third   = isset($_POST['cant_third']) ? $_POST['cant_third'] : 0;

$prize_four   = isset($_POST['prize_four']) ? $_POST['prize_four'] : '0';
$mond_four    = isset($_POST['mond_four']) ? $_POST['mond_four'] : '$';
$cant_four    = isset($_POST['cant_four']) ? $_POST['cant_four'] : 0;

// Validar valores de moneda
$mond_once = ($mond_once == 'Bs') ? 'Bs' : '$';
$mond_second = ($mond_second == 'Bs') ? 'Bs' : '$';
$mond_third = ($mond_third == 'Bs') ? 'Bs' : '$';
$mond_four = ($mond_four == 'Bs') ? 'Bs' : '$';

// Determinar la moneda principal basada en la mayoría
$monedas = array($mond_once, $mond_second, $mond_third, $mond_four);
$count_dolares = array_count_values($monedas)['$'] ?? 0;
$count_bolivares = array_count_values($monedas)['Bs'] ?? 0;

// La moneda principal será la que más se repite (o $ por defecto)
$moneda_principal = ($count_bolivares > $count_dolares) ? 'Bs' : '$';

// Crear consulta de inserción
$guardar = "INSERT INTO abonos SET 
    id_temp       = '$temporada',
    categoria     = '$categoria',
    ncantidad     = '$ncantidad',
    activo        = '$activo',
    prize_four    = '$prize_four',
    cant_four     = '$cant_four',
    mond_four     = '$mond_four',
    prize_once    = '$prize_once',
    cant_once     = '$cant_once',
    mond_once     = '$mond_once',
    prize_second  = '$prize_second',
    cant_second   = '$cant_second',
    mond_second   = '$mond_second',
    prize_third   = '$prize_third',
    cant_third    = '$cant_third',
    mond_third    = '$mond_third',
    tipo_moneda   = '$moneda_principal'";

// Ejecutar consulta
$resaves = mysqli_query($con, $guardar);

if ($resaves) {
    Header("Location: ../abonos/");
} else {
    die("Error al crear el abono: " . mysqli_error($con));
}
?>