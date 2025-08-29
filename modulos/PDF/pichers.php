<?php
//Inclusión de Archivo Necesario y Conexión
require('vendor/fpdf/fpdf.php');
require('conexion.php');
$con=conectar();
$equipo    = $_POST['equipo'];
$temporada = $_POST['temporada'];
$categoria = $_POST['categoria'];
$actual = $_POST['actual'];
$chmp = $actual;


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
$pdf->SetTitle('PICHERS - Categoria '.$categoria);
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
$pdf->Cell(0,5,utf8_decode(strtoupper('PICHER GANADOS')),0,1,'C');
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,5,'FECHA: '.$timeday,0,1,'L');
$pdf->SetFont('Arial','B',14);
$pdf->Ln(2);



$pdf->SetFont('Arial','B',12);

$pdf->Cell(12.5);
$pdf->Cell(10,6.5,utf8_decode(strtoupper('pos')),1,0,'C');
$pdf->Cell(60,6.5,utf8_decode(strtoupper('Nombre')),1,0,'C');
$pdf->Cell(10,6.5,utf8_decode(strtoupper('JL')),1,0,'C');
$pdf->Cell(10,6.5,utf8_decode(strtoupper('JG')),1,0,'C');
$pdf->Cell(10,6.5,utf8_decode(strtoupper('JP')),1,0,'C');
$pdf->Cell(15,6.5,utf8_decode(strtoupper('AVG')),1,0,'C');
$pdf->Cell(60,6.5,utf8_decode(strtoupper('Equipo')),1,1,'C');


$idtp = "SELECT * FROM temporada WHERE id_temp = $temporada";
$vatp = mysqli_query($con, $idtp);
$dtpp = mysqli_fetch_array($vatp);

$partidos = $dtpp['partidas'];
    
$covl = "SELECT * FROM resumen_lanz WHERE id_temp = $temporada ORDER BY tjg DESC";
$vais = mysqli_query($con, $covl);
$asnm = mysqli_num_rows($vais);


if ($asnm >= 1) {
for ($jg=1; $jg <= 15 ; $jg++) { 
$player = mysqli_fetch_array($vais);
$id_team = $player['id_team'];

$tjg = $player['tjg'];
$tjl = $player['tjl'];
$tjp = $tjl - $tjg;

$pdf->SetFont('Arial','B',9);
$pdf->Cell(12.5);
$pdf->Cell(10,6.5,utf8_decode(strtoupper($jg)),1,0,'C');
$pdf->Cell(60,6.5,utf8_decode($player['name_jglz']),1,0,'C');
$pdf->Cell(10,6.5,utf8_decode($player['tjl']),1,0,'C');
$pdf->Cell(10,6.5,utf8_decode($tjg),1,0,'C');
$pdf->Cell(10,6.5,utf8_decode($tjp),1,0,'C');
$pdf->Cell(15,6.5,utf8_decode($player['avg']),1,0,'C');

$tabla = "SELECT * FROM tab_clasf WHERE id_team = $id_team AND id_temp = $temporada";
$dteg = mysqli_query($con, $tabla);
$datu = mysqli_fetch_array($dteg);

$pdf->Cell(60,6.5,utf8_decode(strtoupper($datu['name_team'])),1,1,'C');

} } 


/////////////////////////////////////////// EFECTIVIDADTRIPLES
$pdf->Ln(8);
$pdf->SetFont('Arial','',14);
$pdf->Cell(0,5,utf8_decode(strtoupper('PICHER efectividad')),0,1,'C');
$pdf->Ln(2);

    
$pdf->SetFont('Arial','B',12);

$pdf->Cell(17);
$pdf->Cell(10,6.5,utf8_decode(strtoupper('pos')),1,0,'C');
$pdf->Cell(60,6.5,utf8_decode(strtoupper('Nombre')),1,0,'C');
$pdf->Cell(10,6.5,utf8_decode(strtoupper('IL')),1,0,'C');
$pdf->Cell(10,6.5,utf8_decode(strtoupper('CPL')),1,0,'C');
$pdf->Cell(15,6.5,utf8_decode(strtoupper('efec')),1,0,'C');
$pdf->Cell(60,6.5,utf8_decode(strtoupper('Equipo')),1,1,'C');


$idtp = "SELECT * FROM temporada WHERE id_temp = $temporada";
$vatp = mysqli_query($con, $idtp);
$dtpp = mysqli_fetch_array($vatp);

    
$covl = "SELECT * FROM resumen_lanz WHERE id_temp = $temporada ORDER BY (til >= $chmp) DESC, efec ASC";
$vais = mysqli_query($con, $covl);
$asnm = mysqli_num_rows($vais);


if ($asnm >= 1) {
for ($jg=1; $jg <= 15 ; $jg++) { 
$player = mysqli_fetch_array($vais);
$id_team = $player['id_team'];

$pdf->SetFont('Arial','B',9);
$pdf->Cell(17);
$pdf->Cell(10,6.5,utf8_decode(strtoupper($jg)),1,0,'C');
$pdf->Cell(60,6.5,utf8_decode($player['name_jglz']),1,0,'C');
$pdf->Cell(10,6.5,utf8_decode($player['til']),1,0,'C');
$pdf->Cell(10,6.5,utf8_decode($player['tcpl']),1,0,'C');
$pdf->Cell(15,6.5,utf8_decode($player['efec']),1,0,'C');

$tabla = "SELECT * FROM tab_clasf WHERE id_team = $id_team AND id_temp = $temporada";
$dteg = mysqli_query($con, $tabla);
$datu = mysqli_fetch_array($dteg);

$pdf->Cell(60,6.5,utf8_decode(strtoupper($datu['name_team'])),1,1,'C');

} } 

} elseif ($equipo > 0) {
$pdf->AddPage();
$pdf->SetFont('Arial','',14);

$pdf->Image('../../fondos/pulpo (2).png', 10, 10, 16);
$pdf->Image('../../fondos/pulpov (2).png', 189, 10, 16);
$pdf->Cell(0,5,utf8_decode(strtoupper('LIGA RECREATIVA SOFTBALL EDUCADORES DEL ESTADO ARAGUA')),0,1,'C');
$pdf->Ln(2);
$pdf->Cell(0,5,utf8_decode(strtoupper('CATEGORIA " '.$categoria.' "')),0,1,'C');
$pdf->Ln(2);
$pdf->Cell(0,5,utf8_decode(strtoupper('PICHERS GANADOS')),0,1,'C');
$pdf->Ln(2);



$pdf->SetFont('Arial','B',12);

$pdf->Cell(12.5);
$pdf->Cell(10,6.5,utf8_decode(strtoupper('pos')),1,0,'C');
$pdf->Cell(60,6.5,utf8_decode(strtoupper('Nombre')),1,0,'C');
$pdf->Cell(10,6.5,utf8_decode(strtoupper('JL')),1,0,'C');
$pdf->Cell(10,6.5,utf8_decode(strtoupper('JG')),1,0,'C');
$pdf->Cell(10,6.5,utf8_decode(strtoupper('JP')),1,0,'C');
$pdf->Cell(15,6.5,utf8_decode(strtoupper('AVG')),1,0,'C');
$pdf->Cell(60,6.5,utf8_decode(strtoupper('Equipo')),1,1,'C');

$idtps = "SELECT * FROM temporada WHERE id_temp = $temporada";
$vatps = mysqli_query($con, $idtps);
$dtpps = mysqli_fetch_array($vatps);

$partidoss = $dtpps['partidas'];
    
$covls = "SELECT resumen_lanz.*, tab_clasf.* FROM resumen_lanz 
INNER JOIN tab_clasf ON resumen_lanz.id_team = tab_clasf.id_team 
WHERE tab_clasf.id_team = $equipo AND tab_clasf.id_temp = $temporada ORDER BY resumen_lanz.avg DESC";
$vaiss = mysqli_query($con, $covls);
$asnms = mysqli_num_rows($vaiss);

if ($asnms >= 1) {
for ($jgp=1; $jgp <= 15 ; $jgp++) { 
$players = mysqli_fetch_array($vaiss);

$tjl = $players['tjl'];
$tjg = $players['tjg'];
$tjp = $partidoss - $tjg;

$pdf->SetFont('Arial','B',9);
$pdf->Cell(12.5);
$pdf->Cell(10,6.5,utf8_decode(strtoupper($jgp)),1,0,'C');
$pdf->Cell(60,6.5,utf8_decode($players['name_jglz']),1,0,'C');
$pdf->Cell(10,6.5,utf8_decode($tjl),1,0,'C');
$pdf->Cell(10,6.5,utf8_decode($tjg),1,0,'C');
$pdf->Cell(10,6.5,utf8_decode($tjp),1,0,'C');
$pdf->Cell(15,6.5,utf8_decode($players['avg']),1,0,'C');
$pdf->Cell(60,6.5,utf8_decode(strtoupper($players['name_team'])),1,1,'C');

} }


/////////////////////////////////////////// EFECTIVIDADTRIPLES
$pdf->Ln(8);
$pdf->SetFont('Arial','',14);
$pdf->Cell(0,5,utf8_decode(strtoupper('PICHER efectividad')),0,1,'C');
$pdf->Ln(2);

$pdf->SetFont('Arial','B',12);

$pdf->Cell(17);
$pdf->Cell(10,6.5,utf8_decode(strtoupper('pos')),1,0,'C');
$pdf->Cell(60,6.5,utf8_decode(strtoupper('Nombre')),1,0,'C');
$pdf->Cell(10,6.5,utf8_decode(strtoupper('IL')),1,0,'C');
$pdf->Cell(10,6.5,utf8_decode(strtoupper('CPL')),1,0,'C');
$pdf->Cell(15,6.5,utf8_decode(strtoupper('efec')),1,0,'C');
$pdf->Cell(60,6.5,utf8_decode(strtoupper('Equipo')),1,1,'C');


$idtp = "SELECT * FROM temporada WHERE id_temp = $temporada";
$vatp = mysqli_query($con, $idtp);
$dtpp = mysqli_fetch_array($vatp);

    
$covl = "SELECT resumen_lanz.*, tab_clasf.* FROM resumen_lanz 
INNER JOIN tab_clasf ON resumen_lanz.id_team = tab_clasf.id_team 
WHERE tab_clasf.id_team = $equipo AND tab_clasf.id_temp = $temporada ORDER BY (resumen_lanz.til > $chmp) DESC, resumen_lanz.efec ASC";
$vais = mysqli_query($con, $covl);
$asnm = mysqli_num_rows($vais);


for ($jg=1; $jg <= 15 ; $jg++) { 
$player = mysqli_fetch_array($vais);
$id_team = $player['id_team'];

$pdf->SetFont('Arial','B',10);
$pdf->Cell(17);
$pdf->Cell(10,6.5,utf8_decode(strtoupper($jg)),1,0,'C');
$pdf->Cell(60,6.5,utf8_decode($player['name_jglz']),1,0,'C');
$pdf->Cell(10,6.5,utf8_decode($player['til']),1,0,'C');
$pdf->Cell(10,6.5,utf8_decode($player['tcpl']),1,0,'C');
$pdf->Cell(15,6.5,utf8_decode($player['efec']),1,0,'C');
$pdf->Cell(60,6.5,utf8_decode(strtoupper($player['name_team'])),1,1,'C');

} 


}



$pdf->Output();
?>