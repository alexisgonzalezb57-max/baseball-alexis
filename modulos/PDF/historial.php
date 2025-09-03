<?php
//Inclusión de Archivo Necesario y Conexión
require('vendor/fpdf/fpdf.php');
require('conexion.php');
$con = conectar();

$it = $_REQUEST['equipo'];
$tm = $_REQUEST['temporada'];
$ct = $_REQUEST['categoria'];
$team = "SELECT * FROM equipos WHERE id_team = $it";
$rest = mysqli_query($con, $team);
$fetc = mysqli_fetch_array($rest);
$name = $fetc['nom_team'];

class PDF extends FPDF
{
    // Cabecera de página
    function Header()
    {
        // Logo
        $this->Image('../../fondos/pulpo (2).png', 10, 8, 12);
        $this->Image('../../fondos/pulpov (2).png', 189, 8, 12);
        
        // Título principal (aumentado +2 puntos)
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 5, utf8_decode('LIGA RECREATIVA SOFTBALL EDUCADORES ARAGUA'), 0, 1, 'C');
        $this->Ln(2);
        $this->Cell(0, 5, utf8_decode('HISTORIAL - TEMPORADA ' . $GLOBALS['tm']), 0, 1, 'C');
        $this->Ln(2);
        $this->Cell(0, 5, utf8_decode('EQUIPO: ' . strtoupper($GLOBALS['name'])), 0, 1, 'C');
        $this->Ln(4);
        
        // Fecha (aumentado +2 puntos)
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 5, 'FECHA: ' . date('d/m/Y'), 0, 1, 'L');
        $this->Ln(5);
    }

    // Pie de página
    function Footer()
    {
        $this->SetY(-12);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 8, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    // Función para crear tabla de información del juego con fuentes aumentadas
    function GameInfoTable($fecha, $numJuego, $equipoElegido, $equipoRival, $puntajeElegido, $puntajeRival, $estado, $valido)
    {
        // Encabezado del juego (aumentado +2 puntos)
        $this->SetFillColor(60, 80, 120);
        $this->SetTextColor(255);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 7, utf8_decode("Juego #$numJuego - " . date('d/m/Y', strtotime($fecha))), 1, 1, 'C', true);
        $this->Ln(3);
        
        // Información de equipos (aumentado +2 puntos)
        $this->SetFillColor(240, 240, 240);
        $this->SetTextColor(0);
        $this->SetFont('Arial', 'B', 10);
        
        // Nombres de equipos (abreviados si son muy largos)
        $equipoLocal = strlen($equipoElegido) > 20 ? substr($equipoElegido, 0, 17) . '...' : $equipoElegido;
        $equipoVisitante = strlen($equipoRival) > 20 ? substr($equipoRival, 0, 17) . '...' : $equipoRival;
        
        $this->Cell(95, 6, 'LOCAL', 1, 0, 'C', true);
        $this->Cell(95, 6, 'VISITANTE', 1, 1, 'C', true);
        
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(95, 7, utf8_decode($equipoLocal), 1, 0, 'C');
        $this->Cell(95, 7, utf8_decode($equipoVisitante), 1, 1, 'C');
        
        // Marcador (aumentado +3 puntos)
        $this->SetFont('Arial', 'B', 17);
        $this->SetFillColor(250, 250, 250);
        $this->Cell(95, 10, $puntajeElegido, 1, 0, 'C', true);
        $this->Cell(95, 10, $puntajeRival, 1, 1, 'C', true);
        
        $this->Ln(3);
        
        // Información adicional (aumentado +2-3 puntos)
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(220, 220, 220);
        
        // Primera fila de info
        $this->Cell(47.5, 6, 'ESTADO', 1, 0, 'C', true);
        $this->SetFont('Arial', '', 9);
        $this->Cell(47.5, 6, utf8_decode($estado), 1, 0, 'C');
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(47.5, 6, utf8_decode('VÁLIDO'), 1, 0, 'C', true);
        $this->SetFont('Arial', '', 9);
        $this->Cell(47.5, 6, $valido ? 'SI' : 'NO', 1, 1, 'C');
        
        // Segunda fila de info
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(47.5, 6, 'FECHA', 1, 0, 'C', true);
        $this->SetFont('Arial', '', 9);
        $this->Cell(47.5, 6, date('d/m/Y', strtotime($fecha)), 1, 0, 'C');
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(47.5, 6, utf8_decode('N° JUEGO'), 1, 0, 'C', true);
        $this->SetFont('Arial', '', 9);
        $this->Cell(47.5, 6, $numJuego, 1, 1, 'C');
        
        // Resultado final (aumentado +2 puntos)
        $this->Ln(3);
        $this->SetFont('Arial', 'B', 11);
        
        if ($puntajeElegido > $puntajeRival) {
            $this->SetFillColor(220, 255, 220);
            $resultado = 'VICTORIA';
        } elseif ($puntajeElegido < $puntajeRival) {
            $this->SetFillColor(255, 220, 220);
            $resultado = 'DERROTA';
        } else {
            $this->SetFillColor(255, 255, 200);
            $resultado = 'EMPATE';
        }
        
        $this->Cell(0, 8, $resultado . ' (' . $puntajeElegido . '-' . $puntajeRival . ')', 1, 1, 'C', true);
        
        $this->Ln(7);
    }
}

// Crear PDF
$pdf = new PDF('P','mm','Letter');
$pdf->AliasNbPages();
$pdf->AddPage();

// Configuración con márgenes ajustados
$pdf->SetMargins(10, 15, 10);
$pdf->SetAutoPageBreak(true, 12);

// config document
$pdf->SetTitle(utf8_decode('Historial: '.$name));
$pdf->SetAuthor('Arturo');
$pdf->SetCreator('FPDF Maker');

// Obtener datos de juegos
$historial = [];
$sql = "SELECT * FROM juegos WHERE team_one = $it AND id_temp = $tm ORDER BY nj ASC";
$result = mysqli_query($con, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    $ht = $row['team_one'];
    $ft = $row['team_two'];

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
        'estado' => $row['estado'],
        'valido' => $row['valido']
    ];
}

// Recorrer y agregar info
$gameCount = 0;
foreach ($historial as $juego) {
    // Verificar si necesitamos nueva página (cada 3 juegos)
    if ($gameCount % 3 == 0 && $gameCount > 0) {
        $pdf->AddPage();
    }
    
    $pdf->GameInfoTable(
        $juego['fecha'],
        $juego['numJuego'],
        $juego['equipoElegido'],
        $juego['equipoRival'],
        $juego['puntajeElegido'],
        $juego['puntajeRival'],
        $juego['estado'],
        $juego['valido']
    );
    
    $gameCount++;
    
    // Línea separadora solo entre juegos, no después del último
    if ($gameCount < count($historial) && $gameCount % 3 != 0) {
        $pdf->SetDrawColor(200, 200, 200);
        $pdf->SetLineWidth(0.2);
        $pdf->Line(15, $pdf->GetY() - 4, 195, $pdf->GetY() - 4);
        $pdf->Ln(5);
    }
}

$pdf->Output();
?>