<?php  include("../../config/conexion.php");
$con = conectar();
$id  = $_POST['id'];
$cat  = $_POST['cat'];
$temp  = $_POST['temp'];
$abono  = $_POST['abono'];

$revisar = "SELECT * FROM monto WHERE numero = $abono AND id_abn = '$id'";
$verificar = mysqli_query($con, $revisar);
$sacar = mysqli_num_rows($verificar);

if ($sacar >= 1) {

        if( isset( $_POST['idequipo'] ) ){
        foreach( $_POST['idequipo'] as $indice => $preg ){
            $monto = $_POST['monto'][$indice];
            $upconsulta_sql = "UPDATE monto SET monto     = '$monto'
                                              WHERE 
                                              numero    = '$abono' AND
                                              categoria = '$cat' AND
                                              id_temp   = '$temp' AND
                                              id_team   = '$preg' AND
                                              id_abn    = '$id'
                                              ";
            mysqli_query( $con, $upconsulta_sql );     
        }
    }
    
} else {

    if( isset( $_POST['idequipo'] ) ){
        foreach( $_POST['idequipo'] as $indice => $preg ){
            $monto = $_POST['monto'][$indice];
            $consulta_sql = "INSERT INTO monto SET id_abn    = '$id', 
                                                   categoria = '$cat', 
                                                   id_temp   = '$temp', 
                                                   id_team   = '$preg', 
                                                   monto     = '$monto',
                                                   numero    = '$abono'";
            mysqli_query( $con, $consulta_sql );     
        }
    }
    
}


header("Location: list.php?idn=$id&id=$temp&cat=$cat" );

?>