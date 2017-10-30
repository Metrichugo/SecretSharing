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
    case "EliminarArchivo";
        eliminarArchivo($usuario, $DBConnection, $carpetActual);
        break;
    case "EditarArchivo";
        editarArchivo($usuario, $DBConnection, $carpetActual);
        break;
    case "SubirArchivo";
        subirArchivo($usuario, $DBConnection, $carpetActual);
        break;
    case "descargarArchivo";
        descargarArchivo($usuario, $DBConnection, $carpetActual);
        break;
    case "moverArchivo";
        moverArchivo($DBConnection, $usuario);
        break;
    default;
        echo "incorrect";
        break;
}

function eliminarArchivo($usuario, $DBConnection, $carpetActual) {
    $idCarpeta = filter_input(INPUT_POST, 'idCarpeta', FILTER_SANITIZE_NUMBER_INT);
    $nombreArchivo = filter_input(INPUT_POST, 'nombreArchivo', FILTER_SANITIZE_STRING);
    if ($DBConnection->eliminarArchivo($usuario, $idCarpeta, $nombreArchivo)) {
        echo "correct";
    } else {
        echo "incorrect";
    }
}

function editarArchivo($usuario, $DBConnection, $carpetActual) {
    $idCarpeta = filter_input(INPUT_POST, 'idCarpeta', FILTER_SANITIZE_NUMBER_INT);
    $nombreArch = filter_input(INPUT_POST, 'nombreArch', FILTER_SANITIZE_STRING);
    $nuevoNomArch = filter_input(INPUT_POST, 'nuevoNomArch', FILTER_SANITIZE_STRING);
    if ($DBConnection->editarArchivo($usuario, $idCarpeta, $nombreArch, $nuevoNomArch)) {
        echo "correct";
    } else {
        echo "incorrect";
    }
}

function moverArchivo($DBConnection, $usuario) {
    $idCarpetaDest = $_POST['idCarpetaDest'];
    $nombreArchivo = $_POST['nomArchivo'];
    $result = $DBConnection->moverArchivo($usuario, $idCarpetaDest, $nombreArchivo);
    if ($result) {
        echo "Se movio el archivo";
    } else {
        echo "Error al mover el archivo";
    }
}

function descargarArchivo($usuario, $DBConnection, $carpetActual) {
    //// Variables del POST
    $nombreArchivo = $_POST["nombreArchivo"];
    $idCarpeta = $_POST["idCarpeta"];
    //
    //
    $dirsubida = "../files/";
    $archivo = $DBConnection->obtieneArchivo($nombreArchivo, $idCarpeta);
    $carpeta_usuario = "/" . $usuario->getidUsuario();

    //echo $archivo->toString();
    //
    ////Ejecucion script
    $comando = "python ../python/recuperar_archivo.py " . $archivo->getNombreArchivoGRID() . " " . $dirsubida . " " . $carpeta_usuario;
    //echo "<p>".$comando."</p>";
    modif_shell_exec($comando, $stdout, $stderr);
    //echo "<p>" . $stdout . "</p>";
    //echo "<p>" . $stderr . "</p>";
    //Validacion recuperacion
    //Renombrado del archivo
    rename($dirsubida . $archivo->getNombreArchivoGRID(), $dirsubida . $archivo->getNombreArchivo());
    $rutaArchivo = '../files/' . $archivo->getNombreArchivo();

    //Contenido de la respuesta
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($rutaArchivo) . '"');
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: must-revalidate');
    ////header('Content-Length: '.filesize($filepath));
    ob_clean();
    flush();
    readfile($rutaArchivo);

    //Eliminacion del archivo en la carpeta del servidor
    unlink($dirsubida . $archivo->getNombreArchivo());
    exit();
}

function subirArchivo($usuario, $DBConnection, $carpetActual) {

    $dirsubida = "../files/";
    $dateTime = new DateTime();
    $timeStamp = $dateTime->getTimestamp();

    if (!empty($_FILES['file']['name'])) {
        //Objeto de tipo archivo
        $nombreArchivo = basename($_FILES['file']['name']);
        $idCarpeta = $carpetActual->getIdCarpeta();
        $idUsuario = $usuario->getidUsuario();
        $tamanio = basename($_FILES['file']['size']);
        $nombreArchivoGRID = preg_replace('/\s+/', '_', trim($nombreArchivo . $timeStamp));
        $fechaSubida = date("Y-m-d");

        $archivo = new Archivo($nombreArchivo, $idCarpeta, $idUsuario, $nombreArchivoGRID, $tamanio, $fechaSubida);

        //Mover del directorio temporal al directorio files
        $result = seMovioTemporal($archivo->getNombreArchivoGRID(), $dirsubida);
        //echo $result;

        if ($result) {
            $carpeta_usuario = "/" . $usuario->getidUsuario();
            $comando = "python ../python/comparte_archivo.py " . $archivo->getNombreArchivoGRID() . " " . $dirsubida . " " . $carpeta_usuario;
            modif_shell_exec($comando, $stdout, $stderr);
            //echo "Salida python: <p>" . $stdout . "</p>";
            //echo "<p>" . $stderr . "</p>";
            //Validar ejecucion de la GRID
            $string_ok = "El archivo se compartio correctamente";
            $log = $dirsubida . $archivo->getNombreArchivoGRID() . ".out";
            //Busca la cadena ok para saber si la ejecucion fue correcta
            if (strpos(file_get_contents($log), $string_ok) !== false) {
                // Insercion en la base de datos           
                $DBConnection->insertaArchivo($archivo);
                //Aumenta espacio utilizado
                $usuario->setEspacioUtilizado($usuario->getEspacioUtilizado() + $archivo->getTamanio());
                $DBConnection->editaEspacioUtilizado($usuario);
                //Fin
                echo "UploadSuccesfull";
                //my_shell_exec("rm " . $dirsubida . $archivo->getNombreArchivoGRID(), $stdout, $stderr);
                unlink($dirsubida . $archivo->getNombreArchivoGRID());
                //unlink($dirsubida . $archivo->getNombreArchivoGRID());
                //unlink($log);
                unlink($dirsubida . $archivo->getNombreArchivoGRID() . ".err");
            } else {
                echo "UploadFailed";
            }
        } else {
            echo "ErrorCantMove ";
        }
    } else {
        echo 'NoFileSelected';
    }
}

function seMovioTemporal($nombreArchivoGRID, $dirsubida) {
    $uploadedFile = $dirsubida . $nombreArchivoGRID;
    return (move_uploaded_file($_FILES['file']['tmp_name'], $uploadedFile));
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