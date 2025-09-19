<?php
//Inclusión de Archivo Necesario y Conexión
require('vendor/fpdf/fpdf.php');
require('conexion.php');
$con=conectar();
$equipo    = $_POST['equipo'];
$temporada = $_POST['temporada'];
$categoria = $_POST['categoria'];
$valorval = 3;

// Obtener fecha actual
$verificar = mysqli_query($con, "SELECT * FROM report");
$vdta = mysqli_fetch_array($verificar);
$vfecha = $vdta['timeday'];
$vtt=$vfecha;
$entero_vtt = strtotime($vtt);
$ano_vtt = date("Y", $entero_vtt);
$mes_vtt = date("m", $entero_vtt);
$dia_vtt = date("d", $entero_vtt);
$timeday=$dia_vtt.'-'.$mes_vtt.'-'.$ano_vtt;

// Clases de Cabezera y Pie de Pagina
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
$pdf->SetTitle('ASISTENCIA EQUIPOS CLASIFICADOS - Categoria '.$categoria);
$pdf->SetAuthor('Arturo');
$pdf->SetCreator('FPDF Maker');

if ($equipo < 1) {
    $pdf->AddPage();
    $pdf->SetFont('Arial','',14);

    $pdf->Image('../../fondos/pulpo (2).png', 10, 10, 16);
    $pdf->Image('../../fondos/pulpov (2).png', 189, 10, 16);
    $pdf->Cell(0,5,utf8_decode(strtoupper('LIGA RECREATIVA SOFTBALL EDUCADORES DEL ESTADO ARAGUA')),0,1,'C');
    $pdf->Ln(1);
    $pdf->Cell(0,5,utf8_decode(strtoupper('CATEGORIA " '.$categoria.' "')),0,1,'C');
    $pdf->Ln(1);
    $pdf->Cell(0,5,utf8_decode(strtoupper('ASISTENCIA - EQUIPOS CLASIFICADOS')),0,1,'C');
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(0,5,'FECHA: '.$timeday,0,1,'L');
    $pdf->Ln(1);

    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(12.5);
    $pdf->Cell(10,5,utf8_decode(strtoupper('pos')),1,0,'C');
    $pdf->Cell(75,5,utf8_decode(strtoupper('Nombre')),1,0,'C');
    $pdf->Cell(12,5,utf8_decode(strtoupper('as')),1,0,'C');
    $pdf->Cell(75,5,utf8_decode(strtoupper('Equipo')),1,1,'C');

    // Consulta modificada para evitar duplicidad
    if ($valorval > 0) {
        $covl = "SELECT rs.id_player, rs.name_jgstats, rs.id_team, tc.name_team, SUM(rs.a) as total_asistencias
                FROM resumen_stats rs
                JOIN tab_clasf tc ON rs.id_team = tc.id_team AND rs.id_temp = tc.id_temp
                JOIN equipo_estados ee ON tc.id_tab = ee.id_tab
                WHERE rs.id_temp = $temporada
                AND tc.categoria = '$categoria'
                AND ee.estado = 'C'
                AND ee.id_temp = $temporada
                AND rs.a <= 3 
                GROUP BY rs.id_player, rs.name_jgstats, rs.id_team, tc.name_team
                ORDER BY tc.name_team ASC, total_asistencias DESC";
    } else {
        $covl = "SELECT rs.id_player, rs.name_jgstats, rs.id_team, tc.name_team, SUM(rs.a) as total_asistencias
                FROM resumen_stats rs
                JOIN tab_clasf tc ON rs.id_team = tc.id_team AND rs.id_temp = tc.id_temp
                JOIN equipo_estados ee ON tc.id_tab = ee.id_tab
                WHERE rs.id_temp = $temporada
                AND tc.categoria = '$categoria'
                AND ee.estado = 'C'
                AND ee.id_temp = $temporada
                GROUP BY rs.id_player, rs.name_jgstats, rs.id_team, tc.name_team
                ORDER BY tc.name_team ASC, total_asistencias DESC";
    }

    $vais = mysqli_query($con, $covl);
    $asnm = mysqli_num_rows($vais);

    if ($asnm >= 1) {
        $contadorEquipos = 0;
        $equipoActual = null;
        $posicion = 1;

        while ($player = mysqli_fetch_array($vais)) {
            $id_team = $player['id_team'];
            $name_team = $player['name_team'];

            if ($id_team !== $equipoActual) {
                $equipoActual = $id_team;
                $contadorEquipos++;
                
                // Añadir encabezado del equipo
                $pdf->SetFont('Arial','B',10);
                $pdf->Cell(12.5);
                $pdf->Cell(172,5,utf8_decode(strtoupper("EQUIPO: $name_team")),1,1,'C');
            }

            // Imprimir fila de jugador
            $pdf->SetFont('Arial','B',7);
            $pdf->Cell(12.5);
            $pdf->Cell(10,3.5,utf8_decode(strtoupper($posicion)),1,0,'C');
            $pdf->Cell(75,3.5,utf8_decode($player['name_jgstats']),1,0,'C');
            $pdf->Cell(12,3.5,utf8_decode(strtoupper($player['total_asistencias'])),1,0,'C');
            $pdf->Cell(75,3.5,utf8_decode(strtoupper($name_team)),1,1,'C');
            
            $posicion++;
        }
    } else {
        $pdf->Cell(0,10,'No se encontraron jugadores con los criterios especificados',0,1,'C');
    }
} else {
    // Código para un equipo específico
    $pdf->AddPage();
    $pdf->SetFont('Arial','',14);

    $pdf->Image('../../fondos/pulpo (2).png', 10, 10, 16);
    $pdf->Image('../../fondos/pulpov (2).png', 189, 10, 16);
    $pdf->Cell(0,5,utf8_decode(strtoupper('LIGA RECREATIVA SOFTBALL EDUCADORES DEL ESTADO ARAGUA')),0,1,'C');
    $pdf->Ln(1);
    $pdf->Cell(0,5,utf8_decode(strtoupper('CATEGORIA " '.$categoria.' "')),0,1,'C');
    $pdf->Ln(1);
    $pdf->Cell(0,5,utf8_decode(strtoupper('ASISTENCIA - EQUIPO ESPECÍFICO')),0,1,'C');
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(0,5,'FECHA: '.$timeday,0,1,'L');
    $pdf->Ln(1);

    // Obtener nombre del equipo
    $query_equipo = "SELECT name_team FROM tab_clasf WHERE id_team = $equipo AND id_temp = $temporada";
    $result_equipo = mysqli_query($con, $query_equipo);
    $equipo_data = mysqli_fetch_array($result_equipo);
    $nombre_equipo = $equipo_data['name_team'];

    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(12.5);
    $pdf->Cell(10,5,utf8_decode(strtoupper('pos')),1,0,'C');
    $pdf->Cell(75,5,utf8_decode(strtoupper('Nombre')),1,0,'C');
    $pdf->Cell(12,5,utf8_decode(strtoupper('as')),1,0,'C');
    $pdf->Cell(75,5,utf8_decode(strtoupper('Equipo')),1,1,'C');

    // Consulta para un equipo específico - corregida para evitar duplicidad
    if ($valorval > 0) {
        $covl = "SELECT rs.id_player, rs.name_jgstats, SUM(rs.a) as total_asistencias
                FROM resumen_stats rs
                WHERE rs.id_temp = $temporada
                AND rs.id_team = $equipo
                AND rs.a >= $valorval 
                GROUP BY rs.id_player, rs.name_jgstats
                ORDER BY total_asistencias DESC";
    } else {
        $covl = "SELECT rs.id_player, rs.name_jgstats, SUM(rs.a) as total_asistencias
                FROM resumen_stats rs
                WHERE rs.id_temp = $temporada
                AND rs.id_team = $equipo
                GROUP BY rs.id_player, rs.name_jgstats
                ORDER BY total_asistencias DESC";
    }

    $vais = mysqli_query($con, $covl);
    $asnm = mysqli_num_rows($vais);

    if ($asnm >= 1) {
        $posicion = 1;
        while ($player = mysqli_fetch_array($vais)) {
            // Imprimir fila de jugador
            $pdf->SetFont('Arial','B',7);
            $pdf->Cell(12.5);
            $pdf->Cell(10,3.5,utf8_decode(strtoupper($posicion)),1,0,'C');
            $pdf->Cell(75,3.5,utf8_decode($player['name_jgstats']),1,0,'C');
            $pdf->Cell(12,3.5,utf8_decode(strtoupper($player['total_asistencias'])),1,0,'C');
            $pdf->Cell(75,3.5,utf8_decode(strtoupper($nombre_equipo)),1,1,'C');
            
            $posicion++;
        }
    } else {
        $pdf->Cell(0,10,'No se encontraron jugadores con los criterios especificados',0,1,'C');
    }
}

$pdf->Ln(7);
$pdf->Output();
?>