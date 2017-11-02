<?php

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.html');
    exit;
}
include_once("BaseDeDatos.php");
include_once("Usuario.php");
include_once("Carpeta.php");
include_once("./Carpeta_Accion.php");

$usuario = unserialize($_SESSION["usuario"]);
$DBConnection = unserialize($_SESSION["DBConnection"]);
$DBConnection->connect(); // Al finaliza el archivo se cierra la conexion con db

$operacion = filter_input(INPUT_POST, 'Operation', FILTER_SANITIZE_STRING);
$carpetActual = unserialize($_SESSION["carpetActual"]);

switch ($operacion) {
    //CRUD
    case "crearNuevaCar";
        //Construcción del objeto de tipo carpeta
        $nombreCarpeta = filter_input(INPUT_POST, 'nombreCarpeta', FILTER_SANITIZE_STRING);
        $idUsuario = $carpetActual->getIdUsuario();
        $idCarpetaSuperior = $carpetActual->getIdCarpeta();
        $carpeta = new Carpeta(null, $idUsuario, $idCarpetaSuperior, $nombreCarpeta, null); //Los argumentos en null se asignan automaticamente en el manejador de la BD
        //Construcción del objeto de tipo Carpeta_Action
        $carpetaAccion = new Carpeta_Accion($carpeta, $DBConnection);
        //Accion
        $carpetaAccion->crearCarpeta();
        break;

    case "eliminarCarpeta";
        //Construcción del objeto de tipo carpeta
        $idCarpeta = filter_input(INPUT_POST, 'idCarpeta', FILTER_SANITIZE_NUMBER_INT);
        $carpeta = $DBConnection->consultaCarpeta($carpetActual->getIdUsuario(), $idCarpeta);
        //Construcción del objeto de tipo Carpeta_Action
        $carpetaAccion = new Carpeta_Accion($carpeta, $DBConnection);
        //Accion
        $carpetaAccion->eliminarCarpeta();
        break;

    case "EditarCar";
        //Construcción del objeto de tipo carpeta
        $nuevoNombreCarpeta = filter_input(INPUT_POST, 'nombreCarpeta', FILTER_SANITIZE_STRING);
        $idCarpetaEditar = filter_input(INPUT_POST, 'idCarpetaEditar', FILTER_SANITIZE_NUMBER_INT);
        $carpeta = $DBConnection->consultaCarpeta($carpetActual->getIdUsuario(), $idCarpetaEditar);
        //Construcción del objeto de tipo Carpeta_Action
        $carpetaAccion = new Carpeta_Accion($carpeta, $DBConnection);
        //Accion
        $carpetaAccion->renombrarCarpeta($nuevoNombreCarpeta, $carpetActual);
        break;
    case "moverCarpeta";
        moverCarpeta($DBConnection, $usuario);
        break;

    //Vista
    case "actualizarCarpetaActual"; //Actualiza la variable de sesion sobre la carpeta en la que esta el usuario
        actualizarCarpetaActual($usuario, $DBConnection);
        break;
    case "actualizarCarpetas"; //Enlista las subcarpetas de la carpeta actual - OK
        actualizarCarpetas($carpetActual, $DBConnection);
        break;
    case "actualizarArchivos"; //Enlista los archivos de la carpeta actual - OK
        actualizarArchivos($carpetActual, $DBConnection);
        break;
    case "irCarpetaAtras"; //Regresa a la carpeta padre de la carpeta actual - OK
        irCarpetaAtras($usuario, $carpetActual, $DBConnection);
        break;
    case "cargarCarpetaRaiz"; //Regresa la carpeta actual a su carpeta raiz
        cargarCarpetaRaiz();
        break;
    case "obtenerSubCarpetas";
        obtenerSubCarpetas($DBConnection, $usuario, $carpetActual);
        break;

    default;
        echo "invalidrequest";
        break;
}

//Falta mover este metodo
function moverCarpeta($DBConnection, $usuario) {
    $idCarpeta = $_POST['idCarpeta'];
    $idCarpetaDest = $_POST['idCarpetaDest'];
    $result = $DBConnection->moverCarpeta($usuario, $idCarpeta, $idCarpetaDest);
    if ($result) {
        echo "Se movio la carpeta";
    } else {
        echo "Error al mover la carpeta";
    }
}

function actualizarCarpetaActual($usuario, $DBConnection) {
    $idCarpetaMoverse = filter_input(INPUT_POST, 'idCarpetaMoverse', FILTER_SANITIZE_NUMBER_INT);
    //Actualizamos el objeto carpeta a la que se va a mostrar en pantalla
    $carpeta = $DBConnection->consultaCarpeta($usuario->getidUsuario(), $idCarpetaMoverse);
    $_SESSION["carpetActual"] = serialize($carpeta);
    echo "correct";
}

function actualizarCarpetas($carpetActual, $DBConnection) {
    $stack = $DBConnection->listaCarpetas($carpetActual);
    $ans = "";
    while (!$stack->isEmpty()) {
        $carpeta = $stack->pop();
        $ans = $ans . '<tr id="row' . $carpeta->getIdCarpeta() . '">
                            <td class="text-center"><a href = "#"> <p id ="' . $carpeta->getIdCarpeta() . '"  onclick = "actualizarContenidoEnPantalla(' . $carpeta->getIdCarpeta() . ')" >' . $carpeta->getNombreCarpeta() . '</p></a></td>
                            <td class="text-center">' . $carpeta->getFechaCreacion() . '</td>
                            <td class="text-center">
                                    <a class="btn btn-primary btn-sm btn-sel-carp" href="#" data-toggle="modal" data-target="#modalMoverCarpeta" data-idCarpeta=' . $carpeta->getIdCarpeta() . '><span class="glyphicon glyphicon-remove"></span> Mover</a>					                                         
                                    <a class="btn btn-info    btn-sm btn-sel-carp" href="#" data-toggle="modal" data-target="#modalEditarCarpeta"  data-idCarpeta=' . $carpeta->getIdCarpeta() . ' ><span class="glyphicon glyphicon-edit"></span> Editar</a>								
                                    <a class="btn btn-danger  btn-sm btn-sel-carp" href="#" data-toggle="modal" data-target="#modalEliminarCarpeta"  data-idCarpeta=' . $carpeta->getIdCarpeta() . '  ><span class="glyphicon glyphicon-remove"></span> Eliminar</a>
                            </td>
                        </tr>';
    }
    echo ($ans);
}

function actualizarArchivos($carpetActual, $DBConnection) {
    $stack = $DBConnection->listaArchivos($carpetActual);
    //Pila donde se almacena los archivos de la carpeta
    $ans = "";
    while (!$stack->isEmpty()) {
        $archivo = $stack->pop();
        $ans = $ans . '<tr id="row' . $archivo->getNombreArchivo() . '">
                                    <td class="text-center"><p id="arch' . $archivo->getNombreArchivo() . '">' . $archivo->getNombreArchivo() . '</p></td>
                                    <td class="text-center">' . $archivo->getTamanio() . '</td>
                                    <td class="text-center">' . $archivo->getFechaSubida() . '</td>
                                    <td class="text-center">
                                            <a class="btn btn-primary btn-sm" href="#" data-toggle="modal" data-target="#modalMoverArchivo"  data-idCarpeta="' . $archivo->getIdCarpeta() . '" data-nomArchivo="' . $archivo->getNombreArchivo() . '" id="mov' . $archivo->getNombreArchivo() . '"><span class="glyphicon glyphicon-remove"></span> Mover</a>									
                                            <a class="btn btn-success btn-sm  descargaArch" href="#" data-idCarpeta="' . $archivo->getIdCarpeta() . '" data-nomArchivo="' . $archivo->getNombreArchivo() . '" id="down' . $archivo->getNombreArchivo() . '"><span class="glyphicon glyphicon-edit"></span> Descargar</a>
                                            <a class="btn btn-info    btn-sm" href="#" data-toggle="modal" data-target="#modalEditarArchivo"  data-idCarpeta="' . $archivo->getIdCarpeta() . '" data-nomArchivo="' . $archivo->getNombreArchivo() . '" id="edit' . $archivo->getNombreArchivo() . '"><span class="glyphicon glyphicon-edit"></span> Editar</a>
                                            <a class="btn btn-danger  btn-sm" href="#" data-toggle="modal" data-target="#modalEliminaArchivo"  data-idCarpeta="' . $archivo->getIdCarpeta() . '" data-nomArchivo="' . $archivo->getNombreArchivo() . '" id="del' . $archivo->getNombreArchivo() . '"><span class="glyphicon glyphicon-remove"></span> Eliminar</a>
                                    </td>
                        </tr>';
    }
    echo ($ans);
}

function irCarpetaAtras($usuario, $carpetActual, $DBConnection) {
    if ($carpetActual->getIdCarpeta() == $_SESSION['idCarpetaRaiz']) {
        echo "incorrect";
        exit();
    }
    $idCarpetaSup = $carpetActual->getIdCarpetaSuperior();
    $carpetaSup = $DBConnection->consultaCarpeta($usuario->getidUsuario(), $idCarpetaSup);
    $_SESSION["carpetActual"] = serialize($carpetaSup);
    echo( $idCarpetaSup );
}

function cargarCarpetaRaiz() {
    $carpetaRaiz = unserialize($_SESSION["carpetActual"]);
    $_SESSION['idCarpetaRaiz'] = $carpetaRaiz->getIdCarpeta();
    //Devuelve el ID de la carpeta raiz 
    echo( $carpetaRaiz->getIdCarpeta() );
}

function obtenerSubCarpetas($DBConnection, $usuario, $carpetActual) {
    $idCarpeta = $_POST['idCarpeta'];
    echo $DBConnection->obtenerSubCarpetas($usuario, $carpetActual->getIdCarpeta(), $idCarpeta);
}
?>