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
        $this->Image('../../fondos/pulpo (2).png', 10, 8, 16);
        $this->Image('../../fondos/pulpov (2).png', 189, 8, 16);
        
        // Título principal
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 5, utf8_decode('LIGA RECREATIVA SOFTBALL EDUCADORES DEL ESTADO ARAGUA'), 0, 1, 'C');
        $this->Ln(2);
        $this->Cell(0, 5, utf8_decode('HISTORIAL DE JUEGOS - TEMPORADA ' . $GLOBALS['tm']), 0, 1, 'C');
        $this->Ln(2);
        $this->Cell(0, 5, utf8_decode('EQUIPO: ' . strtoupper($GLOBALS['name'])), 0, 1, 'C');
        $this->Ln(5);
        
        // Fecha
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 5, 'FECHA: ' . date('d/m/Y'), 0, 1, 'L');
        $this->Ln(8);
    }

    // Pie de página
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    // Función para crear tabla de información del juego
    function GameInfoTable($fecha, $numJuego, $equipoElegido, $equipoRival, $puntajeElegido, $puntajeRival, $estado, $valido)
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 8, utf8_decode("Juego #$numJuego - $fecha"), 0, 1, 'L');
        $this->Ln(2);
        
        // Tabla de información
        $this->SetFillColor(40, 40, 40);
        $this->SetTextColor(255);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(95, 8, 'INFORMACION', 1, 0, 'C', true);
        $this->Cell(95, 8, utf8_decode(strtoupper($equipoElegido)) . ' - PARTIDO N° ' . $numJuego, 1, 1, 'C', true);
        
        $this->SetTextColor(0);
        $this->SetFont('Arial', '', 10);
        
        // Primera fila
        $this->Cell(47.5, 8, 'JUEGO', 1, 0, 'C');
        $this->Cell(47.5, 8, $numJuego, 1, 0, 'C');
        $this->Cell(47.5, 8, 'EQUIPO 1', 1, 0, 'C');
        $this->Cell(47.5, 8, utf8_decode($equipoElegido), 1, 1, 'C');
        
        // Segunda fila
        $this->Cell(47.5, 8, 'ESTADO', 1, 0, 'C');
        $this->Cell(47.5, 8, utf8_decode($estado), 1, 0, 'C');
        $this->Cell(47.5, 8, 'CA', 1, 0, 'C');
        $this->Cell(47.5, 8, $puntajeElegido, 1, 1, 'C');
        
        // Tercera fila
        $this->Cell(47.5, 8, 'VALIDO', 1, 0, 'C');
        $this->Cell(47.5, 8, $valido ? 'SI' : 'NO', 1, 0, 'C');
        $this->Cell(47.5, 8, 'EQUIPO 2', 1, 0, 'C');
        $this->Cell(47.5, 8, utf8_decode($equipoRival), 1, 1, 'C');
        
        // Cuarta fila
        $this->Cell(47.5, 8, 'FECHA', 1, 0, 'C');
        $this->Cell(47.5, 8, $fecha, 1, 0, 'C');
        $this->Cell(47.5, 8, 'CE', 1, 0, 'C');
        $this->Cell(47.5, 8, $puntajeRival, 1, 1, 'C');
        
        $this->Ln(10);
    }

    // Función para determinar qué columnas mostrar (solo las que tienen al menos un valor >= 1)
    function getVisibleColumns($data, $columns)
    {
        $visibleColumns = [];
        
        foreach ($columns as $column => $header) {
            $showColumn = false;
            
            foreach ($data as $row) {
                if (isset($row[$column]) && $row[$column] >= 1) {
                    $showColumn = true;
                    break;
                }
            }
            
            if ($showColumn) {
                $visibleColumns[$column] = $header;
            }
        }
        
        return $visibleColumns;
    }

    // Función para tabla de jugadores
    function PlayersTable($players)
    {
        if (empty($players)) {
            $this->SetFont('Arial', 'I', 10);
            $this->Cell(0, 8, 'No hay jugadores registrados para este juego', 0, 1, 'C');
            $this->Ln(5);
            return;
        }

        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 8, 'JUGADORES', 0, 1, 'L');
        $this->Ln(2);
        
        // Definir columnas y determinar cuáles mostrar (excepto # y JUGADOR que siempre se muestran)
        $allColumns = [
            'vb' => 'VB', 'h' => 'H', 'hr' => 'HR', '2b' => '2B', 
            '3b' => '3B', 'ca' => 'CA', 'ci' => 'CI', 'k' => 'K', 
            'b' => 'B', 'a' => 'A', 'sf' => 'FL', 'br' => 'BR', 'gp' => 'GP'
        ];
        
        $visibleColumns = $this->getVisibleColumns($players, $allColumns);
        
        // Siempre mostrar # y JUGADOR
        $finalColumns = ['#' => '#', 'JUGADOR' => 'JUGADOR'];
        foreach ($visibleColumns as $key => $header) {
            $finalColumns[$key] = $header;
        }
        
        // Definir anchos de columnas
        $widths = [];
        $totalWidth = 0;
        
        foreach ($finalColumns as $key => $header) {
            if ($key === '#') {
                $widths[$key] = 8;
            } elseif ($key === 'JUGADOR') {
                $widths[$key] = 35;
            } else {
                $widths[$key] = 10;
            }
            $totalWidth += $widths[$key];
        }
        
        // Ajustar ancho de la columna JUGADOR si es necesario
        if ($totalWidth > 190) {
            $excess = $totalWidth - 190;
            $widths['JUGADOR'] = max(20, $widths['JUGADOR'] - $excess);
        }
        
        // Cabecera de la tabla
        $this->SetFillColor(40, 40, 40);
        $this->SetTextColor(255);
        $this->SetFont('Arial', 'B', 8);
        
        foreach ($finalColumns as $key => $header) {
            $this->Cell($widths[$key], 6, utf8_decode($header), 1, 0, 'C', true);
        }
        $this->Ln();
        
        // Datos de jugadores
        $this->SetTextColor(0);
        $this->SetFont('Arial', '', 8);
        $fill = false;
        $counter = 1;
        
        foreach ($players as $player) {
            if ($this->GetY() > 250) {
                $this->AddPage();
                // Volver a dibujar la cabecera
                $this->SetFillColor(40, 40, 40);
                $this->SetTextColor(255);
                $this->SetFont('Arial', 'B', 8);
                foreach ($finalColumns as $key => $header) {
                    $this->Cell($widths[$key], 6, utf8_decode($header), 1, 0, 'C', true);
                }
                $this->Ln();
                $this->SetTextColor(0);
                $this->SetFont('Arial', '', 8);
            }
            
            $this->SetFillColor($fill ? 240 : 255);
            
            foreach ($finalColumns as $key => $header) {
                if ($key === '#') {
                    $value = $counter;
                } elseif ($key === 'JUGADOR') {
                    $value = substr($player['name_jugador'], 0, floor($widths['JUGADOR']/2.5));
                } else {
                    $value = isset($player[$key]) ? $player[$key] : 0;
                }
                
                $this->Cell($widths[$key], 6, utf8_decode($value), 1, 0, 'C', $fill);
            }
            $this->Ln();
            
            $fill = !$fill;
            $counter++;
        }
        
        $this->Ln(8);
    }

    // Función para tabla de lanzadores
    function PitchersTable($pitchers)
    {
        if (empty($pitchers)) {
            $this->SetFont('Arial', 'I', 10);
            $this->Cell(0, 8, 'No hay lanzadores registrados para este juego', 0, 1, 'C');
            $this->Ln(5);
            return;
        }

        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 8, 'LANZADORES', 0, 1, 'L');
        $this->Ln(2);
        
        // Definir columnas y determinar cuáles mostrar (excepto # y JUGADOR que siempre se muestran)
        $allColumns = [
            'jl' => 'JL', 'jg' => 'JG', 'il' => 'IL', 'cp' => 'CP', 
            'cpl' => 'CPL', 'h' => 'H', '2b' => '2B', '3b' => '3B', 
            'hr' => 'HR', 'b' => 'B', 'k' => 'K', 'va' => 'VB', 
            'gp' => 'GP', 'ile' => 'BR'
        ];
        
        $visibleColumns = $this->getVisibleColumns($pitchers, $allColumns);
        
        // Siempre mostrar # y JUGADOR
        $finalColumns = ['#' => '#', 'JUGADOR' => 'JUGADOR'];
        foreach ($visibleColumns as $key => $header) {
            $finalColumns[$key] = $header;
        }
        
        // Definir anchos de columnas
        $widths = [];
        $totalWidth = 0;
        
        foreach ($finalColumns as $key => $header) {
            if ($key === '#') {
                $widths[$key] = 8;
            } elseif ($key === 'JUGADOR') {
                $widths[$key] = 30;
            } else {
                $widths[$key] = 8;
            }
            $totalWidth += $widths[$key];
        }
        
        // Ajustar ancho de la columna JUGADOR si es necesario
        if ($totalWidth > 190) {
            $excess = $totalWidth - 190;
            $widths['JUGADOR'] = max(15, $widths['JUGADOR'] - $excess);
        }
        
        // Cabecera de la tabla
        $this->SetFillColor(40, 40, 40);
        $this->SetTextColor(255);
        $this->SetFont('Arial', 'B', 7);
        
        foreach ($finalColumns as $key => $header) {
            $this->Cell($widths[$key], 6, utf8_decode($header), 1, 0, 'C', true);
        }
        $this->Ln();
        
        // Datos de lanzadores
        $this->SetTextColor(0);
        $this->SetFont('Arial', '', 7);
        $fill = false;
        $counter = 1;
        
        foreach ($pitchers as $pitcher) {
            if ($this->GetY() > 250) {
                $this->AddPage();
                // Volver a dibujar la cabecera
                $this->SetFillColor(40, 40, 40);
                $this->SetTextColor(255);
                $this->SetFont('Arial', 'B', 7);
                foreach ($finalColumns as $key => $header) {
                    $this->Cell($widths[$key], 6, utf8_decode($header), 1, 0, 'C', true);
                }
                $this->Ln();
                $this->SetTextColor(0);
                $this->SetFont('Arial', '', 7);
            }
            
            $this->SetFillColor($fill ? 240 : 255);
            
            foreach ($finalColumns as $key => $header) {
                if ($key === '#') {
                    $value = $counter;
                } elseif ($key === 'JUGADOR') {
                    $value = substr($pitcher['name_lanz'], 0, floor($widths['JUGADOR']/2));
                } else {
                    $value = isset($pitcher[$key]) ? $pitcher[$key] : 0;
                }
                
                $this->Cell($widths[$key], 6, utf8_decode($value), 1, 0, 'C', $fill);
            }
            $this->Ln();
            
            $fill = !$fill;
            $counter++;
        }
        
        $this->Ln(10);
    }
}

// Crear PDF
$pdf = new PDF('P','mm','Letter');
$pdf->AliasNbPages();
$pdf->AddPage();

// config document
$pdf->SetTitle(utf8_decode('Historial del Equipo: '.$name));
$pdf->SetAuthor('Arturo');
$pdf->SetCreator('FPDF Maker');

// Obtener datos de juegos
$historial = [];
$sql = "SELECT * FROM juegos WHERE team_one = $it AND id_temp = $tm ORDER BY nj ASC";
$result = mysqli_query($con, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    $ht = $row['team_one'];
    $ft = $row['team_two'];
    $pl = $row['fech_part'];

    $tenemy = "SELECT * FROM equipos WHERE id_team = $ft";
    $vart = mysqli_query($con, $tenemy);
    $nym = mysqli_fetch_array($vart);

    // Obtener jugadores para este juego
    $players_sql = "SELECT * FROM jugadores_stats 
                   WHERE id_team = $ht AND id_tab = {$row['id_tab']} AND nj = {$row['nj']} AND categoria LIKE '%$ct%'";
    $players_result = mysqli_query($con, $players_sql);
    $players = [];
    while ($player = mysqli_fetch_assoc($players_result)) {
        $players[] = $player;
    }

    // Obtener lanzadores para este juego
    $pitchers_sql = "SELECT * FROM jugadores_lanz 
                    WHERE id_team = $ht AND id_tab = {$row['id_tab']} AND nj = {$row['nj']} AND categoria LIKE '%$ct%'";
    $pitchers_result = mysqli_query($con, $pitchers_sql);
    $pitchers = [];
    while ($pitcher = mysqli_fetch_assoc($pitchers_result)) {
        $pitchers[] = $pitcher;
    }

    $historial[] = [
        'fecha' => $row['fech_part'],
        'numJuego' => (int)$row['nj'],
        'equipoElegido' => $name,
        'equipoRival' => $nym['nom_team'],
        'puntajeElegido' => (int)$row['ca'],
        'puntajeRival' => (int)$row['ce'],
        'estado' => $row['estado'],
        'valido' => $row['valido'],
        'players' => $players,
        'pitchers' => $pitchers
    ];
}

// Recorrer y agregar info
foreach ($historial as $juego) {
    // Verificar si necesitamos nueva página
    if ($pdf->GetY() > 180) {
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
    
    $pdf->PlayersTable($juego['players']);
    $pdf->PitchersTable($juego['pitchers']);
    
    // Línea separadora entre juegos
    $pdf->SetDrawColor(200, 200, 200);
    $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
    $pdf->Ln(5);
}

$pdf->Output();
?>