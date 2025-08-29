<?php
include("../../config/conexion.php");
$con = conectar();

// Verificar conexión
if (!$con) {
    die("Error de conexión a la base de datos: " . mysqli_connect_error());
}

// Consulta para obtener la fecha
$query = "SELECT timeday FROM report LIMIT 1";
$result = mysqli_query($con, $query);

if (!$result) {
    die("Error en la consulta: " . mysqli_error($con));
}

$ddtt = mysqli_fetch_assoc($result);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inicio</title>
    <link rel="stylesheet" href="../../css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/styles/styleteams.css">
    <script src="../../js/jquery/jquery-3.2.1.js"></script>
    <script src="../../js/bootstrap/popper.min.js"></script>
    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
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
                        <h2 id="hour">00</h2>  
                        <h2 id="dot">:</h2>
                        <h2 id="minute">00</h2>  
                        <h2 id="dot">:</h2>
                        <h2 id="seconds">00</h2> 
                        <span id="ampm">AM</span>
                    </div>
                </a>
            </div>
        </header>
    </section>

    <!--  System Card -->
    <div class="list">
        <div class="seudo"></div>
        <h4 class="titulo">Reportes</h4><br>
        <div class="cardList">
            <table class="table table-sm">
                <thead class="thead-dark">
                    <tr>
                        <th class="alte" colspan="3">
                            <form id="fechaForm">
                                <label>
                                    Fecha para todos los Reportes: 
                                    <input type="date" name="tempo" value="<?php echo htmlspecialchars($ddtt['timeday'], ENT_QUOTES, 'UTF-8'); ?>" required> 
                                    <input type="submit" value="Guardar" class="btn btn-info">
                                </label>
                            </form>
                        </th>
                    </tr>
                    <tr>
                        <th class="alte">Tipo</th>
                        <th class="alte">Contenido</th>
                        <th class="alte">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th style="border-bottom: 4px solid #000; border-right: 4px solid #444;" class="alte">
                            Calendario
                        </th>
                        <th style="border-bottom: 4px solid #000;">
                            Horario de todas la temporadas activas por fecha para Generar la Tabla
                        </th>
                        <th style="border-bottom: 4px solid #000;">
                            <a href="calendario.php"><button type="button" class="btn btn-info" style="color: #000;">Ingresar</button></a>
                        </th>
                    </tr>
                    <tr>
                        <th style="border-bottom: 4px solid #000; border-right: 4px solid #444;" class="alte">
                            Nomina
                        </th>
                        <th style="border-bottom: 4px solid #000;">
                            Elige el listado del equipo de la Nomina
                        </th>
                        <th style="border-bottom: 4px solid #000;">
                            <a href="nomina.php"><button type="button" class="btn btn-info" style="color: #000;">Ingresar</button></a>
                        </th>
                    </tr>
                    <tr>
                        <th style="border-bottom: 4px solid #000; border-right: 4px solid #444;" class="alte">
                            Abonos
                        </th>
                        <th style="border-bottom: 4px solid #000;">
                            Elige la temporada
                        </th>
                        <th style="border-bottom: 4px solid #000;">
                            <a href="../PDF/abono.php"><button type="button" class="btn btn-info" style="color: #000;">Ingresar</button></a>
                        </th>
                    </tr>
                    <tr>
                        <th style="border-bottom: 4px solid #000; border-right: 4px solid #444;" class="alte">
                            Tabla de Clasificación
                        </th>
                        <th style="border-bottom: 4px solid #000;">
                            Muestra todas las tablas de clasificación de todas las temporadas "activas", se puede elegir si es por AVG o por la Diferencia de Carreras (Dif) para Generar la Tabla
                        </th>
                        <th style="border-bottom: 4px solid #000;">
                            <a href="clasificacion.php"><button type="button" class="btn btn-info" style="color: #000;">Ingresar</button></a>
                        </th>
                    </tr>
                    <tr>
                        <th style="border-bottom: 4px solid #000; border-right: 4px solid #444;" class="alte">
                            Resumen - Jugadores | Pichers
                        </th>
                        <th style="border-bottom: 4px solid #000;">
                            Elige la Categoria y la temporada Activa, luego puede elegir si es un solo Equipo para Generar la Tabla
                        </th>
                        <th style="border-bottom: 4px solid #000;">
                            <a href="resumen.php"><button type="button" class="btn btn-info" style="color: #000;">Ingresar</button></a>
                        </th>
                    </tr>
                    <tr>
                        <th style="border-bottom: 4px solid #000; border-right: 4px solid #444;" class="alte">
                            Asistencia Basica / Por Valor
                        </th>
                        <th style="border-bottom: 4px solid #000;">
                            Elige la Categoria y la temporada Activa para Generar la Tabla
                        </th>
                        <th style="border-bottom: 4px solid #000;">
                            <a href="asistencia.php"><button type="button" class="btn btn-info" style="color: #000;">Ingresar</button></a>
                        </th>
                    </tr>
                    <tr>
                        <th style="border-bottom: 4px solid #000; border-right: 4px solid #444;" class="alte">
                            Asistencia Manual
                        </th>
                        <th style="border-bottom: 4px solid #000;">
                            Elige la Categoria y la temporada Activa para Generar la Tabla
                        </th>
                        <th style="border-bottom: 4px solid #000;">
                            <a href="asistencia_manual.php"><button type="button" class="btn btn-info" style="color: #000;">Ingresar</button></a>
                        </th>
                    </tr>
                    <tr>
                        <th style="border-bottom: 4px solid #000; border-right: 4px solid #444;" class="alte">
                            Pichers - Ganados | Efectividad
                        </th>
                        <th style="border-bottom: 4px solid #000;">
                            Elige la Categoria y la temporada Activa para Generar la Tabla por Cada Equipo o Todos los Equipos en una sola Tabla
                        </th>
                        <th style="border-bottom: 4px solid #000;">
                            <a href="pichers.php"><button type="button" class="btn btn-info" style="color: #000;">Ingresar</button></a>
                        </th>
                    </tr>
                    <tr>
                        <th style="border-bottom: 4px solid #000; border-right: 4px solid #444;" class="alte">
                            Lideres - En Bateo | En Jonrones
                        </th>
                        <th style="border-bottom: 4px solid #000;">
                            Elige la Categoria y la temporada Activa para Generar la Tabla por Cada Equipo o Todos los Equipos en una sola Tabla
                        </th>
                        <th style="border-bottom: 4px solid #000;">
                            <a href="lidvbhr.php"><button type="button" class="btn btn-info" style="color: #000;">Ingresar</button></a>
                        </th>
                    </tr>
                    <tr>
                        <th style="border-bottom: 4px solid #000; border-right: 4px solid #444;" class="alte">
                            Lideres - En Dobles | En Triples
                        </th>
                        <th style="border-bottom: 4px solid #000;">
                            Elige la Categoria y la temporada Activa para Generar la Tabla por Cada Equipo o Todos los Equipos en una sola Tabla
                        </th>
                        <th style="border-bottom: 4px solid #000;">
                            <a href="lid2b3b.php"><button type="button" class="btn btn-info" style="color: #000;">Ingresar</button></a>
                        </th>
                    </tr>
                    <tr>
                        <th style="border-bottom: 4px solid #000; border-right: 4px solid #444;" class="alte">
                            Lideres en Carreras - Anotadas | Empujadas
                        </th>
                        <th style="border-bottom: 4px solid #000;">
                            Elige la Categoria y la temporada Activa para Generar la Tabla por Cada Equipo o Todos los Equipos en una sola Tabla
                        </th>
                        <th style="border-bottom: 4px solid #000;">
                            <a href="lidcaci.php"><button type="button" class="btn btn-info" style="color: #000;">Ingresar</button></a>
                        </th>
                    </tr>
                    <tr>
                        <th style="border-bottom: 4px solid #000; border-right: 4px solid #444;" class="alte">
                            Lideres en Carreras - Ponches | Boletos
                        </th>
                        <th style="border-bottom: 4px solid #000;">
                            Elige la Categoria y la temporada Activa para Generar la Tabla por Cada Equipo o Todos los Equipos en una sola Tabla
                        </th>
                        <th style="border-bottom: 4px solid #000;">
                            <a href="lidkobo.php"><button type="button" class="btn btn-info" style="color: #000;">Ingresar</button></a>
                        </th>
                    </tr>
                    <tr>
                        <th style="border-bottom: 4px solid #000; border-right: 4px solid #444;" class="alte">
                            Historial
                        </th>
                        <th style="border-bottom: 4px solid #000;">
                            Elige la Categoria y la temporada Activa para Generar la Tabla
                        </th>
                        <th style="border-bottom: 4px solid #000;">
                            <a href="historial.php"><button type="button" class="btn btn-info" style="color: #000;">Ingresar</button></a>
                        </th>
                    </tr>
                </tbody>
            </table>
        </div><br>
    </div>

<script>
    function clock() {
        let hour = document.getElementById('hour');
        let minute = document.getElementById('minute');
        let seconds = document.getElementById('seconds');
        let ampm = document.getElementById('ampm');

        let h = new Date().getHours();
        let m = new Date().getMinutes();
        let s = new Date().getSeconds();
        let am = 'AM';

        if (h > 12) {
            h -= 12;
            am = 'PM';
        }
        if (h === 0) h = 12; // Soporte para 12 AM

        hour.textContent = h < 10 ? '0' + h : h;
        minute.textContent = m < 10 ? '0' + m : m;
        seconds.textContent = s < 10 ? '0' + s : s;
        ampm.textContent = am;
    }

    setInterval(clock, 1000);
    clock();

    document.getElementById('fechaForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(this);

        fetch('save.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert('¡Fecha guardada correctamente!');
            } else {
                alert('Error al guardar la fecha');
            }
        })
        .catch(error => {
            alert('Ocurrió un error: ' + error.message);
        });
    });
</script>

<script src="../../js/bootstrap/bootstrap.bundle.min.js"></script>
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>
