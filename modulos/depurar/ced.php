<?php

include("../../config/conexion.php");
$con=conectar();

                $tablast = "DELETE FROM jugadores_stats WHERE cedula = 9435";
                $quetast = mysqli_query($con, $tablast);

                $tablast = "DELETE FROM jugadores_stats WHERE cedula = 8146";
                $quetast = mysqli_query($con, $tablast);

                $tablast = "DELETE FROM jugadores_stats WHERE cedula = 4567";
                $quetast = mysqli_query($con, $tablast);

                $tablast = "DELETE FROM jugadores_stats WHERE cedula = 7249";
                $quetast = mysqli_query($con, $tablast);

                $tablast = "DELETE FROM jugadores_stats WHERE cedula = 6315";
                $quetast = mysqli_query($con, $tablast);

                $tablast = "DELETE FROM jugadores_stats WHERE cedula = 9435";
                $quetast = mysqli_query($con, $tablast);
?>