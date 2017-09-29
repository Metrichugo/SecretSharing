<?php
    class Carpeta{
        protected $idUsuario;
        protected $idCarpeta;
        protected $nombre;
        protected $fechaCreacion;
        protected $idCarpetaSup;

        public function crearCarpeta($idUsuario,$idCarpeta,$nombre,$idCarpetaSup){
            $this->idUsuario = $idUsuario;
            $this->idCarpeta = $idCarpeta;
            $this->nombre = $nombre;
            $this->idCarpetaSup = $idCarpetaSup;
            $this->fechaCreacion = ""; //TODO: Code for extract date from system 
        }

        public function getIdUsuario(){
            return $this->idUsuario;
        }

        public function getIdCarpeta(){
            return $this->idCarpeta;
        }

        public function getNombre(){
            return $this->nombre;
        }

        public function getIdCarpetaSup(){
            return $this->idCarpetaSup;
        }

        public function getFechaCreacion(){
            return $this->fechaCreacion;
        }

        public function toString(){
            printf("%s %s %s %s %s \n",$this->idUsuario,$this->idCarpeta,$this->nombre,$this->idCarpetaSup,$this->fechaCreacion);
        }

    }
?>