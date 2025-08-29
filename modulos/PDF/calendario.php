<?php
//Inclusión de Archivo Necesario y Conexión
require('vendor/fpdf/fpdf.php');
require('conexion.php');
$con=conectar();
$fecha = $_REQUEST['fecha'];
$trg=$fecha;
$entero_trg = strtotime($trg);
$ano_trg = date("Y", $entero_trg);
$mes_trg = date("m", $entero_trg);
$dia_trg = date("d", $entero_trg);
$desde_reorder=$dia_trg.'-'.$mes_trg.'-'.$ano_trg;

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
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);


// config document
$pdf->SetTitle('CALENDARIO - '.$desde_reorder);
$pdf->SetAuthor('Arturo');
$pdf->SetCreator('FPDF Maker');


$pdf->Image('../../fondos/pulpo (2).png', 10, 10, 16);
$pdf->Image('../../fondos/pulpov (2).png', 189, 10, 16);
$pdf->Cell(0,5,utf8_decode(strtoupper('LIGA RECREATIVA SOFTBALL EDUCADORES DEL ESTADO ARAGUA')),0,1,'C');
$pdf->Ln(2);
$pdf->Cell(0,5,utf8_decode(strtoupper('CALENDARIO')),0,1,'C');
$pdf->Ln(2);
$pdf->Cell(0,5,utf8_decode(strtoupper('SÁBADO '.$desde_reorder)),0,1,'C');
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,5,'FECHA: '.$timeday,0,1,'L');
$pdf->SetFont('Arial','B',14);
$pdf->Ln(4);
$pdf->Cell(0,5,utf8_decode(strtoupper('Campo N° 1 ')),0,1,'C');
$pdf->Ln(2);



$pdf->SetFont('Arial','B',10);
$pdf->Cell(25,6,utf8_decode(strtoupper('categoria')),1,0,'C');
$pdf->Cell(20,6,utf8_decode(strtoupper('día')),1,0,'C');
$pdf->Cell(21,6,utf8_decode(strtoupper('hora')),1,0,'C');
$pdf->Cell(50,6,utf8_decode(strtoupper('Equipo')),1,0,'C');
$pdf->Cell(8,6,utf8_decode(strtoupper('vs')),1,0,'C');
$pdf->Cell(50,6,utf8_decode(strtoupper('Equipo')),1,0,'C');
$pdf->Cell(25,6,utf8_decode(strtoupper('Campo')),1,1,'C');

$val   = "SELECT * FROM calendario WHERE campo = 1 AND fecha LIKE '%$fecha%' ORDER BY categoria DESC, STR_TO_DATE(hora, '%h:%i %p');";
$query = mysqli_query($con, $val);
$num = mysqli_num_rows($query);
if ($num >= 1) {
    for ($i = 1; $i <= $num ; ++$i) {
        $obt = mysqli_fetch_array($query);

$pdf->SetFont('Arial','B',9);
$pdf->Cell(25,6,utf8_decode(strtoupper($obt['categoria'])),1,0,'C');
$pdf->Cell(20,6,utf8_decode(strtoupper('SÁBADO')),1,0,'C');
$pdf->Cell(21,6,utf8_decode(strtoupper($obt['hora'])),1,0,'C');
$pdf->Cell(50,6,utf8_decode(strtoupper($obt['name_team_one'])),1,0,'C');
$pdf->Cell(8,6,utf8_decode(strtoupper('vs')),1,0,'C');
$pdf->Cell(50,6,utf8_decode(strtoupper($obt['name_team_two'])),1,0,'C');
$pdf->Cell(25,6,utf8_decode(strtoupper('N° '.$obt['campo'])),1,1,'C');

    }
}

$pdf->SetFont('Arial','B',14);
$pdf->Ln(7);
$pdf->Cell(0,5,utf8_decode(strtoupper('Campo N° 2 ')),0,1,'C');
$pdf->Ln(2);

$pdf->SetFont('Arial','B',10);
$pdf->Cell(25,6,utf8_decode(strtoupper('categoria')),1,0,'C');
$pdf->Cell(20,6,utf8_decode(strtoupper('día')),1,0,'C');
$pdf->Cell(21,6,utf8_decode(strtoupper('hora')),1,0,'C');
$pdf->Cell(50,6,utf8_decode(strtoupper('Equipo')),1,0,'C');
$pdf->Cell(8,6,utf8_decode(strtoupper('vs')),1,0,'C');
$pdf->Cell(50,6,utf8_decode(strtoupper('Equipo')),1,0,'C');
$pdf->Cell(25,6,utf8_decode(strtoupper('Campo')),1,1,'C');

$vald   = "SELECT * FROM calendario WHERE campo = 2 AND fecha LIKE '%$fecha%' ORDER BY categoria DESC, STR_TO_DATE(hora, '%h:%i %p');";
$queryd = mysqli_query($con, $vald);
$numd = mysqli_num_rows($queryd);
if ($numd >= 1) {
    for ($r = 1; $r <= $numd ; ++$r) {
        $obtt = mysqli_fetch_array($queryd);

$pdf->SetFont('Arial','B',9);
$pdf->Cell(25,6,utf8_decode(strtoupper($obtt['categoria'])),1,0,'C');
$pdf->Cell(20,6,utf8_decode(strtoupper('SÁBADO')),1,0,'C');
$pdf->Cell(21,6,utf8_decode(strtoupper($obtt['hora'])),1,0,'C');
$pdf->Cell(50,6,utf8_decode(strtoupper($obtt['name_team_one'])),1,0,'C');
$pdf->Cell(8,6,utf8_decode(strtoupper('vs')),1,0,'C');
$pdf->Cell(50,6,utf8_decode(strtoupper($obtt['name_team_two'])),1,0,'C');
$pdf->Cell(25,6,utf8_decode(strtoupper('N° '.$obtt['campo'])),1,1,'C');

    }
}

$pdf->Output();
?>