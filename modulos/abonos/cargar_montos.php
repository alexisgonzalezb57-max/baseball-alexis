<?php
include("../../config/conexion.php");
$con = conectar();

// Verificar conexión
if (!$con) {
    die(json_encode(['success' => false, 'error' => 'Error de conexión']));
}

// Obtener parámetros
$id_abn = isset($_GET['id_abn']) ? intval($_GET['id_abn']) : 0;
$id_temp = isset($_GET['id_temp']) ? intval($_GET['id_temp']) : 0;
$categoria = isset($_GET['categoria']) ? mysqli_real_escape_string($con, $_GET['categoria']) : '';
$abono = isset($_GET['abono']) ? intval($_GET['abono']) : 0;

if ($id_abn <= 0 || $id_temp <= 0 || empty($categoria) || $abono <= 0) {
    echo json_encode(['success' => false, 'error' => 'Parámetros no válidos']);
    exit;
}

// Primero, verificar si ya existen datos para este abono
$verificar_existencia = "SELECT COUNT(*) as total FROM monto 
                        WHERE id_abn = $id_abn 
                        AND id_temp = $id_temp 
                        AND categoria LIKE '%$categoria%'
                        AND numero = $abono";
$result_existencia = mysqli_query($con, $verificar_existencia);
$row_existencia = mysqli_fetch_assoc($result_existencia);
$existe = $row_existencia['total'] > 0;

// Obtener todos los equipos de esta temporada y categoría
$equipos_query = "SELECT id_team FROM tab_clasf 
                  WHERE id_temp = $id_temp 
                  AND categoria LIKE '%$categoria%'";
$equipos_result = mysqli_query($con, $equipos_query);

$montos = [];
$equipos_con_datos = 0;

while ($equipo = mysqli_fetch_assoc($equipos_result)) {
    $id_team = $equipo['id_team'];
    
    // Buscar el monto para este equipo y abono
    $monto_query = "SELECT monto FROM monto 
                    WHERE id_abn = $id_abn 
                    AND id_temp = $id_temp 
                    AND categoria LIKE '%$categoria%'
                    AND id_team = $id_team
                    AND numero = $abono";
    $monto_result = mysqli_query($con, $monto_query);
    
    $monto_valor = '0.00';
    if (mysqli_num_rows($monto_result) > 0) {
        $monto_row = mysqli_fetch_assoc($monto_result);
        $monto_valor = $monto_row['monto'];
        if (floatval($monto_valor) > 0) {
            $equipos_con_datos++;
        }
    }
    
    $montos[] = [
        'id_team' => $id_team,
        'monto' => $monto_valor
    ];
}

// Determinar el estado del abono
$estado = 'nuevo';
if ($existe) {
    if ($equipos_con_datos == 0) {
        $estado = 'existente_vacio';
    } else if ($equipos_con_datos == count($montos)) {
        $estado = 'existente_completo';
    } else {
        $estado = 'existente_parcial';
    }
}

// Devolver respuesta JSON
echo json_encode([
    'success' => true,
    'existe' => $existe,
    'estado' => $estado,
    'total_equipos' => count($montos),
    'equipos_con_datos' => $equipos_con_datos,
    'montos' => $montos
]);

mysqli_close($con);
?>