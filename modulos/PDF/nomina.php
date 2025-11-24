<?php
//Inclusión de Archivo Necesario y Conexión
require('vendor/fpdf/fpdf.php');
require('conexion.php');
$con=conectar();
$equipo    = $_POST['equipo'];
$categoria = $_POST['categoria'];
$sust      = $_POST['sust'];

$name = "SELECT * FROM equipos WHERE id_team = $equipo";
$qyeq = mysqli_query($con, $name);
$ttmm = mysqli_fetch_array($qyeq);


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
$pdf->SetTitle('Nomina - '.$ttmm['nom_team']);
$pdf->SetAuthor('Arturo');
$pdf->SetCreator('FPDF Maker');



$pdf->AddPage();
$pdf->SetFont('Arial','',14);

$pdf->Image('../../fondos/pulpo (2).png', 10, 10, 16);
$pdf->Image('../../fondos/pulpov (2).png', 189, 10, 16);
$pdf->Cell(0,5,utf8_decode(strtoupper('LIGA RECREATIVA SOFTBALL EDUCADORES DEL ESTADO ARAGUA')),0,1,'C');
$pdf->Ln(2);
$pdf->Cell(0,5,utf8_decode(strtoupper('CATEGORIA " '.$categoria.' "')),0,1,'C');
$pdf->Ln(2);
 if (empty($sust)) { 
$pdf->Cell(0,5,'NOMINA  '.$ttmm['nom_team'],0,1,'C');
 } elseif (!empty($sust)) {
$pdf->Cell(0,5, utf8_decode('NOMINA DE SUSTITUCIÓN '.$ttmm['nom_team']),0,1,'C');
}
$pdf->SetFont('Arial','B',12);
// Ancho fijo para las etiquetas
// Configurar anchos
$ancho_etiqueta = 45;
$ancho_valor = 50;

// Líneas principales
$pdf->Cell(0,5,'FECHA: '.$timeday,0,1,'L');
$pdf->Ln();

$pdf->SetFont('Arial','B',10);

$pdf->Cell(8,4,utf8_decode('N°'),1,0,'C');
$pdf->Cell(30,4,utf8_decode(strtoupper('Cedula')),1,0,'C');
$pdf->Cell(80,4,utf8_decode(strtoupper('Nombre y Apellido')),1,0,'C');
$pdf->Cell(35,4,utf8_decode(strtoupper('Fecha Nac')),1,0,'C');
$pdf->Cell(20,4,utf8_decode(strtoupper('Edad')),1,0,'C');
$pdf->Cell(20,4,utf8_decode(strtoupper('Picher')),1,1,'C');


    
$covl = "SELECT * FROM jugadores WHERE id_team = $equipo AND categoria LIKE '%$categoria%'";
$vais = mysqli_query($con, $covl);
$asnm = mysqli_num_rows($vais);


if ($asnm >= 1) {
for ($jg=1; $jg <= 26 ; $jg++) { 
$player = mysqli_fetch_array($vais);
$id_team = $player['id_team'];
$lan = $player['lanzador'];

$trg=$player['fecha'];
$entero_trg = strtotime($trg);
$ano_trg = date("Y", $entero_trg);
$mes_trg = date("m", $entero_trg);
$dia_trg = date("d", $entero_trg);
$desde_reorder=$dia_trg.'-'.$mes_trg.'-'.$ano_trg;

$pdf->SetFont('Arial','B',9);
$pdf->Cell(8,4,utf8_decode(strtoupper($jg)),1,0,'C');
$pdf->Cell(30,4,utf8_decode($player['cedula']),1,0,'C');
$pdf->Cell(80,4,utf8_decode($player['nombre']." ".$player['apellido']),1,0,'C');

if (!empty($player['cedula'])){
    $pdf->Cell(35,4,utf8_decode($desde_reorder),1,0,'C');
} elseif (empty($player['cedula'])) { 
    $pdf->Cell(35,4,"",1,0,'C');
} 

//$pdf->Cell(30,4,utf8_decode($desde_reorder),1,0,'C');
$pdf->Cell(20,4,utf8_decode($player['edad']),1,0,'C');

if (!empty($player['cedula'])){
    if (!empty($lan)){
        $pdf->Cell(20,4,utf8_decode('SI'),1,1,'C');
    } elseif (empty($lan)) { 
        $pdf->Cell(20,4,utf8_decode('NO'),1,1,'C');
    } 
} elseif (empty($player['cedula'])) { 
    $pdf->Cell(20,4,"",1,1,'C');
} 


 if (empty($sust)) { } elseif (!empty($sust)) {
$pdf->Cell(8,4,'',1,0,'C');
$pdf->Cell(30,4,'',1,0,'C');
$pdf->Cell(80,4,'',1,0,'C');
$pdf->Cell(35,4,'',1,0,'C');
$pdf->Cell(20,4,'',1,0,'C');
$pdf->Cell(20,4,'',1,1,'C');
}
} } 


$pdf->Ln(10);
$pdf->SetFont('Arial','B',10);
// Sección de la liga en el lado derecho
$pdf->Cell(($ancho_valor+$ancho_etiqueta),5,"",0,0,'L');
$pdf->SetX(140); // O usar SetX(-60) para posición relativa al margen derecho
$pdf->Cell(0,5,'Por la liga de Educadores',0,1,'L');

$pdf->Cell($ancho_etiqueta,5,'MANAGER:',0,0,'L');
$pdf->Cell($ancho_valor,5,$ttmm['manager'],0,0,'L');
$pdf->SetX(140);
$pdf->Cell(0,5,'Nombre: _______________________',0,1,'L');

$pdf->Cell($ancho_etiqueta,5,'DELEGADO:',0,0,'L');
$pdf->Cell($ancho_valor,5,$ttmm['delegado'],0,0,'L');
$pdf->SetX(140);
$pdf->Cell(0,5,'',0,1,'L'); // Línea en blanco

$pdf->Cell($ancho_etiqueta,5,'SUB-DELEGADO:',0,0,'L');
$pdf->Cell($ancho_valor,5,$ttmm['subdelegado'],0,0,'L');
$pdf->SetX(140);
$pdf->Cell(0,5,'Firma y Sello',0,1,'L');
$pdf->SetFont('Arial','B',14);





$pdf->Output();
?>