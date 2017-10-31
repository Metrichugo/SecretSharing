<?php

class Carpeta {

    protected $idCarpeta;
    protected $idUsuario;
    protected $idCarpetaSuperior;
    protected $nombreCarpeta;
    protected $fechaCreacion;

    function __construct($idCarpeta, $idUsuario, $idCarpetaSuperior, $nombreCarpeta, $fechaCreacion) {
        $this->idCarpeta = $idCarpeta;
        $this->idUsuario = $idUsuario;
        $this->idCarpetaSuperior = $idCarpetaSuperior;
        $this->nombreCarpeta = $nombreCarpeta;
        $this->fechaCreacion = $fechaCreacion;
    }

    function getIdUsuario() {
        return $this->idUsuario;
    }

    function setIdUsuario($idUsuario) {
        $this->idUsuario = $idUsuario;
        return $this;
    }

    public function getIdCarpeta() {
        return $this->idCarpeta;
    }

    public function getIdCarpetaSuperior() {
        return $this->idCarpetaSuperior;
    }

    public function getNombreCarpeta() {
        return $this->nombreCarpeta;
    }

    public function getFechaCreacion() {
        return $this->fechaCreacion;
    }

    public function setIdCarpeta($idCarpeta) {
        $this->idCarpeta = $idCarpeta;
    }

    public function setIdCarpetaSuperior($idCarpetaSuperior) {
        $this->idCarpetaSuperior = $idCarpetaSuperior;
    }

    public function setNombreCarpeta($nombreCarpeta) {
        $this->nombreCarpeta = $nombreCarpeta;
    }

    public function setFechaCreacion($fechaCreacion) {
        $this->fechaCreacion = $fechaCreacion;
    }

    public function toString() {
        printf("idCarpeta = %s idCarpetaSuperior = %s nombreCarpeta = %s fechaCreacion = %s<br>", $this->idCarpeta, $this->idCarpetaSuperior, $this->nombreCarpeta, $this->fechaCreacion);
    }

}

?>