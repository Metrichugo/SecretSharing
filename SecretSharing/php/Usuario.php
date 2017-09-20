<?php
    class Usuario{
        protected $idUsuario;
        protected $contraseña;
        protected $alias;
        protected $status;
        protected $espacioDisp;

        public function getidUsuario(){ return $this->idUsuario;}
        public function setidUsuario($idUsuario){ $this->idUsuario = $idUsuario;}
        public function getContraseña(){ return $this->contraseña;}
        public function setContraseña($contraseña){ $this->contraseña = $contraseña;}
        public function getAlias(){ return $this->alias;}
        public function setAlias($alias) {$this->alias = $alias;}
        public function getStatus(){ return $this->status;}
        public function setStatus($status){ $this->status = $status;}
        public function getEspacioDisp(){return $this->espacioDisp;}
        public function setEspacioDisp($espacioDisp){ $this->espacioDisp = $espacioDisp;}
        public function toString(){
            printf("%s %s %s %s %s \n",$this->idUsuario,$this->contraseña,$this->alias,$this->status,$this->espacioDisp);
        }
    }
?>