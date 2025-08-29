<?php  
include("../../config/conexion.php");
$con = conectar();

// Verificar que todos los campos requeridos estén presentes
if (!isset($_POST['id']) || !isset($_POST['categoria']) || !isset($_POST['temporada']) || !isset($_POST['honor'])) {
    die("Error: Faltan campos requeridos");
}

// Recoger y sanitizar los datos del formulario
$id = intval($_POST['id']);
$categoria    = mysqli_real_escape_string($con, $_POST['categoria']);
$temporada    = mysqli_real_escape_string($con, $_POST['temporada']);
$honor        = mysqli_real_escape_string($con, $_POST['honor']);

// Valores por defecto para campos que podrían no existir en el formulario
$cant_once    = isset($_POST['cant_once']) ? mysqli_real_escape_string($con, $_POST['cant_once']) : '0';
$cant_second  = isset($_POST['cant_second']) ? mysqli_real_escape_string($con, $_POST['cant_second']) : '0';
$cant_third   = isset($_POST['cant_third']) ? mysqli_real_escape_string($con, $_POST['cant_third']) : '0';
$cant_four    = isset($_POST['cant_four']) ? mysqli_real_escape_string($con, $_POST['cant_four']) : '0';
$cant_pg      = isset($_POST['cant_pg']) ? mysqli_real_escape_string($con, $_POST['cant_pg']) : '0';
$cant_pe      = isset($_POST['cant_pe']) ? mysqli_real_escape_string($con, $_POST['cant_pe']) : '0';
$cant_lbt     = isset($_POST['cant_lbt']) ? mysqli_real_escape_string($con, $_POST['cant_lbt']) : '0';
$cant_lj      = isset($_POST['cant_lj']) ? mysqli_real_escape_string($con, $_POST['cant_lj']) : '0';
$cant_ld      = isset($_POST['cant_ld']) ? mysqli_real_escape_string($con, $_POST['cant_ld']) : '0';
$cant_lt      = isset($_POST['cant_lt']) ? mysqli_real_escape_string($con, $_POST['cant_lt']) : '0';
$cant_lca     = isset($_POST['cant_lca']) ? mysqli_real_escape_string($con, $_POST['cant_lca']) : '0';
$cant_lce     = isset($_POST['cant_lce']) ? mysqli_real_escape_string($con, $_POST['cant_lce']) : '0';
$cant_lp      = isset($_POST['cant_lp']) ? mysqli_real_escape_string($con, $_POST['cant_lp']) : '0';
$cant_lb      = isset($_POST['cant_lb']) ? mysqli_real_escape_string($con, $_POST['cant_lb']) : '0';

// Preparar y ejecutar la consulta de actualización
$actualizar = "UPDATE homenaje SET 
    id_temp = '$temporada',
    categoria = '$categoria',
    honor = '$honor',
    cant_four = '$cant_four',
    cant_once = '$cant_once',
    cant_second = '$cant_second',
    cant_third = '$cant_third',
    cant_pg = '$cant_pg',
    cant_pe = '$cant_pe',
    cant_lbt = '$cant_lbt',
    cant_lj = '$cant_lj',
    cant_ld = '$cant_ld',
    cant_lt = '$cant_lt',
    cant_lca = '$cant_lca',
    cant_lce = '$cant_lce',
    cant_lp = '$cant_lp',
    cant_lb = '$cant_lb'
    WHERE id = $id";

$resultado = mysqli_query($con, $actualizar);

// Verificar si la consulta se ejecutó correctamente
if ($resultado) {
    session_start();
    $_SESSION['mensaje'] = "Homenaje actualizado correctamente";
    $_SESSION['tipo_mensaje'] = "success";
} else {
    session_start();
    $_SESSION['mensaje'] = "Error al actualizar el homenaje: " . mysqli_error($con);
    $_SESSION['tipo_mensaje'] = "error";
}

// Redireccionar
header("Location: homenaje.php");
exit();
?>