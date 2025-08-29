<?php
//Inclusión de Archivo Necesario y Conexión
require('vendor/fpdf/fpdf.php');
include('conexion.php');
$gra=graficas();
$con=conectar();
$coner=conectarsiabis();
$desde=$_REQUEST['desde'];
$hasta=$_REQUEST['hasta'];


$trg=$desde;
$entero_trg = strtotime($trg);
$ano_trg = date("Y", $entero_trg);
$mes_trg = date("m", $entero_trg);
$dia_trg = date("d", $entero_trg);
$desde_reorder=$ano_trg.'-'.$dia_trg.'-'.$mes_trg;

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
            $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
        }
}

// Creación del objeto de la clase heredada
$pdf = new FPDF('P','mm','Letter');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);

$consulta="SELECT * FROM grafics WHERE nom_niv_eva='NIVEL SUPERVISOR' AND f_desde='$desde' AND f_hasta='$hasta'";
$query=mysqli_query($gra,$consulta);
$requests=mysqli_fetch_array($query);
$img=$requests['variable'];
$pic= 'data://text/plain;base64,'. $img;
$pdf->Image($pic, 60,120,90,0,'png');


$consultatg ="SELECT * FROM datos_generales";
$querytg=mysqli_query($con,$consultatg);
$datotg=mysqli_fetch_array($querytg); 
$pdf->Image($datotg['directorio'], $datotg['horizontal'], $datotg['vertical'], $datotg['tamano']);
$pdf->Ln(5);
$pdf->Cell(0,5,utf8_decode(strtoupper($datotg['membrete'])),0,1,'C');


/*
$pdf->Image('logo_cea_oficios.png', 23, 8, 22);
$pdf->Ln(5);
$pdf->Cell(0,5,utf8_decode(strtoupper('CONTRALORIA DEL ESTADO ARAGUA')),0,1,'C');*/
$pdf->Ln(10);

$pdf->SetFont('Times','',10);
$pdf->Cell(190,5,utf8_decode('TABLA N° 1'),0,1,'C');
$pdf->Cell(190,5,utf8_decode('NIVEL SUPERVISOR'),0,1,'C');
$pdf->Cell(190,5,'PERIODO '.$desde.' - '.$hasta,0,1,'C');
$pdf->Cell(5,5,'',0,1,'C');

$consultacom = "SELECT * FROM competencias WHERE tip_eva = 'NIVEL SUPERVISOR' and perio_i = '$desde' ORDER BY id_c;";
$resultadocom = mysqli_query($con,$consultacom);
$num_resultadoscom = mysqli_num_rows($resultadocom);
if ($num_resultadoscom >= 1) {


$pdf->SetFillColor(213, 216, 220);
$pdf->Cell(10,10,'ID',1,0,'C',true);
$pdf->Cell(110,5,'NIVEL SUPERVISOR',1,0,'C',true);
$pdf->Cell(70,5,'NIVEL DE RENDIMIENTO',1,1,'C',true);
$pdf->Cell(10);
$pdf->Cell(110,5,'COMPETENCIAS',1,0,'C',true);
$pdf->Cell(17.5,5,'0,1 - 12,5',1,0,'C');
$pdf->Cell(17.5,5,'12,6 - 25',1,0,'C');
$pdf->Cell(17.5,5,'25,1 -37,5',1,0,'C');
$pdf->Cell(17.5,5,'37,6 - 50',1,1,'C');

    $trc1=0;$trc2=0;$trc3=0;$trc4=0;
    for ($x=1; $x<=$num_resultadoscom;$x++) {
        $registrocom=mysqli_fetch_array($resultadocom);
        $compe = $registrocom['competencia'];

            $consultacom2 = "SELECT seccion_a.*, seccion_c_ii.* FROM seccion_a inner join seccion_c_ii on seccion_a.id_eva = seccion_c_ii.id_eva_c_ii where tip_com = 'NIVEL SUPERVISOR' and comp_c = '$compe' and p_des = '$desde' ORDER BY ren_c;";

            $resultadocom2 = mysqli_query($con,$consultacom2);
            $num_resultadoscom2 = mysqli_num_rows($resultadocom2);
            $rc1=0;$rc2=0;$rc3=0;$rc4=0;
                for ($h=1; $h<=$num_resultadoscom2;$h++) {
                    $registrocom2=mysqli_fetch_array($resultadocom2);
                     $compe2 = $registrocom2['comp_c'];
                    $ren_com = $registrocom2['ren_c'];

            // CONTAR EVALUACIONES POR COMPETENCIAS
                    if ($ren_com == 1) {$rc1 = $rc1 + 1;}
                    if ($ren_com == 2) {$rc2 = $rc2 + 1;}
                    if ($ren_com == 3) {$rc3 = $rc3 + 1;}
                    if ($ren_com == 4) {$rc4 = $rc4 + 1;}

            }   // fin 2do for
                    $trc1 = $trc1 + $rc1;
                    $trc2 = $trc2 + $rc2;
                    $trc3 = $trc3 + $rc3;
                    $trc4 = $trc4 + $rc4;

$pdf->Cell(10,5,$x,1,0,'C');
$newcompe=$compe;
$pcausa= explode(':',$newcompe);

$pdf->SetFont('Times','',8.5);
$pdf->Cell(110,5,$pcausa[0],1,0,'J');
$pdf->SetFont('Times','',10);
$pdf->Cell(17.5,5,$rc1,1,0,'C');
$pdf->Cell(17.5,5,$rc2,1,0,'C');
$pdf->Cell(17.5,5,$rc3,1,0,'C');
$pdf->Cell(17.5,5,$rc4,1,1,'C');

    }
}
$pdf->SetFillColor(213, 216, 220); 
$pdf->Cell(190,0.5,'',1,1,'C',true);
$pdf->Cell(120,5,'TOTALES',1,0,'R');
$pdf->Cell(17.5,5,$trc1,1,0,'C');
$pdf->Cell(17.5,5,$trc2,1,0,'C');
$pdf->Cell(17.5,5,$trc3,1,0,'C');
$pdf->Cell(17.5,5,$trc4,1,1,'C');
$pdf->Cell(190,5,'',0,1,'C');
$pdf->SetFont('Times','',10);
$pdf->SetFillColor(255, 255, 255); 

$pdf->Ln(5);
$pdf->Cell(190,5,utf8_decode('GRAFICA N° 1 - RENDIMIENTO'),0,1,'C');
$pdf->Cell(190,5,utf8_decode('NIVEL SUPERVISOR'),0,1,'C');
$pdf->Cell(190,5,utf8_decode('PERIODO '.$desde.' - '.$hasta),0,1,'C');
$pdf->Ln(70);

if (empty($trc1) || $trc1 == 0) { $tone1=0; } else { $tone1=1; }
if (empty($trc2) || $trc2 == 0) { $tone2=0; } else { $tone2=1; }
if (empty($trc3) || $trc3 == 0) { $tone3=0; } else { $tone3=1; }
if (empty($trc4) || $trc4 == 0) { $tone4=0; } else { $tone4=1; }
$tonetotal = $tone1 + $tone2 + $tone3 + $tone4;

if     ($tonetotal == 1) { $pdf->Cell(90); } 
elseif ($tonetotal == 2) { $pdf->Cell(80); } 
elseif ($tonetotal == 3) { $pdf->Cell(65); } 
elseif ($tonetotal == 4) { $pdf->Cell(55); } 
else   { }

if (empty($trc1) || $trc1 == 0) { } else {
$pdf->SetFillColor(255, 105, 97);
$pdf->Cell(5,3,'',1,0,'C',true);
$pdf->Cell(10,3,$trc1,0,0,'C');
$pdf->Cell(10,3,'',0,0,'C');
}

if (empty($trc2) || $trc2 == 0) { } else {
$pdf->SetFillColor(119, 221, 119);
$pdf->Cell(5,3,'',1,0,'C',true);
$pdf->Cell(10,3,$trc2,0,0,'C');
$pdf->Cell(10,3,'',0,0,'C');
}

if (empty($trc3) || $trc3 == 0) { } else {
$pdf->SetFillColor(253, 253, 150);
$pdf->Cell(5,3,'',1,0,'C',true);
$pdf->Cell(10,3,$trc3,0,0,'C');
$pdf->Cell(10,3,'',0,0,'C');
}

if (empty($trc4) || $trc4 == 0) { } else {
$pdf->SetFillColor(132, 182, 244);
$pdf->Cell(5,3,'',1,0,'C',true);
$pdf->Cell(10,3,$trc4,0,1,'C');
}


/*------------------------------------------------------------------------------------*/

$pdf->SetFont('Times','',12);
$pdf->AddPage();

$consulta="SELECT * FROM grafics WHERE nom_niv_eva='NIVEL PROFESIONAL' AND f_desde='$desde' AND f_hasta='$hasta'";
$query=mysqli_query($gra,$consulta);
$requests=mysqli_fetch_array($query);
$img=$requests['variable'];
$pic= 'data://text/plain;base64,'. $img;
$pdf->Image($pic, 60,125,90,0,'png');


$consultatg ="SELECT * FROM datos_generales";
$querytg=mysqli_query($con,$consultatg);
$datotg=mysqli_fetch_array($querytg); 
$pdf->Image($datotg['directorio'], $datotg['horizontal'], $datotg['vertical'], $datotg['tamano']);
$pdf->Ln(5);
$pdf->Cell(0,5,utf8_decode(strtoupper($datotg['membrete'])),0,1,'C');


/*
$pdf->Image('logo_cea_oficios.png', 23, 8, 22);
$pdf->Ln(5);
$pdf->Cell(0,5,utf8_decode(strtoupper('CONTRALORIA DEL ESTADO ARAGUA')),0,1,'C');*/
$pdf->Ln(10);

$pdf->SetFont('Times','',10);
$pdf->Cell(190,5,utf8_decode('TABLA N° 2'),0,1,'C');
$pdf->Cell(190,5,utf8_decode('NIVEL PROFESIONAL'),0,1,'C');
$pdf->Cell(190,5,'PERIODO '.$desde.' - '.$hasta,0,1,'C');
$pdf->Cell(5,5,'',0,1,'C');


$consultacom = "SELECT * FROM competencias WHERE tip_eva = 'NIVEL PROFESIONAL' and perio_i = '$desde' ORDER BY id_c;";
$resultadocom = mysqli_query($con,$consultacom);
$num_resultadoscom = mysqli_num_rows($resultadocom);
if ($num_resultadoscom >= 1) {


$pdf->SetFillColor(213, 216, 220);
$pdf->Cell(10,10,'ID',1,0,'C',true);
$pdf->Cell(110,5,'NIVEL PROFESIONAL',1,0,'C',true);
$pdf->Cell(70,5,'NIVEL DE RENDIMIENTO',1,1,'C',true);
$pdf->Cell(10);
$pdf->Cell(110,5,'COMPETENCIAS',1,0,'C',true);
$pdf->Cell(17.5,5,'0,1 - 12,5',1,0,'C');
$pdf->Cell(17.5,5,'12,6 - 25',1,0,'C');
$pdf->Cell(17.5,5,'25,1 -37,5',1,0,'C');
$pdf->Cell(17.5,5,'37,6 - 50',1,1,'C');

    $trc1=0;$trc2=0;$trc3=0;$trc4=0;
    for ($x=1; $x<=$num_resultadoscom;$x++) {
        $registrocom=mysqli_fetch_array($resultadocom);
        $compe = $registrocom['competencia'];

            $consultacom2 = "SELECT seccion_a.*, seccion_c_ii.* FROM seccion_a inner join seccion_c_ii on seccion_a.id_eva = seccion_c_ii.id_eva_c_ii where tip_com = 'NIVEL PROFESIONAL' and comp_c = '$compe' and p_des = '$desde' ORDER BY ren_c;";

            $resultadocom2 = mysqli_query($con,$consultacom2);
            $num_resultadoscom2 = mysqli_num_rows($resultadocom2);
            $rc1=0;$rc2=0;$rc3=0;$rc4=0;
                for ($h=1; $h<=$num_resultadoscom2;$h++) {
                    $registrocom2=mysqli_fetch_array($resultadocom2);
                     $compe2 = $registrocom2['comp_c'];
                    $ren_com = $registrocom2['ren_c'];

            // CONTAR EVALUACIONES POR COMPETENCIAS
                    if ($ren_com == 1) {$rc1 = $rc1 + 1;}
                    if ($ren_com == 2) {$rc2 = $rc2 + 1;}
                    if ($ren_com == 3) {$rc3 = $rc3 + 1;}
                    if ($ren_com == 4) {$rc4 = $rc4 + 1;}

            }   // fin 2do for
                    $trc1 = $trc1 + $rc1;
                    $trc2 = $trc2 + $rc2;
                    $trc3 = $trc3 + $rc3;
                    $trc4 = $trc4 + $rc4;

$pdf->Cell(10,5,$x,1,0,'C');
$newcompe=$compe;
$pcausa= explode(':',$newcompe);

$pdf->SetFont('Times','',8.5);
if ($x == 6) {
$pdf->Cell(110,5,utf8_decode('CAPACIDAD DE ANÁLISIS. SÍNTESIS Y DE REDACCIÓN'),1,0,'J');
} else {
$pdf->Cell(110,5,$pcausa[0],1,0,'J');
}
$pdf->SetFont('Times','',10);
$pdf->Cell(17.5,5,$rc1,1,0,'C');
$pdf->Cell(17.5,5,$rc2,1,0,'C');
$pdf->Cell(17.5,5,$rc3,1,0,'C');
$pdf->Cell(17.5,5,$rc4,1,1,'C');

    }
}
$pdf->SetFillColor(213, 216, 220); 
$pdf->Cell(190,0.5,'',1,1,'C',true);
$pdf->Cell(120,5,'TOTALES',1,0,'R');
$pdf->Cell(17.5,5,$trc1,1,0,'C');
$pdf->Cell(17.5,5,$trc2,1,0,'C');
$pdf->Cell(17.5,5,$trc3,1,0,'C');
$pdf->Cell(17.5,5,$trc4,1,1,'C');
$pdf->Cell(190,5,'',0,1,'C');

$pdf->Ln(5);
$pdf->Cell(190,5,utf8_decode('GRAFICA N° 2 - RENDIMIENTO'),0,1,'C');
$pdf->Cell(190,5,utf8_decode('NIVEL PROFESIONAL'),0,1,'C');
$pdf->Cell(190,5,utf8_decode('PERIODO '.$desde.' - '.$hasta),0,1,'C');
$pdf->Ln(70);

if (empty($trc1) || $trc1 == 0) { $ttwo1=0; } else { $ttwo1=1; }
if (empty($trc2) || $trc2 == 0) { $ttwo2=0; } else { $ttwo2=1; }
if (empty($trc3) || $trc3 == 0) { $ttwo3=0; } else { $ttwo3=1; }
if (empty($trc4) || $trc4 == 0) { $ttwo4=0; } else { $ttwo4=1; }
$ttwototal = $ttwo1 + $ttwo2 + $ttwo3 + $ttwo4;

if     ($ttwototal == 1) { $pdf->Cell(90); } 
elseif ($ttwototal == 2) { $pdf->Cell(80); } 
elseif ($ttwototal == 3) { $pdf->Cell(65); } 
elseif ($ttwototal == 4) { $pdf->Cell(55); } 
else   { }

if (empty($trc1) || $trc1 == 0) { } else {
$pdf->SetFillColor(255, 105, 97);
$pdf->Cell(5,3,'',1,0,'C',true);
$pdf->Cell(10,3,$trc1,0,0,'C');
$pdf->Cell(10,3,'',0,0,'C');
}

if (empty($trc2) || $trc2 == 0) { } else {
$pdf->SetFillColor(119, 221, 119);
$pdf->Cell(5,3,'',1,0,'C',true);
$pdf->Cell(10,3,$trc2,0,0,'C');
$pdf->Cell(10,3,'',0,0,'C');
}

if (empty($trc3) || $trc3 == 0) { } else {
$pdf->SetFillColor(253, 253, 150);
$pdf->Cell(5,3,'',1,0,'C',true);
$pdf->Cell(10,3,$trc3,0,0,'C');
$pdf->Cell(10,3,'',0,0,'C');
}

if (empty($trc4) || $trc4 == 0) { } else {
$pdf->SetFillColor(132, 182, 244);
$pdf->Cell(5,3,'',1,0,'C',true);
$pdf->Cell(10,3,$trc4,0,1,'C');
}

/*------------------------------------------------------------------------------------*/


$pdf->SetFont('Times','',12);
$pdf->AddPage();

$consulta="SELECT * FROM grafics WHERE nom_niv_eva='NIVEL ADMINISTRATIVO' AND f_desde='$desde' AND f_hasta='$hasta'";
$query=mysqli_query($gra,$consulta);
$requests=mysqli_fetch_array($query);
$img=$requests['variable'];
$pic= 'data://text/plain;base64,'. $img;
$pdf->Image($pic, 60,120,90,0,'png');


$consultatg ="SELECT * FROM datos_generales";
$querytg=mysqli_query($con,$consultatg);
$datotg=mysqli_fetch_array($querytg); 
$pdf->Image($datotg['directorio'], $datotg['horizontal'], $datotg['vertical'], $datotg['tamano']);
$pdf->Ln(5);
$pdf->Cell(0,5,utf8_decode(strtoupper($datotg['membrete'])),0,1,'C');


/*
$pdf->Image('logo_cea_oficios.png', 23, 8, 22);
$pdf->Ln(5);
$pdf->Cell(0,5,utf8_decode(strtoupper('CONTRALORIA DEL ESTADO ARAGUA')),0,1,'C');*/
$pdf->Ln(10);

$pdf->SetFont('Times','',10);
$pdf->Cell(190,5,utf8_decode('TABLA N° 3'),0,1,'C');
$pdf->Cell(190,5,utf8_decode('NIVEL ADMINISTRATIVO'),0,1,'C');
$pdf->Cell(190,5,'PERIODO '.$desde.' - '.$hasta,0,1,'C');
$pdf->Cell(5,5,'',0,1,'C');

$consultacom = "SELECT * FROM competencias WHERE tip_eva = 'NIVEL ADMINISTRATIVO' and perio_i = '$desde' ORDER BY id_c;";
$resultadocom = mysqli_query($con,$consultacom);
$num_resultadoscom = mysqli_num_rows($resultadocom);
if ($num_resultadoscom >= 1) {


$pdf->SetFillColor(213, 216, 220);
$pdf->Cell(10,10,'ID',1,0,'C',true);
$pdf->Cell(110,5,'NIVEL ADMINISTRATIVO',1,0,'C',true);
$pdf->Cell(70,5,'NIVEL DE RENDIMIENTO',1,1,'C',true);
$pdf->Cell(10);
$pdf->Cell(110,5,'COMPETENCIAS',1,0,'C',true);
$pdf->Cell(17.5,5,'0,1 - 12,5',1,0,'C');
$pdf->Cell(17.5,5,'12,6 - 25',1,0,'C');
$pdf->Cell(17.5,5,'25,1 -37,5',1,0,'C');
$pdf->Cell(17.5,5,'37,6 - 50',1,1,'C');

    $trc1=0;$trc2=0;$trc3=0;$trc4=0;
    for ($x=1; $x<=$num_resultadoscom;$x++) {
        $registrocom=mysqli_fetch_array($resultadocom);
        $compe = $registrocom['competencia'];

            $consultacom2 = "SELECT seccion_a.*, seccion_c_ii.* FROM seccion_a inner join seccion_c_ii on seccion_a.id_eva = seccion_c_ii.id_eva_c_ii where tip_com = 'NIVEL ADMINISTRATIVO' and comp_c = '$compe' and p_des = '$desde' ORDER BY ren_c;";

            $resultadocom2 = mysqli_query($con,$consultacom2);
            $num_resultadoscom2 = mysqli_num_rows($resultadocom2);
            $rc1=0;$rc2=0;$rc3=0;$rc4=0;
                for ($h=1; $h<=$num_resultadoscom2;$h++) {
                    $registrocom2=mysqli_fetch_array($resultadocom2);
                     $compe2 = $registrocom2['comp_c'];
                    $ren_com = $registrocom2['ren_c'];

            // CONTAR EVALUACIONES POR COMPETENCIAS
                    if ($ren_com == 1) {$rc1 = $rc1 + 1;}
                    if ($ren_com == 2) {$rc2 = $rc2 + 1;}
                    if ($ren_com == 3) {$rc3 = $rc3 + 1;}
                    if ($ren_com == 4) {$rc4 = $rc4 + 1;}

            }   // fin 2do for
                    $trc1 = $trc1 + $rc1;
                    $trc2 = $trc2 + $rc2;
                    $trc3 = $trc3 + $rc3;
                    $trc4 = $trc4 + $rc4;

$pdf->Cell(10,5,$x,1,0,'C');
$newcompe=$compe;
$pcausa= explode(':',$newcompe);

$pdf->SetFont('Times','',8.5);
$pdf->Cell(110,5,$pcausa[0],1,0,'J');
$pdf->SetFont('Times','',10);
$pdf->Cell(17.5,5,$rc1,1,0,'C');
$pdf->Cell(17.5,5,$rc2,1,0,'C');
$pdf->Cell(17.5,5,$rc3,1,0,'C');
$pdf->Cell(17.5,5,$rc4,1,1,'C');

    }
}
$pdf->SetFillColor(213, 216, 220); 
$pdf->Cell(190,0.5,'',1,1,'C',true);
$pdf->Cell(120,5,'TOTALES',1,0,'R');
$pdf->Cell(17.5,5,$trc1,1,0,'C');
$pdf->Cell(17.5,5,$trc2,1,0,'C');
$pdf->Cell(17.5,5,$trc3,1,0,'C');
$pdf->Cell(17.5,5,$trc4,1,1,'C');
$pdf->Cell(190,5,'',0,1,'C');
$pdf->SetFont('Times','',10);
$pdf->SetFillColor(255, 255, 255); 

$pdf->Ln(5);
$pdf->Cell(190,5,utf8_decode('GRAFICA N° 3 - RENDIMIENTO'),0,1,'C');
$pdf->Cell(190,5,utf8_decode('NIVEL ADMINISTRATIVO'),0,1,'C');
$pdf->Cell(190,5,utf8_decode('PERIODO '.$desde.' - '.$hasta),0,1,'C');
$pdf->Ln(70);

if (empty($trc1) || $trc1 == 0) { $tthree1=0; } else { $tthree1=1; }
if (empty($trc2) || $trc2 == 0) { $tthree2=0; } else { $tthree2=1; }
if (empty($trc3) || $trc3 == 0) { $tthree3=0; } else { $tthree3=1; }
if (empty($trc4) || $trc4 == 0) { $tthree4=0; } else { $tthree4=1; }
$tthreetotal = $tthree1 + $tthree2 + $tthree3 + $tthree4;

if     ($tthreetotal == 1) { $pdf->Cell(90); } 
elseif ($tthreetotal == 2) { $pdf->Cell(80); } 
elseif ($tthreetotal == 3) { $pdf->Cell(65); } 
elseif ($tthreetotal == 4) { $pdf->Cell(55); } 
else   { }

if (empty($trc1) || $trc1 == 0) { } else {
$pdf->SetFillColor(255, 105, 97);
$pdf->Cell(5,3,'',1,0,'C',true);
$pdf->Cell(10,3,$trc1,0,0,'C');
$pdf->Cell(10,3,'',0,0,'C');
}

if (empty($trc2) || $trc2 == 0) { } else {
$pdf->SetFillColor(119, 221, 119);
$pdf->Cell(5,3,'',1,0,'C',true);
$pdf->Cell(10,3,$trc2,0,0,'C');
$pdf->Cell(10,3,'',0,0,'C');
}

if (empty($trc3) || $trc3 == 0) { } else {
$pdf->SetFillColor(253, 253, 150);
$pdf->Cell(5,3,'',1,0,'C',true);
$pdf->Cell(10,3,$trc3,0,0,'C');
$pdf->Cell(10,3,'',0,0,'C');
}

if (empty($trc4) || $trc4 == 0) { } else {
$pdf->SetFillColor(132, 182, 244);
$pdf->Cell(5,3,'',1,0,'C',true);
$pdf->Cell(10,3,$trc4,0,1,'C');
}

/*------------------------------------------------------------------------------------*/

$pdf->SetFont('Times','',12);
$pdf->AddPage();

$consulta="SELECT * FROM grafics WHERE nom_niv_eva='NIVEL OBRERO' AND f_desde='$desde' AND f_hasta='$hasta'";
$query=mysqli_query($gra,$consulta);
$requests=mysqli_fetch_array($query);
$img=$requests['variable'];
$pic= 'data://text/plain;base64,'. $img;
$pdf->Image($pic, 60,105,90,0,'png');


$consultatg ="SELECT * FROM datos_generales";
$querytg=mysqli_query($con,$consultatg);
$datotg=mysqli_fetch_array($querytg); 
$pdf->Image($datotg['directorio'], $datotg['horizontal'], $datotg['vertical'], $datotg['tamano']);
$pdf->Ln(5);
$pdf->Cell(0,5,utf8_decode(strtoupper($datotg['membrete'])),0,1,'C');


/*
$pdf->Image('logo_cea_oficios.png', 23, 8, 22);
$pdf->Ln(5);
$pdf->Cell(0,5,utf8_decode(strtoupper('CONTRALORIA DEL ESTADO ARAGUA')),0,1,'C');*/
$pdf->Ln(10);

$pdf->SetFont('Times','',10);
$pdf->Cell(190,5,utf8_decode('TABLA N° 4'),0,1,'C');
$pdf->Cell(190,5,utf8_decode('NIVEL OBRERO'),0,1,'C');
$pdf->Cell(190,5,'PERIODO '.$desde.' - '.$hasta,0,1,'C');
$pdf->Cell(5,5,'',0,1,'C');


$consultacom = "SELECT * FROM competencias WHERE tip_eva = 'NIVEL OBRERO' and perio_i = '$desde' ORDER BY id_c;";
$resultadocom = mysqli_query($con,$consultacom);
$num_resultadoscom = mysqli_num_rows($resultadocom);
if ($num_resultadoscom >= 1) {


$pdf->SetFillColor(213, 216, 220);
$pdf->Cell(10,10,'ID',1,0,'C',true);
$pdf->Cell(110,5,'NIVEL OBRERO',1,0,'C',true);
$pdf->Cell(70,5,'NIVEL DE RENDIMIENTO',1,1,'C',true);
$pdf->Cell(10);
$pdf->Cell(110,5,'COMPETENCIAS',1,0,'C',true);
$pdf->Cell(17.5,5,'0,1 - 12,5',1,0,'C');
$pdf->Cell(17.5,5,'12,6 - 25',1,0,'C');
$pdf->Cell(17.5,5,'25,1 -37,5',1,0,'C');
$pdf->Cell(17.5,5,'37,6 - 50',1,1,'C');

    $trc1=0;$trc2=0;$trc3=0;$trc4=0;
    for ($x=1; $x<=$num_resultadoscom;$x++) {
        $registrocom=mysqli_fetch_array($resultadocom);
        $compe = $registrocom['competencia'];

            $consultacom2 = "SELECT seccion_a.*, seccion_c_ii.* FROM seccion_a inner join seccion_c_ii on seccion_a.id_eva = seccion_c_ii.id_eva_c_ii where tip_com = 'NIVEL OBRERO' and comp_c = '$compe' and p_des = '$desde' ORDER BY ren_c;";

            $resultadocom2 = mysqli_query($con,$consultacom2);
            $num_resultadoscom2 = mysqli_num_rows($resultadocom2);
            $rc1=0;$rc2=0;$rc3=0;$rc4=0;
                for ($h=1; $h<=$num_resultadoscom2;$h++) {
                    $registrocom2=mysqli_fetch_array($resultadocom2);
                     $compe2 = $registrocom2['comp_c'];
                    $ren_com = $registrocom2['ren_c'];

            // CONTAR EVALUACIONES POR COMPETENCIAS
                    if ($ren_com == 1) {$rc1 = $rc1 + 1;}
                    if ($ren_com == 2) {$rc2 = $rc2 + 1;}
                    if ($ren_com == 3) {$rc3 = $rc3 + 1;}
                    if ($ren_com == 4) {$rc4 = $rc4 + 1;}

            }   // fin 2do for
                    $trc1 = $trc1 + $rc1;
                    $trc2 = $trc2 + $rc2;
                    $trc3 = $trc3 + $rc3;
                    $trc4 = $trc4 + $rc4;

$pdf->Cell(10,5,$x,1,0,'C');
$newcompe=$compe;
$pcausa= explode(':',$newcompe);

$pdf->SetFont('Times','',8.5);
$pdf->Cell(110,5,$pcausa[0],1,0,'J');
$pdf->SetFont('Times','',10);
$pdf->Cell(17.5,5,$rc1,1,0,'C');
$pdf->Cell(17.5,5,$rc2,1,0,'C');
$pdf->Cell(17.5,5,$rc3,1,0,'C');
$pdf->Cell(17.5,5,$rc4,1,1,'C');

    }
}
$pdf->SetFillColor(213, 216, 220); 
$pdf->Cell(190,0.5,'',1,1,'C',true);
$pdf->Cell(120,5,'TOTALES',1,0,'R');
$pdf->Cell(17.5,5,$trc1,1,0,'C');
$pdf->Cell(17.5,5,$trc2,1,0,'C');
$pdf->Cell(17.5,5,$trc3,1,0,'C');
$pdf->Cell(17.5,5,$trc4,1,1,'C');
$pdf->Cell(190,5,'',0,1,'C');

$pdf->Ln(5);
$pdf->Cell(190,5,utf8_decode('GRAFICA N° 4 - RENDIMIENTO'),0,1,'C');
$pdf->Cell(190,5,utf8_decode('NIVEL OBRERO'),0,1,'C');
$pdf->Cell(190,5,utf8_decode('PERIODO '.$desde.' - '.$hasta),0,1,'C');
$pdf->Ln(70);

if (empty($trc1) || $trc1 == 0) { $tfour1=0; } else { $tfour1=1; }
if (empty($trc2) || $trc2 == 0) { $tfour2=0; } else { $tfour2=1; }
if (empty($trc3) || $trc3 == 0) { $tfour3=0; } else { $tfour3=1; }
if (empty($trc4) || $trc4 == 0) { $tfour4=0; } else { $tfour4=1; }
$tfourtotal = $tfour1 + $tfour2 + $tfour3 + $tfour4;

if     ($tfourtotal == 1) { $pdf->Cell(90); } 
elseif ($tfourtotal == 2) { $pdf->Cell(80); } 
elseif ($tfourtotal == 3) { $pdf->Cell(65); } 
elseif ($tfourtotal == 4) { $pdf->Cell(55); } 
else   { }

if (empty($trc1) || $trc1 == 0) { } else {
$pdf->SetFillColor(255, 105, 97);
$pdf->Cell(5,3,'',1,0,'C',true);
$pdf->Cell(10,3,$trc1,0,0,'C');
$pdf->Cell(10,3,'',0,0,'C');
}

if (empty($trc2) || $trc2 == 0) { } else {
$pdf->SetFillColor(119, 221, 119);
$pdf->Cell(5,3,'',1,0,'C',true);
$pdf->Cell(10,3,$trc2,0,0,'C');
$pdf->Cell(10,3,'',0,0,'C');
}

if (empty($trc3) || $trc3 == 0) { } else {
$pdf->SetFillColor(253, 253, 150);
$pdf->Cell(5,3,'',1,0,'C',true);
$pdf->Cell(10,3,$trc3,0,0,'C');
$pdf->Cell(10,3,'',0,0,'C');
}

if (empty($trc4) || $trc4 == 0) { } else {
$pdf->SetFillColor(132, 182, 244);
$pdf->Cell(5,3,'',1,0,'C',true);
$pdf->Cell(10,3,$trc4,0,1,'C');
}

/*------------------------------------------------------------------------------------*/

$pdf->SetFont('Times','',12);
$pdf->AddPage();

$consulta="SELECT * FROM grafics WHERE nom_niv_eva='TOTAL DE EVALUADOS POR NIVEL' AND f_desde='$desde' AND f_hasta='$hasta'";
$query=mysqli_query($gra,$consulta);
$requests=mysqli_fetch_array($query);
$img=$requests['variable'];
$pic= 'data://text/plain;base64,'. $img;
$pdf->Image($pic, 60,90,90,0,'png');



$consultatg ="SELECT * FROM datos_generales";
$querytg=mysqli_query($con,$consultatg);
$datotg=mysqli_fetch_array($querytg); 
$pdf->Image($datotg['directorio'], $datotg['horizontal'], $datotg['vertical'], $datotg['tamano']);
$pdf->Ln(5);
$pdf->Cell(0,5,utf8_decode(strtoupper($datotg['membrete'])),0,1,'C');


/*
$pdf->Image('logo_cea_oficios.png', 23, 8, 22);
$pdf->Ln(5);
$pdf->Cell(0,5,utf8_decode(strtoupper('CONTRALORIA DEL ESTADO ARAGUA')),0,1,'C');*/
$pdf->Ln(10);

$pdf->SetFont('Times','',10);
$pdf->Cell(190,5,utf8_decode('TABLA N° 5'),0,1,'C');
$pdf->Cell(190,5,utf8_decode('TOTAL DE EVALUADOS POR NIVEL'),0,1,'C');
$pdf->Cell(190,5,'PERIODO '.$desde.' - '.$hasta,0,1,'C');
$pdf->Cell(5,5,'',0,1,'C');


    $consultanoeva = "SELECT * FROM noeva WHERE p_ini = '$desde'";
    $resultadonoeva = mysqli_query($con,$consultanoeva);
    while($row = mysqli_fetch_array($resultadonoeva)) {
            $nuevoi=$row["ingreso"];
            $reposos=$row["reposo"];
            $renuncias=$row["renuncia"];
            $comision=$row["comision"];
            $jubi=$row["jubilado"];
    }
    $totalnoeva = $nuevoi + $reposos + $renuncias + $comision + $jubi;

    
    $consulta="SELECT personal.fichalab.cedu, personal.fichalab.nape, odi.seccion_a.* FROM personal.fichalab
left join odi.seccion_a on personal.fichalab.cedu = odi.seccion_a.ced_eva
where seccion_a.pe_desde = '$desde';";
    $resultado = mysqli_query($con,$consulta);
    $numerodefilas= mysqli_num_rows($resultado);
                    
for($b = 1; $b <= $numerodefilas; $b++) {
        $registro=mysqli_fetch_array($resultado);
        $tip_com = $registro['tip_com'];
            if ($tip_com == "NIVEL SUPERVISOR") {$tot_sup++;}
            if ($tip_com == "NIVEL PROFESIONAL") {$tot_pro++;}
            if ($tip_com == "NIVEL ADMINISTRATIVO") {$tot_adm++;}
            if ($tip_com == "NIVEL OBRERO") {$tot_obr++;}
}
$pdf->SetFillColor(213, 216, 220);
$pdf->Cell(190,5,utf8_decode('TOTAL DE EVALUADOS POR NIVEL'),1,1,'C',true);
$pdf->Cell(60,5,'TOTAL NIVEL SUPERVISOR:',1,0,'C');
$pdf->Cell(130,5,$tot_sup,1,1,'C');
$pdf->Cell(60,5,'TOTAL NIVEL PROFESIONAL:',1,0,'C');
$pdf->Cell(130,5,$tot_pro,1,1,'C');
$pdf->Cell(60,5,'TOTAL NIVEL ADMINISTRATIVO:',1,0,'C');
$pdf->Cell(130,5,$tot_adm,1,1,'C');
$pdf->Cell(60,5,'TOTAL NIVEL OBRERO:',1,0,'C');
$pdf->Cell(130,5,$tot_obr,1,1,'C');
$pdf->SetFillColor(213, 216, 220); 
$pdf->Cell(190,1,'',1,1,'C',true);
$pdf->Cell(60,5,'TOTAL EVALUADOS:',1,0,'C');
$pdf->Cell(130,5,$numerodefilas,1,1,'C');


$pdf->Ln(5);
$pdf->Cell(190,5,utf8_decode('GRAFICA N° 5 - RENDIMIENTO'),0,1,'C');
$pdf->Cell(190,5,utf8_decode('TOTAL DE EVALUADOS POR NIVEL'),0,1,'C');
$pdf->Cell(190,5,utf8_decode('PERIODO '.$desde.' - '.$hasta),0,1,'C');
$pdf->Ln(70);

if (empty($tot_sup) || $tot_sup == 0) { $tfive1=0; } else { $tfive1=1; }
if (empty($tot_pro) || $tot_pro == 0) { $tfive2=0; } else { $tfive2=1; }
if (empty($tot_adm) || $tot_adm == 0) { $tfive3=0; } else { $tfive3=1; }
if (empty($tot_obr) || $tot_obr == 0) { $tfive4=0; } else { $tfive4=1; }
$tfivetotal = $tfive1 + $tfive2 + $tfive3 + $tfive4;

if     ($tfivetotal == 1) { $pdf->Cell(90); } 
elseif ($tfivetotal == 2) { $pdf->Cell(80); } 
elseif ($tfivetotal == 3) { $pdf->Cell(65); } 
elseif ($tfivetotal == 4) { $pdf->Cell(55); } 
else   { }

if (empty($tot_sup) || $tot_sup == 0) { } else {
$pdf->SetFillColor(255, 105, 97);
$pdf->Cell(5,3,'',1,0,'C',true);
$pdf->Cell(10,3,$tot_sup,0,0,'C');
$pdf->Cell(10,3,'',0,0,'C');
}

if (empty($tot_pro) || $tot_pro == 0) { } else {
$pdf->SetFillColor(119, 221, 119);
$pdf->Cell(5,3,'',1,0,'C',true);
$pdf->Cell(10,3,$tot_pro,0,0,'C');
$pdf->Cell(10,3,'',0,0,'C');
}

if (empty($tot_adm) || $tot_adm == 0) { } else {
$pdf->SetFillColor(253, 253, 150);
$pdf->Cell(5,3,'',1,0,'C',true);
$pdf->Cell(10,3,$tot_adm,0,0,'C');
$pdf->Cell(10,3,'',0,0,'C');
}

if (empty($tot_obr) || $tot_obr == 0) { } else { 
$pdf->SetFillColor(132, 182, 244);
$pdf->Cell(5,3,'',1,0,'C',true);
$pdf->Cell(10,3,$tot_obr,0,1,'C');
}


/*------------------------------------------------------------------------------------*/


$pdf->SetFont('Times','',12);
$pdf->AddPage();

$consulta="SELECT * FROM grafics WHERE nom_niv_eva='PERSONAL NO EVALUADO' AND f_desde='$desde' AND f_hasta='$hasta'";
$query=mysqli_query($gra,$consulta);
$requests=mysqli_fetch_array($query);
$img=$requests['variable'];
$pic= 'data://text/plain;base64,'. $img;
$pdf->Image($pic, 60,85,90,0,'png');



$consultatg ="SELECT * FROM datos_generales";
$querytg=mysqli_query($con,$consultatg);
$datotg=mysqli_fetch_array($querytg); 
$pdf->Image($datotg['directorio'], $datotg['horizontal'], $datotg['vertical'], $datotg['tamano']);
$pdf->Ln(5);
$pdf->Cell(0,5,utf8_decode(strtoupper($datotg['membrete'])),0,1,'C');


/*
$pdf->Image('logo_cea_oficios.png', 23, 8, 22);
$pdf->Ln(5);
$pdf->Cell(0,5,utf8_decode(strtoupper('CONTRALORIA DEL ESTADO ARAGUA')),0,1,'C');*/
$pdf->Ln(10);

$pdf->SetFont('Times','',10);
$pdf->Cell(190,5,utf8_decode('TABLA N° 6'),0,1,'C');
$pdf->Cell(190,5,utf8_decode('PERSONAL NO EVALUADO'),0,1,'C');
$pdf->Cell(190,5,'PERIODO '.$desde.' - '.$hasta,0,1,'C');
$pdf->Cell(5,5,'',0,1,'C');


$consultanoeva = "SELECT * FROM noeva WHERE p_ini = '$desde' AND p_fin = '$hasta';";
$resultadonoeva = mysqli_query($con,$consultanoeva);
$num_resultadosnoeva = mysqli_num_rows($resultadonoeva);


$pdf->Ln(5);
$pdf->SetFillColor(213, 216, 220);
$pdf->Cell(190,5,'PERSONAL NO EVALUADO',1,1,'C',true);
$pdf->Cell(10,5,'ID',1,0,'C');
$pdf->Cell(27,5,'Nuevos Ingresos',1,0,'C');
$pdf->Cell(31,5,'Personal de Reposo',1,0,'C');
$pdf->Cell(22,5,'Renuncias',1,0,'C');
$pdf->Cell(32,5,'Comision de Servicio',1,0,'C');
$pdf->Cell(24,5,'Jubilados',1,0,'C');
$pdf->Cell(24,5,'Directivos',1,0,'C');
$pdf->Cell(20,5,'Otros',1,1,'C');

    for ($e=1; $e<=$num_resultadosnoeva;$e++) {
    $registronoeva=mysqli_fetch_array($resultadonoeva);
    $ingreso = $registronoeva['ingreso'];
    $reposo = $registronoeva['reposo'];
    $renuncia = $registronoeva['renuncia'];
    $comision = $registronoeva['comision'];
    $jubilado = $registronoeva['jubilado'];
    $dir_no_eva = $registronoeva['dir_no_eva'];
    $otros = $registronoeva['otros'];

    $total_noeva = $ingreso + $reposo + $renuncia + $comision + $jubilado + $dir_no_eva + $otros;

$pdf->Cell(10,5,$e,1,0,'C');
$pdf->Cell(27,5,$registronoeva['ingreso'],1,0,'C');
$pdf->Cell(31,5,$registronoeva['reposo'],1,0,'C');
$pdf->Cell(22,5,$registronoeva['renuncia'],1,0,'C');
$pdf->Cell(32,5,$registronoeva['comision'],1,0,'C');
$pdf->Cell(24,5,$registronoeva['jubilado'],1,0,'C');
$pdf->Cell(24,5,$registronoeva['dir_no_eva'],1,0,'C');
$pdf->Cell(20,5,$registronoeva['otros'],1,1,'C');
$pdf->SetFillColor(213, 216, 220); 
$pdf->Cell(190,1,'',1,1,'C',true);
$pdf->Cell(27,5,'Total:',1,0,'C');
$pdf->Cell(163,5,$total_noeva,1,1,'C');
}


$pdf->Ln(5);
$pdf->Cell(190,5,utf8_decode('GRAFICA N° 6 - RENDIMIENTO'),0,1,'C');
$pdf->Cell(190,5,utf8_decode('PERSONAL NO EVALUADO'),0,1,'C');
$pdf->Cell(190,5,utf8_decode('PERIODO '.$desde.' - '.$hasta),0,1,'C');
$pdf->Ln(70);


if (empty($registronoeva['ingreso'])  || $registronoeva['ingreso'] == 0)  { 
$tsix1=0; } else { $tsix1=1; }

if (empty($registronoeva['reposo'])   || $registronoeva['reposo'] == 0)   { 
$tsix2=0; } else { $tsix2=1; }

if (empty($registronoeva['renuncia']) || $registronoeva['renuncia'] == 0) { 
$tsix3=0; } else { $tsix3=1; }

if (empty($registronoeva['comision']) || $registronoeva['comision'] == 0) { 
$tsix4=0; } else { $tsix4=1; }

if (empty($registronoeva['jubilado']) || $registronoeva['jubilado'] == 0) { 
$tsix5=0; } else { $tsix5=1; }

if (empty($registronoeva['dir_no_eva']) || $registronoeva['dir_no_eva'] == 0) { 
$tsix6=0; } else { $tsix6=1; }

if (empty($registronoeva['otros']) || $registronoeva['otros'] == 0) { 
$tsix7=0; } else { $tsix7=1; }

$tsixtotal = $tsix1 + $tsix2 + $tsix3 + $tsix4 + $tsix5 + $tsix6 + $tsix7;

if     ($tsixtotal == 1) { $pdf->Cell(90); } 
elseif ($tsixtotal == 2) { $pdf->Cell(80); } 
elseif ($tsixtotal == 3) { $pdf->Cell(65); } 
elseif ($tsixtotal == 4) { $pdf->Cell(55); } 
elseif ($tsixtotal == 5) { $pdf->Cell(40); } 
elseif ($tsixtotal == 6) { $pdf->Cell(30); } 
elseif ($tsixtotal == 7) { $pdf->Cell(20); } 
else   { }


if (empty($registronoeva['ingreso']) || $registronoeva['ingreso'] == 0) { } 
elseif (!empty($registronoeva['ingreso']) || $registronoeva['ingreso'] != 0) {
$pdf->SetFillColor(255, 105, 97); 
$pdf->Cell(5,3,'',1,0,'C',true);
$pdf->Cell(10,3,$registronoeva['ingreso'],0,0,'C');
$pdf->Cell(10,3,'',0,0,'C');
}


if (empty($registronoeva['reposo']) || $registronoeva['reposo'] == 0) { } 
elseif (!empty($registronoeva['reposo']) || $registronoeva['reposo'] != 0) {
$pdf->SetFillColor(119, 221, 119);
$pdf->Cell(5,3,'',1,0,'C',true);
$pdf->Cell(10,3,$registronoeva['reposo'],0,0,'C');
$pdf->Cell(10,3,'',0,0,'C');
}


if (empty($registronoeva['renuncia']) || $registronoeva['renuncia'] == 0) { } 
elseif (!empty($registronoeva['renuncia']) || $registronoeva['renuncia'] != 0) {
$pdf->SetFillColor(253, 253, 150);
$pdf->Cell(5,3,'',1,0,'C',true);
$pdf->Cell(10,3,$registronoeva['renuncia'],0,0,'C');
$pdf->Cell(10,3,'',0,0,'C');
}


if (empty($registronoeva['comision']) || $registronoeva['comision'] == 0) { } 
elseif (!empty($registronoeva['comision']) || $registronoeva['comision'] != 0) {
$pdf->SetFillColor(132, 182, 244);
$pdf->Cell(5,3,'',1,0,'C',true);
$pdf->Cell(10,3,$registronoeva['comision'],0,0,'C');
$pdf->Cell(10,3,'',0,0,'C');
}


if (empty($registronoeva['jubilado']) || $registronoeva['jubilado'] == 0) { } 
elseif (!empty($registronoeva['jubilado']) || $registronoeva['jubilado'] != 0) {
$pdf->SetFillColor(253, 202, 225);
$pdf->Cell(5,3,'',1,0,'C',true);
$pdf->Cell(10,3,$registronoeva['jubilado'],0,0,'C');
$pdf->Cell(10,3,'',0,0,'C');
}


if (empty($registronoeva['dir_no_eva']) || $registronoeva['dir_no_eva'] == 0) { } 
elseif (!empty($registronoeva['dir_no_eva']) || $registronoeva['dir_no_eva'] != 0) {
$pdf->SetFillColor(230, 177, 98);
$pdf->Cell(5,3,'',1,0,'C',true);
$pdf->Cell(10,3,$registronoeva['dir_no_eva'],0,0,'C');
$pdf->Cell(10,3,'',0,0,'C');
}

if (empty($registronoeva['otros']) || $registronoeva['otros'] == 0) { } 
elseif (!empty($registronoeva['otros']) || $registronoeva['otros'] != 0) {
$pdf->SetFillColor(200, 173, 141);
$pdf->Cell(5,3,'',1,0,'C',true);
$pdf->Cell(10,3,$registronoeva['otros'],0,1,'C');
}

/*------------------------------------------------------------------------------------*/

$pdf->SetFont('Times','',12);
$pdf->AddPage();

$consulta="SELECT * FROM grafics WHERE nom_niv_eva='TOTAL DE EVALUADOS POR DESEMPENO' AND f_desde='$desde' AND f_hasta='$hasta'";
$query=mysqli_query($gra,$consulta);
$requests=mysqli_fetch_array($query);
$img=$requests['variable'];
$pic= 'data://text/plain;base64,'. $img;
$pdf->Image($pic, 60,95,90,0,'png');



$consultatg ="SELECT * FROM datos_generales";
$querytg=mysqli_query($con,$consultatg);
$datotg=mysqli_fetch_array($querytg); 
$pdf->Image($datotg['directorio'], $datotg['horizontal'], $datotg['vertical'], $datotg['tamano']);
$pdf->Ln(5);
$pdf->Cell(0,5,utf8_decode(strtoupper($datotg['membrete'])),0,1,'C');


/*
$pdf->Image('logo_cea_oficios.png', 23, 8, 22);
$pdf->Ln(5);
$pdf->Cell(0,5,utf8_decode(strtoupper('CONTRALORIA DEL ESTADO ARAGUA')),0,1,'C');*/
$pdf->Ln(10);

$pdf->SetFont('Times','',10);
$pdf->Cell(190,5,utf8_decode('TABLA N° 7'),0,1,'C');
$pdf->Cell(190,5,utf8_decode('TOTAL DE EVALUADOS POR DESEMPEÑO'),0,1,'C');
$pdf->Cell(190,5,'PERIODO '.$desde.' - '.$hasta,0,1,'C');
$pdf->Cell(5,5,'',0,1,'C');


$consultatotal = "SELECT seccion_a.*, seccion_b.*, seccion_c.* from seccion_a
INNER JOIN seccion_b ON seccion_a.ced_eva=seccion_b.ced_b
INNER JOIN seccion_c ON seccion_a.ced_eva=seccion_c.ced_c
where seccion_a.pe_desde = '$desde'
order by unidad;";
$resultadototal = mysqli_query($con,$consultatotal);
$num_resultadostotal = mysqli_num_rows($resultadototal);
if ($num_resultadostotal >= 1) {
    for ($e=1; $e<=$num_resultadostotal;$e++) {
        $registrot=mysqli_fetch_array($resultadototal);
        $pun_b = $registrot['puntuacion_b'];
        $pun_c = $registrot['puntuacion_c'];
        $total_eva = $pun_b + $pun_c;
        if (($total_eva >= 1) && ($total_eva <=259)) {$resul_eva_def = $resul_eva_def + 1;}
        if (($total_eva >= 260) && ($total_eva <=295)) {$resul_eva_reg = $resul_eva_reg +1;}
        if (($total_eva >= 296) && ($total_eva <=339)) {$resul_eva_bue = $resul_eva_bue +1;}
        if (($total_eva >= 340) && ($total_eva <=419)) {$resul_eva_mb = $resul_eva_mb + 1;}
    }}


$pdf->Ln(5);
$pdf->SetFillColor(213, 216, 220);
$pdf->Cell(190,5,utf8_decode('TOTAL DE EVALUADOS POR DESEMPEÑO'),1,1,'C',true);



$pdf->Cell(60,5,'DEFICIENTE',1,0,'C');
if (empty($resul_eva_def)) {
$pdf->Cell(130,5,'0',1,1,'C');  
} elseif (!empty($resul_eva_def)) {
$pdf->Cell(130,5,$resul_eva_def,1,1,'C');
}


$pdf->Cell(60,5,'REGULAR',1,0,'C');
if (empty($resul_eva_reg)) {
$pdf->Cell(130,5,'0',1,1,'C'); 
} elseif (!empty($resul_eva_reg)) {
$pdf->Cell(130,5,$resul_eva_reg,1,1,'C');
}



$pdf->Cell(60,5,'BUENO',1,0,'C');
if (empty($resul_eva_bue)) {
$pdf->Cell(130,5,'0',1,1,'C'); 
} elseif (!empty($resul_eva_bue)) {
$pdf->Cell(130,5,$resul_eva_bue,1,1,'C');
}



$pdf->Cell(60,5,'MUY BUENO',1,0,'C');
if (empty($resul_eva_mb)) {
$pdf->Cell(130,5,'0',1,1,'C'); 
} elseif (!empty($resul_eva_mb)) {
$pdf->Cell(130,5,$resul_eva_mb,1,1,'C');
}



$t_eva = $resul_eva_def + $resul_eva_reg + $resul_eva_bue + $resul_eva_mb;
$pdf->SetFillColor(213, 216, 220); 
$pdf->Cell(190,1,'',1,1,'C',true);
$pdf->Cell(60,5,'TOTAL EVALUADOS',1,0,'C');
$pdf->Cell(130,5,$t_eva,1,1,'C');

$pdf->Ln(5);
$pdf->Cell(190,5,utf8_decode('GRAFICA N° 7 - RENDIMIENTO'),0,1,'C');
$pdf->Cell(190,5,utf8_decode('TOTAL DE EVALUADOS POR DESEMPEÑO'),0,1,'C');
$pdf->Cell(190,5,utf8_decode('PERIODO '.$desde.' - '.$hasta),0,1,'C');
$pdf->Ln(70);
 

if (empty($resul_eva_def)) { $tseven1=0; } else { $tseven1=1; }
if (empty($resul_eva_reg)) { $tseven2=0; } else { $tseven2=1; }
if (empty($resul_eva_bue)) { $tseven3=0; } else { $tseven3=1; }
if (empty($resul_eva_mb))  { $tseven4=0; } else { $tseven4=1; }

$tseventotal = $tseven1 + $tseven2 + $tseven3 + $tseven4;

if     ($tseventotal == 1) { $pdf->Cell(90); } 
elseif ($tseventotal == 2) { $pdf->Cell(80); } 
elseif ($tseventotal == 3) { $pdf->Cell(65); } 
elseif ($tseventotal == 4) { $pdf->Cell(55); } 
else   { }



if (empty($resul_eva_def)) { } elseif (!empty($resul_eva_def)) {
$pdf->SetFillColor(255, 105, 97);
$pdf->Cell(5,3,'',1,0,'C',true);
$pdf->Cell(10,3,$resul_eva_def,0,0,'C');
$pdf->Cell(10,3,'',0,0,'C');
}


if (empty($resul_eva_reg)) { } elseif (!empty($resul_eva_reg)) {
$pdf->SetFillColor(119, 221, 119);
$pdf->Cell(5,3,'',1,0,'C',true);
$pdf->Cell(10,3,$resul_eva_reg,0,0,'C');
$pdf->Cell(10,3,'',0,0,'C');
}


if (empty($resul_eva_bue)) { } elseif (!empty($resul_eva_bue)) {
$pdf->SetFillColor(253, 253, 150);
$pdf->Cell(5,3,'',1,0,'C',true);
$pdf->Cell(10,3,$resul_eva_bue,0,0,'C');
$pdf->Cell(10,3,'',0,0,'C');
}


if (empty($resul_eva_mb)) { } elseif  (!empty($resul_eva_mb)) {
$pdf->SetFillColor(132, 182, 244);
$pdf->Cell(5,3,'',1,0,'C',true);
$pdf->Cell(10,3,$resul_eva_mb,0,0,'C');
$pdf->Cell(10,3,'',0,0,'C');
}


// config document
$pdf->SetTitle(utf8_decode('ODI - Gráfica'));
$pdf->SetAuthor('Arturo');
$pdf->SetCreator('FPDF Maker');



$pdf->Output();
?>