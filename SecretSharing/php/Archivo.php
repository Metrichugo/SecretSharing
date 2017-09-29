<?php
    class Archivo{
        protected $idUsuario;
        protected $nombre;
        protected $tamanio;
        protected $fechaSubida;
        protected $idCarpeta;

        public function getNombre(){return $this->nombre;}
        public function setNombre($nombre){$this->nombre = $nombre;}
        public function getTamanio(){return $this->tamanio;}
        public function setTamanio($tamanio){$this->tamanio = $tamanio;}
        public function getFechaSubida(){return $this->fechaSubida;}
        public function setFechaSubida($fechaSubida){$this->fechaSubida = $fechaSubida;}
        public function getIdUsuario(){return $this->idUsuario;}
        public function setIdUsuario($idUsuario){$this->idUsuario = $idUsuario;}

        /*public function subeArchivo(){
            //TODO: Code for upload file
        }

        public function descargaArchivo(){
            //TODO: Code for download file
        }

        public function renombrarArchivo($nombre){
            $this->nombre = $nombre;
        }*/
    }
?>