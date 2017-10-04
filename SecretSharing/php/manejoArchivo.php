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

    $operacion = $_POST['Operation'];
    $carpetActual = unserialize($_SESSION["carpetActual"]);

    if($operacion == "eliminarArchivo"){
		        
    	$idCarpeta =  $_POST["idCarpeta"];
        $nombreArchivo  = $_POST["nombreArchivo"];

        $result = $DBConnection->eliminarArchivo($usuario,$idCarpeta, $nombreArchivo);

        if($result) echo "correct";
        else{ echo "incorrect"; }
        exit();
    }

?>