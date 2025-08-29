<?php  include("../../config/conexion.php");
$con       = conectar();
$categoria = $_POST['categoria'];
$temporada = $_POST['temporada'];
$partida   = $_POST['partida'];
$team_one  = $_POST['team_one'];
$team_two  = $_POST['team_two'];
$dia       = $_POST['dia'];
$id_hora   = $_POST['hora'];
$campo     = $_POST['campo'];
$fecha     = $_POST['fecha'];
$id        = $_POST['id'];


/*Nombre Equipo 1*/
$val_one   = "SELECT * FROM tab_clasf WHERE id_temp = $temporada AND id_team = $team_one";
$query_one = mysqli_query($con, $val_one);
$obteneron = mysqli_fetch_array($query_one);
$name_one  = $obteneron['name_team'];
$id_tab    = $obteneron['id_tab'];

/*Nombre Equipo 2*/
$val_two   = "SELECT * FROM tab_clasf WHERE id_temp = $temporada AND id_team = $team_two";
$query_two = mysqli_query($con, $val_two);
$obtenertw = mysqli_fetch_array($query_two);
$name_two  = $obtenertw['name_team'];
$id_tabt   = $obtenertw['id_tab'];

/*Nombre hora*/
$val_hor   = "SELECT * FROM tiempos WHERE id_tiempo = $id_hora";
$query_hor = mysqli_query($con, $val_hor);
$obtenerho = mysqli_fetch_array($query_hor);
$name_hor  = $obtenerho['hora'];


$guardar = "UPDATE  calendario  SET fecha         = '$fecha',
                                    categoria     = '$categoria',
                                    id_temporada  = '$temporada',
                                    id_team_one   = '$team_one',
                                    name_team_one = '$name_one',
                                    id_team_two   = '$team_two',
                                    name_team_two = '$name_two',
                                    dia           = '$dia',
                                    id_hora       = '$id_hora',
                                    hora          = '$name_hor',
                                    campo         = '$campo',
                                    partida       = '$partida' 
                                    WHERE id_cal  = '$id';";
$resaves = mysqli_query($con, $guardar);




Header("Location: ../calendario/");


?>