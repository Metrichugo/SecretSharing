<?php

class Archivo {

    protected $nombreArchivo;
    protected $idCarpeta;
    protected $idUsuario;
    protected $nombreArchivoGRID;
    protected $tamanio;
    protected $fechaSubida;

    function __construct($nombreArchivo, $idCarpeta, $idUsuario, $nombreArchivoGRID, $tamanio, $fechaSubida) {
        $this->nombreArchivo = $nombreArchivo;
        $this->idCarpeta = $idCarpeta;
        $this->idUsuario = $idUsuario;
        $this->nombreArchivoGRID = $nombreArchivoGRID;
        $this->tamanio = $tamanio;
        $this->fechaSubida = $fechaSubida;
    }

    public function getNombreArchivo() {
        return $this->nombreArchivo;
    }

    public function getTamanio() {
        return $this->tamanio;
    }

    public function getFechaSubida() {
        return $this->fechaSubida;
    }

    public function setNombreArchivo($nombreArchivo) {
        $this->nombreArchivo = $nombreArchivo;
    }

    public function setTamanio($tamanio) {
        $this->tamanio = $tamanio;
    }

    public function setFechaSubida($fechaSubida) {
        $this->fechaSubida = $fechaSubida;
    }

    public function toString() {
        printf("nombreArchivo = %s tamanio = %s fechaSubida = %s <br>", $this->nombreArchivo, $this->tamanio, $this->fechaSubida);
    }

    function getIdCarpeta() {
        return $this->idCarpeta;
    }

    function getIdUsuario() {
        return $this->idUsuario;
    }

    function getNombreArchivoGRID() {
        return $this->nombreArchivoGRID;
    }

    function setIdCarpeta($idCarpeta) {
        $this->idCarpeta = $idCarpeta;
        return $this;
    }

    function setIdUsuario($idUsuario) {
        $this->idUsuario = $idUsuario;
        return $this;
    }

    function setNombreArchivoGRID($nombreArchivoGRID) {
        $this->nombreArchivoGRID = $nombreArchivoGRID;
        return $this;
    }

    function eliminaGRID() {
        $handle = fopen("../ejecutables/servidores.txt", "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $line = str_replace("\n", "", $line);
                $comando = "ssh " . $line . " \"rm -rf ~/RCSS/" . $this->idUsuario . "/" . $this->nombreArchivoGRID . "\"";
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