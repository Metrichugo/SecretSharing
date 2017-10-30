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
$userForm->setidUsuario(filter_input(INPUT_POST, 'Email', FILTER_VALIDATE_EMAIL));
$userForm->setContrasenia(filter_input(INPUT_POST, 'Password'));
$userForm->setAlias(filter_input(INPUT_POST, 'Alias'), FILTER_SANITIZE_STRING);

$es_valida_contrasenia = $userForm->validaContrasenia();
$es_valido_email = ($userForm->getidUsuario() != '');

if (!$es_valida_contrasenia || !$es_valido_email) {
    echo "invalidrequest";
    return;
}

//User exists?
$result = $DBConnection->existeUsuario($userForm);

if ($result) {
    $DBConnection->close();
    echo "Duplicated User";
} else {

    $userForm->setStatus(STATUS);
    $userForm->setEspacioUtilizado(ESP_UTILIZADO);
    $r1 = $DBConnection->insertaUsuario($userForm);
    if ($r1) {

        $r2 = $DBConnection->insertaCarpetaRaiz($userForm);
        if ($r2) {
            echo "correct";
        } else {
            echo "incorrect";
        }
    } else {
        echo "incorrect";
    }
}
?>