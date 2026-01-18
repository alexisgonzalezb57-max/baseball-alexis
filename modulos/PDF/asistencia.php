<?php
//Inclusión de Archivo Necesario y Conexión
require('vendor/fpdf/fpdf.php');
require('conexion.php');
$con=conectar();
$equipo    = $_POST['equipo'];
$temporada = $_POST['temporada'];
$categoria = $_POST['categoria'];
$valorval = $_POST['valorval'];


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
$pdf->SetTitle('ASISTENCIA - Categoria '.$categoria);
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
$pdf->Cell(0,5,utf8_decode(strtoupper('ASISTENCIA')),0,1,'C');
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,5,'FECHA: '.$timeday,0,1,'L');
$pdf->Ln(1);



$pdf->SetFont('Arial','B',10);

$pdf->Cell(12.5);
$pdf->Cell(10,5,utf8_decode(strtoupper('pos')),1,0,'C');
$pdf->Cell(75,5,utf8_decode(strtoupper('Nombre')),1,0,'C');
$pdf->Cell(12,5,utf8_decode(strtoupper('as')),1,0,'C');
$pdf->Cell(75,5,utf8_decode(strtoupper('Equipo')),1,1,'C');

if ($valorval > 0) {

$covl = "SELECT rs.*, MAX(rs.a) as max_asistencias
FROM resumen_stats rs
JOIN (
    SELECT id_team, SUM(a) AS total_asistencias
    FROM resumen_stats
    WHERE id_temp = $temporada
    GROUP BY id_team
) AS team_totals ON rs.id_team = team_totals.id_team
WHERE rs.id_temp = $temporada
  AND rs.a >= $valorval 
GROUP BY rs.name_jgstats
ORDER BY team_totals.total_asistencias DESC, rs.id_team ASC, max_asistencias DESC;";

} else {

$covl = "SELECT rs.*, MAX(rs.a) as max_asistencias
FROM resumen_stats rs
JOIN (
    SELECT id_team, SUM(a) AS total_asistencias
    FROM resumen_stats
    WHERE id_temp = $temporada
    GROUP BY id_team
) AS team_totals ON rs.id_team = team_totals.id_team
WHERE rs.id_temp = $temporada
GROUP BY rs.name_jgstats
ORDER BY team_totals.total_asistencias DESC, rs.id_team ASC, max_asistencias DESC;";

}

$vais = mysqli_query($con, $covl);
$asnm = mysqli_num_rows($vais);

if ($asnm >= 1) {
    $contadorEquipos = 0;
    $equipoActual = null;

    for ($jg=1; $jg <= $asnm ; $jg++) { 
        $player = mysqli_fetch_array($vais);
        $id_team = $player['id_team'];

        if ($valorval <= 0) {
            // Aplicar límite de 2 equipos por página
            if ($id_team !== $equipoActual) {
                $equipoActual = $id_team;
                $contadorEquipos++;
                if ($contadorEquipos > 1 && $contadorEquipos % 2 == 1) {
                    $pdf->AddPage();
                    // Repetir encabezado de tabla
                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(12.5);
                    $pdf->Cell(10,5,utf8_decode(strtoupper('pos')),1,0,'C');
                    $pdf->Cell(75,5,utf8_decode(strtoupper('Nombre')),1,0,'C');
                    $pdf->Cell(12,5,utf8_decode(strtoupper('as')),1,0,'C');
                    $pdf->Cell(75,5,utf8_decode(strtoupper('Equipo')),1,1,'C');
                }
            }
        } else {
            // Cuando $valorval > 0 no se limita la cantidad de equipos por página
            $equipoActual = $id_team;
            // No incrementar contador ni salto de página
        }

        // Imprimir fila de jugador
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(12.5);
        $pdf->Cell(10,3.5,utf8_decode(strtoupper($jg)),1,0,'C');
        $pdf->Cell(75,3.5,utf8_decode($player['name_jgstats']),1,0,'C');
        $pdf->Cell(12,3.5,utf8_decode(strtoupper($player['max_asistencias'])),1,0,'C');

        $tabla = "SELECT * FROM tab_clasf WHERE id_team = $id_team";
        $dteg = mysqli_query($con, $tabla);
        $datu = mysqli_fetch_array($dteg);

        $pdf->Cell(75,3.5,utf8_decode(strtoupper($datu['name_team'])),1,1,'C');
    }
} 


} elseif ($equipo >= 1) {

$pdf->AddPage();
$pdf->SetFont('Arial','',14);

$pdf->Image('../../fondos/pulpo (2).png', 10, 10, 16);
$pdf->Image('../../fondos/pulpov (2).png', 189, 10, 16);
$pdf->Cell(0,5,utf8_decode(strtoupper('LIGA RECREATIVA SOFTBALL EDUCADORES DEL ESTADO ARAGUA')),0,1,'C');
$pdf->Ln(2);
$pdf->Cell(0,5,utf8_decode(strtoupper('CATEGORIA " '.$categoria.' "')),0,1,'C');
$pdf->Ln(2);
$pdf->Cell(0,5,utf8_decode(strtoupper('ASISTENCIA')),0,1,'C');
$pdf->Ln(2);



$pdf->SetFont('Arial','B',12);

$pdf->Cell(12.5);
$pdf->Cell(10,6,utf8_decode(strtoupper('pos')),1,0,'C');
$pdf->Cell(75,6,utf8_decode(strtoupper('Nombre')),1,0,'C');
$pdf->Cell(12,6,utf8_decode(strtoupper('as')),1,0,'C');
$pdf->Cell(75,6,utf8_decode(strtoupper('Equipo')),1,1,'C');



if ($valorval > 0) {
    $covl = "SELECT 
                rs.id_team,
                rs.name_jgstats,
                rs.cedula,
                MAX(rs.a) as max_asistencias,
                rs.tnj,
                rs.categoria
            FROM resumen_stats rs
            WHERE rs.id_team = $equipo
              AND rs.id_temp = $temporada
              AND rs.categoria = '$categoria'
              AND rs.a >= $valorval
            GROUP BY rs.cedula, rs.name_jgstats
            ORDER BY max_asistencias DESC;";
} else {
    $covl = "SELECT 
                rs.id_team,
                rs.name_jgstats,
                rs.cedula,
                MAX(rs.a) as max_asistencias,
                rs.tnj,
                rs.categoria
            FROM resumen_stats rs
            WHERE rs.id_team = $equipo
              AND rs.id_temp = $temporada
              AND rs.categoria = '$categoria'
            GROUP BY rs.cedula, rs.name_jgstats
            ORDER BY max_asistencias DESC;";
}

$vais = mysqli_query($con, $covl);
$asnm = mysqli_num_rows($vais);

if ($asnm >= 1) {
for ($jg=1; $jg <= $asnm ; $jg++) { 
$player = mysqli_fetch_array($vais);
$id_team = $player['id_team'];

$pdf->SetFont('Arial','B',9);
$pdf->Cell(12.5);
$pdf->Cell(10,6,utf8_decode(strtoupper($jg)),1,0,'C');
$pdf->Cell(75,6,utf8_decode($player['name_jgstats']),1,0,'C');
$pdf->Cell(12,6,utf8_decode(strtoupper($player['max_asistencias'])),1,0,'C');

$tabla = "SELECT * FROM tab_clasf WHERE id_team = $id_team";
$dteg = mysqli_query($con, $tabla);
$datu = mysqli_fetch_array($dteg);

$pdf->Cell(75,6,utf8_decode(strtoupper($datu['name_team'])),1,1,'C');

} } 


}

$pdf->Ln(7);


$pdf->Output();
?>