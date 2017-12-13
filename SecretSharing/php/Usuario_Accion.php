<?php

include_once("Usuario.php");
include_once("BaseDeDatos.php");
include_once("Carpeta.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../assets/PHPMailer/src/Exception.php';
require '../assets/PHPMailer/src/PHPMailer.php';
require '../assets/PHPMailer/src/SMTP.php';

//Constantes para crear un usuario
const STATUS = 1;
const ESP_UTILIZADO = 0;

class Usuario_Accion {

    protected $usuario;
    protected $DBConnection;

    function __construct($usuario, $DBConnection) {
        $this->usuario = $usuario;
        $this->DBConnection = $DBConnection;
    }

    public function registrarUsuario() {
        $es_valida_contrasenia = $this->validarContrasenia($this->usuario->getidUsuario(), $this->usuario->getContrasenia());
        $es_valido_email = $this->validarIdUsuario($this->usuario->getidUsuario());
        if (!$es_valida_contrasenia || !$es_valido_email) {
            echo "invalidrequest";
            return;
        }

        //User exists?
        if ($this->DBConnection->existeUsuario($this->usuario)) {
            $this->DBConnection->close();
            echo "duplicated";
        } else {
            $this->usuario->setStatus(STATUS);
            $this->usuario->setEspacioUtilizado(ESP_UTILIZADO);
            //Se inserta el usuario y su carpeta raiz en la BD
            if ($this->DBConnection->insertarUsuario($this->usuario) && $this->DBConnection->insertarCarpetaRaiz($this->usuario)) {
                echo "correct";
            } else {
                echo "incorrect";
            }
        }
    }

    private function validarContrasenia($idUsuario, $contrasenia) {
        $regex_contrasenia = '/^(?=.*\d)(?=.*[\!-\/:-@\[-_)(?=.*[A-Z])(?=.*[a-z])\S{8,64}$/';
        $resultado_regex = preg_match($regex_contrasenia, $contrasenia);
        $igual = ($idUsuario == $contrasenia);
        return $resultado_regex && !$igual;
    }

    private function validarIdUsuario($idUsuario) {
        return ($idUsuario != '' && strlen($idUsuario) < 255);
    }

    public function iniciarSesion() {
        //El usuario existe?
        if ($this->DBConnection->existeUsuarioContrasenia($this->usuario)) {
            $this->usuario = $this->DBConnection->consultarUsuario($this->usuario);
            $this->usuario->setModificarse(FALSE);
            //Se inicia una sesión de php
            session_start();
            //Se serializa el usuario y la conexion a la BD como variables de sesion
            $_SESSION["usuario"] = serialize($this->usuario);
            $_SESSION["DBConnection"] = serialize($this->DBConnection);
            echo "correct";
        } else {
            $this->DBConnection->close();
            echo "incorrect";
        }
    }

    public function cerrarSesion() {
        // remove all session variables
        session_unset();
        // destroy the session 
        session_destroy();
        echo "correct";
    }

    public function validarIngresoContrasenia($password) {
        if (strcmp($this->usuario->getContrasenia(), $password) == 0) {
            $this->usuario->setModificarse(TRUE);
            $_SESSION["usuario"] = serialize($this->usuario);
            echo "correct";
        } else {
            echo "incorrect";
        }
    }

    public function modificarContrasenia($newPassword) {
        $oldPassword = $this->usuario->getContrasenia();
        $this->usuario->modificarContrasenia($newPassword);
        $es_valida_contrasenia = $this->validarContrasenia($this->usuario->getidUsuario(), $this->usuario->getContrasenia());
        if ($es_valida_contrasenia && $this->DBConnection->actualizarUsuario($this->usuario)) {
            $_SESSION["usuario"] = serialize($this->usuario);
            echo "correct";
        } else {
            $this->usuario->modificarContrasenia($oldPassword);
            $_SESSION["usuario"] = serialize($this->usuario);
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

    public function modificarNombreUsuario($newNombreUsuario) {
        $oldUsuario = clone($this->usuario);
        $newUsuario = clone($this->usuario);
        $newUsuario->setidUsuario($newNombreUsuario);
        $es_valido_email = $this->validarIdUsuario($newUsuario->getidUsuario());
        if (!$es_valido_email) {
            echo "4";
            return;
        }

        if (!$this->DBConnection->existeUsuario($newUsuario)) {
            if (strcmp($newNombreUsuario, $oldUsuario->getContrasenia()) !== 0) {
                if ($this->DBConnection->actualizaIdUsuario($oldUsuario, $newNombreUsuario)) {
                    $_SESSION["usuario"] = serialize($newUsuario);
                    echo "0";
                    //Cambio de nombre del directorio
                    $newNombreUsuario;
                    $handle = fopen("../ejecutables/servidores.txt", "r");
                    if ($handle) {
                        while (($line = fgets($handle)) !== false) {
                            $line = str_replace("\n", "", $line);
                            $comando = "ssh " . $line . " \"mv ~/RCSS/" . $oldUsuario->getidUsuario() . " ~/RCSS/" . $newNombreUsuario . "\"";
                            //echo $comando;                
                            $this->modif_shell_exec($comando, $stdout, $stderr);
                            //echo "Salida:" . $stdout . $stderr . " ";
                        }
                        fclose($handle);
                    }
                } else {
                    $_SESSION["usuario"] = serialize($oldUsuario);
                    echo "3";
                }
            } else {
                echo "1";
            }
        } else {
            echo "2";
            return;
        }
    }

    private function modif_shell_exec($cmd, &$stdout = null, &$stderr = null) {
        $pipes = null;
        $proc = proc_open($cmd, [
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
                ], $pipes);
        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        return proc_close($proc);
    }

    public function eliminarCuentaUsuario() {
        if ($this->DBConnection->borrarUsuario($this->usuario)) {
            $this->usuario->eliminaGRID();
            $this->cerrarSesion();
        } else {
            $this->DBConnection->close();
            echo "incorrect";
        }
    }

    public function recuperarUsuario() {
        if ($this->DBConnection->existeUsuario($this->usuario)) {
            $this->usuario = $this->DBConnection->consultarUsuario($this->usuario);
            if ($this->enviarCorreo()) {
                echo "correct";
            } else {
                echo "incorrect";
            }
        } else {
            $this->DBConnection->close();
            echo "incorrect";
        }
    }

    private function enviarCorreo() {
        $urlRecuperacion = bin2hex(openssl_random_pseudo_bytes(31));
        //Insertar en la BD
        $this->DBConnection->editarEnlaceUsuario($this->usuario, $urlRecuperacion);
        $enlace = "https://maestro/php/manejadorRecuperarCuenta.php?usuario=" . $this->usuario->getidUsuario() . "&url=" . $urlRecuperacion;

        $mail = new PHPMailer();
        $mail->CharSet = 'UTF-8';
        //configuracion de phpmailer

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'secretocompartido1@gmail.com';
        $mail->Password = 'Vaporru1';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        //configuracion del correo a enviar 

        $mail->setFrom('secretocompartido1@gmail.com');
        $mail->addAddress($this->usuario->getidUsuario());
        $mail->Subject = 'Recuperación de la contraseña Secreto Compartido';
        $mail->Body = 'Recientemente hemos recibido una solicitud para recuperar su cuenta del Sistema Secreto Compartido '
                . $this->usuario->getidUsuario()
                . ' . Haga clic en el siguiente enlace: ' . $enlace;
        return $mail->send();
    }

}
