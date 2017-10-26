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
        <script src="../js/usuario.js"></script>
        
        <script src="../js/descargaArchivo.js"></script>
        
        <script src="../js/GestionCuenta.js">
        
        <script src="../assets/Highcharts/code/highcharts.js"></script>
        <script src="../assets/Highcharts/code/modules/exporting.js"></script>
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
                <a class="nav-link active" id = "visualizarDetalles" href="GestionCuenta.php" > Visualizar detalles </a>
            </li>
            <li class="nav-item">
                <a class="nav-link"        id = "modificarCuenta"    href="#" data-toggle="modal" data-target="#modalModificarCuenta">Modificar cuenta</a>
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
        
        
        <!-- Contenedor para modificarCuenta -->
        <div class = "container" id = "contenedorModificarCuenta">
                    <div class = "row justify-content-center">
            <div class="col-sm-12 col-md-4">
               
               <img  src="../assets/Login-Icon.png" width=70% height="auto" >
               <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
         
            <button id= "eliminarCuenta" type="button" class="col-sm-12 btn btn-primary">Eliminar Cuenta</button>
              
               
            </div>
            <div class="col-sm-12 col-md-8">
               <h4>Cambiar nombre de usuario</h4>
                <div class="form-bottom" id = "formNombreUsuario">
                    <form>
                        <div class="form-group row">
                            <label for="Email" class="col-sm-12 col-form-label">Nombre de Usuario (Email)</label>
                            <div class="col-sm-12 col-md-10">
                                <input type="email" class="form-control" id="Email" placeholder="ejemplo@servidor.com" name="Email" required autofocus>
                            </div>
                            <label for="confirmaEmail" class="col-sm-12 col-form-label">Confirmación del nombre de usuario (Email)</label>
                            <div class="col-sm-12 col-md-10">
                                <input type="email" class="form-control" id="confirmaEmail" placeholder="ejemplo@servidor.com" name="confirmacionEmail" required autofocus>
                            </div>
                            <div class="col-sm-12 col-md-10" id="ErrorCambiarEmail"> </div>
                            <div class="col-sm-12 col-md-10" id="ResCambiarEmail"> </div>
                           
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6 offset-sm-3 offset-md-3">
                                <button id = "cambiarNombreUsuario" type="button" class="btn btn-primary">Cambiar nombre de usuario</button>
                            </div>
                        </div>
                        
                </div>
                
                <h4>Cambiar contraseña</h4>
                <div class="form-bottom" id="formPassword">
                    <form>
                        <div class="form-group row">
                            <label for="pass" class="col-sm-12 col-form-label">Contraseña</label>
                            <div class="col-sm-12 col-md-10">
                                <input type="password" class="form-control" id="password" placeholder="ejemplo: 1@Javier" name="Email" required autofocus>
                            </div>
                            <label for="confirmaPassword" class="col-sm-12 col-form-label">Confirmación de nueva contraseña</label>
                            <div class="col-sm-12 col-md-10">
                                <input type="password" class="form-control" id="confirmaPassword" placeholder="ejemplo: 1@Javier" name="confirmacionEmail" required autofocus>
                            </div>
                            <div class="col-sm-12 col-md-10" id="ErrorPassword"> </div>
                            <div class="col-sm-12 col-md-10" id="ResPassword"> </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6 offset-sm-3 offset-md-3">
                                <button  type="button" class="btn btn-primary" id="cambiarPassword">Cambiar contraseña</button>                          
                            </div>
                        </div>
                        
                </div>
            </div>
            
        </div>
    
        </div>
        
        
        <!-- Modal contraseña para modificar detalles de la cuenta -->
        <div class="modal fade" id="modalModificarCuenta" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tituloModalEditarArchivo">Modificar cuenta</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>                   
                    </div>

                    <div class="modal-body">
                        <p>Para poder modificar tu cuenta introduce nuevamente tu contraseña <p>
                        <div class="col-sm-12 ">
                            
                            <input type="password" class="form-control" id="contraseniaModal" placeholder="Introduce aqui tu contraseña" name="contraseña">
                        </div>
                        <div class="col-sm-12" id = "ErrorContrasenia"></div>                       
                    </div>
                    <div class="modal-footer">
                        <button id="modalCancelar" type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button id="modalAceptar"  type="button" class="btn btn-primary"   >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>  
        
        <div class="container">
                
        </div>
        
    </body>
</html>

