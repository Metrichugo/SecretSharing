<?php
    require_once("BaseDeDatos.php");
    require_once("Usuario.php");
    //Stablishing connection with Database
    $DBConnection = new BaseDeDatos();
    $DBConnection->connect();
    //Saving and recovering values from LoginForm
    $userForm = new Usuario();
    $userForm->setidUsuario($_POST['Email']);
    $userForm->setContraseña($_POST['Password']);
    //User exists?
    $result = $DBConnection->existeUsuario($userForm);
    if($result){
        $userForm->iniciaSesion();
        echo "correct";
    }else{
        echo "incorrect";
    }
?>