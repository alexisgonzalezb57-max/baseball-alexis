<?php
// Reporte Consolidado de Líderes - Sistema Baseball
require('vendor/fpdf/fpdf.php');
require('conexion.php');
$con = conectar();

// Verificar conexión
if (!$con) {
    die("Error de conexión a la base de datos");
}

// Parámetros recibidos
$categoria = $_POST['categoria'] ?? '';
$temporada = $_POST['temporada'] ?? '';

// Líderes seleccionados manualmente
$lideres_ci = [
    1 => $_POST['lider_ci_1'] ?? '',
    2 => $_POST['lider_ci_2'] ?? '', 
    3 => $_POST['lider_ci_3'] ?? ''
];

$lideres_avg = [
    1 => $_POST['lider_avg_1'] ?? '',
    2 => $_POST['lider_avg_2'] ?? '',
    3 => $_POST['lider_avg_3'] ?? ''
];

$lideres_hr = [
    1 => $_POST['lider_hr_1'] ?? '',
    2 => $_POST['lider_hr_2'] ?? '',
    3 => $_POST['lider_hr_3'] ?? ''
];

$lideres_picher = [
    1 => $_POST['lider_picher_1'] ?? '',
    2 => $_POST['lider_picher_2'] ?? '',
    3 => $_POST['lider_picher_3'] ?? ''
];

// VALORES CONFIGURABLES
$valor_pichers = 20;

// Obtener fecha del reporte
$verificar = mysqli_query($con, "SELECT * FROM report");
$vdta = mysqli_fetch_array($verificar);
$vfecha = $vdta['timeday'];
$vtt = $vfecha;
$entero_vtt = strtotime($vtt);
$ano_vtt = date("Y", $entero_vtt);
$mes_vtt = date("m", $entero_vtt);
$dia_vtt = date("d", $entero_vtt);
$timeday = $dia_vtt . '-' . $mes_vtt . '-' . $ano_vtt;

// Clase PDF personalizada
class PDF extends FPDF
{
    // Cabecera de página
    function Header()
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Image('../../fondos/pulpo (2).png', 10, 6, 12);
        $this->Image('../../fondos/pulpov (2).png', 189, 6, 12);
        $this->Cell(0, 5, utf8_decode(strtoupper('LIGA RECREATIVA SOFTBALL EDUCADORES DEL ESTADO ARAGUA')), 0, 1, 'C');
        $this->Ln(1);
        $this->Cell(0, 5, utf8_decode(strtoupper('CATEGORÍA "' . $GLOBALS['categoria'] . '"')), 0, 1, 'C');
        $this->Ln(1);
        $this->Cell(0, 5, utf8_decode('REPORTE CONSOLIDADO DE LÍDERES'), 0, 1, 'C');
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 5, 'FECHA: ' . $GLOBALS['timeday'], 0, 1, 'L');
        $this->Ln(5);
    }
    
    // Pie de página
    function Footer()
    {
        $this->SetY(-12);
        $this->SetFont('Arial', 'I', 7);
        $this->Cell(0, 5, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
    
    // Función para tabla de líderes
    function TablaLideres($headers, $data, $colWidths)
    {
        // Colors, line width and bold font
        $this->SetFillColor(220, 220, 220);
        $this->SetTextColor(0);
        $this->SetDrawColor(0);
        $this->SetLineWidth(.3);
        $this->SetFont('Arial', 'B', 9);
        
        // Header
        $this->Cell(30);
        for($i=0; $i<count($headers); $i++) {
            $this->Cell($colWidths[$i], 7, utf8_decode(strtoupper($headers[$i])), 1, 0, 'C', true);
        }
        $this->Ln();
        
        // Color and font restoration
        $this->SetFillColor(255, 255, 255);
        $this->SetTextColor(0);
        $this->SetFont('Arial', '', 8);
        
        // Data
        $fill = false;
        foreach($data as $row) {
            $this->Cell(30);
            for($i=0; $i<count($headers); $i++) {
                $this->Cell($colWidths[$i], 6, utf8_decode($row[$i]), 'LR', 0, 'C', $fill);
            }
            $this->Ln();
            $fill = !$fill;
        }
        
        // Closing line
        $this->Cell(30);
        for($i=0; $i<count($headers); $i++) {
            $this->Cell($colWidths[$i], 0, '', 'T');
        }
        $this->Ln(8);
    }
    
    // Función para tabla de premios
    function TablaPremios($headers, $data, $colWidths)
    {
        // Colors, line width and bold font
        $this->SetFillColor(220, 220, 220);
        $this->SetTextColor(0);
        $this->SetDrawColor(0);
        $this->SetLineWidth(.3);
        $this->SetFont('Arial', 'B', 9);
        
        // Header
        $this->Cell(30);
        for($i=0; $i<count($headers); $i++) {
            $this->Cell($colWidths[$i], 7, utf8_decode(strtoupper($headers[$i])), 1, 0, 'C', true);
        }
        $this->Ln();
        
        // Color and font restoration
        $this->SetFillColor(255, 255, 255);
        $this->SetTextColor(0);
        $this->SetFont('Arial', '', 8);
        
        // Data
        $fill = false;
        foreach($data as $row) {
            $this->Cell(30);
            for($i=0; $i<count($headers); $i++) {
                $align = ($i == 0) ? 'L' : 'C';
                $this->Cell($colWidths[$i], 6, utf8_decode($row[$i]), 'LR', 0, $align, $fill);
            }
            $this->Ln();
            $fill = !$fill;
        }
        
        // Closing line
        $this->Cell(30);
        for($i=0; $i<count($headers); $i++) {
            $this->Cell($colWidths[$i], 0, '', 'T');
        }
        $this->Ln(8);
    }
}

// Creación del objeto PDF
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();

// Configuración del documento
$pdf->SetTitle(utf8_decode('Reporte Consolidado de Líderes - Categoría ' . $categoria));
$pdf->SetAuthor('Sistema Baseball');
$pdf->SetCreator('Reporte Consolidado');

// Agregar página inicial
$pdf->AddPage();

// =============================================================================
// 1. LÍDERES EN CARRERAS EMPUJADAS (CI)
// =============================================================================
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 5, utf8_decode('LÍDERES EN CARRERAS EMPUJADAS (CI)'), 0, 1, 'C');
$pdf->Ln(2);

$data_ci = [];
$posiciones = ['1ro', '2do', '3ro'];

foreach ($lideres_ci as $pos => $player_id) {
    if (!empty($player_id)) {
        $query = "SELECT rs.name_jgstats, rs.ci, tc.name_team 
                 FROM resumen_stats rs 
                 LEFT JOIN tab_clasf tc ON rs.id_team = tc.id_team 
                 WHERE rs.id_player = $player_id 
                 LIMIT 1";
        
        $result = mysqli_query($con, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_array($result);
            $data_ci[] = [
                $posiciones[$pos-1],
                $row['name_jgstats'],
                $row['ci'],
                $row['name_team']
            ];
        } else {
            $data_ci[] = [
                $posiciones[$pos-1],
                'No seleccionado',
                '-',
                '-'
            ];
        }
    } else {
        $data_ci[] = [
            $posiciones[$pos-1],
            'No seleccionado',
            '-',
            '-'
        ];
    }
}

$pdf->TablaLideres(
    ['POS', 'NOMBRE', 'CI', 'EQUIPO'],
    $data_ci,
    [12, 60, 12, 45]
);

// =============================================================================
// 2. LÍDERES EN BATEO (AVG)
// =============================================================================
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 5, utf8_decode('LÍDERES EN BATEO (AVG)'), 0, 1, 'C');
$pdf->Ln(2);

$data_avg = [];
foreach ($lideres_avg as $pos => $player_id) {
    if (!empty($player_id)) {
        $query = "SELECT rs.name_jgstats, rs.tvb, rs.th, rs.avg, tc.name_team 
                 FROM resumen_stats rs 
                 LEFT JOIN tab_clasf tc ON rs.id_team = tc.id_team 
                 WHERE rs.id_player = $player_id 
                 LIMIT 1";
        
        $result = mysqli_query($con, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_array($result);
            // Convertir avg a formato decimal
            $avg_decimal = number_format($row['avg'] / 1000, 3);
            $data_avg[] = [
                $posiciones[$pos-1],
                $row['name_jgstats'],
                $row['tvb'],
                $row['th'],
                $avg_decimal,
                $row['name_team']
            ];
        } else {
            $data_avg[] = [
                $posiciones[$pos-1],
                'No seleccionado',
                '-',
                '-',
                '-',
                '-'
            ];
        }
    } else {
        $data_avg[] = [
            $posiciones[$pos-1],
            'No seleccionado',
            '-',
            '-',
            '-',
            '-'
        ];
    }
}

$pdf->TablaLideres(
    ['POS', 'NOMBRE', 'VB', 'H', 'AVG', 'EQUIPO'],
    $data_avg,
    [10, 45, 10, 10, 15, 40]
);

// =============================================================================
// 3. LÍDERES EN JONRONES (HR)
// =============================================================================
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 5, utf8_decode('LÍDERES EN JONRONES (HR)'), 0, 1, 'C');
$pdf->Ln(2);

$data_hr = [];
foreach ($lideres_hr as $pos => $player_id) {
    if (!empty($player_id)) {
        $query = "SELECT rs.name_jgstats, rs.hr, tc.name_team 
                 FROM resumen_stats rs 
                 LEFT JOIN tab_clasf tc ON rs.id_team = tc.id_team 
                 WHERE rs.id_player = $player_id 
                 LIMIT 1";
        
        $result = mysqli_query($con, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_array($result);
            $data_hr[] = [
                $posiciones[$pos-1],
                $row['name_jgstats'],
                $row['hr'],
                $row['name_team']
            ];
        } else {
            $data_hr[] = [
                $posiciones[$pos-1],
                'No seleccionado',
                '-',
                '-'
            ];
        }
    } else {
        $data_hr[] = [
            $posiciones[$pos-1],
            'No seleccionado',
            '-',
            '-'
        ];
    }
}

$pdf->TablaLideres(
    ['POS', 'NOMBRE', 'HR', 'EQUIPO'],
    $data_hr,
    [12, 60, 12, 45]
);

// =============================================================================
// 4. PICHERS GANADORES
// =============================================================================
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 5, utf8_decode('PICHERS GANADORES'), 0, 1, 'C');
$pdf->Ln(2);

$data_pichers = [];
foreach ($lideres_picher as $pos => $player_id) {
    if (!empty($player_id)) {
        $query = "SELECT rl.name_jglz, rl.tjl, rl.tjg, 
                         (rl.tjl - rl.tjg) as tjp, rl.avg, tc.name_team 
                  FROM resumen_lanz rl 
                  LEFT JOIN tab_clasf tc ON rl.id_team = tc.id_team 
                  WHERE rl.id_player = $player_id 
                  LIMIT 1";
        
        $result = mysqli_query($con, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_array($result);
            // Convertir avg a formato decimal para efectividad
            $efectividad = number_format($row['avg'] / 1000, 3);
            $data_pichers[] = [
                $posiciones[$pos-1],
                $row['name_jglz'],
                $row['tjl'] ?? '0',
                $row['tjg'] ?? '0',
                $row['tjp'] ?? '0',
                $efectividad,
                $row['name_team']
            ];
        } else {
            $data_pichers[] = [
                $posiciones[$pos-1],
                'No seleccionado',
                '-',
                '-',
                '-',
                '-',
                '-'
            ];
        }
    } else {
        $data_pichers[] = [
            $posiciones[$pos-1],
            'No seleccionado',
            '-',
            '-',
            '-',
            '-',
            '-'
        ];
    }
}

$pdf->TablaLideres(
    ['POS', 'NOMBRE', 'JL', 'JG', 'JP', 'EF', 'EQUIPO'],
    $data_pichers,
    [10, 40, 10, 10, 10, 15, 35]
);

// =============================================================================
// 5. RESUMEN DE PREMIOS
// =============================================================================
/*
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 5, utf8_decode('RESUMEN DE PREMIOS'), 0, 1, 'C');
$pdf->Ln(2);

$data_premios = [
    ['Líder en Carreras Empujadas - 1er Lugar', '$50'],
    ['Líder en Carreras Empujadas - 2do Lugar', '$30'],
    ['Líder en Carreras Empujadas - 3er Lugar', '$20'],
    ['Líder en Bateo - 1er Lugar', '$50'],
    ['Líder en Bateo - 2do Lugar', '$30'],
    ['Líder en Bateo - 3er Lugar', '$20'],
    ['Líder en Jonrones - 1er Lugar', '$50'],
    ['Líder en Jonrones - 2do Lugar', '$30'],
    ['Líder en Jonrones - 3er Lugar', '$20'],
    ['Picher Ganador - 1er Lugar', '$' . $valor_pichers],
    ['Picher Ganador - 2do Lugar', '$' . $valor_pichers],
    ['Picher Ganador - 3er Lugar', '$' . $valor_pichers]
];

$pdf->TablaPremios(
    ['CATEGORÍA', 'PREMIO'],
    $data_premios,
    [120, 30]
);
*/

// =============================================================================
// GENERAR EL PDF
// =============================================================================
$pdf->Output('I', 'Reporte_Lideres_' . $categoria . '_' . $temporada . '.pdf');

// Cerrar conexión
mysqli_close($con);
?>