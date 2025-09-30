<?php
include("../../config/conexion.php");
$con = conectar();

// Verificar conexión
if (!$con) {
    die(json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']));
}

// Obtener parámetros
$equipo = $_POST['equipo'] ?? '';
$temporada = $_POST['temporada'] ?? '';
$categoria = $_POST['categoria'] ?? '';

if (!$equipo || !$temporada || !$categoria) {
    echo json_encode(['success' => false, 'message' => 'Parámetros incompletos']);
    exit;
}

// Consulta para obtener pitchers con sus estadísticas
$query = "SELECT 
            rl.id_player,
            rl.name_jglz,
            rl.tjl,
            rl.tjg,
            rl.avg,
            tc.name_team as equipo
          FROM resumen_lanz rl
          LEFT JOIN tab_clasf tc ON rl.id_team = tc.id_team
          WHERE rl.id_temp = ? 
          AND rl.id_team = ? 
          AND rl.categoria = ?
          ORDER BY rl.tjg DESC";

$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "iis", $temporada, $equipo, $categoria);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$pitchers = [];
while ($row = mysqli_fetch_assoc($result)) {
    $pitchers[] = [
        'id' => $row['id_player'],
        'name' => $row['name_jglz'],
        'tjl' => $row['tjl'] ?? 0,
        'tjg' => $row['tjg'] ?? 0,
        'avg' => $row['avg'] ?? 0,
        'equipo' => $row['equipo']
    ];
}

mysqli_stmt_close($stmt);

if (empty($pitchers)) {
    echo json_encode(['success' => false, 'message' => 'No se encontraron pitchers para los criterios seleccionados']);
} else {
    echo json_encode(['success' => true, 'pitchers' => $pitchers]);
}
?>