<?php
// Reporte Consolidado de Líderes - Sistema Baseball
require('vendor/fpdf/fpdf.php');
require('conexion.php');
$con = conectar();

// Parámetros recibidos
$equipo = $_POST['equipo'];
$temporada = $_POST['temporada'];
$categoria = $_POST['categoria'];

// VALOR CONFIGURABLE PARA PICHERS - Puede ser ajustado según necesidades
$valor_pichers = 20; // <-- VALOR MODIFICABLE PARA PICHERS

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
    
    // Función para agregar título de sección
    function SectionTitle($title)
    {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 5, utf8_decode($title), 0, 1, 'C');
        $this->Ln(2);
    }
    
    // Función para agregar tabla
    function AddTable($headers, $data, $colWidths, $aligns = [])
    {
        // Establecer alineaciones por defecto si no se proporcionan
        if (empty($aligns)) {
            $aligns = array_fill(0, count($headers), 'C');
        }
        
        // Configurar fuentes y colores para la tabla
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(220, 220, 220);
        
        // Imprimir encabezados
        for ($i = 0; $i < count($headers); $i++) {
            $this->Cell($colWidths[$i], 6, utf8_decode(strtoupper($headers[$i])), 1, 0, $aligns[$i], true);
        }
        $this->Ln();
        $this->cell(30);
        
        // Imprimir datos
        $this->SetFont('Arial', '', 8);
        $this->SetFillColor(255, 255, 255);
        
        foreach ($data as $row) {
            $height = 5;
            for ($i = 0; $i < count($headers); $i++) {
                $this->Cell($colWidths[$i], $height, utf8_decode($row[$i]), 1, 0, $aligns[$i]);
            }
            $this->Ln();
        $this->cell(30);
        }
        $this->Ln(4);
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
// 1. LÍDERES EN CARRERAS ANOTADAS Y EMPUJADAS (de lidcaci.php)
// =============================================================================


// Líderes en carreras empujadas
$pdf->SectionTitle('LÍDERES EN CARRERAS ANOTADAS');

$query_ca = "SELECT DISTINCT rs.*, tc.name_team 
            FROM resumen_stats rs
            LEFT JOIN tab_clasf tc ON rs.id_team = tc.id_team
            WHERE rs.id_temp = $temporada 
            ORDER BY rs.ca DESC 
            LIMIT 3";

$result_ca = mysqli_query($con, $query_ca);
$data_ca = [];

while ($row = mysqli_fetch_array($result_ca)) {
    $data_ca[] = [
        count($data_ca) + 1,
        $row['name_jgstats'],
        $row['ca'],
        $row['name_team']
    ];
}

$pdf->Cell(30);
$pdf->AddTable(
    ['POS', 'NOMBRE', 'CA', 'EQUIPO'],
    $data_ca,
    [12, 60, 12, 45]
);



// =============================================================================
// 3. LÍDERES EN BATEO Y JONRONES (de lidvbhr.php)
// =============================================================================
$pdf->SectionTitle('LÍDERES EN BATEO');

// VALOR CONFIGURABLE PARA CAMPEONES DE BATEO
$valor_campeon_bateo = 20; // <-- VALOR MODIFICABLE PARA CAMPEONES DE BATEO

$query_avg = "SELECT rs.name_jgstats, MAX(rs.tvb) as tvb, MAX(rs.th) as th, MAX(rs.avg) as avg, tc.name_team 
FROM resumen_stats rs 
INNER JOIN tab_clasf tc ON rs.id_team = tc.id_team 
WHERE rs.id_temp = $temporada 
GROUP BY rs.name_jgstats, tc.name_team
ORDER BY (MAX(rs.tvb) >= $valor_campeon_bateo) DESC, MAX(rs.avg) DESC, MAX(rs.cb) DESC 
LIMIT 3";

$result_avg = mysqli_query($con, $query_avg);
$data_avg = [];

while ($row = mysqli_fetch_array($result_avg)) {
    $data_avg[] = [
        count($data_avg) + 1,
        $row['name_jgstats'],
        $row['tvb'],
        $row['th'],
        $row['avg'],
        $row['name_team']
    ];
}

$pdf->Cell(30);
$pdf->AddTable(
    ['POS', 'NOMBRE', 'VB', 'H', 'AVG', 'EQUIPO'],
    $data_avg,
    [10, 45, 10, 10, 15, 40]
);

// Líderes en jonrones
$pdf->SectionTitle('LÍDERES EN JONRONES');

$query_hr = "SELECT DISTINCT rs.name_jgstats, rs.hr, tc.name_team 
FROM resumen_stats rs 
INNER JOIN tab_clasf tc ON rs.id_team = tc.id_team 
WHERE rs.id_temp = $temporada 
ORDER BY rs.hr DESC 
LIMIT 3";

$result_hr = mysqli_query($con, $query_hr);
$data_hr = [];

while ($row = mysqli_fetch_array($result_hr)) {
    $data_hr[] = [
        count($data_hr) + 1,
        $row['name_jgstats'],
        $row['hr'],
        $row['name_team']
    ];
}

$pdf->Cell(30);
$pdf->AddTable(
    ['POS', 'NOMBRE', 'HR', 'EQUIPO'],
    $data_hr,
    [12, 60, 12, 45]
);

// =============================================================================
// 4. LÍDERES PICHERS (de pichers.php)
// =============================================================================
$pdf->SectionTitle('PICHERS GANADORES');

$query_pichers_gan = "SELECT DISTINCT rl.name_jglz, rl.tjl, rl.tjg, 
                      (rl.tjl - rl.tjg) as tjp, rl.avg, tc.name_team 
                      FROM resumen_lanz rl 
                      INNER JOIN tab_clasf tc ON rl.id_team = tc.id_team 
                      WHERE rl.id_temp = $temporada 
                      ORDER BY rl.tjg DESC 
                      LIMIT 3";

$result_pichers_gan = mysqli_query($con, $query_pichers_gan);
$data_pichers_gan = [];

while ($row = mysqli_fetch_array($result_pichers_gan)) {
    $data_pichers_gan[] = [
        count($data_pichers_gan) + 1,
        $row['name_jglz'],
        $row['tjl'],
        $row['tjg'],
        $row['tjp'],
        $row['avg'],
        $row['name_team']
    ];
}

$pdf->Cell(30);
$pdf->AddTable(
    ['POS', 'NOMBRE', 'JL', 'JG', 'JP', 'AVG', 'EQUIPO'],
    $data_pichers_gan,
    [10, 40, 10, 10, 10, 15, 35]
);



// =============================================================================
// GENERAR EL PDF
// =============================================================================
$pdf->Output();
?>