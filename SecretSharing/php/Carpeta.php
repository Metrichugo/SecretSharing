<?php 
    class Carpeta{
        protected $idCarpeta;
        protected $idCarpetaSuperior;
        protected $nombreCarpeta;
        protected $fechaCreacion;
        
        function __construct($idCarpeta, $idCarpetaSuperior, $nombreCarpeta, $fechaCreacion){
            $this->idCarpeta = $idCarpeta;
            $this->idCarpetaSuperior = $idCarpetaSuperior;
            $this->nombreCarpeta = $nombreCarpeta;
            $this->fechaCreacion = $fechaCreacion;
        }
        public function getIdCarpeta(){ return $this->idCarpeta;}
        public function getIdCarpetaSuperior(){ return $this->idCarpetaSuperior;}
        public function getNombreCarpeta(){ return $this->nombreCarpeta;}
        public function getFechaCreacion(){ return $this->fechaCreacion;}

        public function setIdCarpeta($idCarpeta){
            $this->idCarpeta = $idCarpeta;
        }
        public function setIdCarpetaSuperior($idCarpetaSuperior){
            $this->idCarpetaSuperior = $idCarpetaSuperior;
        }
        public function setNombreCarpeta($nombreCarpeta){
            $this->nombreCarpeta = $nombreCarpeta;
        }
        public function setFechaCreacion($fechaCreacion){
            $this->fechaCreacion = $fechaCreacion;
        }
        public function toString(){
            printf("idCarpeta = %s idCarpetaSuperior = %s nombreCarpeta = %s fechaCreacion = %s<br>",
                 $this->idCarpeta, $this->idCarpetaSuperior, $this->nombreCarpeta, $this->fechaCreacion);
        }

    }

 ?>