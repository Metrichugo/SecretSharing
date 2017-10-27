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
$carpetActual = unserialize($_SESSION["carpetActual"]);

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