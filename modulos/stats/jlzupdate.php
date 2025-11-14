<?php  include("../../config/conexion.php");
$con       = conectar();
$id_player = $_POST['id_player'];
$id_team   = $_POST['id_team'];
$id_tab    = $_POST['id_tab'];
$cedula    = $_POST['cedula'];
$id_lanz   = $_POST['id_lanz'];
$nj        = $_POST['nj'];


$jl      = $_POST['jl'];
$jg      = $_POST['jg'];
$il      = $_POST['il'];
$cp      = $_POST['cp'];
$cpl     = $_POST['cpl'];
$h       = $_POST['h'];
$twob    = $_POST['2b'];
$threeb  = $_POST['3b'];
$hr      = $_POST['hr'];
$b       = $_POST['b'];
$k       = $_POST['k'];
$va      = $_POST['va'];
$gp      = $_POST['gp'];
$br      = $_POST['br'];

echo $guardar = "UPDATE jugadores_lanz SET jl  = '$jl',
                                      jg  = '$jg',
                                      il  = '$il',
                                      cp  = '$cp',
                                      cpl = '$cpl',
                                      h   = '$h',
                                      2b  = '$twob',
                                      3b  = '$threeb',
                                      hr  = '$hr',
                                      b   = '$b',
                                      k   = '$k',
                                      va  = '$va',
                                      gp  = '$gp',
                                      ile  = '$br' 
                                      WHERE id_lanz = '$id_lanz';";
$resaves = mysqli_query($con, $guardar);

Header("Location: jlzcalcular.php?id_tab=$id_tab&id_team=$id_team&id_player=$id_player&cedula=$cedula&nj=$nj");


?>