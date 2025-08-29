<? session_start(); 

//Inclusión de Archivo Necesario y Conexión
require('vendor/fpdf/fpdf.php');
require('conexion.php');
$con=conectar();
$coner=conectarsiabis();

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
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);

$cedula=$_REQUEST['cedula'];
$desde= $_SESSION['desde'];
$hasta= $_SESSION['hasta'];

// config document
$pdf->SetTitle('ODI - '.$cedula);
$pdf->SetAuthor('Arturo');
$pdf->SetCreator('FPDF Maker');


$consulta ="SELECT * FROM seccion_a WHERE ced_eva = '$cedula' and pe_desde = '$desde'";
$querys=mysqli_query($con,$consulta);
$dato=mysqli_fetch_array($querys); 

$consulta1 ="SELECT * FROM seccion_b WHERE ced_b = '$cedula' and p_desde = '$desde';";
$querys1=mysqli_query($con,$consulta1);
$datob=mysqli_fetch_array($querys1); 

$consulta2 ="SELECT * FROM seccion_c WHERE ced_c = '$cedula' and p_desde = '$desde';";
$querys2=mysqli_query($con,$consulta2);
$datoc=mysqli_fetch_array($querys2); 

$evaluador=$dato['evaluador'];
$grhrh="CACURRI LA ROSA, PATRICIA ALEJANDRA";
$gdesp="FIGUEROA HERRERA, CARLOS RAFAEL";

$consiabis="SELECT * FROM fichalab WHERE nape = '$evaluador'";
$quesiabis=mysqli_query($coner,$consiabis);
$obtsia=mysqli_fetch_array($quesiabis);

$consiabisdos="SELECT * FROM fichalab WHERE nape = '$grhrh'";
$quesiabisdos=mysqli_query($coner,$consiabisdos);
$obtsiados=mysqli_fetch_array($quesiabisdos);

$consiabistres="SELECT * FROM fichalab WHERE nape = '$gdesp'";
$quesiabistres=mysqli_query($coner,$consiabistres);
$obtsiatres=mysqli_fetch_array($quesiabistres);

$consiabiscuatro="SELECT fichalab.*, depart.* FROM fichalab INNER JOIN depart ON
fichalab.depart = depart.depart WHERE fichalab.cedu = '$cedula'";
$quesiabiscuatro=mysqli_query($coner,$consiabiscuatro);
$obtsiacuatro=mysqli_fetch_array($quesiabiscuatro);
$num_gerencia=$obtsiacuatro['coddepart'];


$consultatg ="SELECT * FROM datos_generales";
$querytg=mysqli_query($con,$consultatg);
$datotg=mysqli_fetch_array($querytg); 
$pdf->Image($datotg['directorio'], $datotg['horizontal'], $datotg['vertical'], $datotg['tamano']);
$pdf->Image('../../siabis/fotos/'.$cedula.'.jpg', 183, 32, 17);
$pdf->Cell(0,5,utf8_decode(strtoupper($datotg['membrete'])),0,1,'C');

/*
$pdf->Image('logo_cea_oficios.png', 23, 8, 22);
$pdf->Image('../../siabis/fotos/'.$cedula.'.jpg', 183, 32, 17);
$pdf->Cell(0,5,utf8_decode(strtoupper('CONTRALORIA DEL ESTADO ARAGUA')),0,1,'C');*/
$pdf->Cell(0,5,utf8_decode(strtoupper('EVALUACION DEL DESEMPEÑO INDIVIDUAL')),0,1,'C');
$pdf->Ln(1);

// Seccion D - Datos

$pdf->SetFont('Times','',10);
$pdf->Cell(0,5,utf8_decode(strtoupper('Seccion "D"')),0,1,'C');
$pdf->Ln(3);

$pdf->SetFillColor(213, 216, 220); 
$pdf->SetFont('Times','B',6);
$pdf->Cell(0,3,utf8_decode(strtoupper('DATOS DEL EVALUADO')),1,1,'C',true);
$pdf->SetFillColor(242, 244, 244); 
$pdf->SetFont('Times','',7);
$pdf->Cell(35,7,utf8_decode(strtoupper('Apellidos y Nombres :')),1,0,'C');
$pdf->Cell(100,7,utf8_decode(strtoupper($dato['nom_eva'])),1,0,'C',true);
$pdf->Cell(8,7,utf8_decode(strtoupper('c.i. :')),1,0,'C');
$pdf->Cell(30,7,utf8_decode(strtoupper('V.- ')).$dato['ced_eva'],1,0,'C',true);
$pdf->Cell(17,21,'',1,0,'C');


$pdf->SetFillColor(255, 255, 255); 
$pdf->SetFont('Times','',7);
$pdf->Cell(10,21,'','L',1,'J',true);

$pdf->Ln(-14);
$pdf->SetFillColor(242, 244, 244); 
$pdf->SetFont('Times','',7);
$pdf->Cell(13,7,utf8_decode(strtoupper('cargo :')),1,0,'C');
$pdf->Cell(95,7,utf8_decode(strtoupper($dato['cargo'])),1,0,'C',true);
$pdf->Cell(27,7,utf8_decode(strtoupper('fecha de ingreso :')),1,0,'C');


$ingreso=$dato['fec_ing_eva'];
$entero_ingreso = strtotime($ingreso);
$ano_ingreso = date("Y", $entero_ingreso);
$mes_ingreso = date("m", $entero_ingreso);
$dia_ingreso = date("d", $entero_ingreso);
$pdf->Cell(38,7,utf8_decode(strtoupper($dia_ingreso.' / '.$mes_ingreso.' / '.$ano_ingreso)),1,1,'C',true);

$pdf->Cell(35,7,utf8_decode(strtoupper('periodo de evaluacion :')),1,0,'C');


$desde=$dato['pe_desde'];
$entero_desde = strtotime($desde);
$ano_desde = date("Y", $entero_desde);
$mes_desde = date("m", $entero_desde);
$dia_desde = date("d", $entero_desde);
$pdf->Cell(18,7,utf8_decode(strtoupper('desde :')),1,0,'C');
$pdf->Cell(50,7,utf8_decode(strtoupper($dia_desde.' / '.$mes_desde.' / '.$ano_desde)),1,0,'C',true);


$hasta=$dato['pe_hasta'];
$entero_hasta = strtotime($hasta);
$ano_hasta = date("Y", $entero_hasta);
$mes_hasta = date("m", $entero_hasta);
$dia_hasta = date("d", $entero_hasta);
$pdf->Cell(20,7,utf8_decode(strtoupper('hasta :')),1,0,'C');
$pdf->Cell(50,7,utf8_decode(strtoupper($dia_hasta.' / '.$mes_hasta.' / '.$ano_hasta)),1,1,'C',true);

// Seccion D - Calculo
$pdf->SetFillColor(255, 255, 255); 
$pdf->SetFont('Times','B',6);
$pdf->Cell(190,5,'','T,B',0,'C',true);
$pdf->Cell(10,5,'',0,1,'C',true);


$pdf->SetFillColor(213, 216, 220); 
$pdf->SetFont('Times','B',6);
$pdf->Cell(0,3,utf8_decode(strtoupper('CRITERIO Y RANGO PARA LA EVALUACIÓN GENERAL DEL TRABAJADOR')),1,1,'C',true);
$pdf->SetFont('Times','',5);
$pdf->Cell(30,5,utf8_decode(strtoupper('ESCALA CUANTITATIVA')),1,0,'C');
$pdf->Cell(40,5,utf8_decode(strtoupper('RANGO DE ACTUACIÓN')),1,0,'C');
$pdf->SetFillColor(255, 255, 255); 
$pdf->SetFont('Times','',5);
$pdf->Cell(120,5,utf8_decode(strtoupper('DEFINICIÓN DE LOS RANGOS DE ACTUACIÓN')),1,1,'J',true);

$pdf->Cell(30,5,utf8_decode(strtoupper('HASTA 259')),1,0,'C');
$pdf->Cell(40,5,utf8_decode(strtoupper('ACTUACIÓN DEFICIENTE')),1,0,'C');
$pdf->MultiCell(120,5,utf8_decode(strtoupper('El trabajador obtiene un rendimiento inferior al esperado y no cumple en la totalidad con las tareas asignadas.')),1,'J',true);

$pdf->Cell(30,5,utf8_decode(strtoupper('HASTA 260 - 295')),1,0,'C');
$pdf->Cell(40,5,utf8_decode(strtoupper('ACTUACIÓN REGULAR')),1,0,'C');
$pdf->MultiCell(120,5,utf8_decode(strtoupper('Obtiene un bajo rendimiento y cumple medianamente con las funciones y actividades asignadas.')),1,'J',true);

$pdf->Cell(30,5,utf8_decode(strtoupper('HASTA 296 - 339')),1,0,'C');
$pdf->Cell(40,5,utf8_decode(strtoupper('ACTUACIÓN BUENO')),1,0,'C');
$pdf->Cell(120,5,utf8_decode(strtoupper('Desempeño satisfactorio, cumple con todos los objetivos asignados')),1,1,'J',true);

$pdf->Cell(30,5,utf8_decode(strtoupper('HASTA 340 - 419')),1,0,'C');
$pdf->Cell(40,5,utf8_decode(strtoupper('ACTUACIÓN MUY BUENO')),1,0,'C');
$pdf->MultiCell(120,2.5,utf8_decode(strtoupper('Desempeño por encima de lo esperado y contribuye al logro de los objetivos propuestos, en ocasiones obtiene logros adicionales')),1,'J',true);

// Seccion D - Calificación Final
$pdf->Ln(5);
$puntuacion_b=$datob['puntuacion_b'];
$puntuacion_c=$datoc['puntuacion_c'];
$total=$puntuacion_b+$puntuacion_c;

if ($total <= 259) {
    $calificacion="DEFICIENTE";

} elseif (260 >= $total || $total <= 295) {
    $calificacion="REGULAR";

} elseif (296 >= $total || $total <= 339) {
    $calificacion="BUENO";

} elseif (340 >= $total || $total <= 419) {
    $calificacion="MUY BUENO";
} else {
    $calificacion="No se encuentran en el rango";
}

$pdf->SetFillColor(213, 216, 220); 
$pdf->SetFont('Times','B',6);
$pdf->Cell(130,3,utf8_decode(strtoupper('CALIFICACIÓN FINAL')),1,0,'C',true);
$pdf->Cell(60,3,utf8_decode(strtoupper('RANGO DE ACTUACIÓN')),1,1,'C',true);

$pdf->SetFillColor(242, 244, 244); 
$pdf->SetFont('Times','B',8);
$pdf->Cell(65,5,utf8_decode(strtoupper('TOTAL SECCIÓN "B"')),1,0,'C');
$pdf->Cell(65,5,utf8_decode(strtoupper($datob['puntuacion_b'])),1,0,'C',true);
$pdf->Cell(60,15,utf8_decode(strtoupper($calificacion)),1,1,'C',true);
$pdf->Ln(-10);
$pdf->Cell(65,5,utf8_decode(strtoupper('TOTAL SECCIÓN "C"')),1,0,'C');
$pdf->Cell(65,5,utf8_decode(strtoupper($datoc['puntuacion_c'])),1,0,'C',true);
$pdf->Cell(60,5,'',0,1,'J');


$pdf->Cell(65,5,utf8_decode(strtoupper('TOTAL SECCIÓN "B+C"')),1,0,'C');
$pdf->SetFillColor(213, 216, 220); 
$pdf->SetFont('Times','B',10);
$pdf->Cell(65,5,utf8_decode(strtoupper($total)),1,0,'C',true);
$pdf->Cell(60,5,'',0,1,'J');

// Seccion D - COMENTARIOS DEL EVALUADOR
$pdf->Ln();
$pdf->SetFont('Times','B',6);
$pdf->Cell(190,3,utf8_decode(strtoupper('COMENTARIOS DEL EVALUADOR')),1,1,'C',true);
$pdf->Cell(190,7,'',1,1,'C');
$pdf->Cell(190,7,'',1,1,'C');
$pdf->Cell(190,7,'',1,1,'C');


// Seccion D - DATOS DEL EVALUADOR
$pdf->Ln();
$pdf->SetFont('Times','B',7);
$pdf->Cell(190,5,utf8_decode(strtoupper('DATOS DEL EVALUADOR')),0,1,'J');
$pdf->Cell(190,5,utf8_decode(strtoupper('NOMBRE Y APELLIDO:')),0,1,'J');
$pdf->Ln(-5);
$pdf->Cell(190,15,'',1,1,'J');
$pdf->Cell(95,5,'FIRMA DEL EVALUADOR:',0,0,'J');
$pdf->Cell(95,5,'FECHA:',0,1,'J');
$pdf->Ln(-5);
$pdf->Cell(95,15,'',1,0,'J');
$pdf->Cell(95,15,'',1,1,'J');



// Seccion D - COMENTARIOS DEL EVALUADO
$pdf->Ln(5);
$pdf->SetFont('Times','B',7);
$pdf->Cell(190,5,utf8_decode(strtoupper('COMENTARIOS DEL EVALUADO')),0,1,'J');
$pdf->Cell(100,5,utf8_decode(strtoupper('¿ESTÁ DE ACUERDO CON LA EVALUACIÓN? ')),0,0,'J');

$pdf->Cell(7,3,'',0,0,'J');
$pdf->Cell(7,3,utf8_decode(strtoupper('SI ')),0,0,'J');
$pdf->Cell(7,3,'',1,0,'J');
$pdf->Cell(7,3,'',0,0,'J');
$pdf->Cell(7,3,utf8_decode(strtoupper('NO ')),0,0,'J');
$pdf->Cell(7,3,'',1,1,'J');
$pdf->Ln(2);

$pdf->Cell(190,7,'',1,1,'J');
$pdf->Cell(190,7,'',1,1,'J');
$pdf->Cell(190,7,'',1,1,'J');


// Seccion D - FIRMA DEL EVALUADO
$pdf->Ln();
$pdf->SetFont('Times','B',7);
$pdf->Cell(95,5,'FIRMA DEL EVALUADO:',0,0,'J');
$pdf->Cell(95,5,'FECHA:',0,1,'J');
$pdf->Ln(-5);
$pdf->Cell(95,15,'',1,0,'J');
$pdf->Cell(95,15,'',1,1,'J');

// Seccion D - FECHA DE LA EVALUACION Y CONFORMADO
$pdf->SetFont('Times','B',7);
$pdf->Cell(95,5,utf8_decode(strtoupper('FECHA DE LA EVALUACIÓN: '.$datob['fec_eva'])),0,0,'J');
$pdf->Cell(95,5,'CONFORMADO POR:',0,1,'C');

$pdf->Ln(9);

// Seccion D - DATOS DEL EVALUADOR
$pdf->Ln(10);



/* Auditoria Interna  -  gerente evaluador: Figueroa*/
if ($num_gerencia == 22) {


$pdf->SetFont('Times','B',7);
$pdf->Cell(95,3,utf8_decode(strtoupper($gdesp)),0,0,'C');
$pdf->Cell(95,3,utf8_decode(strtoupper($grhrh)),0,1,'C');

$pdf->Cell(95,3,strtoupper('DIRECTOR GENERAL'),0,0,'C');
$pdf->Cell(95,3,strtoupper('GERENTE ( E ) DE '.$obtsiados['depart']),0,1,'C');

$pdf->Cell(95,3,strtoupper($obtsiatres['cdesemp']),0,0,'C');
$pdf->Cell(95,3,strtoupper($obtsiados['cdesemp']),0,1,'C');

$pdf->Cell(95,3,utf8_decode(strtoupper('de la Contraloría del Estado Aragua')),0,0,'C');
$pdf->Cell(95,3,utf8_decode(strtoupper('de la Contraloría del Estado Aragua')),0,1,'C');


} /* Determinación de Responsabilidad  -  gerente evaluador: Figueroa*/ 
elseif ($num_gerencia == 26) {


$pdf->SetFont('Times','B',7);
$pdf->Cell(95,3,utf8_decode(strtoupper($gdesp)),0,0,'C');
$pdf->Cell(95,3,utf8_decode(strtoupper($grhrh)),0,1,'C');

$pdf->Cell(95,3,strtoupper('DIRECTOR GENERAL'),0,0,'C');
$pdf->Cell(95,3,strtoupper('GERENTE ( E ) DE '.$obtsiados['depart']),0,1,'C');

$pdf->Cell(95,3,strtoupper($obtsiatres['cdesemp']),0,0,'C');
$pdf->Cell(95,3,strtoupper($obtsiados['cdesemp']),0,1,'C');

$pdf->Cell(95,3,utf8_decode(strtoupper('de la Contraloría del Estado Aragua')),0,0,'C');
$pdf->Cell(95,3,utf8_decode(strtoupper('de la Contraloría del Estado Aragua')),0,1,'C');


} /* Dirección General  -  gerente evaluador: Figueroa*/ 
elseif ($num_gerencia == 29) {


$pdf->SetFont('Times','B',7);
$pdf->Cell(95,3,utf8_decode(strtoupper($gdesp)),0,0,'C');
$pdf->Cell(95,3,utf8_decode(strtoupper($grhrh)),0,1,'C');

$pdf->Cell(95,3,strtoupper('DIRECTOR GENERAL'),0,0,'C');
$pdf->Cell(95,3,strtoupper('GERENTE ( E ) DE '.$obtsiados['depart']),0,1,'C');

$pdf->Cell(95,3,strtoupper($obtsiatres['cdesemp']),0,0,'C');
$pdf->Cell(95,3,strtoupper($obtsiados['cdesemp']),0,1,'C');

$pdf->Cell(95,3,utf8_decode(strtoupper('de la Contraloría del Estado Aragua')),0,0,'C');
$pdf->Cell(95,3,utf8_decode(strtoupper('de la Contraloría del Estado Aragua')),0,1,'C');


} /* Servicios Generales  -  gerente evaluador: Figueroa*/ 
elseif ($num_gerencia == 36) {


$pdf->SetFont('Times','B',7);
$pdf->Cell(95,3,utf8_decode(strtoupper($gdesp)),0,0,'C');
$pdf->Cell(95,3,utf8_decode(strtoupper($grhrh)),0,1,'C');

$pdf->Cell(95,3,strtoupper('DIRECTOR GENERAL'),0,0,'C');
$pdf->Cell(95,3,strtoupper('GERENTE ( E ) DE '.$obtsiados['depart']),0,1,'C');

$pdf->Cell(95,3,strtoupper($obtsiatres['cdesemp']),0,0,'C');
$pdf->Cell(95,3,strtoupper($obtsiados['cdesemp']),0,1,'C');

$pdf->Cell(95,3,utf8_decode(strtoupper('de la Contraloría del Estado Aragua')),0,0,'C');
$pdf->Cell(95,3,utf8_decode(strtoupper('de la Contraloría del Estado Aragua')),0,1,'C');


} /* Despacho  -  gerente evaluador: Laura */ 
elseif ($num_gerencia == 37) {


$pdf->SetFont('Times','B',7);
$pdf->Cell(95,3,utf8_decode(strtoupper('FUENTES BETANCOURT, LAURA ELIZABETH')),0,0,'C');
$pdf->Cell(95,3,utf8_decode(strtoupper($grhrh)),0,1,'C');

$pdf->Cell(95,3,strtoupper('GERENTE DE Despacho'),0,0,'C');
$pdf->Cell(95,3,strtoupper('GERENTE ( E ) DE '.$obtsiados['depart']),0,1,'C');

$pdf->Cell(95,3,utf8_decode(strtoupper('de la Contraloría del Estado Aragua')),0,0,'C');
$pdf->Cell(95,3,strtoupper($obtsiados['cdesemp']),0,1,'C');

$pdf->Cell(95,3,utf8_decode(strtoupper('')),0,0,'C');
$pdf->Cell(95,3,utf8_decode(strtoupper('de la Contraloría del Estado Aragua')),0,1,'C');


} /* Recursos Humanos  -  gerente evaluador: Cacurri */ 
elseif ($num_gerencia == 30) {


$pdf->SetFont('Times','B',7);
$pdf->Cell(95,3,utf8_decode(strtoupper($grhrh)),0,0,'C');
$pdf->Cell(95,3,utf8_decode(strtoupper($gdesp)),0,1,'C');

$pdf->Cell(95,3,strtoupper('GERENTE ( E ) DE '.$obtsiados['depart']),0,0,'C');
$pdf->Cell(95,3,strtoupper('DIRECTOR GENERAL'),0,1,'C');

$pdf->Cell(95,3,strtoupper($obtsiados['cdesemp']),0,0,'C');
$pdf->Cell(95,3,strtoupper($obtsiatres['cdesemp']),0,1,'C');

$pdf->Cell(95,3,utf8_decode(strtoupper('de la Contraloría del Estado Aragua')),0,0,'C');
$pdf->Cell(95,3,utf8_decode(strtoupper('de la Contraloría del Estado Aragua')),0,1,'C');


} /* Evaluador de su Gerencia  -  gerente evaluador: el de la Gerencia */ 
else {

$pdf->SetFont('Times','B',7);
$pdf->Cell(95,3,utf8_decode(strtoupper($dato['evaluador'])),0,0,'C');
$pdf->Cell(95,3,utf8_decode(strtoupper($grhrh)),0,1,'C');

$pdf->Cell(95,3,strtoupper('GERENTE DE '.$obtsia['depart']),0,0,'C');
$pdf->Cell(95,3,strtoupper('GERENTE ( E ) DE '.$obtsiados['depart']),0,1,'C');

$pdf->Cell(95,3,strtoupper($obtsia['cdesemp']),0,0,'C');
$pdf->Cell(95,3,strtoupper($obtsiados['cdesemp']),0,1,'C');

$pdf->Cell(95,3,utf8_decode(strtoupper('de la Contraloría del Estado Aragua')),0,0,'C');
$pdf->Cell(95,3,utf8_decode(strtoupper('de la Contraloría del Estado Aragua')),0,1,'C');


}


$pdf->Output();
?>