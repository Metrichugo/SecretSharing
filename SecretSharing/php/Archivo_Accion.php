<?php

class Archivo_Accion {

    protected $archivo;
    protected $DBConnection;

    function __construct($archivo, $DBConnection) {
        $this->archivo = $archivo;
        $this->DBConnection = $DBConnection;
    }

    public function subirArchivo() {

        $usuario = unserialize($_SESSION["usuario"]); //Objeto de sesion tipo usuario
        if (!$this->validarArchivo($usuario)) {//Archivo no válido
            return; //
        }

        $dirsubida = "../files/"; //Directorio de Apache donde se almacenan los archivos 
        if ($this->seMovioTemporal($this->archivo->getNombreArchivoGRID(), $dirsubida)) { //Si se movio el archivo del directorio temporal al directorio files
            $carpeta_usuario = "/" . $this->archivo->getIdUsuario();
            $comando = "python ../python/comparte_archivo.py " . "\"" . $this->archivo->getNombreArchivoGRID() . "\" " . $dirsubida . " " . $carpeta_usuario;
            //echo $comando;
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

                $usuario->setEspacioUtilizado($usuario->getEspacioUtilizado() + $this->archivo->getTamanio());
                $this->DBConnection->editaEspacioUtilizado($usuario);
                //Actualiza la variable de sesion 
                $_SESSION["usuario"] = serialize($usuario);

                //Borrado del archivo
                unlink($dirsubida . $this->archivo->getNombreArchivoGRID());
                //unlink($log);
                //unlink($dirsubida . $this->archivo->getNombreArchivoGRID() . ".err");
                //Enviar JSON a la vista 
                $htmlArchivo = $this->getHTMLArchivo($this->archivo);
                echo json_encode(array(
                    "Status" => "UploadSuccesfull",
                    "Html" => $htmlArchivo
                ));
            } else {
                echo json_encode(array("Status" => "UploadFailed"));
            }
        } else {
            echo json_encode(array("Status" => "ErrorCantMove"));
        }
    }

    private function validarArchivo($usuario) {
        //Archivo duplicado
        if ($this->DBConnection->existeArchivo($this->archivo)) {
            echo json_encode(array("Status" => "duplicated"));
            return false;
        } else if ((($usuario->getEspacioUtilizado() + $this->archivo->getTamanio()) / 1E6) > 5000.0) {//Insuficiente espacio
            echo json_encode(array("Status" => "notenoughspace"));
            return false;
        } else if (($this->archivo->getTamanio()) / 1E6 > 1.0) {//Sobrepaso tamaño de archivo
            echo json_encode(array("Status" => "overload"));
            return false;
        }
        return true;
    }

    private function seMovioTemporal($nombreArchivoGRID, $dirsubida) {
        $uploadedFile = $dirsubida . $nombreArchivoGRID;
        return (move_uploaded_file($_FILES['file']['tmp_name'], $uploadedFile));
    }

    // Se agrego este método
    private function getHTMLArchivo($archivo) {
        return $htmlArchivo = '<tr id="row' . $archivo->getNombreArchivo() . '">
                                    <td class="text-center"><p id="arch' . $archivo->getNombreArchivo() . '">' . $archivo->getNombreArchivo() . '</p></td>
                                    <td class="text-center">' . $archivo->getTamanio() . '</td>
                                    <td class="text-center">' . $archivo->getFechaSubida() . '</td>
                                    <td class="text-center">
                                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalMoverArchivo"  data-idCarpeta="' . $archivo->getIdCarpeta() . '" data-nomArchivo="' . $archivo->getNombreArchivo() . '" id="mov' . $archivo->getNombreArchivo() . '"><i class="fa fa-exchange" aria-hidden="true"></i></button> <!--Mover-->									
                                        <button class="btn btn-success btn-sm  descargaArch" data-idCarpeta="' . $archivo->getIdCarpeta() . '" data-nomArchivo="' . $archivo->getNombreArchivo() . '" id="down' . $archivo->getNombreArchivo() . '"><i class="fa fa-download" aria-hidden="true"></i></button> <!--Descargar-->
                                        <button class="btn btn-info    btn-sm" data-toggle="modal" data-target="#modalEditarArchivo"  data-idCarpeta="' . $archivo->getIdCarpeta() . '" data-nomArchivo="' . $archivo->getNombreArchivo() . '" id="edit' . $archivo->getNombreArchivo() . '"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button> <!--Editar-->
                                        <button class="btn btn-danger  btn-sm" data-toggle="modal" data-target="#modalEliminaArchivo"  data-idCarpeta="' . $archivo->getIdCarpeta() . '" data-nomArchivo="' . $archivo->getNombreArchivo() . '" id="del' . $archivo->getNombreArchivo() . '"><i class="fa fa-trash" aria-hidden="true"></i></button>  <!--Borrar-->
                                    </td>
                                </tr>';
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
        $this->archivo->setIdCarpeta($idCarpetaDestino);
        if ($this->DBConnection->existeArchivo($this->archivo)) {
            echo "duplicated";
            return;
        } else if ($this->DBConnection->moverArchivo($this->archivo)) {
            echo "correct";
        } else {
            echo "incorrect";
        }
    }

    public function prepararArchivo() {
        //Directorio de subida
        $dirsubida = "../files/";
        $carpeta_usuario = "/" . $this->archivo->getIdUsuario();

        ////Ejecucion script
        $comando = "python ../python/recuperar_archivo.py " . $this->archivo->getNombreArchivoGRID() . " " . $dirsubida . " " . $carpeta_usuario;
        $this->modif_shell_exec($comando);
        //echo "<p>" . $stdout . "</p>";
        //echo "<p>" . $stderr . "</p>";
        ////Validación recuperación
        $string_ok = "El archivo se recupero correctamente";
        $log = $dirsubida . $this->archivo->getNombreArchivoGRID() . ".out";
        //Busca la cadena ok para saber si la ejecucion fue correcta
        if (strpos(file_get_contents($log), $string_ok) !== false) {
            echo "correct";
            //Renombrado del archivo
            rename($dirsubida . $this->archivo->getNombreArchivoGRID(), $dirsubida . $this->archivo->getNombreArchivo());
        } else {
            echo "downloadFailed";
        }
    }

    public function descargarArchivo() {
        //Directorio de subida       
        $rutaArchivo = '../files/' . $this->archivo->getNombreArchivo();

        //Contenido de la respuesta
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($rutaArchivo) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate');
        ob_clean();
        flush();
        readfile($rutaArchivo);

        //Eliminacion del archivo en la carpeta del servidor
        unlink($rutaArchivo);
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
