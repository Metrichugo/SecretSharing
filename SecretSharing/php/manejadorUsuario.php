<?php

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.html');
    exit;
}

include_once("BaseDeDatos.php");
include_once("Usuario.php");

$operacion = filter_input(INPUT_POST, 'Operation', FILTER_SANITIZE_STRING);

switch ($operacion) {
//    case "iniciaSesion";
//        $usuario = new Usuario();
//        $usuario->setidUsuario(filter_input(INPUT_POST, 'Email', FILTER_VALIDATE_EMAIL));
//        $usuario->setContrasenia(filter_input(INPUT_POST, 'Password'));
//        iniciaSesion($usuario);
//        break;
    case "cerrarSesion";
        $usuario = unserialize($_SESSION["usuario"]);
        cerrarSesion($usuario);
        break;
    case "validarPassword";
        $usuario = unserialize($_SESSION["usuario"]);
        $password = filter_input(INPUT_POST, 'password');
        validarContrasenia($usuario, $password);
        break;
    case "cambiarPassword";
        $usuario = unserialize($_SESSION["usuario"]);
        $DBConnection = unserialize($_SESSION["DBConnection"]);
        $DBConnection->connect();
        $newPassword = filter_input(INPUT_POST, 'newPassword');
        modificarContrasenia($usuario, $newPassword, $DBConnection);
        break;
    case "cambiarNombreUsuario";
        $usuario = unserialize($_SESSION["usuario"]);
        $DBConnection = unserialize($_SESSION["DBConnection"]);
        $DBConnection->connect();
        $newNombreUsuario = filter_input(INPUT_POST, 'newNombreUsuario');
        $newNombreLocal = filter_input(INPUT_POST, 'newNombreLocal');
        modificarNombreUsuario($usuario, $newNombreUsuario, $newNombreLocal, $DBConnection);
        break;
    default;
        echo "invalidrequest";
        break;
}

function iniciaSesion($usuario) {
    $DBConnection = new BaseDeDatos();
    $DBConnection->connect();
    //El usuario existe?
    $result = $DBConnection->existeUsuarioContrasenia($usuario);
    if ($result) {
        $usuario->iniciaSesion();
        $usuario = $DBConnection->consultaUsuario($usuario);
        $_SESSION["usuario"] = serialize($usuario);
        $_SESSION["DBConnection"] = serialize($DBConnection);
        echo "correct";
    } else {
        $DBConnection->close();
        echo "incorrect";
    }
}

function cerrarSesion($usuario) {
    $usuario->cerrarSesion();
    echo "correct";
}

function validarContrasenia($usuario, $password) {
    if (strcmp($usuario->getContrasenia(), $password) == 0) {
        echo "correct";
    } else {
        echo "incorrect";
    }
}

function modificarContrasenia($usuario, $newPassword, $DBConnection) {
    $oldPassword = $usuario->getContrasenia();
    $usuario->modificarContrasenia($newPassword);
    $res = $DBConnection->actualizaUsuario($usuario);
    if ($res) {
        $_SESSION["usuario"] = serialize($usuario);
        echo "correct";
    } else {
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

function modificarNombreUsuario($usuario, $newNombreUsuario, $newNombreLocal, $DBConnection) {
    $oldUsuario = clone($usuario);
    $newUsuario = clone($usuario);
    $newUsuario->setidUsuario($newNombreUsuario);

    if (!$DBConnection->existeUsuario($newUsuario)) {
        if (strcmp($newNombreLocal, $oldUsuario->getContrasenia()) !== 0) {
            $res = $DBConnection->actualizaIdUsuario($oldUsuario, $newNombreUsuario);
            if ($res) {
                $_SESSION["usuario"] = serialize($newUsuario);
                echo "0";
            } else {
                $_SESSION["usuario"] = serialize($oldUsuario);
                echo "3";
            }
        } else {
            echo "1";
        }
    } else {
        echo "2";
    }
}

?>