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
        
        // Imprimir datos
        $this->SetFont('Arial', '', 8);
        $this->SetFillColor(255, 255, 255);
        
        foreach ($data as $row) {
            $height = 5;
            for ($i = 0; $i < count($headers); $i++) {
                $this->Cell($colWidths[$i], $height, utf8_decode($row[$i]), 1, 0, $aligns[$i]);
            }
            $this->Ln();
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
$pdf->SectionTitle('LÍDERES EN CARRERAS ANOTADAS');

// Consulta para carreras anotadas
$query_ca = "SELECT rs.name_jgstats, rs.ca, tc.name_team 
             FROM resumen_stats rs 
             INNER JOIN tab_clasf tc ON rs.id_team = tc.id_team 
             WHERE rs.id_temp = $temporada 
             ORDER BY rs.ca DESC 
             LIMIT 3";

$result_ca = mysqli_query($con, $query_ca);
$data_ca = [];

while ($row = mysqli_fetch_array($result_ca)) {
    $data_ca[] = [
        $pdf->PageNo() > 1 ? count($data_ca) + 1 : count($data_ca) + 1,
        $row['name_jgstats'],
        $row['ca'],
        $row['name_team']
    ];
}

$pdf->AddTable(
    ['POS', 'NOMBRE', 'CA', 'EQUIPO'],
    $data_ca,
    [12, 60, 12, 45]
);

// Líderes en carreras empujadas
$pdf->SectionTitle('LÍDERES EN CARRERAS EMPUJADAS');

$query_ci = "SELECT rs.name_jgstats, rs.ci, tc.name_team 
             FROM resumen_stats rs 
             INNER JOIN tab_clasf tc ON rs.id_team = tc.id_team 
             WHERE rs.id_temp = $temporada 
             ORDER BY rs.ci DESC 
             LIMIT 3";

$result_ci = mysqli_query($con, $query_ci);
$data_ci = [];

while ($row = mysqli_fetch_array($result_ci)) {
    $data_ci[] = [
        count($data_ci) + 1,
        $row['name_jgstats'],
        $row['ci'],
        $row['name_team']
    ];
}

$pdf->AddTable(
    ['POS', 'NOMBRE', 'CI', 'EQUIPO'],
    $data_ci,
    [12, 60, 12, 45]
);

// =============================================================================
// 2. LÍDERES EN PONCHES Y BOLETOS (de lidkobo.php)
// =============================================================================
$pdf->SectionTitle('LÍDERES EN PONCHES');

$query_k = "SELECT rs.name_jgstats, rs.k, tc.name_team 
            FROM resumen_stats rs 
            INNER JOIN tab_clasf tc ON rs.id_team = tc.id_team 
            WHERE rs.id_temp = $temporada 
            ORDER BY rs.k DESC 
            LIMIT 3";

$result_k = mysqli_query($con, $query_k);
$data_k = [];

while ($row = mysqli_fetch_array($result_k)) {
    $data_k[] = [
        count($data_k) + 1,
        $row['name_jgstats'],
        $row['k'],
        $row['name_team']
    ];
}

$pdf->AddTable(
    ['POS', 'NOMBRE', 'K', 'EQUIPO'],
    $data_k,
    [12, 60, 12, 45]
);

// Líderes en boletos
$pdf->SectionTitle('LÍDERES EN BOLETOS');

$query_b = "SELECT rs.name_jgstats, rs.b, tc.name_team 
            FROM resumen_stats rs 
            INNER JOIN tab_clasf tc ON rs.id_team = tc.id_team 
            WHERE rs.id_temp = $temporada 
            ORDER BY rs.b DESC 
            LIMIT 3";

$result_b = mysqli_query($con, $query_b);
$data_b = [];

while ($row = mysqli_fetch_array($result_b)) {
    $data_b[] = [
        count($data_b) + 1,
        $row['name_jgstats'],
        $row['b'],
        $row['name_team']
    ];
}

$pdf->AddTable(
    ['POS', 'NOMBRE', 'B', 'EQUIPO'],
    $data_b,
    [12, 60, 12, 45]
);

// =============================================================================
// 3. LÍDERES EN BATEO Y JONRONES (de lidvbhr.php)
// =============================================================================
$pdf->SectionTitle('LÍDERES EN BATEO');

// VALOR CONFIGURABLE PARA CAMPEONES DE BATEO
$valor_campeon_bateo = 20; // <-- VALOR MODIFICABLE PARA CAMPEONES DE BATEO

$query_avg = "SELECT rs.name_jgstats, rs.tvb, rs.th, rs.avg, tc.name_team 
              FROM resumen_stats rs 
              INNER JOIN tab_clasf tc ON rs.id_team = tc.id_team 
              WHERE rs.id_temp = $temporada 
              ORDER BY (rs.tvb >= $valor_campeon_bateo) DESC, rs.avg DESC, rs.cb DESC 
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

$pdf->AddTable(
    ['POS', 'NOMBRE', 'VB', 'H', 'AVG', 'EQUIPO'],
    $data_avg,
    [10, 45, 10, 10, 15, 40]
);

// Líderes en jonrones
$pdf->SectionTitle('LÍDERES EN JONRONES');

$query_hr = "SELECT rs.name_jgstats, rs.hr, tc.name_team 
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

$pdf->AddTable(
    ['POS', 'NOMBRE', 'HR', 'EQUIPO'],
    $data_hr,
    [12, 60, 12, 45]
);

// =============================================================================
// 4. LÍDERES PICHERS (de pichers.php)
// =============================================================================
$pdf->SectionTitle('PICHERS GANADORES');

$query_pichers_gan = "SELECT rl.name_jglz, rl.tjl, rl.tjg, 
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

$pdf->AddTable(
    ['POS', 'NOMBRE', 'JL', 'JG', 'JP', 'AVG', 'EQUIPO'],
    $data_pichers_gan,
    [10, 40, 10, 10, 10, 15, 35]
);

// Picher efectividad
$pdf->SectionTitle('PICHERS POR EFECTIVIDAD');

$query_pichers_efec = "SELECT rl.name_jglz, rl.til, rl.tcpl, rl.efec, tc.name_team 
                       FROM resumen_lanz rl 
                       INNER JOIN tab_clasf tc ON rl.id_team = tc.id_team 
                       WHERE rl.id_temp = $temporada 
                       ORDER BY (rl.til >= $valor_pichers) DESC, rl.efec ASC 
                       LIMIT 3";

$result_pichers_efec = mysqli_query($con, $query_pichers_efec);
$data_pichers_efec = [];

while ($row = mysqli_fetch_array($result_pichers_efec)) {
    $data_pichers_efec[] = [
        count($data_pichers_efec) + 1,
        $row['name_jglz'],
        $row['til'],
        $row['tcpl'],
        $row['efec'],
        $row['name_team']
    ];
}

$pdf->AddTable(
    ['POS', 'NOMBRE', 'IL', 'CPL', 'EFEC', 'EQUIPO'],
    $data_pichers_efec,
    [10, 40, 10, 10, 15, 35]
);

// =============================================================================
// 5. LÍDERES EN DOBLES Y TRIPLES (de lid2b3b.php)
// =============================================================================
$pdf->SectionTitle('LÍDERES EN DOBLES');

$query_2b = "SELECT rs.name_jgstats, rs.2b, tc.name_team 
             FROM resumen_stats rs 
             INNER JOIN tab_clasf tc ON rs.id_team = tc.id_team 
             WHERE rs.id_temp = $temporada 
             ORDER BY rs.2b DESC 
             LIMIT 3";

$result_2b = mysqli_query($con, $query_2b);
$data_2b = [];

while ($row = mysqli_fetch_array($result_2b)) {
    $data_2b[] = [
        count($data_2b) + 1,
        $row['name_jgstats'],
        $row['2b'],
        $row['name_team']
    ];
}

$pdf->AddTable(
    ['POS', 'NOMBRE', '2B', 'EQUIPO'],
    $data_2b,
    [12, 60, 12, 45]
);

// Líderes en triples
$pdf->SectionTitle('LÍDERES EN TRIPLES');

$query_3b = "SELECT rs.name_jgstats, rs.3b, tc.name_team 
             FROM resumen_stats rs 
             INNER JOIN tab_clasf tc ON rs.id_team = tc.id_team 
             WHERE rs.id_temp = $temporada 
             ORDER BY rs.3b DESC 
             LIMIT 3";

$result_3b = mysqli_query($con, $query_3b);
$data_3b = [];

while ($row = mysqli_fetch_array($result_3b)) {
    $data_3b[] = [
        count($data_3b) + 1,
        $row['name_jgstats'],
        $row['3b'],
        $row['name_team']
    ];
}

$pdf->AddTable(
    ['POS', 'NOMBRE', '3B', 'EQUIPO'],
    $data_3b,
    [12, 60, 12, 45]
);

// =============================================================================
// GENERAR EL PDF
// =============================================================================
$pdf->Output();
?>