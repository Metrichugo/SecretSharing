<?php

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.html');
    exit;
}
include_once("BaseDeDatos.php");
include_once("Usuario.php");
include_once("Carpeta.php");

//Variables de sesión
$usuario = unserialize($_SESSION["usuario"]); //Objeto de sesion tipo usuario
$carpetActual = unserialize($_SESSION["carpetActual"]); //Objeto de sesion de tipo 
$DBConnection = unserialize($_SESSION["DBConnection"]); //Objeto para la conexion con la DB
$DBConnection->connect(); // Conexion con la BD
//Lectura desde el metodo POST
$operacion = filter_input(INPUT_POST, 'Operation', FILTER_SANITIZE_STRING); //Operacion a realizar leida desde el metodo POST
$nombreArchivo = filter_input(INPUT_POST, 'nombreArchivo', FILTER_SANITIZE_STRING); //Nombre del archivo leido desde el metodo POST

switch ($operacion) {
    case "EliminarArchivo";
        //Construcción del objeto de tipo archivo
        $archivo = $DBConnection->consultaArchivo($nombreArchivo, $carpetActual->getIdCarpeta(), $usuario->getidUsuario());
        eliminarArchivo($archivo, $DBConnection);
        break;
    case "EditarArchivo";
        $archivo = $DBConnection->consultaArchivo($nombreArchivo, $carpetActual->getIdCarpeta(), $usuario->getidUsuario());
        editarArchivo($archivo, $DBConnection);
        break;
    case "SubirArchivo";
        subirArchivo($usuario, $carpetActual, $DBConnection);
        break;
    case "descargarArchivo";
        $archivo = $DBConnection->consultaArchivo($nombreArchivo, $carpetActual->getIdCarpeta(), $usuario->getidUsuario());
        descargarArchivo($archivo);
        break;
    case "moverArchivo";
        $archivo = $DBConnection->consultaArchivo($nombreArchivo, $carpetActual->getIdCarpeta(), $usuario->getidUsuario());
        moverArchivo($archivo, $DBConnection);
        break;
    default;
        echo "invalidrequest";
        break;
}

function eliminarArchivo($archivo, $DBConnection) {
    if ($DBConnection->eliminarArchivo($archivo)) {
        echo "correct";
    } else {
        echo "incorrect";
    }
}

function editarArchivo($archivo, $DBConnection) {
    $nuevoNomArch = filter_input(INPUT_POST, 'nuevoNomArch', FILTER_SANITIZE_STRING);
    if ($DBConnection->actualizaArchivo($archivo, $nuevoNomArch)) {
        echo "correct";
    } else {
        echo "incorrect";
    }
}

function moverArchivo($archivo, $DBConnection) {
    $idCarpetaDest = filter_input(INPUT_POST, 'idCarpetaDest', FILTER_SANITIZE_NUMBER_INT);
    if ($DBConnection->moverArchivo($archivo, $idCarpetaDest)) {
        echo "Se movio el archivo";
    } else {
        echo "Error al mover el archivo";
    }
}

function descargarArchivo($archivo) {
    //
    //
    $dirsubida = "../files/";
    $carpeta_usuario = "/" . $archivo->getIdUsuario();

    //echo $archivo->toString();
    //
    ////Ejecucion script
    $comando = "python ../python/recuperar_archivo.py " . $archivo->getNombreArchivoGRID() . " " . $dirsubida . " " . $carpeta_usuario;
    //echo "<p>".$comando."</p>";
    modif_shell_exec($comando, $stdout = null, $stderr = null);
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

function subirArchivo($usuario, $carpetActual, $DBConnection) {
    $dirsubida = "../files/"; //Directorio de Apache donde se almacenan los archivos 
    if (!empty($_FILES['file']['name'])) { //Se verifica que el usuario haya seleccionado un archivo
        $dateTime = new DateTime();
        $timeStamp = $dateTime->getTimestamp();
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
                //Actualiza la variable de sesion 
                $_SESSION["usuario"] = serialize($usuario);
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