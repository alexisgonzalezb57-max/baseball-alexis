<?php  include("../../config/conexion.php");
$con       = conectar();
$id        = $_POST['id'];
$team      = $_POST['team'];
$cedula    = $_POST['cedula'];
$nombre    = $_POST['nombre'];
$apellido  = $_POST['apellido'];
$lanzador  = $_POST['lanz'];
$cat       = $_POST['cat'];
$fecha     = $_POST['fecha'];
$edad      = $_POST['edad'];
$categoria = $_POST['cat'];

    $guardar = "UPDATE jugadores SET cedula   = '$cedula',
                                     nombre   = '$nombre',
                                     apellido = '$apellido',
                                     fecha    = '$fecha',
                                     edad     = '$edad',
                                     lanzador = '$lanzador' 
                                     WHERE id_player = '$id' ";
    $resaves = mysqli_query($con, $guardar);

    if ($lanzador == 1) {
        $res = "SI";
    } elseif ($lanzador == 0) {
        $res = "NO";
    }

    // Mostrar mensaje más vistoso (con carga automática de SweetAlert)
    echo "<html>
    <head>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
    <script>
        Swal.fire({
            icon: 'success',
            title: '¡Jugador Editado Exitosamente!',
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
            window.location.href = '../jugadores/list.php?id=$team&cat=$cat';
        });
    </script>
    </body>
    </html>";
    exit();

?>