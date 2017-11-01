<?php

class Archivo_Accion {

    protected $archivo;
    protected $DBConnection;

    function __construct($archivo, $DBConnection) {
        $this->archivo = $archivo;
        $this->DBConnection = $DBConnection;
    }

    public function subirArchivo() {
        $dirsubida = "../files/"; //Directorio de Apache donde se almacenan los archivos 
        if ($this->seMovioTemporal($this->archivo->getNombreArchivoGRID(), $dirsubida)) { //Si se movio el archivo del directorio temporal al directorio files
            $carpeta_usuario = "/" . $this->archivo->getIdUsuario();
            $comando = "python ../python/comparte_archivo.py " . $this->archivo->getNombreArchivoGRID() . " " . $dirsubida . " " . $carpeta_usuario;
            $this->modif_shell_exec($comando, $stdout, $stderr);
            //echo "Salida python: <p>" . $stdout . "</p>";
            //echo "<p>" . $stderr . "</p>";
            //Validar ejecucion de la GRID
            $string_ok = "El archivo se compartio correctamente";
            $log = $dirsubida . $this->archivo->getNombreArchivoGRID() . ".out";
            //Busca la cadena ok para saber si la ejecucion fue correcta
            if (strpos(file_get_contents($log), $string_ok) !== false) {
                // Insercion en la base de datos           
                $this->DBConnection->insertaArchivo($this->archivo);
                //Aumenta espacio utilizado
                $usuario = unserialize($_SESSION["usuario"]); //Objeto de sesion tipo usuario
                $usuario->setEspacioUtilizado($usuario->getEspacioUtilizado() + $this->archivo->getTamanio());
                $this->DBConnection->editaEspacioUtilizado($usuario);
                //Actualiza la variable de sesion 
                $_SESSION["usuario"] = serialize($usuario);
                //Fin
                echo "UploadSuccesfull";
                //Borrado del archivo
                unlink($dirsubida . $this->archivo->getNombreArchivoGRID());
                //unlink($dirsubida . $this->archivo->getNombreArchivoGRID());
                //unlink($log);
                unlink($dirsubida . $this->archivo->getNombreArchivoGRID() . ".err");
            } else {
                echo "UploadFailed";
            }
        } else {
            echo "ErrorCantMove ";
        }
    }

    private function seMovioTemporal($nombreArchivoGRID, $dirsubida) {
        $uploadedFile = $dirsubida . $nombreArchivoGRID;
        return (move_uploaded_file($_FILES['file']['tmp_name'], $uploadedFile));
    }

    public function eliminarArchivo() {
        if ($this->DBConnection->eliminarArchivo($this->archivo)) {
            $this->archivo->eliminaGRID();
            //Actualiza la variable de sesion
            $usuario = unserialize($_SESSION["usuario"]); //Objeto de sesion tipo usuario
            $usuario->setEspacioUtilizado($usuario->getEspacioUtilizado() - $this->archivo->getTamanio());
            $this->DBConnection->editaEspacioUtilizado($usuario);
            //Actualiza la variable de sesion 
            $_SESSION["usuario"] = serialize($usuario);
            echo "correct";
        } else {
            echo "incorrect";
        }
    }

    public function renombrarArchivo($nuevoNombreArchivo) {
        if ($this->DBConnection->actualizaArchivo($this->archivo, $nuevoNombreArchivo)) {
            echo "correct";
        } else {
            echo "incorrect";
        }
    }

    public function moverArchivo($idCarpetaDestino) {
        if ($this->DBConnection->moverArchivo($this->archivo, $idCarpetaDestino)) {
            echo "correct";
        } else {
            echo "incorrect";
        }
    }

    public function descargarArchivo() {
        //Directorio de subida
        $dirsubida = "../files/";
        $carpeta_usuario = "/" . $this->archivo->getIdUsuario();

        ////Ejecucion script
        $comando = "python ../python/recuperar_archivo.py " . $this->archivo->getNombreArchivoGRID() . " " . $dirsubida . " " . $carpeta_usuario;
        $this->modif_shell_exec($comando);
        //echo "<p>" . $stdout . "</p>";
        //echo "<p>" . $stderr . "</p>";
        //Validacion recuperacion
        //
        
        //Renombrado del archivo
        rename($dirsubida . $this->archivo->getNombreArchivoGRID(), $dirsubida . $this->archivo->getNombreArchivo());
        $rutaArchivo = '../files/' . $this->archivo->getNombreArchivo();

        //Contenido de la respuesta
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($rutaArchivo) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate');
        ////header('Content-Length: '.filesize($filepath));
        ob_clean();
        flush();
        readfile($rutaArchivo);

        //Eliminacion del archivo en la carpeta del servidor
        unlink($dirsubida . $this->archivo->getNombreArchivo());
        exit();
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
