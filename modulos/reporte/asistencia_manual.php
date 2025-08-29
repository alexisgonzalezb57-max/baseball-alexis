<?php
include("../../config/conexion.php");
$con = conectar();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Asistencia Manual</title>
<link rel="stylesheet" href="../../css/bootstrap/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="../../css/styles/styleteams.css">
<script src="../../js/jquery/jquery-3.2.1.js"></script>
<script src="../../css/bootstrap/popper.min.js"></script>
<script>
    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    })
</script>

<style>
  /* Para que el div contenedor tenga fondo blanco con algo de padding y borde redondeado */
  .form-container {
      background-color: white;
      padding: 25px 30px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      margin-top: 20px;
      margin-bottom: 40px;
  }
</style>

</head>
<body>

<section>
  <header>
    <a href="../../" class="logo">BASEBALL</a>
    <ul class="navigation">
      <li><a href="../equipos/">Equipos</a></li>
      <li><a href="../juego/">Temporada</a></li>
      <li><a href="../calendario/">Calendario</a></li>
      <li><a href="../homenaje/homenaje.php">Homenaje</a></li>
      <li><a href="../abonos/">Abono</a></li>
      <li><a href="../reporte/reporte.php">Reportes</a></li>
    </ul>
    <div class="second">
      <a class="content" href="#">
        <div class="clock">
          <h2 id="hour">00</h2> <h2 id="dot">:</h2>
          <h2 id="minute">00</h2> <h2 id="dot">:</h2>
          <h2 id="seconds">00</h2> <span id="ampm">AM</span>
        </div>
      </a>
    </div>
</section>
</header>

<div class="container mt-4">

    <!-- Form Container con fondo blanco -->
    <div class="form-container" style="margin-top: -850px;">

        <h3>Asistencia Manual</h3>

        <form id="formAsistencia" method="POST" target="_blank" action="../PDF/generar_asistencia_manual.php">

            <div class="form-group row">
                <label for="categoria" style="font-size: 1.7em; color: var(--black);" class="col-sm-2 col-form-label">Categoría</label>
                <div class="col-sm-3">
                    <select style="font-size: 1.3em; color: var(--black);" id="categoria" name="categoria" class="form-control" required>
                        <option value="">-- Seleccionar --</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                    </select>
                </div>

                <label for="temporada" style="font-size: 1.7em; color: var(--black);" class="col-sm-3 col-form-label">Temporada</label>
                <div class="col-sm-4">
                    <select style="font-size: 1.3em; color: var(--black);" id="temporada" name="temporada" class="form-control" required disabled>
                        <option value="">-- Seleccionar categoría primero --</option>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-sm-12">
                    <button type="button" id="btnCargar" class="btn btn-primary" disabled>Cargar Equipos y Jugadores</button>
                    <a href="reporte.php" class="btn btn-info ml-2" data-toggle="tooltip" data-placement="top" title="Volver">Volver</a>
                </div>
            </div>

            <hr>

            <div id="listaEquiposJugadores">
                <!-- Aquí se cargarán por AJAX los equipos y jugadores con checkboxes -->
            </div>

            <div class="form-group mt-3">
                <button type="submit" class="btn btn-success" id="btnEnviar" disabled>Generar PDF Asistencia</button>
            </div>

        </form>

    </div> <!-- /form-container -->

</div> <!-- /container -->

<script>
$(document).ready(function() {

    // Deshabilitar botones al inicio
    $('#btnCargar').prop('disabled', true);
    $('#btnEnviar').prop('disabled', true);

    // Al cambiar categoría, cargar temporadas relacionadas
    $('#categoria').change(function() {
        let categoria = $(this).val();
        $('#temporada').prop('disabled', true).html('<option>Cargando...</option>');
        $('#listaEquiposJugadores').html('');
        $('#btnCargar').prop('disabled', true);
        $('#btnEnviar').prop('disabled', true);

        if(categoria) {
            $.ajax({
                url: 'get_temporadas.php',
                type: 'GET',
                data: { categoria: categoria },
                success: function(data) {
                    $('#temporada').html(data).prop('disabled', false);
                },
                error: function() {
                    $('#temporada').html('<option>Error cargando temporadas</option>').prop('disabled', true);
                }
            });
        } else {
            $('#temporada').html('<option>Seleccione categoría primero</option>').prop('disabled', true);
        }
    });

    // Al cambiar temporada, habilitar cargar y limpiar lista
    $('#temporada').change(function() {
        $('#listaEquiposJugadores').html('');
        $('#btnCargar').prop('disabled', !$(this).val());
        $('#btnEnviar').prop('disabled', true);
    });

    // Cargar equipos y jugadores por AJAX
    $('#btnCargar').click(function() {
        let categoria = $('#categoria').val();
        let temporada = $('#temporada').val();

        if (!categoria || !temporada) {
            alert('Seleccione categoría y temporada primero.');
            return;
        }

        $('#listaEquiposJugadores').html('Cargando equipos y jugadores...');
        $('#btnEnviar').prop('disabled', true);

        $.ajax({
            url: 'get_equipos_jugadores.php',
            type: 'GET',
            data: { categoria: categoria, temporada: temporada },
            success: function(data) {
                $('#listaEquiposJugadores').html(data);
                $('#btnEnviar').prop('disabled', false);
            },
            error: function() {
                $('#listaEquiposJugadores').html('<div class="alert alert-danger">Error cargando equipos y jugadores.</div>');
                $('#btnEnviar').prop('disabled', true);
            }
        });
    });

    // Validar que haya al menos un jugador seleccionado antes de enviar
    $('#formAsistencia').submit(function(e) {
        if ($('input[name="asistencia[]"]:checked').length === 0) {
            alert('Debe seleccionar al menos un jugador.');
            e.preventDefault();
        }
    });

});
</script>

<script>
function clock(){
    let hour = document.getElementById('hour');
    let minute = document.getElementById('minute');
    let seconds = document.getElementById('seconds');
    let ampm = document.getElementById('ampm');

    let h = new Date().getHours();
    let m = new Date().getMinutes();
    let s = new Date().getSeconds();
    var am = 'AM';

    if ( h > 12 ) {
        h = h - 12;
        am = 'PM';
    }

    h = ( h < 10 ) ? '0' + h : h;
    m = ( m < 10 ) ? '0' + m : m;
    s = ( s < 10 ) ? '0' + s : s;

    hour.innerHTML = h;
    minute.innerHTML = m;
    seconds.innerHTML = s;
    ampm.innerHTML = am;
}

setInterval(clock, 1000);
</script>

<script src="../../js/bootstrap/bootstrap.bundle.min.js"></script>
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

</body>
</html>
