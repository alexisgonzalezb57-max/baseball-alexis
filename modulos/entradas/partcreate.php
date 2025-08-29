<?php  include("../../config/conexion.php");
$con     = conectar();
$id_tab  = $_POST['id_tab'];
$id_temp = $_POST['id_temp'];
$id_team = $_POST['anfitrion'];
$nj      = $_POST['nj'];
$cat     = $_POST['cat'];

$jj      = $_POST['jj'];
$one     = $_POST['team_one'];
$ca      = $_POST['ca'];
$two     = $_POST['team_two'];
$ce      = $_POST['ce'];
$estado  = $_POST['estado'];
$valido  = $_POST['val'];
$fecha   = $_POST['fech_part'];


$revisar = "SELECT temporada.*, tab_clasf.* FROM temporada INNER JOIN tab_clasf ON temporada.id_temp = tab_clasf.id_temp WHERE tab_clasf.id_team = $two AND tab_clasf.id_temp = $id_temp";
$query   = mysqli_query($con,$revisar);
$data    = mysqli_fetch_array($query);
$id_tabt = $data['id_tab'];


    if ($estado == "Empatado") {

      $alt_est = $estado;
       
    } elseif ($estado == "Ganando") {

      $alt_est = "Perdido";
       
    } elseif ($estado == "Perdido") {

      $alt_est = "Ganando";

    }


  $guardar = "INSERT INTO juegos     SET id_tab    = '$id_tab',
                                          id_temp   = '$id_temp',
                                          nj        = '$nj',
                                          jj        = '$jj',
                                          team_one  = '$id_team',
                                          ca        = '$ca' ,
                                          team_two  = '$two' ,
                                          ce        = '$ce' ,
                                          estado    = '$estado',
                                          valido    = '$valido',
                                          fech_part = '$fecha';"; 
  $resaves = mysqli_query($con, $guardar);

if ($two == 0) { } elseif ($two > 0) {

  $eguardar = "INSERT INTO juegos     SET id_tab    = '$id_tabt',
                                          id_temp   = '$id_temp',
                                          nj        = '$nj',
                                          jj        = '$jj',
                                          team_one  = '$two',
                                          ca        = '$ce' ,
                                          team_two  = '$id_team' ,
                                          ce        = '$ca' ,
                                          estado    = '$alt_est',
                                          valido    = '$valido',
                                          fech_part = '$fecha';";
   $eresaves = mysqli_query($con, $eguardar);

}

    if ($estado == "Empatado") {

      $pg = 0;      $gp = 0;
      $pp = 0;      $bp = 0;
      $pe = 1;      $ep = 1;

    } elseif ($estado == "Ganando") {

      $pg = 1;      $gp = 0;
      $pp = 0;      $bp = 1;
      $pe = 0;      $ep = 0;
       
    } elseif ($estado == "Perdido") {

      $pg = 0;      $gp = 1;
      $pp = 1;      $bp = 0;
      $pe = 0;      $ep = 0;
      
    }


    $configurar = "INSERT INTO puntaje SET id_tab  = '$id_tab',
                                         id_team = '$id_team',
                                         nj = '$nj',
                                         jj = '$jj',
                                         jg = '$pg',
                                         jp = '$pp',
                                         je = '$pe',
                                         ca = '$ca',
                                         ce = '$ce',
                                         val = '$valido',
                                         fech_part = '$fecha';"; 
    $resav = mysqli_query($con, $configurar);

if ($two == 0) { } elseif ($two > 0) {

    $econfigurar = "INSERT INTO puntaje SET id_tab  = '$id_tabt',
                                         id_team = '$two',
                                         nj = '$nj',
                                         jj = '$jj',
                                         jg = '$gp',
                                         jp = '$bp',
                                         je = '$ep',
                                         ca = '$ce',
                                         ce = '$ca',
                                         val = '$valido',
                                         fech_part = '$fecha';";
    $eresav = mysqli_query($con, $econfigurar);

}

Header("Location: calcular.php?id_tab=$id_tab&id_tabt=$id_tabt&id_temp=$id_temp&id_team=$id_team&id_teamt=$two&cat=$cat&nj=$nj");

?>