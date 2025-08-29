<?php
include("../../config/conexion.php");
$con=conectar();
$day = $_POST['tempo'];

$verificar = mysqli_query($con, "SELECT * FROM report");
$nums = mysqli_num_rows($verificar);

if ($nums == 1) {
	$ssvv = "UPDATE report SET timeday = '$day'";
	$save = mysqli_query($con,$ssvv);
	$success = true; // o false si falla
} elseif ($nums == 0) {
	$ssvv = "INSERT INTO report SET timeday = '$day'";
	$save = mysqli_query($con,$ssvv);
	$success = true; // o false si falla
}

header('Content-Type: application/json');
echo json_encode(['success' => $success]);

?>