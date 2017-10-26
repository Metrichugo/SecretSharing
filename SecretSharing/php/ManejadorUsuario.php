<?php

    session_start();
    if (!isset($_SESSION['usuario'])) {
        header('Location: ../index.html');
        exit;
    }
    
    include_once("BaseDeDatos.php");
    include_once("Usuario.php");
   
    $usuario = unserialize($_SESSION["usuario"]);
    $DBConnection = unserialize($_SESSION["DBConnection"]);
    $DBConnection->connect();
    $operacion = $_POST['Operation'];
    
    switch($operacion){
        case "validarPassword";
            $password = $_POST['password'];
            validarContrasenia($usuario, $password );
            break;
        case "cambiarPassword";
            $newPassword = $_POST['newPassword'];
            modificarContrasenia($usuario, $newPassword, $DBConnection);
            break;
        
        case "cambiarNombreUsuario";
            $newNombreUsuario = $_POST['newNombreUsuario'];
            $newNombreLocal = $_POST['newNombreLocal'];
            modificarNombreUsuario($usuario, $newNombreUsuario, $newNombreLocal, $DBConnection);
            
            break;
        default;
            
            break;
    }
    
               
    
    
    function validarContrasenia($usuario, $password){
        if(strcmp( $usuario->getContrasenia(), $password ) == 0  ) echo "correct";
        else echo "incorrect";
    }
    function modificarContrasenia($usuario, $newPassword, $DBConnection){
        $oldPassword = $usuario->getContrasenia();
        $usuario->modificarContrasenia($newPassword);
        $res = $DBConnection->actualizaUsuario($usuario);
        if($res){
            $_SESSION["usuario"] = serialize($usuario);
            echo "correct";
        }
        else{
            $usuario->modificarContrasenia($oldPassword);
            $_SESSION["usuario"] = serialize($usuario);
            echo "incorrect";
        }
        
    }
    
    /*
     * 0 se cambio
     * 
     * 1 no por ser igual a contraseña
     * 2 no por duplicidad
     * 3 cualquier otro error
     */
    function modificarNombreUsuario($usuario, $newNombreUsuario, $newNombreLocal, $DBConnection){
        $oldUsuario = clone($usuario);
        $newUsuario = clone($usuario);
        $newUsuario->setidUsuario($newNombreUsuario);

        if(!$DBConnection->existeUsuario($newUsuario) ){
            if(strcmp($newNombreLocal, $oldUsuario->getContrasenia()) !== 0){
                $res = $DBConnection->actualizaIdUsuario($oldUsuario, $newNombreUsuario);
                if($res){
                    $_SESSION["usuario"] = serialize($newUsuario);
                    echo "0";
                }
                else{                    
                    $_SESSION["usuario"] = serialize($oldUsuario);
                    echo "3";
                }
            }else{
                echo "1";
            }
            
        }else{
            echo "2";
        }
    }
    
?>