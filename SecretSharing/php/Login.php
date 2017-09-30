<?php
    require_once("BaseDeDatos.php");
    require_once("Usuario.php");
    //Stablishing connection with Database
    $DBConnection = new BaseDeDatos();
    $DBConnection->connect();
    //Saving and recovering values from LoginForm
    $userForm = new Usuario();
    $userForm->setidUsuario($_POST['Email']);
    $userForm->setContrasenia($_POST['Password']);
    //User exists?
    $result = $DBConnection->existeUsuario($userForm);
    if($result){
        $userForm->iniciaSesion();
        $usuario = $DBConnection->consultaUsuario($userForm);
        $_SESSION["usuario"] = serialize($usuario);
        $_SESSION["DBConnection"] = serialize($DBConnection);

        echo "correct";
    }else{
        $DBConnection->close();
        echo "incorrect";

    }
    
?>