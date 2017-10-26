<?php

include("Usuario.php");
include("Carpeta.php");
include("Archivo.php");

class BaseDeDatos {

    protected $DB_NAME = "SecretSharing";
    protected $DB_USER = "root";
    protected $DB_PASS = "root";
    protected $DB_HOST = "localhost";

    /*     * ********************************** Methods for DB **************************************** */

    public function connect() {
        $this->connection = new mysqli($this->DB_HOST, $this->DB_USER, $this->DB_PASS, $this->DB_NAME);
        if (mysqli_connect_error()) {
            die('Error de Conexión (' . mysqli_connect_errno() . ') '
                    . mysqli_connect_error());
        }
    }

    public function close() {
        if (!mysqli_close($this->connection)) {
            die('Error de cierre de Conexión');
        }
    }

    /*     * **********************************  Actions for USERS  *********************************** */

    public function insertaUsuario($Usuario) {
        $email = $Usuario->getidUsuario();
        $password = $Usuario->getContrasenia();
        $alias = $Usuario->getAlias();
        $status = $Usuario->getStatus();
        $espacioUtilizado = $Usuario->getEspacioUtilizado();
        $stmt = $this->connection->prepare("INSERT INTO usuario VALUES (?,?,?,?,?)");
        $stmt->bind_param("sssii", $email, $password, $alias, $status, $espacioUtilizado);
        return $stmt->execute();
    }

    public function borraUsuario($Usuario) {
        $email = $Usuario->getIdUsuario();
        $stmt = $this->connection->prepare("DELETE FROM usuario WHERE idUsuario = ?");
        $stmt->bind_param("s", $email);
        return $stmt->execute();
    }

    public function consultaUsuario($Usuario) {
        $email = $Usuario->getidUsuario();
        $stmt = $this->connection->prepare("SELECT * FROM usuario WHERE idUsuario = ?");
        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
            $stmt->bind_result($idUsuario, $contrasenia, $alias, $status, $espacioUtilizado);
            while ($stmt->fetch()) {
                $User = new Usuario($idUsuario, $contrasenia, $alias, $status, $espacioUtilizado);
                $User->setidUsuario($idUsuario);
                $User->setContrasenia($contrasenia);
                $User->setAlias($alias);
                $User->setStatus($status);
                $User->setEspacioUtilizado($espacioUtilizado);
            }
            $stmt->close();
            return $User;
        }
        return;
    }

    public function actualizaUsuario($Usuario) {
        $email = $Usuario->getidUsuario();
        $password = $Usuario->getContrasenia();
        $alias = $Usuario->getAlias();
        $stmt = $this->connection->prepare("UPDATE usuario SET idUsuario = ?, contrasenia = ?, alias = ? WHERE idUsuario=?");
        $stmt->bind_param("ssss", $email, $password, $alias, $email);
        return $stmt->execute();
    }
    public function actualizaIdUsuario($Usuario, $newIdUsuario) {
        $email = $Usuario->getidUsuario();
        $password = $Usuario->getContrasenia();
        $alias = $Usuario->getAlias();
        $stmt = $this->connection->prepare("UPDATE usuario SET idUsuario = ?, contrasenia = ?, alias = ? WHERE idUsuario=?");
        $stmt->bind_param("ssss", $newIdUsuario, $password, $alias, $email);
        return $stmt->execute();
    }

    public function existeUsuario($Usuario) {
        $email = $Usuario->getidUsuario();
        $password = $Usuario->getContrasenia();
        $stmt = $this->connection->prepare("SELECT COUNT(idUsuario) AS RESULT FROM usuario WHERE idUsuario = ?");
        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
            $stmt->bind_result($result);
            while ($stmt->fetch()) {
                return ($result == 1);
            }
        }
    }

    /*     * **********************************  Actions for CARPETAS  *********************************** */

    public function consultaCarpetaRaiz($Usuario) {
        $idUsuario = $Usuario->getidUsuario();
        $stmt = $this->connection->prepare("SELECT * FROM carpeta WHERE idUsuario = ? and idCarpetaSuperior IS NULL");
        $stmt->bind_param("s", $idUsuario);
        if ($stmt->execute()) {
            $stmt->bind_result($idCarpeta, $idUsuario, $idCarpetaSuperior, $nombreCarpeta, $fechaCreacion);
            while ($stmt->fetch()) {
                $carpeta = new Carpeta($idCarpeta, $idCarpetaSuperior, $nombreCarpeta, $fechaCreacion);
                $stmt->close();
                return $carpeta;
            }
        }
        return;
    }

    public function consultaCarpeta($Usuario, $idCarpeta) {
        $idUsuario = $Usuario->getidUsuario();
        $stmt = $this->connection->prepare("SELECT * FROM carpeta WHERE idCarpeta = ? AND idUsuario = ? ");
        $stmt->bind_param("is", $idCarpeta, $idUsuario);
        if ($stmt->execute()) {
            $stmt->bind_result($idCarpeta, $idUsuario, $idCarpetaSuperior, $nombreCarpeta, $fechaCreacion);
            while ($stmt->fetch()) {
                $carpeta = new Carpeta($idCarpeta, $idCarpetaSuperior, $nombreCarpeta, $fechaCreacion);
                $stmt->close();
                return $carpeta;
            }
        }
        return;
    }

    public function existeCarpeta($Usuario, $carpetaActual, $nombreNuevaCarpeta) {
        $idUsuario = $Usuario->getidUsuario();
        $idCarpetaSup = $carpetaActual->getIdCarpeta();
        $stmt = $this->connection->prepare("SELECT COUNT(idCarpeta) AS result FROM carpeta "
                . "WHERE  idUsuario = ? and nombreCarpeta = ? and idCarpetaSuperior = ?");

        $stmt->bind_param("ssi", $idUsuario, $nombreNuevaCarpeta, $idCarpetaSup);
        if ($stmt->execute()) {
            $stmt->bind_result($result);
            while ($stmt->fetch()) {
                return ($result == 1);
            }
        }
    }

    public function insertaCarpetaRaiz($Usuario) {
        $idUsuario = $Usuario->getidUsuario();
        $nombreNuevaCarpeta = $Usuario->getidUsuario();
        $stmt = $this->connection->prepare("INSERT INTO carpeta (idUsuario,  nombreCarpeta, fechaCreacion) "
                . "VALUES  (?, ?, CURDATE() )");
        $stmt->bind_param("ss", $idUsuario, $nombreNuevaCarpeta);
        return $stmt->execute();
    }

    public function insertaCarpeta($Usuario, $carpetaActual, $nombreNuevaCarpeta) {
        $idUsuario = $Usuario->getidUsuario();
        $idCarpetaSup = $carpetaActual->getIdCarpeta();
        $stmt = $this->connection->prepare("INSERT INTO carpeta (idUsuario, idCarpetaSuperior,  nombreCarpeta, fechaCreacion) "
                . "VALUES (?,?,?, CURDATE())");
        $stmt->bind_param("sis", $idUsuario, $idCarpetaSup, $nombreNuevaCarpeta);
        return $stmt->execute();
    }

    public function listaCarpetas($Usuario, $carpetaActual) {
        $idUsuario = $Usuario->getidUsuario();
        $idCarpetaActual = $carpetaActual->getIdCarpeta();
        $ans = null;
        $stmt = $this->connection->prepare("SELECT * from carpeta WHERE  idUsuario = ? and  idCarpetaSuperior = ? ");
        $stmt->bind_param("si", $idUsuario, $idCarpetaActual);
        if ($stmt->execute()) {
            $stmt->bind_result($idCarpeta, $idUsuario, $idCarpetaSuperior, $nombreCarpeta, $fechaCreacion);
            while ($stmt->fetch()) {
                $carpeta = new Carpeta($idCarpeta, $idCarpetaSuperior, $nombreCarpeta, $fechaCreacion);
                $ans = $ans . '<tr id="row' . $carpeta->getIdCarpeta() . '">
                                    <td class="text-center"><a href = "#"> <p id ="' . $carpeta->getIdCarpeta() . '"  onclick = "actualizarContenidoEnPantalla(' . $carpeta->getIdCarpeta() . ')" >' . $carpeta->getNombreCarpeta() . '</p></a></td>
                                    <td class="text-center">' . $carpeta->getFechaCreacion() . '</td>
                                    <td class="text-center">
                                            <a class="btn btn-primary btn-sm btn-sel-carp" href="#"><span class="glyphicon glyphicon-remove"></span> Mover</a>
                                            <a class="btn btn-info    btn-sm btn-sel-carp" href="#" data-toggle="modal" data-target="#modalEditarCarpeta"  data-idCarpeta=' . $carpeta->getIdCarpeta() . ' ><span class="glyphicon glyphicon-edit"></span> Editar</a>								
                                            <a class="btn btn-danger  btn-sm btn-sel-carp" href="#" data-toggle="modal" data-target="#modalEliminarCarpeta"  data-idCarpeta=' . $carpeta->getIdCarpeta() . '  ><span class="glyphicon glyphicon-remove"></span> Eliminar</a>
                                    </td>
                                </tr>';
            }
            $stmt->close();
        }
        return $ans;
    }

    public function eliminarCarpeta($usuario, $carpeta) {
        $idUsuario = $usuario->getidUsuario();
        $idCarpetaEliminar = $carpeta->getIdCarpeta();
        $stmt = $this->connection->prepare("DELETE FROM carpeta WHERE idUsuario = ? and idCarpeta = ?");
        $stmt->bind_param("si", $idUsuario, $idCarpetaEliminar);
        return $stmt->execute();
    }

    public function editarCarpeta($usuario, $idCarpetaEditar, $nombreCarpeta) {
        $idUsuario = $usuario->getidUsuario();
        $stmt = $this->connection->prepare("UPDATE carpeta SET nombreCarpeta = ? "
                . "WHERE idCarpeta = ? AND idUsuario = ?");
        $stmt->bind_param("sis", $nombreCarpeta, $idCarpetaEditar, $idUsuario);
        return $stmt->execute();
    }

    /*     * **********************************  Actions for FILES  *********************************** */

    public function listaArchivos($Usuario, $carpetaActual) {
        $ans = null;

        $idUsuario = $Usuario->getidUsuario();
        $idCarpetaActual = $carpetaActual->getIdCarpeta();

        $stmt = $this->connection->prepare("SELECT * FROM Archivo WHERE idUsuario = ? AND idCarpeta = ?");
        $stmt->bind_param("si", $idUsuario, $idCarpetaActual);

        if ($stmt->execute()) {
            $stmt->bind_result($nombreArchivo, $idCarpeta, $idUsuario, $nombreArchivoGRID, $tamanio, $fechaSubida);
            while ($stmt->fetch()) {
                $archivo = new Archivo($nombreArchivo, $idCarpeta, $idUsuario, $nombreArchivoGRID, $tamanio, $fechaSubida);
                $ans = $ans . '<tr id="row' . $archivo->getNombreArchivo() . '">
                                    <td class="text-center"><p id="arch' . $archivo->getNombreArchivo() . '">' . $archivo->getNombreArchivo() . '</p></td>
                                    <td class="text-center">' . $archivo->getTamanio() . '</td>
                                    <td class="text-center">' . $archivo->getFechaSubida() . '</td>
                                    <td class="text-center">
                                            <a class="btn btn-primary btn-sm" href="#" id="mov' . $archivo->getNombreArchivo() . '"><span class="glyphicon glyphicon-remove"></span> Mover</a>
                                            <a class="btn btn-success btn-sm  descargaArch" href="#" data-idCarpeta="' . $idCarpeta . '" data-nomArchivo="' . $archivo->getNombreArchivo() . '" id="down' . $archivo->getNombreArchivo() . '"><span class="glyphicon glyphicon-edit"></span> Descargar</a>
                                            <a class="btn btn-info    btn-sm" href="#" data-toggle="modal" data-target="#modalEditarArchivo"  data-idCarpeta="' . $idCarpeta . '" data-nomArchivo="' . $archivo->getNombreArchivo() . '" id="edit' . $archivo->getNombreArchivo() . '"><span class="glyphicon glyphicon-edit"></span> Editar</a>
                                            <a class="btn btn-danger  btn-sm" href="#" data-toggle="modal" data-target="#modalEliminaArchivo"  data-idCarpeta="' . $idCarpeta . '" data-nomArchivo="' . $archivo->getNombreArchivo() . '" id="del' . $archivo->getNombreArchivo() . '"><span class="glyphicon glyphicon-remove"></span> Eliminar</a>
                                    </td>
                                </tr>';
            }
            $stmt->close();
        }
        return $ans;
    }

    public function eliminarArchivo($usuario, $idCarpeta, $nombreArchivo) {
        $idUsuario = $usuario->getidUsuario();
        $stmt = $this->connection->prepare("DELETE FROM archivo WHERE nombreArchivo = ? AND idCarpeta = ? AND idUsuario = ? ");
        $stmt->bind_param("sis", $nombreArchivo, $idCarpeta, $idUsuario);
        return $stmt->execute();
    }

    public function editarArchivo($usuario, $idCarpeta, $nombreArchivo, $nuevoNomArch) {
        $idUsuario = $usuario->getidUsuario();
        $stmt = $this->connection->prepare("UPDATE archivo SET nombreArchivo = ? "
                . "WHERE nombreArchivo = ? and idCarpeta = ? and idUsuario = ?");
        $stmt->bind_param("ssis", $nuevoNomArch, $nombreArchivo, $idCarpeta, $idUsuario);
        return $stmt->execute();
    }

    public function insertaArchivo($archivo) {
        $nombreArchivo = $archivo->getNombreArchivo();
        $idCarpeta = $archivo->getIdCarpeta();
        $idUsuario = $archivo->getIdUsuario();
        $nombreArchivoGRID = $archivo->getNombreArchivoGRID();
        $tamanio = $archivo->getTamanio();
        $fechaSubida = $archivo->getFechaSubida();
        $stmt = $this->connection->prepare("INSERT INTO archivo (nombreArchivo,idCarpeta,idUsuario,nombreArchivoGRID,tamanio,fechaSubida)"
                . " VALUES (?,?,?,?,?,?)");

        $stmt->bind_param("sissis", $nombreArchivo, $idCarpeta, $idUsuario, $nombreArchivoGRID, $tamanio, $fechaSubida);
        return $stmt->execute();
    }

    public function obtieneArchivo($nombreArchivo, $idCarpeta) {
        $stmt = $this->connection->prepare("SELECT * FROM archivo WHERE nombreArchivo=? AND idCarpeta=?");
        $stmt->bind_param("si", $nombreArchivo, $idCarpeta); //s->String, i->Integer        
        $stmt->execute();
        $stmt->bind_result($nombreArchivo, $idCarpeta, $idUsuario, $nombreArchivoGRID, $tamanio, $fechaSubida);
        $archivo = null;
        while ($stmt->fetch()) {
            $archivo = new Archivo($nombreArchivo, $idCarpeta, $idUsuario, $nombreArchivoGRID, $tamanio, $fechaSubida);
        }
        return $archivo;
    }

    public function editaEspacioUtilizado($usuario) {
        $stmt = $this->connection->prepare("UPDATE usuario SET espacioUtilizado=? WHERE idUsuario=?");
        $espacioUtilizado = $usuario->getEspacioUtilizado();
        $idUsuario = $usuario->getidUsuario();
        $stmt->bind_param("is", $espacioUtilizado, $idUsuario); //s->String, i->Integer
        $stmt->execute();
    }

    /*     * ***************************** */

    //Se creo este método
    public function getHTMLCarpeta($Usuario, $carpetaActual, $nombreCarpeta) {
        $idUsuario = $Usuario->getidUsuario();
        $idCarpetaSup = $carpetaActual->getIdCarpeta();
        $htmlCarpeta = null;
        $stmt = $this->connection->prepare("select * from carpeta where  idUsuario = ? and  idCarpetaSuperior = ? and nombreCarpeta = ?");
        $stmt->bind_param("sis", $idUsuario, $idCarpetaSup, $nombreCarpeta);
        if ($stmt->execute()) {
            $stmt->bind_result($idCarpeta, $idUsuario, $idCarpetaSuperior, $nombreCarpeta, $fechaCreacion);
            while ($stmt->fetch()) {
                $carpeta = new Carpeta($idCarpeta, $idCarpetaSuperior, $nombreCarpeta, $fechaCreacion);
                $htmlCarpeta = '<tr id=row' . $carpeta->getIdCarpeta() . '>
                                    <td class="text-center"><a href = "#"> <p id =' . $carpeta->getIdCarpeta() . '  onclick = "actualizarContenidoEnPantalla(' . $carpeta->getIdCarpeta() . ')" >' . $carpeta->getNombreCarpeta() . '</p></a></td>
                                    <td class="text-center">' . $carpeta->getFechaCreacion() . '</td>
                                    <td class="text-center">
                                            <a class="btn btn-primary btn-sm btn-sel-carp" href="#"><span class="glyphicon glyphicon-remove"></span> Mover</a>
                                            <a class="btn btn-info    btn-sm btn-sel-carp" href="#" data-toggle="modal" data-target="#modalEditarCarpeta"  data-idCarpeta=' . $carpeta->getIdCarpeta() . ' ><span class="glyphicon glyphicon-edit"></span> Editar</a>								
                                            <a class="btn btn-danger  btn-sm btn-sel-carp" href="#" data-toggle="modal" data-target="#modalEliminarCarpeta"  data-idCarpeta=' . $carpeta->getIdCarpeta() . '  ><span class="glyphicon glyphicon-remove"></span> Eliminar</a>
                                    </td>
                                </tr>';
            }
            $stmt->close();
        }
        return $htmlCarpeta;
    }

}

?>
