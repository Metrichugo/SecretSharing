<?php

include_once("BaseDeDatos.php");
include_once("Usuario.php");
include_once("./Usuario_Accion.php");
include_once("./Usuario.php");

$usuario = new Usuario();
$email = filter_input(INPUT_GET, 'usuario', FILTER_VALIDATE_EMAIL);
$usuario->setidUsuario($email);
$url = filter_input(INPUT_GET, 'url', FILTER_SANITIZE_STRING);

if (!empty($email) && !empty($url)) {
    $DBConnection = new BaseDeDatos();
    $DBConnection->connect();
    if ($DBConnection->consultarEnlaceUsuario($usuario, $url)) {//El enlace es válido
        $usuario = $DBConnection->consultarUsuario($usuario);
        //Construcción del objeto de tipo Usuario_Accion
        $usuarioAccion = new Usuario_Accion($usuario, $DBConnection);
        //Accion
        $usuarioAccion->iniciarSesion();
        //Permitir la modificacion de contraseña
        $usuario->setModificarse(TRUE);
        $_SESSION["usuario"] = serialize($usuario);
        header('Location: ./ModificarCuenta.php');
    } else {
        header('Location: ../index.html');
        exit;
    }
} else {
    header('Location: ../index.html');
    exit;
}
