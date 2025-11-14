<?php  include("../../config/conexion.php");
$con       = conectar();
$id_player = $_POST['id_player'];
$id_team   = $_POST['id_team'];
$id_tab    = $_POST['id_tab'];
$cedula    = $_POST['cedula'];
$id_js     = $_POST['id_js'];
$nj        = $_POST['nj'];


$vb      = $_POST['vb'];
$h       = $_POST['h'];
$hr      = $_POST['hr'];
$twob    = $_POST['2b'];
$threeb  = $_POST['3b'];
$ca      = $_POST['ca'];
$ci      = $_POST['ci'];
$k       = $_POST['k'];
$b       = $_POST['b'];
$a       = $_POST['a'];
$fl      = $_POST['fl'];
$br      = $_POST['br'];
$gp      = $_POST['gp'];

echo $guardar = "UPDATE jugadores_stats SET vb = '$vb',
                                       h  = '$h',
                                       hr = '$hr',
                                       2b = '$twob',
                                       3b = '$threeb',
                                       ca = '$ca',
                                       ci = '$ci',
                                       k  = '$k',
                                       b  = '$b',
                                       a  = '$a',
                                       sf = '$fl',
                                       br = '$br',
                                       gp = '$gp' 
                                       WHERE id_js = '$id_js';";
/*$resaves = mysqli_query($con, $guardar);


Header("Location: jgcalcular.php?id_tab=$id_tab&id_team=$id_team&id_player=$id_player&cedula=$cedula&nj=$nj");
*/

?>