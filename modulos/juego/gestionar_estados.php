<?php
include("../../config/conexion.php");
$con = conectar();

// Verificar conexión
if (!$con) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Obtener y sanitizar parámetros
$accion = filter_var($_POST['accion'] ?? '', FILTER_SANITIZE_STRING);
$id_temp = filter_var($_POST['id_temp'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
$categoria = filter_var($_POST['categoria'] ?? '', FILTER_SANITIZE_STRING);

if ($accion === 'clasificar' || $accion === 'eliminar') {
    $estado = ($accion === 'clasificar') ? 'C' : 'E';
    $equipos = $_POST['equipos'] ?? [];
    
    // Eliminar estados existentes para estos equipos en esta temporada
    if (!empty($equipos)) {
        $placeholders = implode(',', array_fill(0, count($equipos), '?'));
        $deleteQuery = "DELETE FROM equipo_estados WHERE id_temp = ? AND id_tab IN ($placeholders)";
        $stmt = mysqli_prepare($con, $deleteQuery);
        $types = str_repeat('i', count($equipos));
        mysqli_stmt_bind_param($stmt, "i$types", $id_temp, ...$equipos);
        mysqli_stmt_execute($stmt);
    }
    
    // Insertar nuevos estados
    if (!empty($equipos)) {
        $insertQuery = "INSERT INTO equipo_estados (id_tab, id_temp, estado) VALUES ";
        $values = [];
        $params = [];
        $types = '';
        
        foreach ($equipos as $id_tab) {
            $values[] = "(?, ?, ?)";
            $params[] = $id_tab;
            $params[] = $id_temp;
            $params[] = $estado;
            $types .= 'iis';
        }
        
        $insertQuery .= implode(',', $values);
        $stmt = mysqli_prepare($con, $insertQuery);
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
    }
    
    header("Location: formclasf.php?id=$id_temp&cat=" . urlencode($categoria) . "&success=1");
    exit();
    
} elseif ($accion === 'retirar') {
    $equipos_retirados = $_POST['equipos_retirados'] ?? [];
    $equipos_beneficiados = $_POST['equipos_beneficiados'] ?? [];
    
    if (empty($equipos_retirados) {
        header("Location: formclasf.php?id=$id_temp&cat=" . urlencode($categoria) . "&error=No se seleccionaron equipos retirados");
        exit();
    }
    
    if (empty($equipos_beneficiados)) {
        header("Location: formclasf.php?id=$id_temp&cat=" . urlencode($categoria) . "&error=No se seleccionaron equipos beneficiados");
        exit();
    }
    
    // Iniciar transacción
    mysqli_begin_transaction($con);
    
    try {
        // 1. Registrar equipos como retirados
        $placeholders = implode(',', array_fill(0, count($equipos_retirados), '?'));
        $deleteQuery = "DELETE FROM equipo_estados WHERE id_temp = ? AND id_tab IN ($placeholders)";
        $stmt = mysqli_prepare($con, $deleteQuery);
        $types = str_repeat('i', count($equipos_retirados));
        mysqli_stmt_bind_param($stmt, "i$types", $id_temp, ...$equipos_retirados);
        mysqli_stmt_execute($stmt);
        
        $insertQuery = "INSERT INTO equipo_estados (id_tab, id_temp, estado) VALUES ";
        $values = [];
        $params = [];
        $types = '';
        
        foreach ($equipos_retirados as $id_tab) {
            $values[] = "(?, ?, ?)";
            $params[] = $id_tab;
            $params[] = $id_temp;
            $params[] = 'R';
            $types .= 'iis';
        }
        
        $insertQuery .= implode(',', $values);
        $stmt = mysqli_prepare($con, $insertQuery);
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        
        // 2. Redistribuir puntos de los equipos retirados
        $puntos_por_equipo = count($equipos_retirados) / count($equipos_beneficiados);
        $puntos_redistribuir = ceil($puntos_por_equipo); // Redondeamos hacia arriba
        
        foreach ($equipos_beneficiados as $id_tab) {
            // Actualizar JJ (solo sumar 1 por equipo beneficiado)
            $updateQuery = "UPDATE tab_clasf SET jj = jj + 1, jg = jg + ? WHERE id_tab = ? AND id_temp = ?";
            $stmt = mysqli_prepare($con, $updateQuery);
            mysqli_stmt_bind_param($stmt, "iii", $puntos_redistribuir, $id_tab, $id_temp);
            mysqli_stmt_execute($stmt);
            
            // Recalcular AVG
            $recalcQuery = "UPDATE tab_clasf SET avg = ROUND(jg / jj, 3) WHERE id_tab = ? AND id_temp = ?";
            $stmt = mysqli_prepare($con, $recalcQuery);
            mysqli_stmt_bind_param($stmt, "ii", $id_tab, $id_temp);
            mysqli_stmt_execute($stmt);
        }
        
        // Confirmar transacción
        mysqli_commit($con);
        
        header("Location: formclasf.php?id=$id_temp&cat=" . urlencode($categoria) . "&success=2");
        exit();
        
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        mysqli_rollback($con);
        header("Location: formclasf.php?id=$id_temp&cat=" . urlencode($categoria) . "&error=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    header("Location: formclasf.php?id=$id_temp&cat=" . urlencode($categoria) . "&error=Acción no válida");
    exit();
}
?>