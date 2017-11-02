<?php

class Usuario {

    protected $idUsuario;
    protected $contrasenia;
    protected $alias;
    protected $status;
    protected $espacioUtilizado;

    public function getidUsuario() {
        return $this->idUsuario;
    }

    public function getContrasenia() {
        return $this->contrasenia;
    }

    public function getAlias() {
        return $this->alias;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getEspacioUtilizado() {
        return $this->espacioUtilizado;
    }

    public function setidUsuario($idUsuario) {
        $this->idUsuario = $idUsuario;
    }

    public function setContrasenia($contrasenia) {
        $this->contrasenia = $contrasenia;
    }

    public function setAlias($alias) {
        $this->alias = $alias;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function setEspacioUtilizado($espacioUtilizado) {
        $this->espacioUtilizado = $espacioUtilizado;
    }

    public function modificarContrasenia($Contrasenia) {
        $this->setContrasenia($Contrasenia);
    }

    public function modificarAlias($alias) {
        $this->setAlias($alias);
    }

    public function toString() {
        printf("idUsuario = %s Contrasenia = %s alias = %s status = %s espacioUtilizado = %s \n", $this->idUsuario, $this->contrasenia, $this->alias, $this->status, $this->espacioUtilizado);
    }



}

?>