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

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css">
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js"></script>

        <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">

        <!-- Descarga del archivo -->
        <script type="text/javascript" src = "../js/ajaxdownloader.min.js"></script>

        <script src="../js/carpeta.js"></script>
        <script src="../js/archivo.js"></script>
        <script src="../js/usuario.js"></script>

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
                    <li class="nav-item active">
                        <a class="nav-link" href="#">Principal <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="GestionCuenta.php">Gestión de cuenta</a>
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

        <!-- Contenedor principal -->
        <div class="container-fluid" id="ContenidoPrincipal">
            <div id = "ErroresPrincipal"></div>
            <div class="row">
                <div class="col-12 col-md-12" id="Contenido">
                    <br>

                    <!-- Contenedor de carpetas -->
                    <div class="card">

                        <!--Encabezado del contenedor-->
                        <div class="clearfix card-header">
                            <!-- Titulo Carpeta -->
                            <div class="panel-heading float-left"><h3>Carpetas</h3></div>
                            <!-- Boton crear carpeta -->
                            <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#modalCrearCarpeta"> Crear carpeta</button>

                            <!-- Boton carpeta atras -->
                            <div class="row col-md-12 col-md-offset-2 custyle">
                                <button type="button" class="btn btn-secondary btn-sm" onclick = "irCarpetaAtras()" ><i class="fa fa-arrow-circle-left"></i>&nbsp;Ir anterior</button>
                            </div>    
                        </div> 

                        <!-- Cuerpo del contenedor -->  
                        <div class="card-body" id = "contenedorCarpetas">
                            <!-- Tabla de carpetas -->
                            <table class="table table-striped table-sm ">
                                <thead>
                                    <tr>
                                        <th class="text-center">Nombre</th>
                                        <th class="text-center">Fecha de creación</th>
                                        <th class="text-center">Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaCarpetas">
                                </tbody>
                            </table>
                        </div>
                    </div>


                    <br>

                    <!-- Contenedor de archivos-->
                    <div class="card">                        
                        <div class="clearfix card-header">
                            <div class="panel-heading float-left" ><h3>Archivos</h3></div>
                            <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#UploadFile" role="button">Subir archivo</button>    
                        </div>

                        <div id = "contenedorArchivos" class="card-body">
                            <!--Tabla de archivos -->
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th class="text-center">Nombre</th>
                                        <th class="text-center">Tamaño (bytes)</th>
                                        <th class="text-center">Fecha de subida</th>
                                        <th class="text-center">Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaArchivos">
                                </tbody>
                            </table>
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
                        <form>
                            <div class="col-sm-12 ">
                                <input type="text" class="form-control" id="nombreCarpeta" placeholder="Introduce el nombre de la carpeta" name="nombreCarpeta" required>
                            </div>
                            <div class="col-sm-12" id = "ErrorNombreCarpeta">
                            </div><div class="col-sm-12" id = "resultadoCrearCarpeta">
                            </div>
                        </form>
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
                        <button type="button" class="btn btn-primary" id="deleteFolder" onclick = "return eliminarCarpeta()">Aceptar</button>
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
                        <div class="col-sm-12" id = "ErrorEditarArchivo"></div>
                        <div class="col-sm-12" id = "resultadoEditarArchivo"></div>
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
                            <div class="col-sm-13" id = "errorSubirArchivo"></div>
                            <div class="col-sm-13" id = "resultadoSubirArchivo"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary" id="botonSubirArchivo">Subir archivo</button>

                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal para mover un archivo -->
        <div class="modal fade" id="modalMoverArchivo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Mover Archivo</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="col-sm-12 ">
                            <label for="listaCarpetas">Seleccione la carpeta a la cual desea mover el archivo</label>
                            <select class="form-control" id="selectCarpetasArch">
                            </select>
                        </div>
                        <div class="col-sm-12" id = "ErrorMoverArchivo">
                        </div><div class="col-sm-12" id = "resultadoMoverArchivo">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary"  id="moveArchivo" onclick = "return moverArchivo()">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para mover una carpeta -->
        <div class="modal fade" id="modalMoverCarpeta" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Mover Carpeta</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="col-sm-12 ">
                            <label for="listaCarpetas">Seleccione la carpeta a la cual desea mover la carpeta actual</label>
                            <select class="form-control" id="selectCarpetas">
                            </select>
                        </div>
                        <div class="col-sm-12" id = "ErrorMoverCarpeta">
                        </div><div class="col-sm-12" id = "resultadoMoverCarpeta">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary"  id="moveCarpeta" onclick = "return moverCarpeta()">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>

    </body>
</html>
