<?php

class Carpeta_Accion {

    protected $carpeta;
    protected $DBConnection;

    function __construct($carpeta, $DBConnection) {
        $this->carpeta = $carpeta;
        $this->DBConnection = $DBConnection;
    }

    public function crearCarpeta() {
        //Se verifica existencia en la BD
        if ($this->DBConnection->existeCarpeta($this->carpeta)) { //Carpeta repetida
            echo json_encode(array(
                "Status" => "incorrect"
            ));
            exit();
        }
        //Creacion de objeto de tipo carpeta
        //Inserción en la base de datos
        if ($this->DBConnection->insertaCarpeta($this->carpeta)) {
            //Se envia a la vista el código HTML de la carpeta
            $carpetaVista = $this->DBConnection->consultaCarpetaObjeto($this->carpeta);
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
                                    <td class="text-center"><a href = "#"> <p id =' . $carpeta->getIdCarpeta() . '  onclick = "actualizarContenidoEnPantalla(' . $carpeta->getIdCarpeta() . ')" >' . $carpeta->getNombreCarpeta() . '</p></a></td>
                                    <td class="text-center">' . $carpeta->getFechaCreacion() . '</td>
                                    <td class="text-center">
                                        <a class="btn btn-primary btn-sm btn-sel-carp" href="#" data-toggle="modal" data-target="#modalMoverCarpeta" data-idCarpeta=' . $carpeta->getIdCarpeta() . '><span class="glyphicon glyphicon-remove"></span> Mover</a>								                                            
                                        <a class="btn btn-info    btn-sm btn-sel-carp" href="#" data-toggle="modal" data-target="#modalEditarCarpeta"  data-idCarpeta=' . $carpeta->getIdCarpeta() . ' ><span class="glyphicon glyphicon-edit"></span> Editar</a>								
                                        <a class="btn btn-danger  btn-sm btn-sel-carp" href="#" data-toggle="modal" data-target="#modalEliminarCarpeta"  data-idCarpeta=' . $carpeta->getIdCarpeta() . '  ><span class="glyphicon glyphicon-remove"></span> Eliminar</a>
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
        exit();
    }

    private function eliminaCarpetaYArchivosGRID($carpeta, $DBConnection) {
        //Eliminación de subcarpetas
        $pilaSubcarpetas = $DBConnection->listaCarpetas($carpeta);
        while (!$pilaSubcarpetas->isEmpty()) {
            eliminaCarpetaYArchivosGRID($pilaSubcarpetas->pop(), $DBConnection);
        }

        //Eliminacion de archivos de la carpeta
        $pilaArchivos = $DBConnection->listaArchivos($carpeta);
        while (!$pilaArchivos->isEmpty()) {
            $archivo = $pilaArchivos->pop();
            $archivo->eliminaGRID();
            //Actualiza la variable de sesion
            $usuario = unserialize($_SESSION["usuario"]); //Objeto de sesion tipo usuario
            $usuario->setEspacioUtilizado($usuario->getEspacioUtilizado() - $archivo->getTamanio());
            $DBConnection->editaEspacioUtilizado($usuario);
            //Actualiza la variable de sesion 
            $_SESSION["usuario"] = serialize($usuario);
        }
    }

    public function moverCarpeta() {//Falta llenar este método
        
    }

    public function renombrarCarpeta($nuevoNombreCarpeta, $carpetaActual) {
        //Comprueba si existe una carpeta con el mismo nombre
        $carpetaTemporal=new Carpeta(null, $carpetaActual->getIdUsuario(), $carpetaActual->getIdCarpeta(), $nuevoNombreCarpeta, null);
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

}