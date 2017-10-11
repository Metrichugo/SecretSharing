<?php
//Constants to create an user
const STATUS = 1;
const ESP_UTILIZADO = 0;
require_once("BaseDeDatos.php");
require_once("Usuario.php");
//Stablishing connection with Database
$DBConnection = new BaseDeDatos();
$DBConnection->connect();
//Saving and recovering values from LoginForm
$userForm = new Usuario();
$userForm->setidUsuario($_POST['Email']);
$userForm->setContrasenia($_POST['Password']);
$userForm->setAlias($_POST['Alias']);
//User exists?
$result = $DBConnection->existeUsuario($userForm);
if($result){
	$DBConnection->close();
    echo "Duplicated User";
}else{
    $userForm->setStatus(STATUS);
    $userForm->setEspacioUtilizado(ESP_UTILIZADO);
    $r1 = $DBConnection->insertaUsuario($userForm);
    if($r1){

    	$r2 = $DBConnection->insertaCarpetaRaiz($userForm);
    	if($r2){
    		echo "correct";
    	}else{
    		echo "incorrect";	
    	}
    }else{
    	echo "incorrect";
    }
}

?>