<?php
include("../../config/conexion.php");
$con = conectar();

// Verificar conexión
if (!$con) {
    echo json_encode(['campo_existe' => false]);
    exit;
}

// Verificar si el campo tipo_moneda existe en la tabla abonos
$verificar_campo = "SHOW COLUMNS FROM abonos LIKE 'tipo_moneda'";
$result_verificar = mysqli_query($con, $verificar_campo);

$campo_existe = mysqli_num_rows($result_verificar) > 0;

echo json_encode(['campo_existe' => $campo_existe]);

mysqli_close($con);
?>