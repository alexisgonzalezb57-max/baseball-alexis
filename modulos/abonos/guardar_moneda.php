<?php
include("../../config/conexion.php");
$con = conectar();

// Verificar conexión
if (!$con) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión']);
    exit;
}

// Obtener datos
$id_abn = isset($_POST['id_abn']) ? intval($_POST['id_abn']) : 0;
$tipo_moneda = isset($_POST['tipo_moneda']) ? ($_POST['tipo_moneda'] == 'Bs' ? 'Bs' : '$') : '$';

if ($id_abn <= 0) {
    echo json_encode(['success' => false, 'error' => 'ID de abono no válido']);
    exit;
}

// Verificar si el campo tipo_moneda existe en la tabla
$verificar_campo = "SHOW COLUMNS FROM abonos LIKE 'tipo_moneda'";
$result_verificar = mysqli_query($con, $verificar_campo);

if (mysqli_num_rows($result_verificar) > 0) {
    // El campo existe, actualizarlo
    $actualizar = "UPDATE abonos SET tipo_moneda = '$tipo_moneda' WHERE id_abn = $id_abn";
    
    if (mysqli_query($con, $actualizar)) {
        echo json_encode(['success' => true, 'message' => 'Moneda actualizada correctamente']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al actualizar: ' . mysqli_error($con)]);
    }
} else {
    // El campo no existe, intentar agregarlo
    $agregar_campo = "ALTER TABLE abonos ADD tipo_moneda ENUM('$','Bs') DEFAULT '$'";
    
    if (mysqli_query($con, $agregar_campo)) {
        // Ahora actualizar el valor
        $actualizar = "UPDATE abonos SET tipo_moneda = '$tipo_moneda' WHERE id_abn = $id_abn";
        
        if (mysqli_query($con, $actualizar)) {
            echo json_encode(['success' => true, 'message' => 'Campo creado y moneda actualizada']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al actualizar nuevo campo: ' . mysqli_error($con)]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'No se pudo crear el campo tipo_moneda: ' . mysqli_error($con)]);
    }
}

mysqli_close($con);
?>