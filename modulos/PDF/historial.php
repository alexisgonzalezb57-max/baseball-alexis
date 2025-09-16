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
        $this->Image('../../fondos/pulpo (2).png', 10, 8, 10);
        $this->Image('../../fondos/pulpov (2).png', 189, 8, 10);
        
        // Título principal (reducido)
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 4, utf8_decode('LIGA RECREATIVA SOFTBALL EDUCADORES ARAGUA'), 0, 1, 'C');
        $this->Ln(1);
        $this->Cell(0, 4, utf8_decode('HISTORIAL - TEMPORADA ' . $GLOBALS['tm']), 0, 1, 'C');
        $this->Ln(1);
        $this->Cell(0, 4, utf8_decode('EQUIPO: ' . strtoupper($GLOBALS['name'])), 0, 1, 'C');
        $this->Ln(2);
        
        // Fecha (reducido)
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(0, 4, 'FECHA: ' . date('d/m/Y'), 0, 1, 'L');
        $this->Ln(2);
    }

    // Pie de página
    function Footer()
    {
        $this->SetY(-10);
        $this->SetFont('Arial', 'I', 7);
        $this->Cell(0, 6, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    // Función para crear tabla de información del juego compacta
    function GameInfoTable($fecha, $numJuego, $equipoElegido, $equipoRival, $puntajeElegido, $puntajeRival, $estado, $valido)
    {
        // Encabezado del juego (compacto) - Ajustado al ancho completo
        $this->SetFillColor(60, 80, 120);
        $this->SetTextColor(255);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(180, 5, utf8_decode("Juego #$numJuego - " . date('d/m/Y', strtotime($fecha))), 1, 1, 'C', true);
        
        // Información de equipos (compacta) - Mejor distribución
        $this->SetFillColor(240, 240, 240);
        $this->SetTextColor(0);
        $this->SetFont('Arial', 'B', 8);
        
        // Nombres de equipos (abreviados si son muy largos)
        $equipoLocal = strlen($equipoElegido) > 20 ? substr($equipoElegido, 0, 17) . '...' : $equipoElegido;
        $equipoVisitante = strlen($equipoRival) > 20 ? substr($equipoRival, 0, 17) . '...' : $equipoRival;
        
        // Primera fila: Encabezados
        $this->Cell(70, 4, 'LOCAL', 1, 0, 'C', true);
        $this->Cell(20, 4, 'PTS', 1, 0, 'C', true);
        $this->Cell(70, 4, 'VISITANTE', 1, 0, 'C', true);
        $this->Cell(20, 4, 'PTS', 1, 1, 'C', true);
        
        // Segunda fila: Datos de equipos
        $this->SetFont('Arial', '', 8);
        $this->Cell(70, 5, utf8_decode($equipoLocal), 1, 0, 'C');
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(20, 5, $puntajeElegido, 1, 0, 'C');
        $this->SetFont('Arial', '', 8);
        $this->Cell(70, 5, utf8_decode($equipoVisitante), 1, 0, 'C');
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(20, 5, $puntajeRival, 1, 1, 'C');
        
        // Tercera fila: Información adicional - Mejor distribuida
        $this->SetFont('Arial', 'B', 7);
        $this->SetFillColor(220, 220, 220);
        
        // Estado
        $this->Cell(30, 4, 'ESTADO', 1, 0, 'C', true);
        $this->SetFont('Arial', '', 7);
        $estadoAbreviado = strlen($estado) > 12 ? substr($estado, 0, 9) . '...' : $estado;
        $this->Cell(40, 4, utf8_decode($estadoAbreviado), 1, 0, 'C');
        
        // Válido
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(25, 4, 'VÁLIDO', 1, 0, 'C', true);
        $this->SetFont('Arial', '', 7);
        $this->Cell(25, 4, $valido ? 'SI' : 'NO', 1, 0, 'C');
        
        // Resultado
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(30, 4, 'RESULTADO', 1, 0, 'C', true);
        
        // Valor del resultado con color
        $this->SetFont('Arial', 'B', 8);
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
        
        $this->Cell(30, 4, $resultado, 1, 1, 'C', true);
        
        $this->Ln(2);
    }
}

// Crear PDF
$pdf = new PDF('P','mm','Letter');
$pdf->AliasNbPages();
$pdf->AddPage();

// Configuración con márgenes ajustados
$pdf->SetMargins(10, 12, 10);
$pdf->SetAutoPageBreak(true, 10);

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
    if ($gameCount < count($historial)) {
        $pdf->SetDrawColor(200, 200, 200);
        $pdf->SetLineWidth(0.1);
        $pdf->Line(10, $pdf->GetY() - 1, 200, $pdf->GetY() - 1);
        $pdf->Ln(1);
    }
}

$pdf->Output();
?>