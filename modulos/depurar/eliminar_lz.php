<?php

include("../../config/conexion.php");
$con=conectar();
$id = $_REQUEST['idrslz'];
$temp = $_REQUEST['temp'];
$cat = $_REQUEST['cat'];
$equip = $_REQUEST['equip'];

                $tablast = "DELETE FROM resumen_lanz WHERE id_team = $equip";
                $quetast = mysqli_query($con, $tablast);
Header("Location: listado.php?temporada=$temp&categoria=$cat&equipone=$equip");

?>