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

//Se comprueba si el usuario se autenticó para poder modificar sus detalles
if (!$usuario->puedeModificarse()) {
    header('Location: ./VisualizarCuenta.php');
    exit;
}

//Recuperacion del objeto BD creado en Login.php
$DBConnection = unserialize($_SESSION["DBConnection"]);
$DBConnection->connect(); // Al finaliza el archivo se cierra la conexion con db
//al cargar la pagina la carpeta actual es la carpeta raiz con idCarpetaSuperior=null;
$carpetActual = $DBConnection->consultarCarpetaRaiz($usuario);
$_SESSION["carpetActual"] = serialize($carpetActual);
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
        <script src="../js/modificarCuenta.js"></script>              
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
                <a class="nav-link" id = "visualizarDetalles" href="VisualizarCuenta.php" > Visualizar detalles </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active " id = "modificarCuenta"    href="#" data-disp="true">Modificar cuenta</a>
            </li>
        </ul>
        <br>       

        <!-- Contenedor para modificarCuenta -->
        <div class="container" id = "contenedorModificarCuenta">
            <div class="row">

                <div class="col-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <img  src="../assets/Login-Icon.png" class="img-thumbnail">
                        </div>
                        <div class="card-footer">
                            <button id= "eliminarCuenta" type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalEliminarCuenta" >Eliminar Cuenta</button>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <!--Contenedor cambiar nombre usuario-->
                    <div class="card">
                        <h4 class="card-header">Cambiar nombre de usuario</h4>
                        <div class="card-body">
                            <form id = "formNombreUsuario">
                                <div class="form-group">
                                    <label for="staticEmail" class="col-form-label">Nombre de usuario actual (email)</label>    
                                    <input type="email" readonly class="form-control" id="staticEmail" value="<?php
                                    echo $usuario->getidUsuario();
                                    ?>">
                                </div>

                                <div class="form-group">
                                    <label for="Email" class="col-form-label">Nombre de usuario (email)</label>
                                    <input type="email" class="form-control" id="Email" placeholder="ejemplo@servidor.com" name="Email" required autofocus>
                                </div>
                                <div class="form-group">
                                    <label for="confirmaEmail" class="col-form-label">Confirmación del nombre de usuario (email)</label>
                                    <input type="email" class="form-control" id="confirmaEmail"  placeholder="ejemplo@servidor.com" name="confirmacionEmail" required>                                
                                </div>
                                <div class="form-group">
                                    <div class="col" id="ErrorCambiarEmail"></div>
                                    <div class="col" id="ResCambiarEmail"></div>
                                    <button id = "cambiarNombreUsuario" type="submit"  class="btn btn-primary text-center">Cambiar nombre de usuario</button>
                                </div>
                            </form> 
                        </div>
                    </div>

                    <br>

                    <!--Contenedor cambiar contraseña-->
                    <div class="card">
                        <h4 class="card-header">Cambiar contraseña</h4>
                        <div class="card-body">                        
                            <form>
                                <div class="form-group">
                                    <label for="pass" class="col-form-label">Contraseña</label>
                                    <input type="password" class="form-control" id="password" placeholder="ejemplo: 1@Javier" name="Email" required>
                                </div>
                                <div class="form-group">
                                    <label for="confirmaPassword" class="col-form-label">Confirmación de nueva contraseña</label>
                                    <input type="password" class="form-control" id="confirmaPassword" placeholder="ejemplo: 1@Javier" name="confirmacionEmail" required>                                    
                                </div>
                                <div class="form-group">
                                    <div class="col" id="ErrorPassword"> </div>
                                    <div class="col" id="ResPassword"> </div>
                                    <button  type="button" class="btn btn-primary text-center" id="cambiarPassword">Cambiar contraseña</button>                          
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal para eliminar la cuenta-->
            <div class="modal fade" id="modalEliminarCuenta" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Eliminación de cuenta</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>                   
                        </div>

                        <div class="modal-body">
                            <p>Al dar clic en Aceptar usted estará eliminando su cuenta.          </p>                                                
                            <p>Su directorio completo de archivos y carpetas será eliminado de forma permanente así como los detalles almacenados acerca de su cuenta.</p>
                            <div class="col-sm-12" id = "ErrorEliminarCuenta"></div>
                            <div class="col-sm-12" id = "OkEliminarCuenta"></div>                       
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button id="modalEliminarAceptar"  type="button" class="btn btn-primary">Aceptar</button>
                        </div>
                    </div>
                </div>
            </div>  
        </div>

    </body>
</html>