<?php

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.html');
    exit;
}
//includes
include_once("BaseDeDatos.php");
include_once("Usuario.php");
include_once("Archivo.php");
include_once("Carpeta.php");

//Variables de sesión 
$usuario = unserialize($_SESSION["usuario"]); //Objeto de tipo Usuario
$DBConnection = unserialize($_SESSION["DBConnection"]);
$carpetActual = unserialize($_SESSION["carpetActual"]); //Objeto de tipo carpeta 
$DBConnection->connect(); // Al finaliza el archivo se cierra la conexion con db
//
//Manejo del archivo
$operacion = $_POST['Operation'];
$dirsubida = "../files/";
$timeStamp = (new DateTime())->getTimestamp();

if ($operacion == "SubirArchivo") {

    if (!empty($_FILES['file']['name'])) {
        //Objeto de tipo archivo
        $nombreArchivo = basename($_FILES['file']['name']);
        $idCarpeta = $carpetActual->getIdCarpeta();
        $idUsuario = $usuario->getidUsuario();
        $tamanio = basename($_FILES['file']['size']);
        $nombreArchivoGRID = trim($nombreArchivo . $timeStamp);
        $fechaSubida = date("Y-m-d");

        $archivo = new Archivo($nombreArchivo, $idCarpeta, $idUsuario, $nombreArchivoGRID, $tamanio, $fechaSubida);

        //Mover del directorio temporal al directorio files
        $result = seMovioTemporal($archivo->getNombreArchivoGRID(), $dirsubida);
        //echo $result;

        if ($result) {
            $carpeta_usuario = "/" . $usuario->getidUsuario();
            $comando = "python ../python/comparte_archivo.py " . $archivo->getNombreArchivoGRID() . " " . $dirsubida . " " . $carpeta_usuario;
            my_shell_exec($comando, $stdout, $stderr);
            echo "<p>" . $stdout . "</p>";
            echo "<p>" . $stderr . "</p>";
            exec("rm " . $dirsubida . $renamedFile);
            //Validar ejecucion de la GRID
            //
            
            // Insercion en la base de datos           
            $DBConnection->insertaArchivo($archivo);
            //Fin
            echo "<p>" . "UploadSuccesfull" . "</p>";
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

function my_shell_exec($cmd, &$stdout = null, &$stderr = null) {
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
