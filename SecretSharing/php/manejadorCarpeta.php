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

$operacion = $_POST['Operation'];
$carpetActual = unserialize($_SESSION["carpetActual"]);



if ($operacion == "actualizarCarpetaActual") {
    $idcarpetaMoverse = $_POST['idCarpetaMoverse'];
    //Actualizamos el objeto carpeta a la que se va a mostrar en pantalla
    $carpeta = $DBConnection->consultaCarpeta($usuario, $idcarpetaMoverse);
    $_SESSION["carpetActual"] = serialize($carpeta);
    echo "correct";
}


if ($operacion == "actualizarCarpetas") {
    $ans = $DBConnection->listaCarpetas($usuario, $carpetActual);
    echo ($ans);
}

if ($operacion == "actualizarArchivos") {                    //No deberia de ir en manejoArchivo?
    $ans = $DBConnection->listaArchivos($usuario, $carpetActual);
    echo ($ans);
}


if ($operacion == "irCarpetaAtras") {
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

//Se modificó esta parte del código
if ($operacion == "crearNuevaCar") {
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

if ($operacion == "eliminarCarpeta") {
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

if ($operacion == "cargarCarpetaRaiz") {
    //al cargar la pagina la carpeta actual es la carpeta raiz con id = 1;
    $carpetaRaiz = unserialize($_SESSION["carpetActual"]);

    $_SESSION['idCarpetaRaiz'] = $carpetaRaiz->getIdCarpeta();

    echo( $carpetaRaiz->getIdCarpeta() );
}

if ($operacion == "EditarCar") {


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
if ($operacion == "obtenerSubCarpetas") {
    obtenerSubCarpetas($DBConnection, $usuario, $carpetActual);
}
if ($operacion == "moverCarpeta") {
    moverCarpeta($DBConnection, $usuario);
}

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