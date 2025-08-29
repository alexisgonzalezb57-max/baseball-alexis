<?php  include("../../config/conexion.php");
$con      = conectar();
$id_juego = $_POST['id'];
$id_tab   = $_POST['id_tab'];
$id_temp  = $_POST['temp'];
$nj       = $_POST['nj'];
$cat      = $_POST['cat'];

$jj       = $_POST['jj'];
$one      = $_POST['team_one'];
$ca       = $_POST['ca'];
$two      = $_POST['team_two'];
$ce       = $_POST['ce'];
$estado   = $_POST['estado'];
$valido   = $_POST['val'];
$fecha    = $_POST['fech_part'];


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


    echo $guardar = "UPDATE juegos         SET jj    = '$jj',
                                          team_one  = '$one',
                                          ca        = '$ca' ,
                                          team_two  = '$two',
                                          ce        = '$ce' ,
                                          estado    = '$estado',
                                          valido    = '$valido',
                                          fech_part = '$fecha' WHERE id_juego = '$id_juego';";
    $resaves = mysqli_query($con, $guardar); ?> <br> <?php

if ($two == 0) { } elseif ($two > 0) {

    echo $eguardar = "UPDATE juegos         SET jj    = '$jj',
                                          team_one  = '$two',
                                          ca        = '$ce' ,
                                          team_two  = '$one',
                                          ce        = '$ca' ,
                                          estado    = '$alt_est',
                                          valido    = '$valido',
                                          fech_part = '$fecha' 
                                          WHERE nj = '$nj' AND id_tab = '$id_tabt' AND id_temp = '$id_temp' ;"; 
    $resaves = mysqli_query($con, $eguardar); ?> <br> <?php
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


  echo $configurar = "UPDATE puntaje SET id_tab  = '$id_tab',
                                         id_team = '$one',
                                         nj = '$nj',
                                         jj = '$jj',
                                         jg = '$pg',
                                         jp = '$pp',
                                         je = '$pe',
                                         ca = '$ca',
                                         ce = '$ce',
                                         val = '$valido',
                                          fech_part = '$fecha' WHERE nj = '$nj' AND id_tab = '$id_tab'; ";
  $resav = mysqli_query($con, $configurar); ?> <br> <?php

if ($two == 0) { } elseif ($two > 0) {

  echo $econfigurar = "UPDATE puntaje SET id_tab  = '$id_tabt',
                                         id_team = '$two',
                                         nj = '$nj',
                                         jj = '$jj',
                                         jg = '$gp',
                                         jp = '$bp',
                                         je = '$ep',
                                         ca = '$ce',
                                         ce = '$ca',
                                         val = '$valido',
                                          fech_part = '$fecha' WHERE nj = '$nj' AND id_tab = '$id_tabt'; ";
  $eresav = mysqli_query($con, $econfigurar);
}

Header("Location: calcular.php?id_tab=$id_tab&id_tabt=$id_tabt&id_temp=$id_temp&id_team=$one&id_teamt=$two&cat=$cat&nj=$nj");


?>