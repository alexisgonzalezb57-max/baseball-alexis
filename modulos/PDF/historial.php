<?php
//Inclusión de Archivo Necesario y Conexión
require('vendor/fpdf/fpdf.php');
require('conexion.php');
$con=conectar();

$it = $_REQUEST['equipo'];
$tm = $_REQUEST['temporada'];
$ct = $_REQUEST['categoria'];
$team = "SELECT * FROM equipos WHERE id_team = $it";
$rest = mysqli_query($con, $team);
$fetc = mysqli_fetch_array($rest);
$name = $fetc['nom_team'];


class PDF extends FPDF
{
    // Función para facilitar el dibujo del historial
    function GameHistoryRow($fecha, $campo, $numJuego, $equipoElegido, $equipoRival, $puntajeElegido, $puntajeRival, $estado)
    {
        // Fecha, campo y número de juego (una línea)
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(40, 7, "Fecha: $fecha", 0, 0);
        //$this->Cell(40, 7, "Campo: $campo", 0, 0);
        $this->Cell(40, 7, "Juego #$numJuego", 0, 1);
        
        // Equipos (siguiente línea)
        $this->SetFont('Arial', '', 12);
        $this->Cell(60, 6, "Equipo: $equipoElegido", 0, 0);
        $this->Cell(60, 6, "Contra: $equipoRival", 0, 1);

        // Puntajes (siguiente línea)
        $this->Cell(60, 6, "Puntaje: $puntajeElegido", 0, 0);
        $this->Cell(60, 6, "Puntaje: $puntajeRival", 0, 1);

        // Estado del partido (siguiente línea)
        // Colores por estado
        if (strtolower($estado) == 'Ganando') {
            $this->SetTextColor(0, 128, 0); // verde
        } elseif (strtolower($estado) == 'Perdido') {
            $this->SetTextColor(255, 0, 0); // rojo
        } else {
            $this->SetTextColor(0, 0, 255); // azul para empate
        }

        $this->Cell(60, 7, "Estado: $estado", 0, 1);

        // Reset color a negro para siguientes líneas
        $this->SetTextColor(0, 0, 0);

        // Línea separadora
        $this->Ln(4);
        $this->Cell(0, 0, '', 'T');
        $this->Ln(6);
    }
}

// Crear PDF
$pdf = new PDF('P','mm','Letter');
$pdf->AddPage();

// config document
$pdf->SetTitle(utf8_decode('Historial del Equipo: '.$name));
$pdf->SetAuthor('Arturo');
$pdf->SetCreator('FPDF Maker');


$pdf->SetFont('Arial','',14);

$pdf->Image('../../fondos/pulpo (2).png', 10, 10, 16);
$pdf->Image('../../fondos/pulpov (2).png', 189, 10, 16);
$pdf->Cell(0,5,utf8_decode(strtoupper('LIGA RECREATIVA SOFTBALL EDUCADORES DEL ESTADO ARAGUA')),0,1,'C');
$pdf->Ln(2);
$pdf->Cell(0,5,utf8_decode(strtoupper('EQUIPO " '.$categoria.' "')),0,1,'C');
$pdf->Ln(2);
$pdf->Cell(0,5,utf8_decode('HISTORIAL DE JUEGOS'),0,1,'C');

$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,5,'FECHA: '.$timeday,0,1,'L');
$pdf->SetFont('Arial','B',14);
$pdf->Ln(2);


$historial = [];

$sql = "SELECT * FROM juegos WHERE team_one = $it AND id_temp = $tm ORDER BY nj ASC";
$result = mysqli_query($con, $sql);

// Ejemplo de datos: podrías traerlos de base de datos
while ($row = mysqli_fetch_assoc($result)) {
$ht = $row['team_one'];
$ft = $row['team_two'];
$pl = $row['fech_part'];

$tenemy = "SELECT * FROM equipos WHERE id_team = $ft";
$vart = mysqli_query($con, $tenemy);
$nym = mysqli_fetch_array($vart);


    $historial[] = [
        'fecha' => $row['fech_part'],
        'numJuego' => (int)$row['nj'],
        'equipoElegido' => $name,
        'equipoRival' => $nym['nom_team'],
        'puntajeElegido' => (int)$row['ca'],
        'puntajeRival' => (int)$row['ce'],
        'estado' => $row['estado']
    ];
}

// Recorrer y agregar info
foreach ($historial as $juego){
    $pdf->GameHistoryRow(
        $juego['fecha'],
        $juego['campo'],
        $juego['numJuego'],
        $juego['equipoElegido'],
        $juego['equipoRival'],
        $juego['puntajeElegido'],
        $juego['puntajeRival'],
        $juego['estado']
    );
}

$pdf->Output();
?>
