<?php
    session_start();  
    if(!isset($_SESSION['usuario'])){
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







    // Force a download dialog on the user's browser:
    $nombreArchivo = $_POST["nombreArchivo"];
    $idCarpeta = $_POST["idCarpeta"];

    $filepath = '../files/'.$nombreArchivo;
    
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: must-revalidate');
    //header('Content-Length: '.filesize($filepath));
    ob_clean();
    flush();
    readfile($filepath);
    exit();
?>