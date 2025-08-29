<?php
//Inclusión de Archivo Necesario y Conexión
require('vendor/fpdf/fpdf.php');
require('conexion.php');
$con=conectar();
$equipo    = $_POST['equipo'];
$temporada = $_POST['temporada'];
$categoria = $_POST['categoria'];


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
$pdf->SetTitle(utf8_decode('Líderes en Carreras Anotadas | En Carreras Empujadas - Categoria '.$categoria));
$pdf->SetAuthor('Arturo');
$pdf->SetCreator('FPDF Maker');


if ($equipo < 1) {
        

$pdf->AddPage();
$pdf->SetFont('Arial','',14);

$pdf->Image('../../fondos/pulpo (2).png', 10, 10, 16);
$pdf->Image('../../fondos/pulpov (2).png', 189, 10, 16);
$pdf->Cell(0,5,utf8_decode(strtoupper('LIGA RECREATIVA SOFTBALL EDUCADORES DEL ESTADO ARAGUA')),0,1,'C');
$pdf->Ln(2);
$pdf->Cell(0,5,utf8_decode(strtoupper('CATEGORIA " '.$categoria.' "')),0,1,'C');
$pdf->Ln(2);
$pdf->Cell(0,5,utf8_decode('LÍDERES EN CARRERAS ANOTADAS'),0,1,'C');

$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,5,'FECHA: '.$timeday,0,1,'L');
$pdf->SetFont('Arial','B',14);
$pdf->Ln(2);



$pdf->SetFont('Arial','B',12);

$pdf->Cell(10);
$pdf->Cell(10,6.5,utf8_decode(strtoupper('pos')),1,0,'C');
$pdf->Cell(85,6.5,utf8_decode(strtoupper('Nombre')),1,0,'C');
$pdf->Cell(15,6.5,utf8_decode(strtoupper('ca')),1,0,'C');
$pdf->Cell(70,6.5,utf8_decode(strtoupper('Equipo')),1,1,'C');


$idtp = "SELECT * FROM temporada WHERE id_temp = $temporada";
$vatp = mysqli_query($con, $idtp);
$dtpp = mysqli_fetch_array($vatp);

$partidos = $dtpp['valor'];
    
$covl = "SELECT * FROM resumen_stats WHERE id_temp = $temporada ORDER BY ca DESC";
$vais = mysqli_query($con, $covl);
$asnm = mysqli_num_rows($vais);


if ($asnm >= 1) {
for ($jg=1; $jg <= 15 ; $jg++) { 
$player = mysqli_fetch_array($vais);
$id_team = $player['id_team'];

$tjg = $player['tjg'];
$tjp = $partidos - $tjg;

$pdf->SetFont('Arial','B',9);
$pdf->Cell(10);
$pdf->Cell(10,6.5,utf8_decode(strtoupper($jg)),1,0,'C');
$pdf->Cell(85,6.5,utf8_decode($player['name_jgstats']),1,0,'C');
$pdf->Cell(15,6.5,utf8_decode($player['ca']),1,0,'C');

$tabla = "SELECT * FROM tab_clasf WHERE id_team = $id_team";
$dteg = mysqli_query($con, $tabla);
$datu = mysqli_fetch_array($dteg);

$pdf->Cell(70,6.5,utf8_decode(strtoupper($datu['name_team'])),1,1,'C');

} } 


/////////////////////////////////////////// CARRERAS EMPUJADAS
$pdf->Ln(8);
$pdf->SetFont('Arial','',14);
$pdf->Cell(0,5,utf8_decode('LÍDERES EN CARRERAS EMPUJADAS'),0,1,'C');
$pdf->Ln(2);



$pdf->SetFont('Arial','B',12);

$pdf->Cell(10);
$pdf->Cell(10,6.5,utf8_decode(strtoupper('pos')),1,0,'C');
$pdf->Cell(85,6.5,utf8_decode(strtoupper('Nombre')),1,0,'C');
$pdf->Cell(15,6.5,utf8_decode(strtoupper('ci')),1,0,'C');
$pdf->Cell(70,6.5,utf8_decode(strtoupper('Equipo')),1,1,'C');


$idtp = "SELECT * FROM temporada WHERE id_temp = $temporada";
$vatp = mysqli_query($con, $idtp);
$dtpp = mysqli_fetch_array($vatp);

    
$covl = "SELECT * FROM resumen_stats WHERE id_temp = $temporada ORDER BY ci DESC";
$vais = mysqli_query($con, $covl);
$asnm = mysqli_num_rows($vais);


if ($asnm >= 1) {
for ($jg=1; $jg <= 15 ; $jg++) { 
$player = mysqli_fetch_array($vais);
$id_team = $player['id_team'];

$pdf->SetFont('Arial','B',9);
$pdf->Cell(10);
$pdf->Cell(10,6.5,utf8_decode(strtoupper($jg)),1,0,'C');
$pdf->Cell(85,6.5,utf8_decode($player['name_jgstats']),1,0,'C');
$pdf->Cell(15,6.5,utf8_decode($player['ci']),1,0,'C');

$tabla = "SELECT * FROM tab_clasf WHERE id_team = $id_team";
$dteg = mysqli_query($con, $tabla);
$datu = mysqli_fetch_array($dteg);

$pdf->Cell(70,6.5,utf8_decode(strtoupper($datu['name_team'])),1,1,'C');

} } 

} elseif ($equipo >= 1) {

$pdf->AddPage();
$pdf->SetFont('Arial','',14);

$pdf->Image('../../fondos/pulpo (2).png', 10, 10, 16);
$pdf->Image('../../fondos/pulpov (2).png', 189, 10, 16);
$pdf->Cell(0,5,utf8_decode(strtoupper('LIGA RECREATIVA SOFTBALL EDUCADORES DEL ESTADO ARAGUA')),0,1,'C');
$pdf->Ln(2);
$pdf->Cell(0,5,utf8_decode(strtoupper('CATEGORIA " '.$categoria.' "')),0,1,'C');
$pdf->Ln(2);
$pdf->Cell(0,5,utf8_decode('LÍDERES EN CARRERAS ANOTADAS'),0,1,'C');

$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,5,'FECHA: '.$timeday,0,1,'L');
$pdf->SetFont('Arial','B',14);
$pdf->Ln(2);



$pdf->SetFont('Arial','B',12);

$pdf->Cell(10);
$pdf->Cell(10,6.5,utf8_decode(strtoupper('pos')),1,0,'C');
$pdf->Cell(85,6.5,utf8_decode(strtoupper('Nombre')),1,0,'C');
$pdf->Cell(15,6.5,utf8_decode(strtoupper('ca')),1,0,'C');
$pdf->Cell(70,6.5,utf8_decode(strtoupper('Equipo')),1,1,'C');

$idtp = "SELECT * FROM temporada WHERE id_temp = $temporada";
$vatp = mysqli_query($con, $idtp);
$dtpp = mysqli_fetch_array($vatp);

$partidos = $dtpp['partidas'];
    
$covl = "SELECT * FROM resumen_stats WHERE id_team = $equipo ORDER BY ca DESC";
$vais = mysqli_query($con, $covl);
$asnm = mysqli_num_rows($vais);


if ($asnm >= 1) {
for ($jg=1; $jg <= 15 ; $jg++) { 
$player = mysqli_fetch_array($vais);
$id_team = $player['id_team'];

$tjg = $player['tjg'];
$tjp = $partidos - $tjg;

$pdf->SetFont('Arial','B',9);
$pdf->Cell(10);
$pdf->Cell(10,6.5,utf8_decode(strtoupper($jg)),1,0,'C');
$pdf->Cell(85,6.5,utf8_decode($player['name_jgstats']),1,0,'C');
$pdf->Cell(15,6.5,utf8_decode($player['ca']),1,0,'C');

$tabla = "SELECT * FROM tab_clasf WHERE id_team = $id_team";
$dteg = mysqli_query($con, $tabla);
$datu = mysqli_fetch_array($dteg);

$pdf->Cell(70,6.5,utf8_decode(strtoupper($datu['name_team'])),1,1,'C');

} } 

/////////////////////////////////////////// CARRERAS EMPUJADAS
$pdf->Ln(8);
$pdf->SetFont('Arial','',14);
$pdf->Cell(0,5,utf8_decode('LÍDERES EN CARRERAS EMPUJADAS'),0,1,'C');
$pdf->Ln(2);



$pdf->SetFont('Arial','B',12);

$pdf->Cell(10);
$pdf->Cell(10,6.5,utf8_decode(strtoupper('pos')),1,0,'C');
$pdf->Cell(85,6.5,utf8_decode(strtoupper('Nombre')),1,0,'C');
$pdf->Cell(15,6.5,utf8_decode(strtoupper('ci')),1,0,'C');
$pdf->Cell(70,6.5,utf8_decode(strtoupper('Equipo')),1,1,'C');


$idtp = "SELECT * FROM temporada WHERE id_temp = $temporada";
$vatp = mysqli_query($con, $idtp);
$dtpp = mysqli_fetch_array($vatp);

    
$covl = "SELECT * FROM resumen_stats WHERE id_team = $equipo ORDER BY ci DESC";
$vais = mysqli_query($con, $covl);
$asnm = mysqli_num_rows($vais);


if ($asnm >= 1) {
for ($jg=1; $jg <= 15 ; $jg++) { 
$player = mysqli_fetch_array($vais);
$id_team = $player['id_team'];

$pdf->SetFont('Arial','B',9);
$pdf->Cell(10);
$pdf->Cell(10,6.5,utf8_decode(strtoupper($jg)),1,0,'C');
$pdf->Cell(85,6.5,utf8_decode($player['name_jgstats']),1,0,'C');
$pdf->Cell(15,6.5,utf8_decode($player['ci']),1,0,'C');

$tabla = "SELECT * FROM tab_clasf WHERE id_team = $id_team";
$dteg = mysqli_query($con, $tabla);
$datu = mysqli_fetch_array($dteg);

$pdf->Cell(70,6.5,utf8_decode(strtoupper($datu['name_team'])),1,1,'C');

} } 


}



$pdf->Output();
?>