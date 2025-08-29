<?php
//Inclusión de Archivo Necesario y Conexión
require('vendor/fpdf/fpdf.php');
require('conexion.php');
$con=conectar();
$id_temp  = $_REQUEST['id_temp'];
$val_hor   = "SELECT * FROM temporada WHERE id_temp = $id_temp";
$query_hor = mysqli_query($con, $val_hor);
$obtenerho = mysqli_fetch_array($query_hor);

$id  = $obtenerho['id_temp'];
$cat = $obtenerho['categoria'];


$revisar = "SELECT temporada.*, tab_clasf.* FROM temporada INNER JOIN tab_clasf ON temporada.id_temp = tab_clasf.id_temp WHERE tab_clasf.id_temp = $id AND temporada.categoria LIKE '%$cat%' ";
$query   = mysqli_query($con,$revisar);
$data    = mysqli_fetch_array($query);

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
$pdf = new FPDF('L','mm','Letter');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',14);


// config document
$pdf->SetTitle('Tabla Clasificatoria - DIF - Categoria "'.$cat.'"');
$pdf->SetAuthor('Arturo');
$pdf->SetCreator('FPDF Maker');


$pdf->Cell(0,5,utf8_decode(strtoupper('LIGA RECREATIVA SOFTBALL EDUCADORES DEL ESTADO ARAGUA')),0,1,'C');
$pdf->Ln(5);
$pdf->Cell(0,5,utf8_decode(strtoupper('TABLA DE CLASIFICACIÓN CATEGORIA " '.$cat.' "')),0,1,'C');
$pdf->Ln(5);
$pdf->Cell(0,5,utf8_decode('En la temporada '.$data['nam_temp']. ' damos en honor a '.$_REQUEST['persona']),0,1,'C');
$pdf->Ln(10);



$pdf->SetFont('Arial','B',14);
$pdf->Cell(10);
$pdf->Cell(11,8,utf8_decode(strtoupper('POS')),1,0,'C');
$pdf->Cell(70,8,utf8_decode(strtoupper('Equipo')),1,0,'C');
$pdf->Cell(20,8,utf8_decode(strtoupper('JJ')),1,0,'C');
$pdf->Cell(20,8,utf8_decode(strtoupper('JG')),1,0,'C');
$pdf->Cell(20,8,utf8_decode(strtoupper('JP')),1,0,'C');
$pdf->Cell(20,8,utf8_decode(strtoupper('JE')),1,0,'C');
$pdf->Cell(20,8,utf8_decode(strtoupper('AVG')),1,0,'C');
$pdf->Cell(20,8,utf8_decode(strtoupper('CA')),1,0,'C');
$pdf->Cell(20,8,utf8_decode(strtoupper('CE')),1,0,'C');
$pdf->Cell(20,8,utf8_decode(strtoupper('DIF')),1,1,'C');

$val   = "SELECT * FROM tab_clasf WHERE id_temp = $id AND categoria LIKE '%$cat%' ORDER BY dif ASC";
$query = mysqli_query($con, $val);
$num = mysqli_num_rows($query);
if ($num >= 1) {
    for ($i = 1; $i <= $num ; ++$i) {
        $obt = mysqli_fetch_array($query);

$pdf->SetFont('Arial','',11);
$pdf->Cell(10);
$pdf->Cell(11,8,utf8_decode(strtoupper($i)),1,0,'C');
$pdf->Cell(70,8,utf8_decode(strtoupper($obt['name_team'])),1,0,'C');
$pdf->Cell(20,8,utf8_decode(strtoupper($obt['jj'])),1,0,'C');
$pdf->Cell(20,8,utf8_decode(strtoupper($obt['jg'])),1,0,'C');
$pdf->Cell(20,8,utf8_decode(strtoupper($obt['jp'])),1,0,'C');
$pdf->Cell(20,8,utf8_decode(strtoupper($obt['je'])),1,0,'C');
$pdf->Cell(20,8,utf8_decode(strtoupper($obt['avg'])),1,0,'C');
$pdf->Cell(20,8,utf8_decode(strtoupper($obt['ca'])),1,0,'C');
$pdf->Cell(20,8,utf8_decode(strtoupper($obt['ce'])),1,0,'C');
$pdf->Cell(20,8,utf8_decode(strtoupper($obt['dif'])),1,1,'C');

    }
}

$pdf->Output();
?>