<?php
include("../../config/conexion.php");
$con = conectar();

$categoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';
$temporada = isset($_GET['temporada']) ? (int)$_GET['temporada'] : 0;
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'bateadores';

if (!$categoria || !$temporada) {
    echo '<option value="">Faltan parámetros</option>';
    exit;
}

if ($tipo === 'bateadores') {
    // Consulta para jugadores bateadores
    $sql = "SELECT DISTINCT id_player, name_jgstats, ci, avg, hr
            FROM resumen_stats 
            WHERE id_temp = ? 
              AND categoria = ?
            ORDER BY ci DESC, avg DESC
            LIMIT 50";
    
    $stmt = $con->prepare($sql);
    $stmt->bind_param('is', $temporada, $categoria);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $options = '';
    while ($row = $result->fetch_assoc()) {
        $id = $row['id_player'];
        $nombre = htmlspecialchars($row['name_jgstats']);
        $ci = $row['ci'];
        $avg = number_format($row['avg'] / 1000, 3);
        $hr = $row['hr'];
        
        $options .= "<option value='$id'>$nombre (CI: $ci, AVG: $avg, HR: $hr)</option>";
    }
    
    if (empty($options)) {
        $options = '<option value="">No hay jugadores disponibles</option>';
    }
    
    echo $options;
    $stmt->close();
    
} else if ($tipo === 'pitchers') {
    // Consulta para pitchers
    $sql = "SELECT DISTINCT id_player, name_jglz, tjl, tjg, avg
            FROM resumen_lanz 
            WHERE id_temp = ? 
              AND categoria = ?
            ORDER BY tjg DESC, avg ASC
            LIMIT 30";
    
    $stmt = $con->prepare($sql);
    $stmt->bind_param('is', $temporada, $categoria);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $options = '';
    while ($row = $result->fetch_assoc()) {
        $id = $row['id_player'];
        $nombre = htmlspecialchars($row['name_jglz']);
        $tjg = $row['tjg'] ?? 0;
        $tjl = $row['tjl'] ?? 0;
        $avg = number_format($row['avg'] / 1000, 3);
        
        $options .= "<option value='$id'>$nombre (G: $tjg, P: $tjl, EF: $avg)</option>";
    }
    
    if (empty($options)) {
        $options = '<option value="">No hay pitchers disponibles</option>';
    }
    
    echo $options;
    $stmt->close();
}

$con->close();
?>