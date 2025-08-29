<?php
//Inclusión de Archivo Necesario y Conexión
require('vendor/fpdf/fpdf.php');
require('conexion.php');
$con=conectar();


$id = $_REQUEST['id'];

$revisar = "SELECT temporada.*, tab_clasf.* FROM temporada INNER JOIN tab_clasf ON temporada.id_temp = tab_clasf.id_temp WHERE tab_clasf.id_tab = $id";

$query   = mysqli_query($con,$revisar);
$data    = mysqli_fetch_array($query);
$id_team = $data['id_team'];
$id_tab  = $data['id_tab'];
$cat     = $data['categoria'];

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
$pdf->SetTitle('RESUMEN - EQUIPO '.$data['name_team']);
$pdf->SetAuthor('Arturo');
$pdf->SetCreator('FPDF Maker');



$pdf->Cell(0,5,utf8_decode(strtoupper('LIGA RECREATIVA SOFTBALL EDUCADORES DEL ESTADO ARAGUA')),0,1,'C');
$pdf->Ln(3);
$pdf->Cell(0,5,utf8_decode(strtoupper($data['name_team'])),0,1,'C');
$pdf->Ln(3);
$pdf->Cell(0,5,utf8_decode(strtoupper('CATEGORIA " '.$cat.' "')),0,1,'C');
$pdf->Ln(5);



$pdf->SetFont('Arial','B',12);

$pdf->Cell(5,8,utf8_decode(strtoupper('#')),1,0,'C');
$pdf->Cell(85,8,utf8_decode(strtoupper('jugador')),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper('vb')),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper('h')),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper('hr')),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper('2b')),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper('3b')),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper('CA')),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper('Ci')),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper('k')),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper('b')),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper('as')),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper('vb')),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper('th')),1,0,'C');
$pdf->Cell(20,8,utf8_decode(strtoupper('avg')),1,1,'C');

$cons = "SELECT * FROM resumen_stats WHERE id_team = $id_team AND id_tab =$id_tab";
$dteg = mysqli_query($con, $cons);
$nums = mysqli_num_rows($dteg);
if ($nums >= 1) {
for ($jg=1; $jg <= $nums ; $jg++) { 
$player = mysqli_fetch_array($dteg);

$pdf->SetFont('Arial','',10);

$pdf->Cell(5,8,utf8_decode(strtoupper($jg)),1,0,'C');
$pdf->Cell(85,8,utf8_decode($player['name_jgstats']),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper($player['vb'])),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper($player['h'])),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper($player['hr'])),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper($player['2b'])),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper($player['3b'])),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper($player['ca'])),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper($player['ci'])),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper($player['k'])),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper($player['b'])),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper($player['a'])),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper($player['tvb'])),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper($player['th'])),1,0,'C');
$pdf->Cell(20,8,utf8_decode(strtoupper($player['avg'])),1,1,'C');

    }
}

$ftor = "SELECT 
SUM(vb)   AS tvb,
SUM(h)    AS th,
SUM(hr)   AS thr,
SUM(2b)   AS t2b,
SUM(3b)   AS t3b,
SUM(ca)   AS tca,
SUM(ci)   AS tci,
SUM(k)    AS tk,
SUM(b)    AS tb,
SUM(a)    AS ta,
SUM(tvb)  AS ttvb,
SUM(th)   AS tthh 
FROM resumen_stats 
WHERE id_team = $id_team AND id_tab =$id_tab";
$vtvt = mysqli_query($con, $ftor);
$dust = mysqli_num_rows($vtvt);
if ($dust >= 1) {
    for ($fr=1; $fr <= $dust ; $fr++) { 
    $gapa = mysqli_fetch_array($vtvt);

    $on_tvb  = $gapa['tvb'];
    $tw_ttvb = $gapa['ttvb'];

if ($on_tvb = $tw_ttvb) {
    $dtvb = $on_tvb;
} else {
    $dtvb = "1";
}

$th  = $gapa['th'];
$thr = $gapa['thr'];
$t2b = $gapa['t2b'];
$t3b = $gapa['t3b'];
$tth = $gapa['tthh'];

    $on_th       = $th + $thr + $t2b + $t3b;
    $tw_th       = $tth;
if ($on_th = $tw_th) {
    $dth = $on_th;
} else {
    $dth = "1";
}

    $avg = ($dth * 1000) / $dtvb;
    $ravg = round($avg);

$pdf->SetFont('Arial','B',12);

$pdf->Cell(90,8,utf8_decode(strtoupper('')),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper($gapa['tvb'])),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper($gapa['th'])),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper($gapa['thr'])),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper($gapa['t2b'])),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper($gapa['t3b'])),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper($gapa['tca'])),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper($gapa['tci'])),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper($gapa['tk'])),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper($gapa['tb'])),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper($gapa['ta'])),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper($dtvb)),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper($dth)),1,0,'C');
$pdf->Cell(20,8,utf8_decode(strtoupper($ravg)),1,1,'C');

} }

$pdf->AddPage();
$pdf->SetFont('Arial','',14);

$pdf->Cell(0,5,utf8_decode(strtoupper('LIGA RECREATIVA SOFTBALL EDUCADORES DEL ESTADO ARAGUA')),0,1,'C');
$pdf->Ln(3);
$pdf->Cell(0,5,utf8_decode(strtoupper($data['name_team'])),0,1,'C');
$pdf->Ln(3);
$pdf->Cell(0,5,utf8_decode(strtoupper('pichers - CATEGORIA " '.$cat.' "')),0,1,'C');
$pdf->Ln(5);



$pdf->SetFont('Arial','B',12);

$pdf->Cell(5,8,utf8_decode(strtoupper('#')),1,0,'C');
$pdf->Cell(89,8,utf8_decode(strtoupper('jugador')),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper('tjl')),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper('tjg')),1,0,'C');
$pdf->Cell(20,8,utf8_decode(strtoupper('avg')),1,0,'C');
$pdf->Cell(16,8,utf8_decode(strtoupper('til')),1,0,'C');
$pdf->Cell(16,8,utf8_decode(strtoupper('tcpl')),1,0,'C');
$pdf->Cell(16,8,utf8_decode(strtoupper('efec')),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper('h')),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper('2b')),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper('3b')),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper('hr')),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper('b')),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper('k')),1,1,'C');

$cons = "SELECT * FROM resumen_lanz WHERE id_team = $id_team AND id_tab =$id_tab";
$dteg = mysqli_query($con, $cons);
$nums = mysqli_num_rows($dteg);
if ($nums >= 1) {
for ($jg=1; $jg <= $nums ; $jg++) { 
$player = mysqli_fetch_array($dteg);

$pdf->SetFont('Arial','',10);
$pdf->Cell(5,8,utf8_decode(strtoupper($jg)),1,0,'C');
$pdf->Cell(89,8,utf8_decode($player['name_jglz']),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper($player['tjl'])),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper($player['tjg'])),1,0,'C');
$pdf->Cell(20,8,utf8_decode(strtoupper($player['avg'])),1,0,'C');
$pdf->Cell(16,8,utf8_decode(strtoupper($player['til'])),1,0,'C');
$pdf->Cell(16,8,utf8_decode(strtoupper($player['tcpl'])),1,0,'C');
$pdf->Cell(16,8,utf8_decode(strtoupper($player['efec'])),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper($player['h'])),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper($player['2b'])),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper($player['3b'])),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper($player['hr'])),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper($player['b'])),1,0,'C');
$pdf->Cell(12,8,utf8_decode(strtoupper($player['k'])),1,1,'C');

    }
}


$pdf->Output();
?>