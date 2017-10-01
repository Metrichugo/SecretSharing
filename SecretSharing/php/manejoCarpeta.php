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



    if($operacion == "actualizarCarpetaActual"){
        $idcarpetaMoverse = $_POST['idCarpetaMoverse'];
    	//Actualizamos el objeto carpeta a la que se va a mostrar en pantalla
    	$carpeta = $DBConnection->consultaCarpeta($usuario, $idcarpetaMoverse);
        $_SESSION["carpetActual"] = serialize($carpeta);
        echo "correct";
    }


    if($operacion == "actualizarCarpetasEnPantalla"){
        $ans = $DBConnection->listaCarpetas($usuario, $carpetActual);
        echo ($ans);
    }   

    if($operacion == "actualizarArchivosEnPantalla"){
        $ans = $DBConnection->listaArchivos($usuario, $carpetActual);
        echo ($ans);
    }  


    if($operacion == "irCarpetaAtras"){
        if($carpetActual-> getIdCarpeta() == "1" ){
            echo "incorrect";
            exit();
        }

        $idCarpetaSup = $carpetActual->getIdCarpetaSuperior();
        $carpetActual->toString();
        $carpetaSup = $DBConnection->consultaCarpeta($usuario, $idCarpetaSup);
        $_SESSION["carpetActual"] = serialize($carpetaSup);
        echo( $idCarpetaSup);
    }
	
    if($operacion == "crearNuevaCar"){
        $nombreNuevaCarpeta = $_POST['nombreCarpeta'];
        $result = $DBConnection->existeCarpeta($usuario, $carpetActual, $nombreNuevaCarpeta);
        if($result){
            echo "incorrect";exit();
        }
        // lo insertamos en la basede datos
        $result =  $DBConnection->insertaCarpeta($usuario, $carpetActual, $nombreNuevaCarpeta);
        if($result){
            echo "correct";exit();
        }
        echo "incorrect";
        exit();
    }


 ?>