<?php
    session_start();  
    if(!isset($_SESSION['usuario'])){
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

    //al cargar la pagina la carpeta actual es la carpeta raiz con id = 1;
    $carpetActual = $DBConnection->consultaCarpeta($usuario,1);
    $_SESSION["carpetActual"] = serialize($carpetActual);
?>

<html>
<head>
    <title>Secret Compartido</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../assets/bootstrap-4.0.0-alpha.6-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/sticky-footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <script src="../js/jquery-3.2.1.min.js"></script>
    <script src="https://npmcdn.com/tether@1.2.4/dist/js/tether.min.js"></script>
    <script src="../assets/bootstrap-4.0.0-alpha.6-dist/js/bootstrap.min.js"></script>
    <script src="../js/carpeta.js"></script>



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
                <a class="btn btn-warning my-2 my-sm-0 mr-sm-2" role="button" href="logout.php">Cerrar Sesión</a>
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
                   <button type="button" class="btn btn-secondary btn-sm" onclick = "irCarpetaAtras()" ><i class="fa fa-arrow-circle-left"></i>    Ir anterior</button>
                </div>

                <div class="row col-md-12 col-md-offset-2 custyle">
                    <div id = "contenedorCarpetas" class="row col-md-12 col-md-offset-2 custyle">
                        
                    </div>
                </div>
                <h3>Archivos</h3>
                <div class="row col-md-12 col-md-offset-2 custyle">
                    <div id = "contenedorArchivos" class="row col-md-12 col-md-offset-2 custyle">
                        
                    </div>
                </div>
            </div>

        </div>
    </div>
</body>
</html>