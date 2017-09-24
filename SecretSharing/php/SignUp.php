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
$userForm->setContraseña($_POST['Password']);
$userForm->setAlias($_POST['Alias']);
//User exists?
$result = $DBConnection->existeUsuario($userForm);
if($result){
    echo "Duplicated User";
}else{
    $userForm->setStatus(STATUS);
    $userForm->setEspacioDisp(ESP_UTILIZADO);
    $DBConnection->insertaUsuario($userForm);
    echo "correct";
}

?>