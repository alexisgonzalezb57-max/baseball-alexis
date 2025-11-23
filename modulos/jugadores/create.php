<?php  include("../../config/conexion.php");
$con       = conectar();
$id        = $_POST['id'];
$cedula    = $_POST['cedula'];
$nombre    = $_POST['nombre'];
$apellido  = $_POST['apellido'];
$lanzador  = $_POST['lanz'];
$categoria = $_POST['cat'];
$fecha     = $_POST['fecha'];
$edad      = $_POST['edad'];

    $guardar = "INSERT INTO jugadores SET id_team   = '$id',
                                          cedula    = '$cedula',
                                          nombre    = '$nombre',
                                          apellido  = '$apellido',
                                          fecha     = '$fecha',
                                          edad      = '$edad',
                                          lanzador  = '$lanzador',
                                          categoria = '$categoria' ";
    $resaves = mysqli_query($con, $guardar);

    if ($lanzador == 1) {
        $res = "SI";
    } elseif ($lanzador == 0) {
        $res = "NO";
    }

if ($resaves) {

$obtener = "SELECT * FROM jugadores WHERE id_team = $id";
$query   = mysqli_query($con, $obtener);
$nums    = mysqli_num_rows($query);

    $update  = "UPDATE equipos SET n_jugadores = '$nums' WHERE id_team = '$id' ";
    $upgrade = mysqli_query($con, $update);


    // Mostrar mensaje más vistoso (con carga automática de SweetAlert)
    echo "<html>
    <head>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
    <script>
        Swal.fire({
            icon: 'success',
            title: '¡Jugador Registrado Exitosamente!',
            html: '<div style=\"text-align: left;\">' +
                  '<div style=\"background: #f0f8f0; padding: 15px; border-radius: 10px; border: 2px solid #2ecc71; margin: 10px 0;\">' +
                  '<p style=\"margin: 8px 0; font-size: 16px;\"><b>🆔 Cédula:</b> $cedula</p>' +
                  '<p style=\"margin: 8px 0; font-size: 16px;\"><b>👤 Nombre:</b> $nombre</p>' +
                  '<p style=\"margin: 8px 0; font-size: 16px;\"><b>👤 Apellido:</b> $apellido</p>' +
                  '<p style=\"margin: 8px 0; font-size: 16px;\"><b>🎯 Lanzador:</b> $res</p>' +
                  '<p style=\"margin: 8px 0; font-size: 16px;\"><b>📊 Categoría:</b> $categoria</p>' +
                  '<p style=\"margin: 8px 0; font-size: 16px;\"><b>📅 Fecha Nac:</b> $fecha</p>' +
                  '<p style=\"margin: 8px 0; font-size: 16px;\"><b>🎂 Edad:</b> $edad años</p>' +
                  '</div>' +
                  '</div>',
            confirmButtonColor: '#27ae60',
            confirmButtonText: 'Aceptar',
            background: '#ffffff',
            customClass: {
                popup: 'animated bounceIn'
            }
        }).then((result) => {
            window.location.href = '../jugadores/form.php?id=$id&cat=$categoria';
        });
    </script>
    </body>
    </html>";
    exit();
  
} else {
  echo "ERROR";
}


?>