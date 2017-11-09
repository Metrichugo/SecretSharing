<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.html');
    exit;
}
include_once("BaseDeDatos.php");
include_once("Usuario.php");
//Recuperación del objeto usuario creado en Login.php
$usuario = unserialize($_SESSION["usuario"]);
//$usuario->toString(); //visualizacion de datos
//Recuperacion del objeto BD creado en Login.php
$DBConnection = unserialize($_SESSION["DBConnection"]);
$DBConnection->connect(); // Al finaliza el archivo se cierra la conexion con db
//al cargar la pagina la carpeta actual es la carpeta raiz con idCarpetaSuperior=null;
$carpetActual = $DBConnection->consultaCarpetaRaiz($usuario);
$_SESSION["carpetActual"] = serialize($carpetActual);
$EspacioTotal = 5000; //5GB
$EspacioUtilizado = ($usuario->getEspacioUtilizado() / 1E6);
$EspacioDisponible = $EspacioTotal - $EspacioUtilizado;
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Gestión de cuenta</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="../assets/bootstrap-4.0.0-beta.2-dist/css/bootstrap.min.css">
        <!-- jQuery, Popper.js, Bootstrap JS -->
        <script src="../js/jquery-3.2.1.min.js"></script>
        <script src="../js/popper.min.js"></script>
        <script src="../assets/bootstrap-4.0.0-beta.2-dist/js/bootstrap.min.js"></script>
        <!--Iconos-->
        <link href="../assets/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet">

        <!--Scripts usuario-->
        <script src="../js/usuario.js"></script>
        <script src="../js/visualizarCuenta.js"></script>

        <!-- Scripts gráfica -->        
        <script type="text/javascript" src="../js//loader.js"></script>
        <script type="text/javascript">
            // Load google charts
            google.charts.load('current', {'packages': ['corechart']});
            google.charts.setOnLoadCallback(drawChart);
            // Draw the chart and set the chart values
            function drawChart() {
                var data = google.visualization.arrayToDataTable([
                    ['Concepto', 'Megabytes'],
                    ['Espacio disponible', <?php echo $EspacioDisponible; ?>],
                    ['Espacio utilizado', <?php echo $EspacioUtilizado; ?>],
                ]);
                // Optional; add a title and set the width and height of the chart
                var options = {'width': 400, 'height': 300};
                // Display the chart inside the <div> element with id="piechart"
                var chart = new google.visualization.PieChart(document.getElementById('piechart'));
                chart.draw(data, options);
            }
        </script>       
    </head>

    <body>
        <!-- Barra de navegacion principal -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <a class="navbar-brand" href="#">Secret Sharing</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="Principal.php">Principal <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="#">Gestión de cuenta</a>
                    </li>
                </ul>
                <ul class="navbar-nav navbar-right ml-auto">
                    <li>
                        <!-- Boton cierre de sesion-->
                        <button type="button" class="btn btn-danger" onclick = "return cerrarSesion()">Cerrar sesión</button>
                    </li>
                </ul>
            </div>
        </nav>
        <br>

        <!-- Barra de navegación secundaria -->
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" id = "visualizarDetalles" href="#" > Visualizar detalles </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id = "modificarCuenta" href="#" data-disp="true">Modificar cuenta</a>
            </li>
        </ul>
        <br>

        <!-- Contenido del usuario -->
        <div class = "container" id = "contenedorGestion" >

            <!-- Contenedor info usuario-->
            <div class="card">
                <h4 class="card-header">Información del usuario</h4>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm">
                            <img  src="../assets/Login-Icon.png" class="rounded float-left ">
                        </div>
                        <div class="col-sm">
                            <label for="nombreUsuario" class="col-12"><strong> Nombre de usuario (Email): </strong></label>
                            <label id ="nombreUsuario" class="col-12" data-nombreUsuario="<?php
                            echo $usuario->getidUsuario();
                            ?>">
                                       <?php
                                       echo $usuario->getidUsuario();
                                       ?>
                            </label>
                            <label for="Alias" class="col-12"><strong>Alias</strong></label>
                            <label id ="Alias" class="col-12">
                                <?php
                                echo $usuario->getAlias();
                                ?>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <br>

            <!--Contenedor del espacio de almacenamiento-->
            <div class="card">
                <h4 class="card-header">Información del espacio de almacenamiento</h4>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm" id="piechart"></div> <!-- Grafica de pastel -->  
                        <div class="col-sm">
                            <label for="EspDisp" class="col-12"><strong>Espacio disponible </strong></label>
                            <label id ="EspDisp" class="col-12">
                                <?php
                                echo $EspacioDisponible . " MB";
                                ?>
                            </label>

                            <label for="EspUtilizado" class="col-12"><strong>Espacio utilizado</strong></label>
                            <label id ="EspUtilizado" class="col-12">
                                <?php
                                echo $EspacioUtilizado . " MB";
                                ?>
                            </label>

                            <label for="EspTotal" class="col-12"><strong>Espacio total</strong></label>

                            <label id ="EspTotal" class="col-12">
                                <?php
                                echo $EspacioTotal . " MB";
                                ?>
                            </label>

                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Modal contraseña para modificar detalles de la cuenta -->
        <div class="modal fade" id="modalModificarCuenta" tabindex="-1" role="dialog" aria-labelledby="Modal" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Modificar cuenta</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>                   
                    </div>

                    <div class="modal-body">
                        <p>Para poder modificar tu cuenta introduce nuevamente tu contraseña <p>
                        <div class="col-sm-12 ">
                            <input type="password" class="form-control" id="contraseniaModal" placeholder="Introduce aquí tu contraseña" name="contraseña">
                        </div>
                        <div class="col-sm-12" id = "ErrorContrasenia"></div>                       
                    </div>
                    <div class="modal-footer">
                        <button id="modalCancelar" type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button id="modalAceptar"  type="button" class="btn btn-primary">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>  

    </body>
</html>