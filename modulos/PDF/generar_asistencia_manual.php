<?php
require('vendor/fpdf/fpdf.php');
require('../../config/conexion.php');

$con = conectar();

$temporada = isset($_POST['temporada']) ? (int)$_POST['temporada'] : 0;
$categoria = isset($_POST['categoria']) ? $_POST['categoria'] : '';
$ids = $_POST['asistencia'] ?? [];


$verificar = mysqli_query($con, "SELECT * FROM report");
$vdta = mysqli_fetch_array($verificar);
$vfecha = $vdta['timeday'];
$vtt=$vfecha;
$entero_vtt = strtotime($vtt);
$ano_vtt = date("Y", $entero_vtt);
$mes_vtt = date("m", $entero_vtt);
$dia_vtt = date("d", $entero_vtt);
$timeday=$dia_vtt.'-'.$mes_vtt.'-'.$ano_vtt;

if ($temporada == 0 || empty($categoria)) {
    die("Faltan datos válidos de temporada o categoría.");
}

if (empty($ids)) {
    die("No seleccionó ningún jugador.");
}

$ids = array_map('intval', $ids);
$placeholders = implode(',', array_fill(0, count($ids), '?'));

$sql = "SELECT rs.name_jgstats, rs.a AS asistencia, t.name_team
        FROM resumen_stats rs
        JOIN tab_clasf t ON rs.id_team = t.id_team
        WHERE rs.id_player IN ($placeholders)
          AND rs.id_temp = ?
          AND rs.categoria = ?
        GROUP BY rs.name_jgstats, rs.a, t.name_team
        ORDER BY rs.a DESC, rs.name_jgstats ASC";

$stmt = $con->prepare($sql);
if ($stmt === false) {
    die("Error preparando consulta: " . $con->error);
}

$types = str_repeat('i', count($ids)) . 'is';
$params = array_merge($ids, [$temporada, $categoria]);

$tmp = [];
foreach ($params as $key => $value) {
    $tmp[$key] = &$params[$key];
}
call_user_func_array([$stmt, 'bind_param'], array_merge([$types], $tmp));

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows < 1) {
    die("No se encontraron datos para los jugadores seleccionados.");
}

class PDF extends FPDF {
    function Header() {
        $this->Image('../../fondos/pulpo (2).png', 7, 13, 16);
        $this->Image('../../fondos/pulpov (2).png', 194, 13, 16);
        $this->SetFont('Arial','B',15);
        $this->Cell(0, 10, utf8_decode(strtoupper('LIGA RECREATIVA SOFTBALL EDUCADORES DEL ESTADO ARAGUA')), 0, 1, 'C');
        $this->Ln(2);
    }
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,utf8_decode('Page ').$this->PageNo().'/{nb}',0,0,'C');
    }
}

$pdf = new PDF('P','mm','Letter');
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Arial','',14);
$pdf->Cell(0,5,utf8_decode(strtoupper('CATEGORÍA " '.$categoria.' "')),0,1,'C');
$pdf->Ln(1);
$pdf->Cell(0,5,utf8_decode('ASISTENCIA'),0,1,'C');
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,5,'FECHA: '.$timeday,0,1,'L');
$pdf->Ln(5);

$pdf->SetFont('Arial','B',10);
$pdf->Cell(10,7,'No.',1,0,'C');
$pdf->Cell(85,7,utf8_decode('Jugador'),1,0,'C');
$pdf->Cell(30,7,utf8_decode('Asistencias'),1,0,'C');
$pdf->Cell(65,7,utf8_decode('Equipo'),1,1,'C');

$pdf->SetFont('Arial','',10);

$index = 1;
while ($row = $result->fetch_assoc()) {
    $pdf->Cell(10,7,$index++,1,0,'C');
    $pdf->Cell(85,7,utf8_decode($row['name_jgstats']),1);
    $pdf->Cell(30,7,$row['asistencia'],1,0,'C');
    $pdf->Cell(65,7,strtoupper(utf8_decode($row['name_team'])),1,1);
}

$pdf->Output();
