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

$operacion = filter_input(INPUT_POST, 'Operation');
$carpetActual = unserialize($_SESSION["carpetActual"]);

switch ($operacion) {
    case "actualizarCarpetaActual";
        actualizarCarpetaActual($usuario, $DBConnection, $carpetActual);
        break;
    case "actualizarCarpetas";
        actualizarCarpetas($usuario, $carpetActual, $DBConnection);
        break;
    case "actualizarArchivos";
        actualizarArchivos($usuario, $carpetActual, $DBConnection);
        break;
    case "irCarpetaAtras";
        irCarpetaAtras($usuario, $carpetActual, $DBConnection);
        break;
    case "crearNuevaCar";
        crearNuevaCarpeta($usuario, $carpetActual, $DBConnection);
        break;
    case "eliminarCarpeta";
        eliminarCarpeta($usuario, $DBConnection);
        break;
    case "cargarCarpetaRaiz";
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
    $idcarpetaMoverse = filter_input(INPUT_POST, 'idCarpetaMoverse', FILTER_SANITIZE_NUMBER_INT);
    //Actualizamos el objeto carpeta a la que se va a mostrar en pantalla
    $carpeta = $DBConnection->consultaCarpeta($usuario, $idcarpetaMoverse);
    $_SESSION["carpetActual"] = serialize($carpeta);
    echo "correct";
}

function actualizarCarpetas($usuario, $carpetActual, $DBConnection) {
    $ans = $DBConnection->listaCarpetas($usuario, $carpetActual);
    echo ($ans);
}

function actualizarArchivos($usuario, $carpetActual, $DBConnection) {
    $ans = $DBConnection->listaArchivos($usuario, $carpetActual);
    echo ($ans);
}

function irCarpetaAtras($usuario, $carpetActual, $DBConnection) {
    if ($carpetActual->getIdCarpeta() == $_SESSION['idCarpetaRaiz']) {
        echo "incorrect";
        exit();
    }
    $idCarpetaSup = $carpetActual->getIdCarpetaSuperior();
    $carpetActual->toString();
    $carpetaSup = $DBConnection->consultaCarpeta($usuario, $idCarpetaSup);
    $_SESSION["carpetActual"] = serialize($carpetaSup);
    echo( $idCarpetaSup );
}

function crearNuevaCarpeta($usuario, $carpetActual, $DBConnection) {//Se modificó esta parte del código
    $nombreNuevaCarpeta = $_POST['nombreCarpeta'];
    $result = $DBConnection->existeCarpeta($usuario, $carpetActual, $nombreNuevaCarpeta);
    if ($result) {
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
    $result = $DBConnection->eliminarCarpeta($usuario, $carpeta);

    if ($result) {
        echo "correct";
    } else {
        echo "incorrect";
    }
    exit();
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

/* Se agregaron estos 2 metodos */

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

?>