<html>
<?php
	include_once("BaseDeDatos.php");
	include_once("Usuario.php");
?>
<head>
    <title>Secret Compartido</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../assets/bootstrap-4.0.0-alpha.6-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/sticky-footer.css">
    <link rel="stylesheet" href="../css/contenidoUsuario.css">
    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="assets/bootstrap-4.0.0-alpha.6-dist/js/bootstrap.min.js"></script>
</head>

<body>
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
                    <a class="nav-link" href="#">Gestion de cuenta</a>
                </li>
            </ul>
            <form class="form-inline my-2 my-lg-0">
                <a class="btn btn-warning my-2 my-sm-0 mr-sm-2" role="button" href="login.html">Cerrar Sesión</a>
            </form>
        </div>
    </nav>

    <div class="container-fluid" id="ContenidoPrincipal">
        <div class="row">
            <div class="col-12 col-md-3" id="menuOpciones">
                <div class="col">
                    <p><a class="btn btn-success btn-lg btn-block" href="" role="button">Subir archivo</a></p>
                </div>
                <div class="col">
                    <p><a class="btn btn-secondary btn-lg btn-block" href="" role="button">Crear carpeta</a></p>
                </div>
            </div>

            <div class="col-12 col-md-9" id="Contenido">
                <h2>Carpetas</h2>
                <div class="row col-md-12 col-md-offset-2 custyle">
                    <div class="row col-md-12 col-md-offset-2 custyle">
                        <table class="table table-striped custab">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Creación</th>
                                    <th class="text-center">Acción</th>
                                </tr>
                            </thead>
																<?php
																	/* Enlistado de Carpetas */
																	$DBConnection = new BaseDeDatos();
																	$DBConnection->connect();
																	$Usuario = new Usuario();
																	$Usuario->setidUsuario("metrichugo13@gmail.com");
																	$DBConnection->listaCarpetas($Usuario);
																?>
                        </table>
                    </div>
                </div>
                <h2>Archivos</h2>
                <div class="row col-md-12 col-md-offset-2 custyle">
                    <table class="table table-striped custab">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Tamaño</th>
                                <th>Subida</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tr>
                            <td>Documento 1</td>
                            <td>1.5 mb</td>
                            <td>03/03/2017</td>
                            <td class="text-center">
                                <a class="btn btn-success btn-sm" href="#"><span class="glyphicon glyphicon-remove"></span> Copiar</a>
                                <a class="btn btn-primary btn-sm" href="#"><span class="glyphicon glyphicon-remove"></span> Mover</a>
                                <a class='btn btn-info btn-sm' href="#"><span class="glyphicon glyphicon-edit"></span> Editar</a>
                                <a class="btn btn-danger btn-sm" href="#"><span class="glyphicon glyphicon-remove"></span> Eliminar</a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>