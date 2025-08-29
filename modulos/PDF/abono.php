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
$pdf->Image('../../fondos/pulpo (2).png', 10, 10, 16);
$pdf->Image('../../fondos/pulpov (2).png', 189, 10, 16);
$pdf->Cell(0,5,utf8_decode(strtoupper('LIGA RECREATIVA SOFTBALL EDUCADORES DEL ESTADO ARAGUA')),0,1,'C');
$pdf->Ln(2);
$pdf->Cell(0,5,utf8_decode(strtoupper('RELACION DE PAGO DE LOS EQUIPOS ')),0,1,'C');
$pdf->Ln(3);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,5,'FECHA: '.$timeday,0,1,'L');
$pdf->SetFont('Arial','B',14);

/*D*/
$pdf->Ln(2);
$val_hord   = "SELECT * FROM temporada WHERE activo = 1 AND categoria LIKE '%D%'";
$query_hord = mysqli_query($con, $val_hord);
$obtenerhod = mysqli_fetch_array($query_hord);
$idd  = $obtenerhod['id_temp'];
$catd = $obtenerhod['categoria'];
$pdf->Cell(0,5,utf8_decode(strtoupper('CATEGORIA '. $catd)),0,1,'C');
$pdf->Ln(4);



$revisardd = "SELECT * FROM abonos WHERE id_temp = $idd AND categoria LIKE '%$catd%';";
$queryd   = mysqli_query($con,$revisardd);
$datad    = mysqli_fetch_array($queryd);
$nabonod  = $datad['ncantidad'];
$idbonod  = $datad['id_abn'];
$fourd    = $datad['prize_four'];
$cfourd    = $datad['cant_four'];
$onced    = $datad['prize_once'];
$conced    = $datad['cant_once'];
$secondd    = $datad['prize_second'];
$csecondd    = $datad['cant_second'];
$thirdd    = $datad['prize_third'];
$cthirdd    = $datad['cant_third'];

$pdf->SetFont('Arial','B',9);


$pdf->Cell(10);
$pdf->Cell(35,5,utf8_decode(strtoupper('Equipo')),1,0, 'C');
    for ($id = 1; $id <= $nabonod; $id++) { 
$pdf->Cell(13,5,utf8_decode(strtoupper('AB')),1,0, 'C');
    }
$pdf->Cell(20,5,utf8_decode(strtoupper('TOTAL')),1,1, 'C');

    $revisard = "SELECT * FROM tab_clasf WHERE id_temp = $idd AND categoria LIKE '%$catd%'";
    $ryqued = mysqli_query($con, $revisard);
    $nunumd = mysqli_num_rows($ryqued);

    if ($nunumd >= 1) {
        for ($td = 1; $td <= $nunumd; $td++) { 
            $bdatad = mysqli_fetch_array($ryqued);
            $nmmmd = $bdatad['name_team'];
            $nttmd = $bdatad['id_team'];

$pdf->Cell(10);
$pdf->Cell(35,5,utf8_decode(strtoupper($nmmmd)),1,0, 'C');
    $suma_montosd = 0; // Inicializamos acumulador
    for ($jd = 1; $jd <= $nabonod; $jd++) { 
        $njnd = "SELECT monto FROM monto WHERE id_abn = $idbonod AND id_team = $nttmd AND numero = $jd";
        $qttd = mysqli_query($con, $njnd);
        $dopd = mysqli_fetch_array($qttd);
        $monto_actuald = isset($dopd['monto']) ? $dopd['monto'] : 0;
            $suma_montosd += $monto_actuald;
$pdf->Cell(13,5,$monto_actuald,1,0, 'C');
    }
$pdf->Cell(20,5,$suma_montosd,1,1, 'C');
    } 
}


if ($nabonod > 3) {
    $trrrd = $nabonod - 3;
    $vaciod = $trrrd * 13;
    $nadad = 1;

 } elseif ($nabonod == 3) {
    $trrrd = $nabonod - 3;
    $nadad = 0;
    $vaciod = 0;

 } 


$pdf->Cell(10);
$pdf->Cell(41,6,utf8_decode('PREMIACIÓN'),0,0,'C');
$pdf->Cell(20);


if (empty($nadad)) {
    
} else {
$pdf->Cell($vaciod,5,'',0,0, 'C');
}

$pdf->Cell(13,5,utf8_decode(strtoupper('Total')),1,0, 'C');

$totalmented = "SELECT SUM(monto) AS total_final FROM monto
WHERE id_temp = $idd AND categoria LIKE '%$catd%' AND numero <= $nabonod";
$tratad = mysqli_query($con, $totalmented);
$datatold = mysqli_fetch_array($tratad);
$vvvd = $datatold['total_final'] + 0;
$pdf->Cell(20,5,$vvvd.' $',1,1, 'C');


 $pdf->Cell(10);
if (empty($onced)) { 
 $pdf->Cell(48);} elseif (!empty($onced)) {
$pdf->Cell(35,5,utf8_decode(strtoupper('1er Lugar')),1,0, 'C');
$pdf->Cell(13,5,$conced.' $',1,1, 'C');
}

$pdf->Cell(10);
if (empty($secondd)) { } elseif (!empty($secondd)) {
$pdf->Cell(35,5,utf8_decode(strtoupper('2do Lugar')),1,0, 'C');
$pdf->Cell(13,5,$csecondd.' $',1,1, 'C');
}

$pdf->Cell(10);
if (empty($thirdd)) { } elseif (!empty($thirdd)) {
$pdf->Cell(35,5,utf8_decode(strtoupper('3er Lugar')),1,0, 'C');
$pdf->Cell(13,5,$cthirdd.' $',1,1, 'C');
}

if (empty($fourd)) { } elseif (!empty($fourd)) {

$pdf->Cell(10);
$pdf->Cell(35,5,utf8_decode(strtoupper('4to Lugar')),1,0, 'C');
$pdf->Cell(13,5,$cfourd.' $',1,1, 'C');
}                         


/*C*/
$pdf->Ln(10);

$pdf->SetFont('Arial','B',14);
$val_horc   = "SELECT * FROM temporada WHERE activo = 1 AND categoria LIKE '%C%'";
$query_horc = mysqli_query($con, $val_horc);
$obtenerhoc = mysqli_fetch_array($query_horc);
$idc  = $obtenerhoc['id_temp'];
$catc = $obtenerhoc['categoria'];
$pdf->Cell(0,5,utf8_decode(strtoupper('CATEGORIA '. $catc)),0,1,'C');
$pdf->Ln(4);



$revisarcc = "SELECT * FROM abonos WHERE id_temp = $idc AND categoria LIKE '%$catc%';";
$queryc   = mysqli_query($con,$revisarcc);
$datac    = mysqli_fetch_array($queryc);
$nabonoc  = $datac['ncantidad'];
$idbonoc  = $datac['id_abn'];
$fourc    = $datac['prize_four'];
$cfourc    = $datac['cant_four'];
$oncec    = $datac['prize_once'];
$concec    = $datac['cant_once'];
$secondc    = $datac['prize_second'];
$csecondc    = $datac['cant_second'];
$thirdc    = $datac['prize_third'];
$cthirdc    = $datac['cant_third'];

$pdf->SetFont('Arial','B',9);


$pdf->Cell(10);
$pdf->Cell(35,5,utf8_decode(strtoupper('Equipo')),1,0, 'C');
    for ($ic = 1; $ic <= $nabonoc; $ic++) { 
$pdf->Cell(13,5,utf8_decode(strtoupper('AB')),1,0, 'C');
    }
$pdf->Cell(20,5,utf8_decode(strtoupper('TOTAL')),1,1, 'C');

    $revisarc = "SELECT * FROM tab_clasf WHERE id_temp = $idc AND categoria LIKE '%$catc%'";
    $ryquec = mysqli_query($con, $revisarc);
    $nunumc = mysqli_num_rows($ryquec);

    if ($nunumc >= 1) {
        for ($tc = 1; $tc <= $nunumc; $tc++) { 
            $bdatac = mysqli_fetch_array($ryquec);
            $nmmmc = $bdatac['name_team'];
            $nttmc = $bdatac['id_team'];

$pdf->Cell(10);
$pdf->Cell(35,5,utf8_decode(strtoupper($nmmmc)),1,0, 'C');
    $suma_montosc = 0; // Inicializamos acumulador
    for ($jc = 1; $jc <= $nabonoc; $jc++) { 
        $njnc = "SELECT monto FROM monto WHERE id_abn = $idbonoc AND id_team = $nttmc AND numero = $jc";
        $qttc = mysqli_query($con, $njnc);
        $dopc = mysqli_fetch_array($qttc);
        $monto_actualc = isset($dopc['monto']) ? $dopc['monto'] : 0;
            $suma_montosc += $monto_actualc;
$pdf->Cell(13,5,$monto_actualc,1,0, 'C');
    }
$pdf->Cell(20,5,$suma_montosc,1,1, 'C');
    } 
}


if ($nabonoc > 3) {
    $trrrc = $nabonoc - 3;
    $vacioc = $trrrc * 13;
    $nadac = 1;

 } elseif ($nabonoc == 3) {
    $trrrc = $nabonoc - 3;
    $nadac = 0;
    $vacioc = 0;

 } 


$pdf->Cell(10);
$pdf->Cell(41,6,utf8_decode('PREMIACIÓN'),0,0,'C');
$pdf->Cell(20);



if (empty($nadac)) {
    
} else {
$pdf->Cell($vacioc,5,'',0,0, 'C');
}

$pdf->Cell(13,5,utf8_decode(strtoupper('Total')),1,0, 'C');

$totalmentec = "SELECT SUM(monto) AS total_final FROM monto
WHERE id_temp = $idc AND categoria LIKE '%$catc%' AND numero <= $nabonoc";
$tratac = mysqli_query($con, $totalmentec);
$datatolc = mysqli_fetch_array($tratac);
$vvvc = $datatolc['total_final'] + 0;
$pdf->Cell(20,5,$vvvc.' $',1,1, 'C');


 $pdf->Cell(10);
if (empty($oncec)) {
 $pdf->Cell(48); } elseif (!empty($oncec)) {
$pdf->Cell(35,5,utf8_decode(strtoupper('1er Lugar')),1,0, 'C');
$pdf->Cell(13,5,$concec.' $',1,1, 'C');
}

$pdf->Cell(10);
if (empty($secondc)) { } elseif (!empty($secondc)) {
$pdf->Cell(35,5,utf8_decode(strtoupper('2do Lugar')),1,0, 'C');
$pdf->Cell(13,5,$csecondc.' $',1,1, 'C');
}

$pdf->Cell(10);
if (empty($thirdc)) { } elseif (!empty($thirdc)) {
$pdf->Cell(35,5,utf8_decode(strtoupper('3er Lugar')),1,0, 'C');
$pdf->Cell(13,5,$cthirdc.' $',1,1, 'C');
}

if (empty($fourc)) { } elseif (!empty($fourc)) {

$pdf->Cell(10);
$pdf->Cell(35,5,utf8_decode(strtoupper('4to Lugar')),1,0, 'C');
$pdf->Cell(13,5,$cfourc.' $',1,1, 'C');
}                            


/*B*/
$pdf->Ln(10);

$val_hor   = "SELECT * FROM temporada WHERE activo = 1 AND categoria LIKE '%B%'";
$query_hor = mysqli_query($con, $val_hor);
$obtenerho = mysqli_fetch_array($query_hor);
$id  = $obtenerho['id_temp'];
$cat = $obtenerho['categoria'];

$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,5,utf8_decode(strtoupper('CATEGORIA '. $cat)),0,1,'C');
$pdf->Ln(4);

$revisar = "SELECT * FROM abonos WHERE id_temp = $id AND categoria LIKE '%$cat%';";
$query   = mysqli_query($con,$revisar);
$data    = mysqli_fetch_array($query);
$nabono  = $data['ncantidad'];
$idbono  = $data['id_abn'];
$four    = $data['prize_four'];
$cfour    = $data['cant_four'];
$once    = $data['prize_once'];
$conce    = $data['cant_once'];
$second    = $data['prize_second'];
$csecond    = $data['cant_second'];
$third    = $data['prize_third'];
$cthird    = $data['cant_third'];

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
$pdf->Cell(41,6,utf8_decode('PREMIACIÓN'),0,0,'C');
$pdf->Cell(20);

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
$pdf->Cell(20,5,$vvv.' $',1,1, 'C');


 $pdf->Cell(10);
if (empty($once)) {
 $pdf->Cell(48); } elseif (!empty($once)) {
$pdf->Cell(35,5,utf8_decode(strtoupper('1er Lugar')),1,0, 'C');
$pdf->Cell(13,5,$conce.' $',1,1, 'C');
}

$pdf->Cell(10);
if (empty($second)) { } elseif (!empty($second)) {
$pdf->Cell(35,5,utf8_decode(strtoupper('2do Lugar')),1,0, 'C');
$pdf->Cell(13,5,$csecond.' $',1,1, 'C');
}

$pdf->Cell(10);
if (empty($third)) { } elseif (!empty($third)) {
$pdf->Cell(35,5,utf8_decode(strtoupper('3er Lugar')),1,0, 'C');
$pdf->Cell(13,5,$cthird.' $',1,1, 'C');
}

if (empty($four)) { } elseif (!empty($four)) {

$pdf->Cell(10);
$pdf->Cell(35,5,utf8_decode(strtoupper('4to Lugar')),1,0, 'C');
$pdf->Cell(13,5,$cfour.' $',1,1, 'C');
}                            



$pdf->Output();
?>