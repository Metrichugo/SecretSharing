<?php
    class Archivo{
        protected $nombreArchivo;
        protected $tamanio;
        protected $fechaSubida;

        function __construct($nombreArchivo, $tamanio, $fechaSubida){
            $this->nombreArchivo = $nombreArchivo;
            $this->tamanio = $tamanio;
            $this->fechaSubida = $fechaSubida;
        }

        public function getNombreArchivo(){return $this->nombreArchivo;}
        public function getTamanio(){return $this->tamanio;}
        public function getFechaSubida(){return $this->fechaSubida;}

        public function setNombreArchivo($nombreArchivo){$this->nombreArchivo = $nombreArchivo;}
        public function setTamanio($tamanio){$this->tamanio = $tamanio;}
        public function setFechaSubida($fechaSubida){$this->fechaSubida = $fechaSubida;}
        
        public function toString(){
            printf("nombreArchivo = %s tamanio = %s fechaSubida = %s <br>",
                 $this->nombreArchivo, $this->tamanio, $this->fechaSubida);
        }

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