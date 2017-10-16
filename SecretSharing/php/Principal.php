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
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Pantalla principal</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="../assets/bootstrap-4.0.0-alpha.6-dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="../css/index.css">
        <link rel="stylesheet" href="../css/navbar.css">
        <link rel="stylesheet" href="../css/sticky-footer.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

        <script src="../js/jquery-3.2.1.min.js"></script>
        <script src="../js/tether.min.js"></script>
        <script src="../assets/bootstrap-4.0.0-alpha.6-dist/js/bootstrap.min.js"></script>

        <script type="text/javascript" src = "../js/ajaxdownloader.min.js
        "></script>

        <script src="../js/carpeta.js"></script>
        <script src="../js/archivo.js"></script>
        <script src="../js/descargaArchivo.js"></script>

    </head>

    <body>
        <!-- Barra de navegacion -->
        <nav class="navbar navbar-toggleable-md navbar-light bg-faded" id="Navigation">
            <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="#">SecretSharing</a>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="#">Principal <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Gestión de cuenta</a>
                    </li>
                </ul>

                <button type="button" class="btn btn-primary " data-toggle="modal" data-target="#UploadFile" role="button">Subir archivo</button>
                <button type="button" class="btn btn-secondary " data-toggle="modal" data-target="#modalCrearCarpeta" >Crear carpeta</button>
                <a class="btn btn-danger" role="button" href="logout.php">Cerrar Sesión</a>
            </div>
        </nav>

        <!-- Contenedor principal -->
        <div class="container-fluid" id="ContenidoPrincipal">
            <div class = "row col-12" id = "ErroresPrincipal">
            </div>
            <div class="row">
                <div class="col-11 col-md-11" id="Contenido">
                    <br>

                    <div class="panel panel-default">
                        <div class="panel-heading"><h3>Carpetas</h3></div>
                        <div class="row col-md-12 col-md-offset-2 custyle">
                            <button type="button" class="btn btn-secondary btn-sm" onclick = "irCarpetaAtras()" ><i class="fa fa-arrow-circle-left"></i>    Ir anterior</button>
                        </div>
                        <div class="panel-body" id = "contenedorCarpetas"> </div>
                    </div>

                    <div class="panel panel-default">
                        <div class="panel-heading"><h3>Archivos</h3></div>
                        <div id = "contenedorArchivos" class="row col-md-12 col-md-offset-2 custyle">
                        </div>                      

                    </div>
                </div>
            </div>

        </div>


        <!-- Modal Para crear nueva carpeta -->
        <div class="modal fade" id="modalCrearCarpeta" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tituloModalCrearCarpeta">Crear nueva carpeta</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="col-sm-12 ">
                            <input type="text" class="form-control" id="nombreCarpeta" placeholder="Introduce el nombre de la carpeta" name="nombreCarpeta" required>
                        </div>
                        <div class="col-sm-12" id = "ErrorNombreCarpeta">
                        </div><div class="col-sm-12" id = "resultadoCrearCarpeta">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary"  onclick = "return crearNuevaCarpeta()">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para editar una carpeta -->
        <div class="modal fade" id="modalEditarCarpeta" tabindex="-1" role="dialog" aria-labelledby="tituloModalEditarCarpeta" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tituloModalEditarCarpeta">Renombrar carpeta</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>

                    <div class="modal-body">
                        <div class="col-sm-12 ">
                            <input type="text" class="form-control" id="nombreEditarCarpeta" placeholder="Introduce el nombre de la carpeta" name="nombreEditarCarpeta" required>
                        </div>
                        <div class="col-sm-12" id = "ErrorEditarCarpeta">
                        </div><div class="col-sm-12" id = "resultadoEditarCarpeta">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary"  onclick = "return editarNombreCarpeta()">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para eliminar una carpeta -->
        <div class="modal fade" id="modalEliminarCarpeta" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Eliminar carpeta</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>

                    <div class="modal-body">
                        <p><strong>¿Desea eliminar la carpeta?</strong></p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary"  onclick = "return eliminarCarpeta()">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para eliminar un archivo -->
        <div class="modal fade" id="modalEliminaArchivo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tituloModalEliminaArchivo">Eliminar archivo</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>

                    <div class="modal-body">
                        <p><strong>¿Desea eliminar el archivo?</strong></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="deleteFile"  onclick = "return eliminarArchivo()">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para editar el nombre de un archivo -->
        <div class="modal fade" id="modalEditarArchivo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tituloModalEditarArchivo">Renombrar archivo</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>

                    <div class="modal-body">
                        <div class="col-sm-12 ">
                            <input type="text" class="form-control" id="nombreEditarArchivo" placeholder="Introduce aqui nuevo nombre del archivo" name="nombreEditarArchivo">
                        </div>
                        <div class="col-sm-12" id = "ErrorEditarArchivo">
                        </div><div class="col-sm-12" id = "resultadoEditarArchivo">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary"  onclick = "return editarNombreArchivo()">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>        

        <!----- MODAL PARA SUBIR ARCHIVOS ---- -->
        <div class="modal fade" id="UploadFile" tabindex="-1" role="dialog" aria-labelledby="tituloModalSubirArchivo" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tituloModalSubirArchivo">Subir archivo</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cancelar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form onsubmit="return subirArchivo()" method="POST" enctype="multipart/form-data" id="postFile">
                        <div class="modal-body">
                            <label>Selecciona el archivo a subir:</label><br>  
                            <input type="file" name="fileToUpload" id="fileToUpload">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Subir archivo</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </body>
</html>
