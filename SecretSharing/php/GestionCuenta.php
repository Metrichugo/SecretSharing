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

$EspacioTotal = 1000000000;
$EspacioUtilizado = $usuario->getEspacioUtilizado();
$EspacioDisponible = $EspacioTotal - $EspacioUtilizado;
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Secreto Compartido</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css">
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js"></script>


        <script src="../js/carpeta.js"></script>
        <script src="../js/archivo.js"></script>
        <script src="../js/descargaArchivo.js"></script>

        <script src="../assets/Highcharts/code/highcharts.js"></script>
        <script src="../assets/Highcharts/code/modules/exporting.js"></script>
        <script src="../js/grafica.js"></script>

        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

        <script type="text/javascript">
            // Load google charts
            google.charts.load('current', {'packages': ['corechart']});
            google.charts.setOnLoadCallback(drawChart);

            // Draw the chart and set the chart values
            function drawChart() {
                var data = google.visualization.arrayToDataTable([
                    ['Task', 'Hours per Day'],
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
        <!-- Barra de navegacion -->
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
                        <a class="btn btn-danger" href="logout.php">Cerrar sesión</a>
                    </li>
                </ul>
            </div>
        </nav>
        <br>



        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" href="GestionCuenta.php" > Visualizar detalles </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Modificar cuenta</a>
            </li>
        </ul>
        <br>

        <!-- Contenido del usuario -->
        <div class = "container" id = "contenedorGestion" >
            <h4>Información del usuario</h4>
            <div class = "row justify-content-center">
                <div class="col">
                </div>
                <div class="col-6">
                    <div class = "row">
                        <div class = "col-6 ">                
                            <img  src="../assets/Login-Icon.png" width=70% height="auto" >
                        </div>
                        <div class = "col-6">
                            <label for="nombreUsuario" class="col-12"><strong> Nombre de usuario (Email): </strong></label>
                            <label id ="nombreUsuario" class="col-12">
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
                <div class="col"></div>    

            </div>
            <br>
            <h4>Información del espacio de almacenamiento</h4>
            <div class = "row justify-content-center">
                <div class="col">
                </div>
                <div class="col-6">
                    <div class = "row">

                        <div class = "col-12 ">  

                            <div id="piechart"></div>


                            <h5>Espacio de almacenamiento</h5>           
                            <label for="EspDisp" class="col-12"><strong>Espacio disponible </strong></label>
                            <label id ="EspDisp" class="col-12">
                                <?php
                                echo $EspacioDisponible . " Mb";
                                ?>
                            </label>

                            <label for="EspUtilizado" class="col-12"><strong>Espacio utilizado</strong></label>
                            <label id ="EspUtilizado" class="col-12">
                                <?php
                                echo $EspacioUtilizado . " Mb";
                                ?>
                            </label>

                            <label for="EspTotal" class="col-12"><strong>Espacio total</strong></label>

                            <label id ="EspTotal" class="col-12">
                                <?php
                                echo $EspacioTotal . " Mb";
                                ?>
                            </label>

                        </div>  
                    </div>

                </div>
                <div class="col"></div>
            </div>

        </div>

    </body>
</html>