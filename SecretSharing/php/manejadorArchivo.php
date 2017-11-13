<?php

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.html');
    exit;
}
include_once("./BaseDeDatos.php");
include_once("./Usuario.php");
include_once("./Carpeta.php");
include_once("./Archivo_Accion.php");

//Variables de sesión
$carpetActual = unserialize($_SESSION["carpetActual"]); //Objeto de tipo carpeta 
$DBConnection = unserialize($_SESSION["DBConnection"]); //Objeto para la conexion con la DB
$DBConnection->connect(); // Conexion con la BD
//Lectura desde el metodo POST
$operacion = filter_input(INPUT_POST, 'Operation', FILTER_SANITIZE_STRING); //Operacion a realizar leida desde el metodo POST
$nombreArchivo = filter_input(INPUT_POST, 'nombreArchivo', FILTER_SANITIZE_STRING); //Nombre del archivo leido desde el metodo POST

switch ($operacion) {
    case "consultarExisteArchivo";
        //Construcción del objeto de tipo archivo
        $archivo = $DBConnection->consultarArchivo($nombreArchivo, $carpetActual);
        if (is_null($archivo)) {
            echo "unique";
        } else {
            echo "exists";
        }
        break;
    case "EliminarArchivo";
        //Construcción del objeto de tipo archivo
        $archivo = $DBConnection->consultarArchivo($nombreArchivo, $carpetActual);
        //Construcción del objeto de tipo Archivo_Action
        $archivoAccion = new Archivo_Accion($archivo, $DBConnection);
        //Accion
        $archivoAccion->eliminarArchivo();
        break;

    case "EditarArchivo";
        //Construcción del objeto de tipo archivo
        $archivo = $DBConnection->consultarArchivo($nombreArchivo, $carpetActual);
        //Lectura del nuevo nombre de archivo desde POST
        $nuevoNombreArchivo = filter_input(INPUT_POST, 'nuevoNomArch', FILTER_SANITIZE_STRING);
        //Construcción del objeto de tipo Archivo_Action
        $archivoAccion = new Archivo_Accion($archivo, $DBConnection);
        //Accion
        $archivoAccion->renombrarArchivo($nuevoNombreArchivo);
        break;

    case "SubirArchivo";
        if (!empty($_FILES['file']['name'])) {//Se verifica que el usuario haya seleccionado un archivo
            //Se obtiene la fecha del servidor
            $dateTime = new DateTime();
            $timeStamp = $dateTime->getTimestamp();
            //Variables del objeto de tipo archivo
            $nombreArchivo = basename($_FILES['file']['name']);
            $idCarpeta = $carpetActual->getIdCarpeta();
            $idUsuario = $carpetActual->getIdUsuario();
            $tamanio = basename($_FILES['file']['size']);
            $nombreArchivoGRID = hash('sha256', $nombreArchivo . $timeStamp);
            $fechaSubida = date("Y-m-d");
            //Construcción del objeto de tipo archivo
            $archivo = new Archivo($nombreArchivo, $idCarpeta, $idUsuario, $nombreArchivoGRID, $tamanio, $fechaSubida);
            //Construcción del objeto de tipo Archivo_Action
            $archivoAccion = new Archivo_Accion($archivo, $DBConnection);
            //Accion
            $archivoAccion->subirArchivo();
        } else {
            echo json_encode(array("Status" => 'NoFileSelected'));
        }
        break;

    case "descargarArchivo";
        //Construcción del objeto de tipo archivo
        $archivo = $DBConnection->consultarArchivo($nombreArchivo, $carpetActual);
        //Construcción del objeto de tipo Archivo_Action
        $archivoAccion = new Archivo_Accion($archivo, $DBConnection);
        //Accion
        $archivoAccion->descargarArchivo();
        break;

    case "prepararArchivo";
        //Construcción del objeto de tipo archivo
        $archivo = $DBConnection->consultarArchivo($nombreArchivo, $carpetActual);
        //Construcción del objeto de tipo Archivo_Action
        $archivoAccion = new Archivo_Accion($archivo, $DBConnection);
        //Accion
        $archivoAccion->prepararArchivo();
        break;

    case "moverArchivo";
        //Construcción del objeto de tipo archivo
        $archivo = $DBConnection->consultarArchivo($nombreArchivo, $carpetActual);
        //Lectura del nuevo idCarpeta desde POST
        $idCarpetaDestino = filter_input(INPUT_POST, 'idCarpetaDest', FILTER_SANITIZE_NUMBER_INT);
        //Construcción del objeto de tipo Archivo_Action
        $archivoAccion = new Archivo_Accion($archivo, $DBConnection);
        //Accion
        $archivoAccion->moverArchivo($idCarpetaDestino);
        break;

    default;
        echo "invalidrequest";
        break;
}
?>