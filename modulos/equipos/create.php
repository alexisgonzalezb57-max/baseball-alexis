<?php  include("../../config/conexion.php");
$con         = conectar();
$name        = $_POST['n_team'];
$categoria   = $_POST['categoria'];
$jugadores   = 0;
$manager     = $_POST['manager'];
$delegado    = $_POST['delegado'];
$subdelegado = $_POST['subdelegado'];

    $guardar = "INSERT INTO equipos SET nom_team    = '$name', 
                                        n_jugadores = '$jugadores',
                                        categoria   = '$categoria',
                                        manager     = '$manager',
                                        delegado    = '$delegado',
                                        subdelegado = '$subdelegado'";
    $resaves = mysqli_query($con, $guardar);
    
    // Mostrar mensaje más vistoso (con carga automática de SweetAlert)
    echo "<html>
    <head>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
    <script>
        Swal.fire({
            icon: 'success',
            title: '¡Equipo Creado Exitosamente!',
            html: '<div style=\"text-align: left;\">' +
                  '<div style=\"background: #f0f8f0; padding: 15px; border-radius: 10px; border: 2px solid #2ecc71; margin: 10px 0;\">' +
                  '<p style=\"margin: 8px 0; font-size: 16px;\"><b>🏷️ Nombre:</b> $name</p>' +
                  '<p style=\"margin: 8px 0; font-size: 16px;\"><b>📊 Categoría:</b> $categoria</p>' +
                  '<p style=\"margin: 8px 0; font-size: 16px;\"><b>👨‍💼 Manager:</b> $manager</p>' +
                  '<p style=\"margin: 8px 0; font-size: 16px;\"><b>🤝 Delegado:</b> $delegado</p>' +
                  '<p style=\"margin: 8px 0; font-size: 16px;\"><b>👥 Subdelegado:</b> $subdelegado</p>' +
                  '</div>' +
                  '</div>',
            confirmButtonColor: '#27ae60',
            confirmButtonText: 'Aceptar',
            background: '#ffffff',
            customClass: {
                popup: 'animated bounceIn'
            }
        }).then((result) => {
            window.location.href = '../equipos/nomina.php?cat=$categoria';
        });
    </script>
    </body>
    </html>";
    exit();
?>