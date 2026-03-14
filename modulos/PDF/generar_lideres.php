<?php
// Activar reporte de errores para depuración (solo para desarrollo)
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// Reporte Consolidado de Líderes - Sistema Baseball
require('vendor/fpdf/fpdf.php');
require('conexion.php');

// Verificar que los archivos requeridos existen
if (!file_exists('vendor/fpdf/fpdf.php')) {
    die("Error: No se encuentra el archivo FPDF");
}

if (!file_exists('conexion.php')) {
    die("Error: No se encuentra el archivo de conexión");
}

$con = conectar();

// Verificar conexión
if (!$con) {
    die("Error de conexión a la base de datos: " . mysqli_connect_error());
}

// Parámetros recibidos
$categoria = $_POST['categoria'] ?? '';
$temporada = $_POST['temporada'] ?? '';

// Función para convertir texto a ISO-8859-1 de manera segura
function convertirTexto($texto) {
    if (empty($texto)) return '';
    // Detectar si ya está en UTF-8
    if (mb_detect_encoding($texto, 'UTF-8', true)) {
        return mb_convert_encoding($texto, 'ISO-8859-1', 'UTF-8');
    }
    return $texto;
}

// Función para obtener datos del jugador (prioriza datos manuales)
function obtenerDatosJugador($con, $player_id, $tipo, $pos, $categoria_key) {
    global $_POST;
    
    // Verificar si es manual - buscar el checkbox correcto
    $manual_check = false;
    
    // Buscar el checkbox manual en diferentes formatos posibles
    if (isset($_POST['manual_' . $categoria_key . '_' . $pos]) && $_POST['manual_' . $categoria_key . '_' . $pos] == '1') {
        $manual_check = true;
    } elseif (isset($_POST['manual_' . $pos]) && $categoria_key == 'ci' && $_POST['manual_' . $pos] == '1') {
        $manual_check = true;
    } elseif (isset($_POST['manual_' . $pos]) && $categoria_key == 'hr' && $_POST['manual_' . $pos] == '1') {
        $manual_check = true;
    } elseif (isset($_POST['manual_' . $pos]) && $categoria_key == 'picher' && $_POST['manual_' . $pos] == '1') {
        $manual_check = true;
    }
    
    // Si es entrada manual
    if ($manual_check) {
        $nombre = '';
        $equipo = '';
        
        // Buscar nombre en diferentes formatos posibles
        if (isset($_POST['manual_nombre_' . $categoria_key . '_' . $pos])) {
            $nombre = $_POST['manual_nombre_' . $categoria_key . '_' . $pos];
        } elseif (isset($_POST['manual_nombre_' . $pos])) {
            $nombre = $_POST['manual_nombre_' . $pos];
        }
        
        // Buscar equipo
        if (isset($_POST['manual_equipo_' . $categoria_key . '_' . $pos])) {
            $equipo = $_POST['manual_equipo_' . $categoria_key . '_' . $pos];
        } elseif (isset($_POST['manual_equipo_' . $pos])) {
            $equipo = $_POST['manual_equipo_' . $pos];
        }
        
        if ($categoria_key == 'ci') {
            $ci = '';
            if (isset($_POST['manual_ci_' . $categoria_key . '_' . $pos])) {
                $ci = $_POST['manual_ci_' . $categoria_key . '_' . $pos];
            } elseif (isset($_POST['manual_ci_' . $pos])) {
                $ci = $_POST['manual_ci_' . $pos];
            }
            
            return [
                'nombre' => $nombre ?: 'No especificado',
                'equipo' => $equipo ?: 'Sin equipo',
                'ci' => $ci ?: '0'
            ];
        } elseif ($categoria_key == 'avg') {
            $vb = '';
            $h = '';
            $avg = '';
            
            if (isset($_POST['manual_vb_' . $categoria_key . '_' . $pos])) {
                $vb = $_POST['manual_vb_' . $categoria_key . '_' . $pos];
            }
            if (isset($_POST['manual_h_' . $categoria_key . '_' . $pos])) {
                $h = $_POST['manual_h_' . $categoria_key . '_' . $pos];
            }
            if (isset($_POST['manual_avg_' . $categoria_key . '_' . $pos])) {
                $avg = $_POST['manual_avg_' . $categoria_key . '_' . $pos];
            }
            
            // Formatear AVG
            if (is_numeric($avg)) {
                if ($avg > 1) {
                    $avg = number_format($avg / 1000, 3);
                } else {
                    $avg = number_format($avg, 3);
                }
            } elseif (strpos($avg, '.') !== 0 && $avg != '') {
                $avg = '.' . str_pad($avg, 3, '0', STR_PAD_RIGHT);
            }
            
            return [
                'nombre' => $nombre ?: 'No especificado',
                'equipo' => $equipo ?: 'Sin equipo',
                'tvb' => $vb ?: '0',
                'th' => $h ?: '0',
                'avg' => $avg ?: '.000'
            ];
        } elseif ($categoria_key == 'hr') {
            $hr = '';
            if (isset($_POST['manual_hr_' . $categoria_key . '_' . $pos])) {
                $hr = $_POST['manual_hr_' . $categoria_key . '_' . $pos];
            } elseif (isset($_POST['manual_hr_' . $pos])) {
                $hr = $_POST['manual_hr_' . $pos];
            }
            
            return [
                'nombre' => $nombre ?: 'No especificado',
                'equipo' => $equipo ?: 'Sin equipo',
                'hr' => $hr ?: '0'
            ];
        } elseif ($categoria_key == 'picher') {
            $jl = '';
            $jg = '';
            $jp = '';
            $ef = '';
            
            if (isset($_POST['manual_jl_' . $categoria_key . '_' . $pos])) {
                $jl = $_POST['manual_jl_' . $categoria_key . '_' . $pos];
            }
            if (isset($_POST['manual_jg_' . $categoria_key . '_' . $pos])) {
                $jg = $_POST['manual_jg_' . $categoria_key . '_' . $pos];
            }
            if (isset($_POST['manual_jp_' . $categoria_key . '_' . $pos])) {
                $jp = $_POST['manual_jp_' . $categoria_key . '_' . $pos];
            }
            if (isset($_POST['manual_ef_' . $categoria_key . '_' . $pos])) {
                $ef = $_POST['manual_ef_' . $categoria_key . '_' . $pos];
            }
            
            // Calcular JP si no se proporcionó
            if (empty($jp) && !empty($jl) && !empty($jg)) {
                $jp = $jl - $jg;
            }
            
            // Formatear efectividad
            if (is_numeric($ef)) {
                if ($ef > 1) {
                    $ef = number_format($ef / 1000, 3);
                } else {
                    $ef = number_format($ef, 3);
                }
            }
            
            return [
                'nombre' => $nombre ?: 'No especificado',
                'equipo' => $equipo ?: 'Sin equipo',
                'tjl' => $jl ?: '0',
                'tjg' => $jg ?: '0',
                'tjp' => $jp ?: '0',
                'ef' => $ef ?: '0.000'
            ];
        }
    }
    
    // Si no es manual y hay player_id, buscar en base de datos
    if (!empty($player_id) && $player_id !== 'null' && $player_id !== '0') {
        if ($tipo == 'bateador') {
            $query = "SELECT rs.name_jgstats, rs.ci, rs.tvb, rs.th, rs.avg, rs.hr, tc.name_team 
                     FROM resumen_stats rs 
                     LEFT JOIN tab_clasf tc ON rs.id_team = tc.id_team 
                     WHERE rs.id_player = " . intval($player_id) . " 
                     LIMIT 1";
        } else {
            $query = "SELECT rl.name_jglz, rl.tjl, rl.tjg, (rl.tjl - rl.tjg) as tjp, rl.avg, tc.name_team 
                     FROM resumen_lanz rl 
                     LEFT JOIN tab_clasf tc ON rl.id_team = tc.id_team 
                     WHERE rl.id_player = " . intval($player_id) . " 
                     LIMIT 1";
        }
        
        $result = mysqli_query($con, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
    }
    
    return null;
}

// VALORES CONFIGURABLES
$valor_pichers = 20;

// Obtener fecha del reporte
$verificar = mysqli_query($con, "SELECT * FROM report LIMIT 1");
if ($verificar && mysqli_num_rows($verificar) > 0) {
    $vdta = mysqli_fetch_array($verificar);
    $vfecha = $vdta['timeday'] ?? date('Y-m-d H:i:s');
} else {
    $vfecha = date('Y-m-d H:i:s');
}
$vtt = $vfecha;
$entero_vtt = strtotime($vtt);
$ano_vtt = date("Y", $entero_vtt);
$mes_vtt = date("m", $entero_vtt);
$dia_vtt = date("d", $entero_vtt);
$timeday = $dia_vtt . '-' . $mes_vtt . '-' . $ano_vtt;

// Clase PDF personalizada
class PDF extends FPDF
{
    // Cabecera de página
    function Header()
    {
        $this->SetFont('Arial', 'B', 12);
        // Verificar que las imágenes existen
        if (file_exists('../../fondos/pulpo (2).png')) {
            $this->Image('../../fondos/pulpo (2).png', 10, 6, 12);
        }
        if (file_exists('../../fondos/pulpov (2).png')) {
            $this->Image('../../fondos/pulpov (2).png', 189, 6, 12);
        }
        $this->Cell(0, 5, convertirTexto(strtoupper('LIGA RECREATIVA SOFTBALL EDUCADORES DEL ESTADO ARAGUA')), 0, 1, 'C');
        $this->Ln(1);
        $this->Cell(0, 5, convertirTexto(strtoupper('CATEGORÍA "' . $GLOBALS['categoria'] . '"')), 0, 1, 'C');
        $this->Ln(1);
        $this->Cell(0, 5, convertirTexto('REPORTE CONSOLIDADO DE LÍDERES'), 0, 1, 'C');
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 5, 'FECHA: ' . $GLOBALS['timeday'], 0, 1, 'L');
        $this->Ln(5);
    }
    
    // Pie de página
    function Footer()
    {
        $this->SetY(-12);
        $this->SetFont('Arial', 'I', 7);
        $this->Cell(0, 5, convertirTexto('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
    
    // Función para tabla de líderes
    function TablaLideres($titulo, $headers, $data, $colWidths)
    {
        // Título de la sección
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 5, convertirTexto($titulo), 0, 1, 'C');
        $this->Ln(2);
        
        // Colors, line width and bold font
        $this->SetFillColor(220, 220, 220);
        $this->SetTextColor(0);
        $this->SetDrawColor(0);
        $this->SetLineWidth(.3);
        $this->SetFont('Arial', 'B', 9);
        
        // Header
        $this->Cell(30);
        for($i=0; $i<count($headers); $i++) {
            $this->Cell($colWidths[$i], 7, convertirTexto(strtoupper($headers[$i])), 1, 0, 'C', true);
        }
        $this->Ln();
        
        // Color and font restoration
        $this->SetFillColor(255, 255, 255);
        $this->SetTextColor(0);
        $this->SetFont('Arial', '', 8);
        
        // Data
        $fill = false;
        foreach($data as $row) {
            $this->Cell(30);
            for($i=0; $i<count($headers); $i++) {
                $valor = isset($row[$i]) ? $row[$i] : '-';
                $this->Cell($colWidths[$i], 6, convertirTexto($valor), 'LR', 0, 'C', $fill);
            }
            $this->Ln();
            $fill = !$fill;
        }
        
        // Closing line
        $this->Cell(30);
        for($i=0; $i<count($headers); $i++) {
            $this->Cell($colWidths[$i], 0, '', 'T');
        }
        $this->Ln(8);
    }
}

// Creación del objeto PDF
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();

// Configuración del documento
$pdf->SetTitle(convertirTexto('Reporte Consolidado de Líderes - Categoría ' . $categoria));
$pdf->SetAuthor('Sistema Baseball');
$pdf->SetCreator('Reporte Consolidado');

// Agregar página inicial
$pdf->AddPage();

// =============================================================================
// 1. LÍDERES EN CARRERAS EMPUJADAS (CI)
// =============================================================================
$data_ci = [];
$posiciones = ['1ro', '2do', '3er'];

for ($pos = 1; $pos <= 3; $pos++) {
    $player_id = isset($_POST['lider_ci_' . $pos]) ? $_POST['lider_ci_' . $pos] : '';
    $datos = obtenerDatosJugador($con, $player_id, 'bateador', $pos, 'ci');
    
    if ($datos) {
        $data_ci[] = [
            $posiciones[$pos-1],
            $datos['nombre'] ?? $datos['name_jgstats'] ?? 'No seleccionado',
            $datos['ci'] ?? '-',
            $datos['equipo'] ?? $datos['name_team'] ?? '-'
        ];
    } else {
        $data_ci[] = [
            $posiciones[$pos-1],
            'No seleccionado',
            '-',
            '-'
        ];
    }
}

$pdf->TablaLideres(
    'LÍDERES EN CARRERAS EMPUJADAS (CI)',
    ['POS', 'NOMBRE', 'CI', 'EQUIPO'],
    $data_ci,
    [12, 60, 12, 45]
);

// =============================================================================
// 2. LÍDERES EN BATEO (AVG)
// =============================================================================
$data_avg = [];
for ($pos = 1; $pos <= 3; $pos++) {
    $player_id = isset($_POST['lider_avg_' . $pos]) ? $_POST['lider_avg_' . $pos] : '';
    $datos = obtenerDatosJugador($con, $player_id, 'bateador', $pos, 'avg');
    
    if ($datos) {
        $data_avg[] = [
            $posiciones[$pos-1],
            $datos['nombre'] ?? $datos['name_jgstats'] ?? 'No seleccionado',
            $datos['tvb'] ?? '0',
            $datos['th'] ?? '0',
            $datos['avg'] ?? '.000',
            $datos['equipo'] ?? $datos['name_team'] ?? '-'
        ];
    } else {
        $data_avg[] = [
            $posiciones[$pos-1],
            'No seleccionado',
            '-',
            '-',
            '-',
            '-'
        ];
    }
}

$pdf->TablaLideres(
    'LÍDERES EN BATEO (AVG)',
    ['POS', 'NOMBRE', 'VB', 'H', 'AVG', 'EQUIPO'],
    $data_avg,
    [10, 45, 10, 10, 15, 40]
);

// =============================================================================
// 3. LÍDERES EN JONRONES (HR)
// =============================================================================
$data_hr = [];
for ($pos = 1; $pos <= 3; $pos++) {
    $player_id = isset($_POST['lider_hr_' . $pos]) ? $_POST['lider_hr_' . $pos] : '';
    $datos = obtenerDatosJugador($con, $player_id, 'bateador', $pos, 'hr');
    
    if ($datos) {
        $data_hr[] = [
            $posiciones[$pos-1],
            $datos['nombre'] ?? $datos['name_jgstats'] ?? 'No seleccionado',
            $datos['hr'] ?? '0',
            $datos['equipo'] ?? $datos['name_team'] ?? '-'
        ];
    } else {
        $data_hr[] = [
            $posiciones[$pos-1],
            'No seleccionado',
            '-',
            '-'
        ];
    }
}

$pdf->TablaLideres(
    'LÍDERES EN JONRONES (HR)',
    ['POS', 'NOMBRE', 'HR', 'EQUIPO'],
    $data_hr,
    [12, 60, 12, 45]
);

// =============================================================================
// 4. PICHERS GANADORES
// =============================================================================
$data_pichers = [];
for ($pos = 1; $pos <= 3; $pos++) {
    $player_id = isset($_POST['lider_picher_' . $pos]) ? $_POST['lider_picher_' . $pos] : '';
    $datos = obtenerDatosJugador($con, $player_id, 'picher', $pos, 'picher');
    
    if ($datos) {
        $data_pichers[] = [
            $posiciones[$pos-1],
            $datos['nombre'] ?? $datos['name_jglz'] ?? 'No seleccionado',
            $datos['tjl'] ?? '0',
            $datos['tjg'] ?? '0',
            $datos['tjp'] ?? '0',
            $datos['ef'] ?? '0.000',
            $datos['equipo'] ?? $datos['name_team'] ?? '-'
        ];
    } else {
        $data_pichers[] = [
            $posiciones[$pos-1],
            'No seleccionado',
            '-',
            '-',
            '-',
            '-',
            '-'
        ];
    }
}

$pdf->TablaLideres(
    'PICHERS GANADORES',
    ['POS', 'NOMBRE', 'JL', 'JG', 'JP', 'EF', 'EQUIPO'],
    $data_pichers,
    [10, 40, 10, 10, 10, 15, 35]
);

// =============================================================================
// GENERAR EL PDF
// =============================================================================
// Asegurar que no haya salida antes del PDF
while (ob_get_level()) {
    ob_end_clean();
}

// Enviar cabeceras PDF
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="Reporte_Lideres_' . $categoria . '_' . $temporada . '.pdf"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

// Generar PDF
$pdf->Output('I', 'Reporte_Lideres_' . $categoria . '_' . $temporada . '.pdf');

// Cerrar conexión
mysqli_close($con);
?>