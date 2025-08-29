<?php
// Inclusión de Archivo Necesario y Conexión
require('vendor/fpdf/fpdf.php');
require('conexion.php');
$con = conectar();

// Obtener fecha formateada
$verificar = mysqli_query($con, "SELECT * FROM report");
$vdta = mysqli_fetch_array($verificar);
$vfecha = $vdta['timeday'];
$entero_vtt = strtotime($vfecha);
$ano_vtt = date("Y", $entero_vtt);
$mes_vtt = date("m", $entero_vtt);
$dia_vtt = date("d", $entero_vtt);
$timeday = $dia_vtt . '-' . $mes_vtt . '-' . $ano_vtt;

// Clase extendida de FPDF para cabecera y pie de página
class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 15);
    }
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Page ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetTitle('Tabla Clasificatoria - AVG - Todos');
$pdf->SetAuthor('Arturo');
$pdf->SetCreator('FPDF Maker');

$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Image('../../fondos/pulpo (2).png', 10, 10, 16);
$pdf->Image('../../fondos/pulpov (2).png', 189, 10, 16);
$pdf->Cell(0, 5, utf8_decode(strtoupper('LIGA RECREATIVA SOFTBALL EDUCADORES DEL ESTADO ARAGUA')), 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(17);
$pdf->Cell(0, 5, 'FECHA: ' . $timeday, 0, 1, 'L');
$pdf->SetFont('Arial', 'B', 14);
$pdf->Ln(4);

// Función auxiliar para imprimir columnas verticales "champion"
function printChampionColumn($pdf, $x, $yStart, $champions)
{
    $lineHeight = 4;  // altura fija
    $y = $yStart;

    foreach ($champions as $champ) {
        if ($champ['cant'] > 0) {
            $pdf->SetXY($x, $y);
            $pdf->Cell($champ['widthLabel'], $lineHeight, utf8_decode(strtoupper($champ['label'])), 1, 0, 'L');
            $pdf->Cell($champ['widthCant'], $lineHeight, $champ['cant'] . ' $', 1, 1, 'C'); // salto línea aquí
            $y += $lineHeight;
        }
    }

    return $y; // última posición Y impresa
}

// Función para imprimir toda la sección de clasificación y premiación por categoría
function imprimirCategoria($con, $pdf, $categoria)
{
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 5, utf8_decode(strtoupper('TABLA DE CLASIFICACIÓN CATEGORIA "' . $categoria . '"')), 0, 1, 'C');

    $val_hor = "SELECT * FROM temporada WHERE activo = 1 AND categoria LIKE '%$categoria%'";
    $query_hor = mysqli_query($con, $val_hor);
    $numsbr = mysqli_num_rows($query_hor);

    for ($e = 1; $e <= $numsbr; ++$e) {
        $obtenerho = mysqli_fetch_array($query_hor);

        $id = $obtenerho['id_temp'];
        $cat = $obtenerho['categoria'];

        $revisar = "SELECT temporada.*, tab_clasf.* FROM temporada INNER JOIN tab_clasf ON temporada.id_temp = tab_clasf.id_temp WHERE tab_clasf.id_temp = $id AND temporada.categoria LIKE '%$cat%'";
        $query = mysqli_query($con, $revisar);
        $data = mysqli_fetch_array($query);
        $idtemp = $data['id_temp'];

        $busc = "SELECT * FROM homenaje WHERE id_temp = $idtemp";
        $quer = mysqli_query($con, $busc);
        $ftdp = mysqli_fetch_array($quer);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->MultiCell(0, 5, utf8_decode(strtoupper('En la temporada ' . $data['name_temp'] . ' damos en honor a ' . $ftdp['honor'])), 0, 'C');
        $pdf->Ln(1);

        // Cabecera Tabla Clasificación
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(10);
        $pdf->Cell(11, 4, utf8_decode(strtoupper('POS')), 1, 0, 'C');
        $pdf->Cell(45, 4, utf8_decode(strtoupper('Equipo')), 1, 0, 'C'); // Reducido de 50 a 45
        $pdf->Cell(12, 4, utf8_decode(strtoupper('JJ')), 1, 0, 'C'); // Reducido de 15 a 12
        $pdf->Cell(12, 4, utf8_decode(strtoupper('JG')), 1, 0, 'C'); // Reducido de 15 a 12
        $pdf->Cell(12, 4, utf8_decode(strtoupper('JP')), 1, 0, 'C'); // Reducido de 15 a 12
        $pdf->Cell(12, 4, utf8_decode(strtoupper('JE')), 1, 0, 'C'); // Reducido de 15 a 12
        $pdf->Cell(12, 4, utf8_decode(strtoupper('AVG')), 1, 0, 'C'); // Reducido de 15 a 12
        $pdf->Cell(12, 4, utf8_decode(strtoupper('CA')), 1, 0, 'C'); // Reducido de 15 a 12
        $pdf->Cell(12, 4, utf8_decode(strtoupper('CE')), 1, 0, 'C'); // Reducido de 15 a 12
        $pdf->Cell(12, 4, utf8_decode(strtoupper('DIF')), 1, 0, 'C'); // Reducido de 15 a 12
        $pdf->Cell(15, 4, utf8_decode(strtoupper('ESTADO')), 1, 1, 'C'); // Nueva columna

        $val = "SELECT t.*, e.estado 
                FROM tab_clasf t 
                LEFT JOIN equipo_estados e ON t.id_tab = e.id_tab AND t.id_temp = e.id_temp
                WHERE t.id_temp = $id AND t.categoria LIKE '%$cat%' 
                ORDER BY t.avg DESC, t.dif DESC";
        $query_tab = mysqli_query($con, $val);
        $num = mysqli_num_rows($query_tab);

        if ($num >= 1) {
            $pdf->SetFont('Arial', '', 9);
            for ($i = 1; $i <= $num; ++$i) {
                $obt = mysqli_fetch_array($query_tab);
                
                // Determinar texto del estado
                $estado = '';
                if ($obt['estado'] == 'C') {
                    $estado = 'C';
                } elseif ($obt['estado'] == 'E') {
                    $estado = 'E';
                } elseif ($obt['estado'] == 'R') {
                    $estado = 'R';
                } elseif ($obt['estado'] == 'G') {
                    $estado = 'CHAMP';
                } else {
                    $estado = '-';
                }

                $pdf->Cell(10);
                $pdf->Cell(11, 4, utf8_decode(strtoupper($i)), 1, 0, 'C');
                $pdf->Cell(45, 4, utf8_decode(strtoupper($obt['name_team'])), 1, 0, 'C');
                $pdf->Cell(12, 4, utf8_decode(strtoupper($obt['jj'])), 1, 0, 'C');
                $pdf->Cell(12, 4, utf8_decode(strtoupper($obt['jg'])), 1, 0, 'C');
                $pdf->Cell(12, 4, utf8_decode(strtoupper($obt['jp'])), 1, 0, 'C');
                $pdf->Cell(12, 4, utf8_decode(strtoupper($obt['je'])), 1, 0, 'C');
                $pdf->Cell(12, 4, utf8_decode(strtoupper($obt['avg'])), 1, 0, 'C');
                $pdf->Cell(12, 4, utf8_decode(strtoupper($obt['ca'])), 1, 0, 'C');
                $pdf->Cell(12, 4, utf8_decode(strtoupper($obt['ce'])), 1, 0, 'C');
                $pdf->Cell(12, 4, utf8_decode(strtoupper($obt['dif'])), 1, 0, 'C');
                $pdf->Cell(15, 4, utf8_decode(strtoupper($estado)), 1, 1, 'C'); // Nueva columna
            }
        }

        $pdf->Ln(3);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(50, 4, utf8_decode('PREMIACIÓN'), 0, 0, 'C');
        $pdf->Cell(12, 4, '', 0, 0, 'C');
        $pdf->Cell(18);
        $pdf->Cell(50, 4, utf8_decode('PREMIACIÓN'), 0, 0, 'C');
        $pdf->Cell(30, 4, '', 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 8);

        // Definir posiciones X de las dos columnas y línea base
        $xLeft = 65;
        $xRight = $xLeft + 60; // 60 = ancho columna izquierda 50 + 10 cantidad, 5 de espacio extra
        $lineHeight = 4;
        $yStart = $pdf->GetY();

        // Array fijo para imprimir títulos reservando espacio aunque estén vacíos
        $titles = [
            ['key' => 'prize_once',   'label' => '1ER LUGAR', 'cant_key' => 'cant_once'],
            ['key' => 'prize_second', 'label' => '2DO LUGAR', 'cant_key' => 'cant_second'],
            ['key' => 'prize_third',  'label' => '3ER LUGAR', 'cant_key' => 'cant_third'],
            ['key' => 'prize_four',   'label' => '4TO LUGAR', 'cant_key' => 'cant_four'],
        ];

        // Imprimir títulos dejando espacio fijo (vacío si no hay datos)
        $yTemp = $yStart;
        foreach ($titles as $title) {
            $pdf->SetXY(10, $yTemp);
            if (!empty($ftdp[$title['key']])) {
                $pdf->Cell(30, $lineHeight, utf8_decode(strtoupper($title['label'])), 1, 0, 'C');
                $pdf->Cell(10, $lineHeight, $ftdp[$title['cant_key']] . ' $', 1, 0, 'C');
            } else {
                $pdf->Cell(30, $lineHeight, '', 0, 0, 'C');
                $pdf->Cell(10, $lineHeight, '', 0, 0, 'C'); 
            }
            $yTemp += $lineHeight;
        }

        // Definir arrays de champions para columna izquierda
        $championsLeft = [
            ['label' => 'champion Bate',          'cant' => (int)$ftdp['cant_lbt'], 'widthLabel' => 30, 'widthCant' => 30],
            ['label' => 'champion HR',      'cant' => (int)$ftdp['cant_lj'],  'widthLabel' => 30, 'widthCant' => 30],
            ['label' => 'champion CE',     'cant' => (int)$ftdp['cant_lce'], 'widthLabel' => 30, 'widthCant' => 30],
            ['label' => 'champion PG','cant' => (int)$ftdp['cant_pg'],  'widthLabel' => 30, 'widthCant' => 30],
            ['label' => 'champion BB',        'cant' => (int)$ftdp['cant_lb'],  'widthLabel' => 30, 'widthCant' => 30],
        ];

        // Columnas derecha
        $championsRight = [
            ['label' => 'champion 2B',           'cant' => (int)$ftdp['cant_ld'],  'widthLabel' => 30, 'widthCant' => 30],
            ['label' => 'champion 3B',          'cant' => (int)$ftdp['cant_lt'],  'widthLabel' => 30, 'widthCant' => 30],
            ['label' => 'champion CA',         'cant' => (int)$ftdp['cant_lca'], 'widthLabel' => 30, 'widthCant' => 30],
            ['label' => 'champion PE','cant' => (int)$ftdp['cant_pe'], 'widthLabel' => 30, 'widthCant' => 30],
            ['label' => 'champion K',          'cant' => (int)$ftdp['cant_lp'],  'widthLabel' => 30, 'widthCant' => 30],
        ];

        // Imprimir columnas verticales alineadas iniciando en la misma Y base
        $yLeftEnd  = printChampionColumn($pdf, $xLeft,   $yStart, $championsLeft);
        $yRightEnd = printChampionColumn($pdf, $xRight,  $yStart, $championsRight);

        // Ajustar cursor debajo del contenido más bajo y con espacio extra
        $pdf->SetY(max($yTemp, $yLeftEnd, $yRightEnd) + 5);
    }
}

// Llamadas para imprimir categorías D, C y B con saltos
imprimirCategoria($con, $pdf, 'D');
$pdf->Ln(8);
imprimirCategoria($con, $pdf, 'C');
$pdf->Ln(8);
imprimirCategoria($con, $pdf, 'B');

$pdf->Output();
?>