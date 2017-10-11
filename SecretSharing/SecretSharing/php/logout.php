<?php 
	session_start();  
    if(!isset($_SESSION['usuario'])){
        header('Location: ../index.html');
        exit;  
    }    
	include_once("BaseDeDatos.php");
	include_once("Usuario.php");
    $usuario = unserialize($_SESSION["usuario"]);// remove all session variables
    $usuario->cerrarSesion();

 ?>