<?php
include("../../config/conexion.php");
$con = conectar();

$categoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';

if (!$categoria) {
    echo '<option value="">Seleccione categoría</option>';
    exit;
}

// Supongamos que quieres todas las temporadas de esa categoría
// que estén activas o disponibles (puedes adaptar según necesites)
$sql = "SELECT id_temp, name_temp FROM temporada WHERE categoria = ? ORDER BY id_temp DESC";

$stmt = $con->prepare($sql);
$stmt->bind_param('s', $categoria);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo '<option value="">-- Seleccionar Temporada --</option>';
    while($fila = $result->fetch_assoc()) {
        echo '<option value="'. $fila['id_temp'] .'">'. htmlspecialchars($fila['name_temp']) .'</option>';
    }
} else {
    echo '<option value="">No hay temporadas disponibles</option>';
}
?>
