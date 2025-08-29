<?php
//Inclusión de Archivo Necesario y Conexión
require('vendor/fpdf/fpdf.php');
require('conexion.php');
$con=conectar();


$verificar = mysqli_query($con, "SELECT * FROM report");
$vdta = mysqli_fetch_array($verificar);
$vfecha = $vdta['timeday'];
$vtt=$vfecha;
$entero_vtt = strtotime($vtt);
$ano_vtt = date("Y", $entero_vtt);
$mes_vtt = date("m", $entero_vtt);
$dia_vtt = date("d", $entero_vtt);
$timeday=$dia_vtt.'-'.$mes_vtt.'-'.$ano_vtt;


//Clases de Cabezera y Pie de Pagina
class PDF extends FPDF
{
    // Cabecera de página
    function Header()
        {
            // Arial bold 15
            $this->SetFont('Arial','B',15);
        }
    // Pie de página
    function Footer()
        {
            // Posición: a 1,5 cm del final
            $this->SetY(-15);
            // Arial italic 8
            $this->SetFont('Arial','I',8);
            // Número de página
            $this->Cell(0,10,utf8_decode('Page ').$this->PageNo().'/{nb}',0,0,'C');
        }
}

// Creación del objeto de la clase heredada
$pdf = new FPDF('P','mm','Letter');
$pdf->AliasNbPages();


// config document
$pdf->SetTitle('ABONO');
$pdf->SetAuthor('Arturo');
$pdf->SetCreator('FPDF Maker');


$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,5,utf8_decode(strtoupper('LIGA RECREATIVA SOFTBALL EDUCADORES DEL ESTADO ARAGUA')),0,1,'C');
$pdf->Cell(0,5,utf8_decode(strtoupper('RELACION DE PAGO DE LOS EQUIPOS ')),0,1,'C');
$pdf->Ln(4);


$id  = $_REQUEST['temporada'];
$cat = $_REQUEST['categoria'];

$revisar = "SELECT * FROM abonos WHERE id_temp = $id AND categoria LIKE '%$cat%';";
$query   = mysqli_query($con,$revisar);
$data    = mysqli_fetch_array($query);
$nabono  = $data['ncantidad'];
$idbono  = $data['id_abn'];
$four    = $data['prize_four'];

$pdf->SetFont('Arial','B',9);


$pdf->Cell(10);
$pdf->Cell(35,5,utf8_decode(strtoupper('Equipo')),1,0, 'C');
    for ($i = 1; $i <= $nabono; $i++) { 
$pdf->Cell(13,5,utf8_decode(strtoupper('AB')),1,0, 'C');
    }
$pdf->Cell(20,5,utf8_decode(strtoupper('TOTAL')),1,1, 'C');

    $revisar = "SELECT * FROM tab_clasf WHERE id_temp = $id AND categoria LIKE '%$cat%'";
    $ryque = mysqli_query($con, $revisar);
    $nunum = mysqli_num_rows($ryque);

    if ($nunum >= 1) {
        for ($t = 1; $t <= $nunum; $t++) { 
            $bdata = mysqli_fetch_array($ryque);
            $nmmm = $bdata['name_team'];
            $nttm = $bdata['id_team'];

$pdf->Cell(10);
$pdf->Cell(35,5,utf8_decode(strtoupper($nmmm)),1,0, 'C');
    $suma_montos = 0; // Inicializamos acumulador
    for ($j = 1; $j <= $nabono; $j++) { 
        $njn = "SELECT monto FROM monto WHERE id_abn = $idbono AND id_team = $nttm AND numero = $j";
        $qtt = mysqli_query($con, $njn);
        $dop = mysqli_fetch_array($qtt);
        $monto_actual = isset($dop['monto']) ? $dop['monto'] : 0;
            $suma_montos += $monto_actual;
$pdf->Cell(13,5,$monto_actual,1,0, 'C');
    }
$pdf->Cell(20,5,$suma_montos,1,1, 'C');
    } 
}


if ($nabono > 3) {
    $trrr = $nabono - 3;
    $vacio = $trrr * 13;
    $nada = 1;

 } elseif ($nabono == 3) {
    $trrr = $nabono - 3;
    $nada = 0;
    $vacio = 0;

 } 


 $pdf->Cell(10);
$pdf->Cell(35,5,utf8_decode(strtoupper('1er Lugar')),1,0, 'C');
$pdf->Cell(26,5,'',1,0, 'C');


if (empty($nada)) {
    
} else {
$pdf->Cell($vacio,5,'',0,0, 'C');
}

$pdf->Cell(13,5,utf8_decode(strtoupper('Total')),1,0, 'C');

$totalmente = "SELECT SUM(monto) AS total_final FROM monto
WHERE id_temp = $id AND categoria LIKE '%$cat%' AND numero <= $nabono";
$trata = mysqli_query($con, $totalmente);
$datatol = mysqli_fetch_array($trata);
$vvv = $datatol['total_final'] + 0;
$pdf->Cell(20,5,$vvv,1,1, 'C');

$pdf->Cell(10);
$pdf->Cell(35,5,utf8_decode(strtoupper('2do Lugar')),1,0, 'C');
$pdf->Cell(26,5,'',1,1, 'C');

$pdf->Cell(10);
$pdf->Cell(35,5,utf8_decode(strtoupper('3er Lugar')),1,0, 'C');
$pdf->Cell(26,5,'',1,1, 'C');

if (empty($four)) { } elseif (!empty($four)) {

$pdf->Cell(10);
$pdf->Cell(35,5,utf8_decode(strtoupper('4to Lugar')),1,0, 'C');
$pdf->Cell(26,5,'',1,1, 'C');
}                         



$pdf->Output();
?>