<?php

include_once("Carpeta.php");

class Carpeta_Accion {

    protected $carpeta;
    protected $DBConnection;

    function __construct($carpeta, $DBConnection) {
        $this->carpeta = $carpeta;
        $this->DBConnection = $DBConnection;
    }

    public function crearCarpeta() {
        //Se verifica el nombre de la carpeta
        if ($this->validaNombreCarpeta($this->carpeta->getNombreCarpeta())) {
            echo "invalidname";
            return;
        }

        //Se verifica existencia en la BD
        if ($this->DBConnection->existeCarpeta($this->carpeta)) { //Carpeta repetida
            echo json_encode(array(
                "Status" => "incorrect"
            ));
            return;
        }
        //Creacion de objeto de tipo carpeta
        //Inserción en la base de datos
        if ($this->DBConnection->insertarCarpeta($this->carpeta)) {
            //Se envia a la vista el código HTML de la carpeta
            $carpetaVista = $this->DBConnection->consultarCarpetaObjeto($this->carpeta);
            $htmlCarpeta = $this->getHTMLCarpeta($carpetaVista);
            echo json_encode(array(
                "Status" => "correct",
                "Html" => $htmlCarpeta
            ));
        } else {
            echo json_encode(array(
                "Status" => "incorrect"
            ));
        }
    }

    private function getHTMLCarpeta($carpeta) {
        return $htmlCarpeta = '<tr id=row' . $carpeta->getIdCarpeta() . '>
                                    <td> <i class="fa fa-folder-open fa-lg" aria-hidden="true"></i> <a href = "#" id =' . $carpeta->getIdCarpeta() . '  onclick = "actualizarContenidoEnPantalla(' . $carpeta->getIdCarpeta() . ')" >' . $carpeta->getNombreCarpeta() . '</a></td>
                                    <td class="text-center">' . $carpeta->getFechaCreacion() . '</td>
                                    <td class="text-center">
                                    <div class="btn-group" role="group" aria-label="Botones carpeta">
                                        <button title="Mover" class="btn btn-primary btn-sm btn-sel-carp" data-toggle="modal" data-target="#modalMoverCarpeta"     data-idCarpeta=' . $carpeta->getIdCarpeta() . '> <i class="fa fa-exchange" aria-hidden="true"></i></button>					                                         
                                        <button title="Editar" class="btn btn-info    btn-sm btn-sel-carp" data-toggle="modal" data-target="#modalEditarCarpeta"    data-idCarpeta=' . $carpeta->getIdCarpeta() . '> <i class="fa fa-pencil-square-o" aria-hidden="true"></i> </button>								
                                        <button title="Eliminar" class="btn btn-danger  btn-sm btn-sel-carp" data-toggle="modal" data-target="#modalEliminarCarpeta"  data-idCarpeta=' . $carpeta->getIdCarpeta() . '> <i class="fa fa-trash" aria-hidden="true"></i> </button>                            
                                    </div>        
                                    </td>
                                </tr>';
    }

    public function eliminarCarpeta() {
        $this->eliminaCarpetaYArchivosGRID($this->carpeta, $this->DBConnection);
        if ($this->DBConnection->eliminarCarpeta($this->carpeta)) {
            echo "correct";
        } else {
            echo "incorrect";
        }
        return;
    }

    private function eliminaCarpetaYArchivosGRID($carpeta, $DBConnection) {
        //Eliminación de subcarpetas
        $pilaSubcarpetas = $DBConnection->listarCarpetas($carpeta);
        while (!$pilaSubcarpetas->isEmpty()) {
            $this->eliminaCarpetaYArchivosGRID($pilaSubcarpetas->pop(), $DBConnection);
        }

        //Eliminacion de archivos de la carpeta
        $pilaArchivos = $DBConnection->listarArchivos($carpeta);
        while (!$pilaArchivos->isEmpty()) {
            $archivo = $pilaArchivos->pop();
            $archivo->eliminaGRID();
            //Actualiza la variable de sesion
            $usuario = unserialize($_SESSION["usuario"]); //Objeto de sesion tipo usuario
            $usuario->setEspacioUtilizado($usuario->getEspacioUtilizado() - $archivo->getTamanio());
            $DBConnection->editarEspacioUtilizado($usuario);
            //Actualiza la variable de sesion 
            $_SESSION["usuario"] = serialize($usuario);
        }
    }

    public function moverCarpeta($carpetaDestino) {
        // Le movi a este método(31-10-2017)
        $this->carpeta->setIdCarpetaSuperior($carpetaDestino->getIdCarpeta());
        if (!$this->DBConnection->existeCarpeta($this->carpeta)) {
            if ($this->DBConnection->moverCarpeta($this->carpeta, $carpetaDestino)) {
                echo "Se movio la carpeta";
            } else {
                echo "Error al mover la carpeta";
            }
        } else {
            echo "Error al mover la carpeta";
        }
    }

    public function renombrarCarpeta($nuevoNombreCarpeta, $carpetaActual) {
        //Se verifica el nombre de la carpeta
        if (!$this->validaNombreCarpeta($nuevoNombreCarpeta)) {
            echo "invalidrequest";
            return;
        }
        //Comprueba si existe una carpeta con el mismo nombre
        $carpetaTemporal = new Carpeta(null, $carpetaActual->getIdUsuario(), $carpetaActual->getIdCarpeta(), $nuevoNombreCarpeta, null);
        if ($this->DBConnection->existeCarpeta($carpetaTemporal)) {
            echo "incorrect";
            return;
        }
        //Edicion en la base de datos
        if ($this->DBConnection->editarCarpeta($this->carpeta, $nuevoNombreCarpeta)) {
            echo "correct";
        } else {
            echo "incorrect";
        }
    }

    private function validaNombreCarpeta($nombreCarpeta) {
        if (strlen($nombreCarpeta) > 255) {//Mayor a 255
            return false;
        } else if ($nombreCarpeta == '.' || $nombreCarpeta == '..') {//Es igual a . o ..
            return false;
        } else if (substr_count($nombreCarpeta, '/')) {//Contiene el caracter barra
            return false;
        } else {
            return true;
        }
    }

}
