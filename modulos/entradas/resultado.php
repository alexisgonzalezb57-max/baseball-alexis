<?php
include("../../config/conexion.php");
$con=conectar();
$ca = $_GET['ca'];
$ce = $_GET['ce'];

if ($ca === $ce) {
	print "<input type='text' class='form-control-plaintext' required value='Empatado' style='font-size: 1.4em; padding-left: 10px; color: var(--black);' readonly name='estado'>";
} elseif ($ca > $ce) {
	print "<input type='text' class='form-control-plaintext' required value='Ganando' style='font-size: 1.4em; padding-left: 10px; color: var(--black);' readonly name='estado'>";
} elseif ($ca < $ce) {
	print "<input type='text' class='form-control-plaintext' required value='Perdido' style='font-size: 1.4em; padding-left: 10px; color: var(--black);' readonly name='estado'>";
}

?>