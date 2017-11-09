<?php

class Usuario {

    protected $idUsuario;
    protected $contrasenia;
    protected $alias;
    protected $status;
    protected $espacioUtilizado;
    protected $puedeModificar;
    
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

    function puedeModificarse() {
        return $this->puedeModificar;
    }

    function setModificarse($puedeModificar) {
        $this->puedeModificar = $puedeModificar;
        return $this;
    }

    function eliminaGRID() {
        $handle = fopen("../ejecutables/servidores.txt", "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $line = str_replace("\n", "", $line);
                $comando = "ssh " . $line . " \"rm -rf ~/RCSS/" . $this->idUsuario . "/" . "\"";
                //echo $comando;                
                $this->modif_shell_exec($comando, $stdout, $stderr);
                //echo "Salida:" . $stdout . $stderr . " ";
            }
            fclose($handle);
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

}

?>