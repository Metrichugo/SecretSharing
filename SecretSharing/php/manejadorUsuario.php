<?php

include_once("BaseDeDatos.php");
include_once("Usuario.php");
include_once("./Usuario_Accion.php");

$operacion = filter_input(INPUT_POST, 'Operation', FILTER_SANITIZE_STRING);

if ($operacion != "iniciarSesion" && $operacion != "registrarUsuario") {    //Modificacion de cuenta
    session_start();
    if (!isset($_SESSION['usuario'])) {
        header('Location: ../index.html');
        exit;
    }

    $usuario = unserialize($_SESSION["usuario"]);
    $DBConnection = unserialize($_SESSION["DBConnection"]);
    $DBConnection->connect();
    //Construcción del objeto de tipo Usuario_Accion
    $usuarioAccion = new Usuario_Accion($usuario, $DBConnection);
    switch ($operacion) {
        case "cerrarSesion";
            //Accion
            $usuarioAccion->cerrarSesion();
            break;
        case "validarPassword";
            $password = filter_input(INPUT_POST, 'password');
            //Accion
            $usuarioAccion->validarIngresoContrasenia($password);
            break;
        case "cambiarPassword";
            $newPassword = filter_input(INPUT_POST, 'newPassword');
            //Accion
            $usuarioAccion->modificarContrasenia($newPassword);
            break;
        case "cambiarNombreUsuario";
            $newNombreUsuario = filter_input(INPUT_POST, 'newNombreUsuario', FILTER_VALIDATE_EMAIL);
            $newNombreLocal = filter_input(INPUT_POST, 'newNombreLocal');
            $usuarioAccion->modificarNombreUsuario($newNombreUsuario, $newNombreLocal);
            break;
        default;
            echo "invalidrequest";
            break;
    }
} else { //Inicio de sesion/registro
    $DBConnection = new BaseDeDatos();
    $DBConnection->connect();
    switch ($operacion) {
        case "iniciarSesion";
            //Creacion del objeto de tipo Usuario
            $usuario = new Usuario();
            $usuario->setidUsuario(filter_input(INPUT_POST, 'Email', FILTER_VALIDATE_EMAIL));
            $usuario->setContrasenia(filter_input(INPUT_POST, 'Password'));
            //Construcción del objeto de tipo Usuario_Accion
            $usuarioAccion = new Usuario_Accion($usuario, $DBConnection);
            //Accion
            $usuarioAccion->iniciarSesion();
            break;
        case "registrarUsuario";
            //Creacion del objeto de tipo Usuario
            $usuario = new Usuario();
            $usuario->setidUsuario(filter_input(INPUT_POST, 'Email', FILTER_VALIDATE_EMAIL));
            $usuario->setContrasenia(filter_input(INPUT_POST, 'Password'));
            $usuario->setAlias(filter_input(INPUT_POST, 'Alias', FILTER_SANITIZE_STRING));
            //Construcción del objeto de tipo Usuario_Accion
            $usuarioAccion = new Usuario_Accion($usuario, $DBConnection);
            //Accion
            $usuarioAccion->registrarUsuario();
            break;
    }
}
?>