<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'baseball';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Agregar columna tipo_moneda si no existe
$sql = "SHOW COLUMNS FROM abonos LIKE 'tipo_moneda'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    $alter_sql = "ALTER TABLE abonos ADD COLUMN tipo_moneda ENUM('$', 'Bs') DEFAULT '$'";
    
    if ($conn->query($alter_sql)) {
        echo "Columna 'tipo_moneda' agregada correctamente.<br>";
        
        // Actualizar registros existentes (todos a $ por defecto)
        $update_sql = "UPDATE abonos SET tipo_moneda = '$' WHERE tipo_moneda IS NULL OR tipo_moneda = ''";
        if ($conn->query($update_sql)) {
            echo "Registros actualizados correctamente.";
        }
    } else {
        echo "Error al agregar columna: " . $conn->error;
    }
} else {
    echo "La columna 'tipo_moneda' ya existe en la tabla.";
}

$conn->close();
?>