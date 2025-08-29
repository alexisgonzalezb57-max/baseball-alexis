<?php
include("../../config/conexion.php");
$con = conectar();

$categoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';
$temporada = isset($_GET['temporada']) ? (int)$_GET['temporada'] : 0;

if (!$categoria || !$temporada) {
    echo '<div class="alert alert-danger">Faltan parámetros válidos.</div>';
    exit;
}

// Obtener equipos de la categoría seleccionada
$sqlEquipos = "SELECT id_team, nom_team FROM equipos WHERE categoria = ? ORDER BY nom_team";
$stmt = $con->prepare($sqlEquipos);
if ($stmt === false) {
    echo '<div class="alert alert-danger">Error preparando consulta de equipos: ' . htmlspecialchars($con->error) . '</div>';
    exit;
}
$stmt->bind_param('s', $categoria);
$stmt->execute();
$resEquipos = $stmt->get_result();

if ($resEquipos->num_rows == 0) {
    echo '<div class="alert alert-warning">No hay equipos para esta categoría.</div>';
    exit;
}

echo '<div class="form-group">';

while ($equipo = $resEquipos->fetch_assoc()) {
    $id_team = (int)$equipo['id_team'];
    $nombreEquipo = htmlspecialchars($equipo['nom_team']);

    echo "<h5 class='mt-3 font-weight-bold'>" .$nombreEquipo. "</h5>";

    // Buscar jugadores de ese equipo y temporada con estadística
    $sqlJugadores = "
        SELECT DISTINCT j.id_player, CONCAT(j.nombre, ' ', j.apellido) AS nombre_completo
        FROM jugadores j
        INNER JOIN resumen_stats rs ON rs.id_player = j.id_player
        WHERE j.id_team = ? 
          AND rs.id_temp = ? 
          AND rs.categoria = ?
        ORDER BY j.nombre, j.apellido";

    $stmtJug = $con->prepare($sqlJugadores);
    if ($stmtJug === false) {
        echo '<p class="text-danger">Error preparando consulta jugadores: ' . htmlspecialchars($con->error) . '</p>';
        continue;
    }
    $stmtJug->bind_param('iis', $id_team, $temporada, $categoria);
    $stmtJug->execute();
    $resJugadores = $stmtJug->get_result();

    if ($resJugadores->num_rows == 0) {
        echo '<p><em class="pl-3 text-muted">No hay jugadores para este equipo en la temporada y categoría seleccionadas.</em></p>';
    } else {
        // Contenedor row para filas con 3 columnas
        echo '<div class="row pl-3">';
        while ($jugador = $resJugadores->fetch_assoc()) {
            $id_player = (int)$jugador['id_player'];
            $nombreJugador = htmlspecialchars($jugador['nombre_completo']);
            echo '<div class="col-md-4 mb-3">';
            echo "<div class='form-check'>";
            // Checkbox más grande y cursor pointer
            echo "<input class='form-check-input' type='checkbox' name='asistencia[]' value='$id_player' id='jugador_$id_player' style='width: 20px; height: 20px; cursor:pointer;'>";
            // Label más grande y cursor pointer con margen a la izquierda
            echo "<label class='form-check-label' for='jugador_$id_player' style='font-size: 1.2rem; font-weight:700; cursor:pointer; margin-left: 8px;'>" .$nombreJugador. "</label>";
            echo "</div>";
            echo '</div>';
        }
        echo '</div>'; // cierre row
    }
    $stmtJug->close();
}

echo '</div>';

$stmt->close();
$con->close();
?>
