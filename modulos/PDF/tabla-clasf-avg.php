<?php
// Inclusión de Archivo Necesario y Conexión
require('vendor/fpdf/fpdf.php');
require('conexion.php');
$con = conectar();

// Obtener parámetros de la URL
$id_temp = isset($_GET['id_temp']) ? intval($_GET['id_temp']) : 0;
$id_tab = isset($_GET['id_tab']) ? intval($_GET['id_tab']) : 0;

// Verificar que los parámetros sean válidos
if ($id_temp <= 0 || $id_tab <= 0) {
    die("Parámetros inválidos. Se requiere id_temp y id_tab válidos.");
}

// Obtener fecha formateada
$verificar = mysqli_query($con, "SELECT * FROM report");
$vdta = mysqli_fetch_array($verificar);
$vfecha = $vdta['timeday'];
$entero_vtt = strtotime($vfecha);
$ano_vtt = date("Y", $entero_vtt);
$mes_vtt = date("m", $entero_vtt);
$dia_vtt = date("d", $entero_vtt);
$timeday = $dia_vtt . '-' . $mes_vtt . '-' . $ano_vtt;

// Obtener información del equipo específico
$query_equipo = "SELECT t.*, temp.name_temp, temp.categoria, e.estado 
                 FROM tab_clasf t 
                 INNER JOIN temporada temp ON t.id_temp = temp.id_temp 
                 LEFT JOIN equipo_estados e ON t.id_tab = e.id_tab AND t.id_temp = e.id_temp
                 WHERE t.id_temp = ? AND t.id_tab = ?";
$stmt_equipo = mysqli_prepare($con, $query_equipo);
mysqli_stmt_bind_param($stmt_equipo, "ii", $id_temp, $id_tab);
mysqli_stmt_execute($stmt_equipo);
$equipo = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_equipo));

// Verificar que el equipo existe
if (!$equipo) {
    die("No se encontró el equipo con los parámetros proporcionados.");
}

// Obtener información de homenaje
$query_homenaje = "SELECT * FROM homenaje WHERE id_temp = ?";
$stmt_homenaje = mysqli_prepare($con, $query_homenaje);
mysqli_stmt_bind_param($stmt_homenaje, "i", $id_temp);
mysqli_stmt_execute($stmt_homenaje);
$homenaje = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_homenaje));

// Obtener todos los equipos de la misma categoría para la tabla comparativa
$query_todos_equipos = "SELECT t.*, e.estado 
                        FROM tab_clasf t 
                        LEFT JOIN equipo_estados e ON t.id_tab = e.id_tab AND t.id_temp = e.id_temp
                        WHERE t.id_temp = ? AND t.categoria LIKE CONCAT('%', ?, '%')
                        ORDER BY t.jg DESC, t.avg DESC";
$stmt_todos = mysqli_prepare($con, $query_todos_equipos);
mysqli_stmt_bind_param($stmt_todos, "is", $id_temp, $equipo['categoria']);
mysqli_stmt_execute($stmt_todos);
$todos_equipos = mysqli_stmt_get_result($stmt_todos);

// Clase extendida de FPDF para cabecera y pie de página
class PDF extends FPDF
{
    private $id_temp;
    private $id_tab;
    private $timeday;
    
    function __construct($id_temp, $id_tab, $timeday) {
        parent::__construct('P', 'mm', 'Letter');
        $this->id_temp = $id_temp;
        $this->id_tab = $id_tab;
        $this->timeday = $timeday;
    }
    
    function Header()
    {
        $this->SetFont('Arial', 'B', 15);
        $this->Image('../../fondos/pulpo (2).png', 10, 10, 16);
        $this->Image('../../fondos/pulpov (2).png', 189, 10, 16);
        $this->Cell(0, 5, utf8_decode(strtoupper('LIGA RECREATIVA SOFTBALL EDUCADORES DEL ESTADO ARAGUA')), 0, 1, 'C');
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(17);
        $this->Cell(0, 5, 'FECHA: ' . $this->timeday, 0, 1, 'L');
        $this->SetFont('Arial', 'B', 14);
        $this->Ln(4);
    }
    
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Reporte personalizado - Temporada: ') . $this->id_temp . utf8_decode(' - Equipo: ') . $this->id_tab . ' - Página ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

$pdf = new PDF($id_temp, $id_tab, $timeday);
$pdf->AliasNbPages();
$pdf->SetTitle('Reporte Personalizado - Equipo: ' . $equipo['name_team']);
$pdf->SetAuthor('Sistema Baseball');
$pdf->SetCreator('FPDF Maker');

$pdf->AddPage();

// Título principal
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, utf8_decode('REPORTE PERSONALIZADO DE EQUIPO'), 0, 1, 'C');
$pdf->Ln(5);

// Información de la temporada
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 8, utf8_decode('TEMPORADA: ' . $equipo['name_temp']), 0, 1, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 6, utf8_decode('Categoría: ' . $equipo['categoria']), 0, 1, 'L');
if (!empty($homenaje['honor'])) {
    $pdf->Cell(0, 6, utf8_decode('Persona de Honor: ' . $homenaje['honor']), 0, 1, 'L');
}
$pdf->Ln(5);

// Información detallada del equipo
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 8, utf8_decode('EQUIPO: ' . $equipo['name_team']), 0, 1, 'L');
$pdf->Ln(3);

// Determinar estado
$estado_texto = '';
if ($equipo['estado'] == 'C') {
    $estado_texto = 'CLASIFICADO';
} elseif ($equipo['estado'] == 'E') {
    $estado_texto = 'ELIMINADO';
} elseif ($equipo['estado'] == 'R') {
    $estado_texto = 'RETIRADO';
} elseif ($equipo['estado'] == 'G') {
    $estado_texto = 'GANADOR';
} else {
    $estado_texto = 'SIN ESTADO DEFINIDO';
}

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 8, utf8_decode('Estado:'), 0, 0, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, utf8_decode($estado_texto), 0, 1, 'L');
$pdf->Ln(3);

// Estadísticas del equipo
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, utf8_decode('ESTADÍSTICAS'), 0, 1, 'L');
$pdf->SetFont('Arial', '', 11);

// Crear tabla de estadísticas
$pdf->Cell(40, 7, utf8_decode('Juegos Jugados (JJ):'), 0, 0, 'L');
$pdf->Cell(20, 7, $equipo['jj'], 0, 0, 'R');
$pdf->Cell(20, 7, '', 0, 0, 'L'); // Espacio
$pdf->Cell(40, 7, utf8_decode('Carreras Anotadas (CA):'), 0, 0, 'L');
$pdf->Cell(20, 7, $equipo['ca'], 0, 1, 'R');

$pdf->Cell(40, 7, utf8_decode('Juegos Ganados (JG):'), 0, 0, 'L');
$pdf->Cell(20, 7, $equipo['jg'], 0, 0, 'R');
$pdf->Cell(20, 7, '', 0, 0, 'L');
$pdf->Cell(40, 7, utf8_decode('Carreras Permitidas (CE):'), 0, 0, 'L');
$pdf->Cell(20, 7, $equipo['ce'], 0, 1, 'R');

$pdf->Cell(40, 7, utf8_decode('Juegos Perdidos (JP):'), 0, 0, 'L');
$pdf->Cell(20, 7, $equipo['jp'], 0, 0, 'R');
$pdf->Cell(20, 7, '', 0, 0, 'L');
$pdf->Cell(40, 7, utf8_decode('Diferencia (DIF):'), 0, 0, 'L');
$pdf->Cell(20, 7, $equipo['dif'], 0, 1, 'R');

$pdf->Cell(40, 7, utf8_decode('Juegos Empatados (JE):'), 0, 0, 'L');
$pdf->Cell(20, 7, $equipo['je'], 0, 0, 'R');
$pdf->Cell(20, 7, '', 0, 0, 'L');
$pdf->Cell(40, 7, utf8_decode('Promedio (AVG):'), 0, 0, 'L');
$pdf->Cell(20, 7, $equipo['avg'], 0, 1, 'R');

$pdf->Ln(10);

// Tabla comparativa con todos los equipos de la categoría
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 8, utf8_decode('TABLA DE CLASIFICACIÓN - CATEGORÍA ' . $equipo['categoria']), 0, 1, 'C');
$pdf->Ln(3);

// Cabecera de la tabla
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(10, 6, utf8_decode('POS'), 1, 0, 'C');
$pdf->Cell(50, 6, utf8_decode('EQUIPO'), 1, 0, 'C');
$pdf->Cell(15, 6, utf8_decode('JJ'), 1, 0, 'C');
$pdf->Cell(15, 6, utf8_decode('JG'), 1, 0, 'C');
$pdf->Cell(15, 6, utf8_decode('JP'), 1, 0, 'C');
$pdf->Cell(15, 6, utf8_decode('JE'), 1, 0, 'C');
$pdf->Cell(15, 6, utf8_decode('AVG'), 1, 0, 'C');
$pdf->Cell(15, 6, utf8_decode('CA'), 1, 0, 'C');
$pdf->Cell(15, 6, utf8_decode('CE'), 1, 0, 'C');
$pdf->Cell(15, 6, utf8_decode('DIF'), 1, 0, 'C');
$pdf->Cell(20, 6, utf8_decode('ESTADO'), 1, 1, 'C');

// Datos de la tabla
$posicion = 1;
$pdf->SetFont('Arial', '', 9);
while ($fila = mysqli_fetch_assoc($todos_equipos)) {
    // Determinar si es el equipo actual para resaltarlo
    if ($fila['id_tab'] == $id_tab) {
        $pdf->SetFillColor(220, 240, 255); // Color de fondo para resaltar
    } else {
        $pdf->SetFillColor(255, 255, 255); // Fondo blanco
    }
    
    // Determinar estado
    $estado = '';
    if ($fila['estado'] == 'C') {
        $estado = 'CLASIF';
    } elseif ($fila['estado'] == 'E') {
        $estado = 'ELIM';
    } elseif ($fila['estado'] == 'R') {
        $estado = 'RET';
    } elseif ($fila['estado'] == 'G') {
        $estado = 'GAN';
    } else {
        $estado = '-';
    }
    
    $pdf->Cell(10, 6, $posicion, 1, 0, 'C', true);
    $pdf->Cell(50, 6, utf8_decode($fila['name_team']), 1, 0, 'L', true);
    $pdf->Cell(15, 6, $fila['jj'], 1, 0, 'C', true);
    $pdf->Cell(15, 6, $fila['jg'], 1, 0, 'C', true);
    $pdf->Cell(15, 6, $fila['jp'], 1, 0, 'C', true);
    $pdf->Cell(15, 6, $fila['je'], 1, 0, 'C', true);
    $pdf->Cell(15, 6, $fila['avg'], 1, 0, 'C', true);
    $pdf->Cell(15, 6, $fila['ca'], 1, 0, 'C', true);
    $pdf->Cell(15, 6, $fila['ce'], 1, 0, 'C', true);
    $pdf->Cell(15, 6, $fila['dif'], 1, 0, 'C', true);
    $pdf->Cell(20, 6, $estado, 1, 1, 'C', true);
    
    $posicion++;
}

$pdf->Ln(10);

// Información de premiación si está disponible
if ($homenaje) {
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 8, utf8_decode('INFORMACIÓN DE PREMIACIÓN'), 0, 1, 'L');
    $pdf->Ln(3);
    
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(50, 6, utf8_decode('PREMIOS POR POSICIÓN'), 0, 0, 'L');
    $pdf->Cell(50, 6, '', 0, 0, 'L');
    $pdf->Cell(50, 6, utf8_decode('PREMIOS ESPECIALES'), 0, 1, 'L');
    
    $pdf->SetFont('Arial', '', 10);
    
    // Premios por posición
    $premios_posicion = [
        ['label' => '1ER LUGAR', 'key' => 'prize_once', 'cant_key' => 'cant_once'],
        ['label' => '2DO LUGAR', 'key' => 'prize_second', 'cant_key' => 'cant_second'],
        ['label' => '3ER LUGAR', 'key' => 'prize_third', 'cant_key' => 'cant_third'],
        ['label' => '4TO LUGAR', 'key' => 'prize_four', 'cant_key' => 'cant_four'],
    ];
    
    // Premios especiales
    $premios_especiales = [
        ['label' => 'CHAMPION BATE', 'key' => 'cant_lbt'],
        ['label' => 'CHAMPION HR', 'key' => 'cant_lj'],
        ['label' => 'CHAMPION CE', 'key' => 'cant_lce'],
        ['label' => 'CHAMPION PG', 'key' => 'cant_pg'],
        ['label' => 'CHAMPION BB', 'key' => 'cant_lb'],
        ['label' => 'CHAMPION 2B', 'key' => 'cant_ld'],
        ['label' => 'CHAMPION 3B', 'key' => 'cant_lt'],
        ['label' => 'CHAMPION CA', 'key' => 'cant_lca'],
        ['label' => 'CHAMPION PE', 'key' => 'cant_pe'],
        ['label' => 'CHAMPION K', 'key' => 'cant_lp'],
    ];
    
    $y = $pdf->GetY();
    
    // Imprimir premios por posición (columna izquierda)
    foreach ($premios_posicion as $premio) {
        if (!empty($homenaje[$premio['key']])) {
            $pdf->SetXY(10, $y);
            $pdf->Cell(40, 6, utf8_decode($premio['label']), 0, 0, 'L');
            $pdf->Cell(40, 6, utf8_decode($homenaje[$premio['key']]), 0, 0, 'L');
            $pdf->Cell(10, 6, '$' . $homenaje[$premio['cant_key']], 0, 0, 'R');
            $y += 6;
        }
    }
    
    $y = $pdf->GetY() - (count($premios_posicion) * 6); // Reset Y position
    
    // Imprimir premios especiales (columna derecha)
    foreach ($premios_especiales as $premio) {
        if (!empty($homenaje[$premio['key']]) && $homenaje[$premio['key']] > 0) {
            $pdf->SetXY(110, $y);
            $pdf->Cell(40, 6, utf8_decode($premio['label']), 0, 0, 'L');
            $pdf->Cell(40, 6, '$' . $homenaje[$premio['key']], 0, 0, 'R');
            $y += 6;
        }
    }
    
    $pdf->SetY(max($pdf->GetY(), $y) + 10);
}

// Pie de página personalizado
$pdf->SetY(-30);
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 6, utf8_decode('Reporte generado el: ' . date('d/m/Y H:i:s')), 0, 1, 'C');
$pdf->Cell(0, 6, utf8_decode('Sistema de Gestión Baseball - Liga Recreativa Softball Educadores del Estado Aragua'), 0, 1, 'C');

$pdf->Output('I', 'Reporte_Equipo_' . $equipo['name_team'] . '.pdf');
?>