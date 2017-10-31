<?php

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.html');
    exit;
}
include_once("BaseDeDatos.php");
include_once("Usuario.php");
include_once("Carpeta.php");


$usuario = unserialize($_SESSION["usuario"]);
$DBConnection = unserialize($_SESSION["DBConnection"]);
$DBConnection->connect(); // Al finaliza el archivo se cierra la conexion con db

$operacion = filter_input(INPUT_POST, 'Operation', FILTER_SANITIZE_STRING);
$carpetActual = unserialize($_SESSION["carpetActual"]);

switch ($operacion) {
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
    case "crearNuevaCar";
        crearNuevaCarpeta($usuario, $carpetActual, $DBConnection);
        break;
    case "eliminarCarpeta";
        eliminarCarpeta($usuario, $DBConnection);
        break;
    case "cargarCarpetaRaiz"; //Regresa la carpeta actual a su carpeta raiz
        cargarCarpetaRaiz();
        break;
    case "EditarCar";
        editarCarpeta($usuario, $carpetActual, $DBConnection);
        break;
    case "obtenerSubCarpetas";
        obtenerSubCarpetas($DBConnection, $usuario, $carpetActual);
        break;
    case "moverCarpeta";
        moverCarpeta($DBConnection, $usuario);
        break;
    default;
        echo "invalidrequest";
        break;
}

function actualizarCarpetaActual($usuario, $DBConnection) {
    $idCarpetaMoverse = filter_input(INPUT_POST, 'idCarpetaMoverse', FILTER_SANITIZE_NUMBER_INT);
    //Actualizamos el objeto carpeta a la que se va a mostrar en pantalla
    $carpeta = $DBConnection->consultaCarpeta($usuario, $idCarpetaMoverse);
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
    $carpetaSup = $DBConnection->consultaCarpeta($usuario, $idCarpetaSup);
    $_SESSION["carpetActual"] = serialize($carpetaSup);
    echo( $idCarpetaSup );
}

function crearNuevaCarpeta($usuario, $carpetActual, $DBConnection) {//Se modificó esta parte del código
    $nombreNuevaCarpeta = $_POST['nombreCarpeta'];
    $result = $DBConnection->existeCarpeta($usuario, $carpetActual, $nombreNuevaCarpeta);
    //Se verifica existencia en la BD
    if ($result) { //Carpeta repetida
        echo json_encode(array(
            "Status" => "incorrect"
        ));
        exit();
    }
    // lo insertamos en la basede datos
    $result = $DBConnection->insertaCarpeta($usuario, $carpetActual, $nombreNuevaCarpeta);
    if ($result) {
        $htmlCarpeta = $DBConnection->getHTMLCarpeta($usuario, $carpetActual, $nombreNuevaCarpeta);
        echo json_encode(array(
            "Status" => "correct",
            "Html" => $htmlCarpeta
        ));
        exit();
    }
    echo json_encode(array(
        "Status" => "incorrect"
    ));
    exit();
}

function eliminarCarpeta($usuario, $DBConnection) {
    $idCarpeta = $_POST['idCarpeta'];
    $carpeta = $DBConnection->consultaCarpeta($usuario, $idCarpeta);
    eliminaCarpetaYArchivosGRID($carpeta, $DBConnection);
    if ($DBConnection->eliminarCarpeta($usuario, $carpeta)) {
        echo "correct";
    } else {
        echo "incorrect";
    }
    exit();
}

function eliminaCarpetaYArchivosGRID($carpeta, $DBConnection) {
    //Eliminación de subcarpetas
    $pilaSubcarpetas = $DBConnection->listaCarpetas($carpeta);
    while (!$pilaSubcarpetas->isEmpty()) {
        eliminaCarpetaYArchivosGRID($pilaSubcarpetas->pop(), $DBConnection);
    }
    
    //Eliminacion de archivos de la carpeta
    $pilaArchivos = $DBConnection->listaArchivos($carpeta);
    while (!$pilaArchivos->isEmpty()) {
        $archivo = $pilaArchivos->pop();
        $archivo->eliminaGRID();
        //Actualiza la variable de sesion
        $usuario = unserialize($_SESSION["usuario"]); //Objeto de sesion tipo usuario
        $usuario->setEspacioUtilizado($usuario->getEspacioUtilizado() - $archivo->getTamanio());
        $DBConnection->editaEspacioUtilizado($usuario);
        //Actualiza la variable de sesion 
        $_SESSION["usuario"] = serialize($usuario);
    }
}

function cargarCarpetaRaiz() {
    $carpetaRaiz = unserialize($_SESSION["carpetActual"]);
    $_SESSION['idCarpetaRaiz'] = $carpetaRaiz->getIdCarpeta();
    //Devuelve el ID de la carpeta raiz 
    echo( $carpetaRaiz->getIdCarpeta() );
}

function editarCarpeta($usuario, $carpetActual, $DBConnection) {
    $nombreCarpeta = $_POST['nombreCarpeta'];
    $idCarpetaEditar = $_POST['idCarpetaEditar'];

    $result = $DBConnection->existeCarpeta($usuario, $carpetActual, $nombreCarpeta);
    if ($result) {
        echo "incorrect";
        exit();
    }

    $result = $DBConnection->editarCarpeta($usuario, $idCarpetaEditar, $nombreCarpeta);
    if ($result) {
        echo "correct";
    } else {
        echo "incorrect";
    }
}

// Se agregaron estos 2 metodos //

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

function obtenerSubCarpetas($DBConnection, $usuario, $carpetActual) {
    $idCarpeta = $_POST['idCarpeta'];
    echo $DBConnection->obtenerSubCarpetas($usuario, $carpetActual->getIdCarpeta(), $idCarpeta);
}

function modif_shell_exec($cmd, &$stdout = null, &$stderr = null) {
    $proc = proc_open($cmd, [
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
            ], $pipes);
    $stdout = stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[2]);
    return proc_close($proc);
}

?>