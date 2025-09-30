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

// Consulta para obtener jugadores con sus estadísticas
$query = "SELECT 
            rs.id_player,
            CONCAT(j.nombre, ' ', j.apellido) as nombre_completo,
            rs.ci,
            rs.avg,
            rs.hr,
            rs.tvb,
            rs.th,
            tc.name_team as equipo
          FROM resumen_stats rs
          LEFT JOIN tab_clasf tc ON rs.id_team = tc.id_team
          LEFT JOIN jugadores j ON rs.id_player = j.id_player
          WHERE rs.id_temp = ? 
          AND rs.id_team = ? 
          AND rs.categoria = ?
          ORDER BY rs.name_jgstats";

$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "iis", $temporada, $equipo, $categoria);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$players = [];
while ($row = mysqli_fetch_assoc($result)) {
    $players[] = [
        'id' => $row['id_player'],
        'name' => $row['nombre_completo'] ?: $row['name_jgstats'],
        'ci' => $row['ci'] ?? 0,
        'avg' => $row['avg'] ?? 0,
        'hr' => $row['hr'] ?? 0,
        'tvb' => $row['tvb'] ?? 0,
        'th' => $row['th'] ?? 0,
        'equipo' => $row['equipo']
    ];
}

mysqli_stmt_close($stmt);

if (empty($players)) {
    echo json_encode(['success' => false, 'message' => 'No se encontraron jugadores para los criterios seleccionados']);
} else {
    echo json_encode(['success' => true, 'players' => $players]);
}
?>