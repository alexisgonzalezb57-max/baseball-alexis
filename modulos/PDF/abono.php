<?php
//Inclusión de Archivo Necesario y Conexión
require('vendor/fpdf/fpdf.php');
require('conexion.php');
$con=conectar();

// Función para determinar la moneda del abono (igual que en list.php)
function determinarMonedaAbono($data_abono) {
    // Si no hay datos, retornar $ por defecto
    if (!$data_abono) {
        return '$';
    }
    
    // Primero, verificar si existe el campo tipo_moneda y tiene valor
    if (isset($data_abono['tipo_moneda']) && !empty($data_abono['tipo_moneda'])) {
        return $data_abono['tipo_moneda'] == 'Bs' ? 'Bs' : '$';
    }
    
    // Si no existe tipo_moneda, usar la lógica anterior basada en mond_*
    $monedas = array(
        isset($data_abono['mond_once']) ? $data_abono['mond_once'] : '$',
        isset($data_abono['mond_second']) ? $data_abono['mond_second'] : '$',
        isset($data_abono['mond_third']) ? $data_abono['mond_third'] : '$',
        isset($data_abono['mond_four']) ? $data_abono['mond_four'] : '$'
    );
    
    $premios_activos = array(
        isset($data_abono['prize_once']) ? $data_abono['prize_once'] : '0',
        isset($data_abono['prize_second']) ? $data_abono['prize_second'] : '0',
        isset($data_abono['prize_third']) ? $data_abono['prize_third'] : '0',
        isset($data_abono['prize_four']) ? $data_abono['prize_four'] : '0'
    );
    
    // 1. Buscar la moneda del primer premio activo
    for ($i = 0; $i < 4; $i++) {
        if ($premios_activos[$i] == '1') {
            return $monedas[$i] == 'Bs' ? 'Bs' : '$';
        }
    }
    
    // 2. Si no hay premios activos, contar monedas
    $count_dolares = 0;
    $count_bolivares = 0;
    
    foreach ($monedas as $moneda) {
        if ($moneda == 'Bs') {
            $count_bolivares++;
        } else {
            $count_dolares++;
        }
    }
    
    // 3. Usar la mayoría, o $ si hay empate o todos están vacíos
    if ($count_bolivares > 0 || $count_dolares > 0) {
        return ($count_bolivares > $count_dolares) ? 'Bs' : '$';
    }
    
    // 4. Si todo está vacío, usar $ por defecto
    return '$';
}

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

// FUNCIÓN PARA OBTENER DATOS DEL ABONO CON VALORES POR DEFECTO
function obtenerDatosAbono($con, $id_temp, $categoria) {
    $revisar = "SELECT * FROM abonos WHERE id_temp = $id_temp AND categoria LIKE '%$categoria%' AND activo = '1'";
    $query = mysqli_query($con, $revisar);
    
    if(mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_array($query);
        
        // Determinar la moneda usando la función
        $moneda_determinada = determinarMonedaAbono($data);
        
        // Asignar valores por defecto si son NULL
        return array(
            'id_abn' => $data['id_abn'] ?? 0,
            'ncantidad' => $data['ncantidad'] ?? 0,
            'prize_four' => $data['prize_four'] ?? '0',
            'cant_four' => $data['cant_four'] ?? 0,
            'mond_four' => $data['mond_four'] ?? '$',
            'prize_once' => $data['prize_once'] ?? '0',
            'cant_once' => $data['cant_once'] ?? 0,
            'mond_once' => $data['mond_once'] ?? '$',
            'prize_second' => $data['prize_second'] ?? '0',
            'cant_second' => $data['cant_second'] ?? 0,
            'mond_second' => $data['mond_second'] ?? '$',
            'prize_third' => $data['prize_third'] ?? '0',
            'cant_third' => $data['cant_third'] ?? 0,
            'mond_third' => $data['mond_third'] ?? '$',
            'tipo_moneda' => $moneda_determinada // Agregar la moneda determinada
        );
    } else {
        // Devolver valores por defecto si no hay registro
        return array(
            'id_abn' => 0,
            'ncantidad' => 0,
            'prize_four' => '0',
            'cant_four' => 0,
            'mond_four' => '$',
            'prize_once' => '0',
            'cant_once' => 0,
            'mond_once' => '$',
            'prize_second' => '0',
            'cant_second' => 0,
            'mond_second' => '$',
            'prize_third' => '0',
            'cant_third' => 0,
            'mond_third' => '$',
            'tipo_moneda' => '$' // Moneda por defecto
        );
    }
}

/*D*/
$pdf->Ln(2);
$val_hord = "SELECT * FROM temporada WHERE activo = 1 AND categoria LIKE '%D%'";
$query_hord = mysqli_query($con, $val_hord);
$obtenerhod = mysqli_fetch_array($query_hord);

// Verificar si hay temporada D activa
if($obtenerhod) {
    $idd = $obtenerhod['id_temp'];
    $catd = $obtenerhod['categoria'];
    
    // Obtener datos del abono con valores por defecto
    $datad = obtenerDatosAbono($con, $idd, $catd);
    
    $nabonod = $datad['ncantidad'];
    $idbonod = $datad['id_abn'];
    $fourd = $datad['prize_four'];
    $cfourd = $datad['cant_four'];
    $mond_fourd = $datad['mond_four'];
    $onced = $datad['prize_once'];
    $conced = $datad['cant_once'];
    $mond_onced = $datad['mond_once'];
    $secondd = $datad['prize_second'];
    $csecondd = $datad['cant_second'];
    $mond_secondd = $datad['mond_second'];
    $thirdd = $datad['prize_third'];
    $cthirdd = $datad['cant_third'];
    $mond_thirdd = $datad['mond_third'];
    $tipo_moneda_d = $datad['tipo_moneda']; // Obtener la moneda determinada
    
    $pdf->Cell(0,5,utf8_decode(strtoupper('CATEGORIA '. $catd)),0,1,'C');
    $pdf->Ln(4);
    
    $pdf->SetFont('Arial','B',9);
    
    // Solo mostrar tabla si hay abonos
    if($nabonod > 0) {
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
                $suma_montosd = 0;
                for ($jd = 1; $jd <= $nabonod; $jd++) { 
                    $monto_actuald = 0;
                    if($idbonod > 0) {
                        $njnd = "SELECT monto FROM monto WHERE id_abn = $idbonod AND id_team = $nttmd AND numero = $jd";
                        $qttd = mysqli_query($con, $njnd);
                        if(mysqli_num_rows($qttd) > 0) {
                            $dopd = mysqli_fetch_array($qttd);
                            $monto_actuald = isset($dopd['monto']) ? $dopd['monto'] : 0;
                        }
                    }
                    $suma_montosd += $monto_actuald;
                    $pdf->Cell(13,5,$monto_actuald,1,0, 'C');
                }
                $pdf->Cell(20,5,$suma_montosd,1,1, 'C');
            }
        }
        
        // Calcular espacio para premiación
        if ($nabonod > 3) {
            $trrrd = $nabonod - 3;
            $vaciod = $trrrd * 13;
            $nadad = 1;
        } elseif ($nabonod == 3) {
            $trrrd = $nabonod - 3;
            $nadad = 0;
            $vaciod = 0;
        } else {
            $vaciod = 0;
            $nadad = 0;
        }
        
        $pdf->Cell(10);
        $pdf->Cell(41,6,utf8_decode('PREMIACIÓN'),0,0,'C');
        $pdf->Cell(20);
        
        if ($vaciod > 0) {
            $pdf->Cell($vaciod,5,'',0,0, 'C');
        }
        
        $pdf->Cell(13,5,utf8_decode(strtoupper('Total')),1,0, 'C');
        
        $totalmented = "SELECT SUM(monto) AS total_final FROM monto 
                       WHERE id_temp = $idd AND categoria LIKE '%$catd%' AND numero <= $nabonod";
        $tratad = mysqli_query($con, $totalmented);
        $datatold = mysqli_fetch_array($tratad);
        $vvvd = isset($datatold['total_final']) ? $datatold['total_final'] : 0;
        // MODIFICADO: Usar la moneda determinada en lugar de '$' fijo
        $pdf->Cell(20,5,$vvvd.' '.$tipo_moneda_d,1,1, 'C');
        
        // Mostrar premiaciones con su moneda correspondiente
        if ($onced == '1' && $conced > 0) {
            $pdf->Cell(10);
            $pdf->Cell(35,5,utf8_decode(strtoupper('1er Lugar')),1,0, 'C');
            $pdf->Cell(13,5,$conced.' '.$mond_onced,1,1, 'C');
        }
        
        if ($secondd == '1' && $csecondd > 0) {
            $pdf->Cell(10);
            $pdf->Cell(35,5,utf8_decode(strtoupper('2do Lugar')),1,0, 'C');
            $pdf->Cell(13,5,$csecondd.' '.$mond_secondd,1,1, 'C');
        }
        
        if ($thirdd == '1' && $cthirdd > 0) {
            $pdf->Cell(10);
            $pdf->Cell(35,5,utf8_decode(strtoupper('3er Lugar')),1,0, 'C');
            $pdf->Cell(13,5,$cthirdd.' '.$mond_thirdd,1,1, 'C');
        }
        
        if ($fourd == '1' && $cfourd > 0) {
            $pdf->Cell(10);
            $pdf->Cell(35,5,utf8_decode(strtoupper('4to Lugar')),1,0, 'C');
            $pdf->Cell(13,5,$cfourd.' '.$mond_fourd,1,1, 'C');
        }
    }
}

/*C*/
$pdf->Ln(10);

$pdf->SetFont('Arial','B',14);
$val_horc = "SELECT * FROM temporada WHERE activo = 1 AND categoria LIKE '%C%'";
$query_horc = mysqli_query($con, $val_horc);
$obtenerhoc = mysqli_fetch_array($query_horc);

// Verificar si hay temporada C activa
if($obtenerhoc) {
    $idc = $obtenerhoc['id_temp'];
    $catc = $obtenerhoc['categoria'];
    
    // Obtener datos del abono con valores por defecto
    $datac = obtenerDatosAbono($con, $idc, $catc);
    
    $nabonoc = $datac['ncantidad'];
    $idbonoc = $datac['id_abn'];
    $fourc = $datac['prize_four'];
    $cfourc = $datac['cant_four'];
    $mond_fourc = $datac['mond_four'];
    $oncec = $datac['prize_once'];
    $concec = $datac['cant_once'];
    $mond_oncec = $datac['mond_once'];
    $secondc = $datac['prize_second'];
    $csecondc = $datac['cant_second'];
    $mond_secondc = $datac['mond_second'];
    $thirdc = $datac['prize_third'];
    $cthirdc = $datac['cant_third'];
    $mond_thirdc = $datac['mond_third'];
    $tipo_moneda_c = $datac['tipo_moneda']; // Obtener la moneda determinada
    
    $pdf->Cell(0,5,utf8_decode(strtoupper('CATEGORIA '. $catc)),0,1,'C');
    $pdf->Ln(4);
    
    $pdf->SetFont('Arial','B',9);
    
    // Solo mostrar tabla si hay abonos
    if($nabonoc > 0) {
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
                $suma_montosc = 0;
                for ($jc = 1; $jc <= $nabonoc; $jc++) { 
                    $monto_actualc = 0;
                    if($idbonoc > 0) {
                        $njnc = "SELECT monto FROM monto WHERE id_abn = $idbonoc AND id_team = $nttmc AND numero = $jc";
                        $qttc = mysqli_query($con, $njnc);
                        if(mysqli_num_rows($qttc) > 0) {
                            $dopc = mysqli_fetch_array($qttc);
                            $monto_actualc = isset($dopc['monto']) ? $dopc['monto'] : 0;
                        }
                    }
                    $suma_montosc += $monto_actualc;
                    $pdf->Cell(13,5,$monto_actualc,1,0, 'C');
                }
                $pdf->Cell(20,5,$suma_montosc,1,1, 'C');
            }
        }
        
        // Calcular espacio para premiación
        if ($nabonoc > 3) {
            $trrrc = $nabonoc - 3;
            $vacioc = $trrrc * 13;
            $nadac = 1;
        } elseif ($nabonoc == 3) {
            $trrrc = $nabonoc - 3;
            $nadac = 0;
            $vacioc = 0;
        } else {
            $vacioc = 0;
            $nadac = 0;
        }
        
        $pdf->Cell(10);
        $pdf->Cell(41,6,utf8_decode('PREMIACIÓN'),0,0,'C');
        $pdf->Cell(20);
        
        if ($vacioc > 0) {
            $pdf->Cell($vacioc,5,'',0,0, 'C');
        }
        
        $pdf->Cell(13,5,utf8_decode(strtoupper('Total')),1,0, 'C');
        
        $totalmentec = "SELECT SUM(monto) AS total_final FROM monto 
                       WHERE id_temp = $idc AND categoria LIKE '%$catc%' AND numero <= $nabonoc";
        $tratac = mysqli_query($con, $totalmentec);
        $datatolc = mysqli_fetch_array($tratac);
        $vvvc = isset($datatolc['total_final']) ? $datatolc['total_final'] : 0;
        // MODIFICADO: Usar la moneda determinada en lugar de '$' fijo
        $pdf->Cell(20,5,$vvvc.' '.$tipo_moneda_c,1,1, 'C');
        
        // Mostrar premiaciones con su moneda correspondiente
        if ($oncec == '1' && $concec > 0) {
            $pdf->Cell(10);
            $pdf->Cell(35,5,utf8_decode(strtoupper('1er Lugar')),1,0, 'C');
            $pdf->Cell(13,5,$concec.' '.$mond_oncec,1,1, 'C');
        }
        
        if ($secondc == '1' && $csecondc > 0) {
            $pdf->Cell(10);
            $pdf->Cell(35,5,utf8_decode(strtoupper('2do Lugar')),1,0, 'C');
            $pdf->Cell(13,5,$csecondc.' '.$mond_secondc,1,1, 'C');
        }
        
        if ($thirdc == '1' && $cthirdc > 0) {
            $pdf->Cell(10);
            $pdf->Cell(35,5,utf8_decode(strtoupper('3er Lugar')),1,0, 'C');
            $pdf->Cell(13,5,$cthirdc.' '.$mond_thirdc,1,1, 'C');
        }
        
        if ($fourc == '1' && $cfourc > 0) {
            $pdf->Cell(10);
            $pdf->Cell(35,5,utf8_decode(strtoupper('4to Lugar')),1,0, 'C');
            $pdf->Cell(13,5,$cfourc.' '.$mond_fourc,1,1, 'C');
        }
    }
}

/*B*/
$pdf->Ln(10);

$val_hor = "SELECT * FROM temporada WHERE activo = 1 AND categoria LIKE '%B%'";
$query_hor = mysqli_query($con, $val_hor);
$obtenerho = mysqli_fetch_array($query_hor);

// Verificar si hay temporada B activa
if($obtenerho) {
    $id = $obtenerho['id_temp'];
    $cat = $obtenerho['categoria'];
    
    // Obtener datos del abono con valores por defecto
    $data = obtenerDatosAbono($con, $id, $cat);
    
    $nabono = $data['ncantidad'];
    $idbono = $data['id_abn'];
    $four = $data['prize_four'];
    $cfour = $data['cant_four'];
    $mond_four = $data['mond_four'];
    $once = $data['prize_once'];
    $conce = $data['cant_once'];
    $mond_once = $data['mond_once'];
    $second = $data['prize_second'];
    $csecond = $data['cant_second'];
    $mond_second = $data['mond_second'];
    $third = $data['prize_third'];
    $cthird = $data['cant_third'];
    $mond_third = $data['mond_third'];
    $tipo_moneda_b = $data['tipo_moneda']; // Obtener la moneda determinada
    
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(0,5,utf8_decode(strtoupper('CATEGORIA '. $cat)),0,1,'C');
    $pdf->Ln(4);
    
    $pdf->SetFont('Arial','B',9);
    
    // Solo mostrar tabla si hay abonos
    if($nabono > 0) {
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
                $suma_montos = 0;
                for ($j = 1; $j <= $nabono; $j++) { 
                    $monto_actual = 0;
                    if($idbono > 0) {
                        $njn = "SELECT monto FROM monto WHERE id_abn = $idbono AND id_team = $nttm AND numero = $j";
                        $qtt = mysqli_query($con, $njn);
                        if(mysqli_num_rows($qtt) > 0) {
                            $dop = mysqli_fetch_array($qtt);
                            $monto_actual = isset($dop['monto']) ? $dop['monto'] : 0;
                        }
                    }
                    $suma_montos += $monto_actual;
                    $pdf->Cell(13,5,$monto_actual,1,0, 'C');
                }
                $pdf->Cell(20,5,$suma_montos,1,1, 'C');
            }
        }
        
        // Calcular espacio para premiación
        if ($nabono > 3) {
            $trrr = $nabono - 3;
            $vacio = $trrr * 13;
            $nada = 1;
        } elseif ($nabono == 3) {
            $trrr = $nabono - 3;
            $nada = 0;
            $vacio = 0;
        } else {
            $vacio = 0;
            $nada = 0;
        }
        
        $pdf->Cell(10);
        $pdf->Cell(41,6,utf8_decode('PREMIACIÓN'),0,0,'C');
        $pdf->Cell(20);
        
        if ($vacio > 0) {
            $pdf->Cell($vacio,5,'',0,0, 'C');
        }
        
        $pdf->Cell(13,5,utf8_decode(strtoupper('Total')),1,0, 'C');
        
        $totalmente = "SELECT SUM(monto) AS total_final FROM monto 
                      WHERE id_temp = $id AND categoria LIKE '%$cat%' AND numero <= $nabono";
        $trata = mysqli_query($con, $totalmente);
        $datatol = mysqli_fetch_array($trata);
        $vvv = isset($datatol['total_final']) ? $datatol['total_final'] : 0;
        // MODIFICADO: Usar la moneda determinada en lugar de '$' fijo
        $pdf->Cell(20,5,$vvv.' '.$tipo_moneda_b,1,1, 'C');
        
        // Mostrar premiaciones con su moneda correspondiente
        if ($once == '1' && $conce > 0) {
            $pdf->Cell(10);
            $pdf->Cell(35,5,utf8_decode(strtoupper('1er Lugar')),1,0, 'C');
            $pdf->Cell(13,5,$conce.' '.$mond_once,1,1, 'C');
        }
        
        if ($second == '1' && $csecond > 0) {
            $pdf->Cell(10);
            $pdf->Cell(35,5,utf8_decode(strtoupper('2do Lugar')),1,0, 'C');
            $pdf->Cell(13,5,$csecond.' '.$mond_second,1,1, 'C');
        }
        
        if ($third == '1' && $cthird > 0) {
            $pdf->Cell(10);
            $pdf->Cell(35,5,utf8_decode(strtoupper('3er Lugar')),1,0, 'C');
            $pdf->Cell(13,5,$cthird.' '.$mond_third,1,1, 'C');
        }
        
        if ($four == '1' && $cfour > 0) {
            $pdf->Cell(10);
            $pdf->Cell(35,5,utf8_decode(strtoupper('4to Lugar')),1,0, 'C');
            $pdf->Cell(13,5,$cfour.' '.$mond_four,1,1, 'C');
        }
    }
}

$pdf->Output();
?>