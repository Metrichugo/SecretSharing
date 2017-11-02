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

    public function existeUsuarioContrasenia($Usuario) {
        $email = $Usuario->getidUsuario();
        $password = $Usuario->getContrasenia();
        $stmt = $this->connection->prepare("SELECT COUNT(idUsuario) AS RESULT FROM usuario WHERE idUsuario = ? AND contrasenia=?");
        $stmt->bind_param("ss", $email, $password);
        if ($stmt->execute()) {
            $stmt->bind_result($result);
            while ($stmt->fetch()) {
                return ($result == 1);
            }
        }
    }

    public function editaEspacioUtilizado($usuario) {
        $stmt = $this->connection->prepare("UPDATE usuario SET espacioUtilizado=? WHERE idUsuario=?");
        $espacioUtilizado = $usuario->getEspacioUtilizado();
        $idUsuario = $usuario->getidUsuario();
        $stmt->bind_param("is", $espacioUtilizado, $idUsuario); //s->String, i->Integer
        $stmt->execute();
    }

    /*     * **********************************  Actions for CARPETAS  *********************************** */

    public function consultaCarpetaRaiz($Usuario) {
        $idUsuario = $Usuario->getidUsuario();
        $stmt = $this->connection->prepare("SELECT * FROM carpeta WHERE idUsuario = ? and idCarpetaSuperior IS NULL");
        $stmt->bind_param("s", $idUsuario);
        if ($stmt->execute()) {
            $stmt->bind_result($idCarpeta, $idUsuario, $idCarpetaSuperior, $nombreCarpeta, $fechaCreacion);
            while ($stmt->fetch()) {
                $carpeta = new Carpeta($idCarpeta, $idUsuario, $idCarpetaSuperior, $nombreCarpeta, $fechaCreacion);
                $stmt->close();
                return $carpeta;
            }
        }
        return;
    }

    public function consultaCarpeta($idUsuario, $idCarpeta) {
        $stmt = $this->connection->prepare("SELECT * FROM carpeta WHERE idCarpeta = ? AND idUsuario = ? ");
        $stmt->bind_param("is", $idCarpeta, $idUsuario);
        if ($stmt->execute()) {
            $stmt->bind_result($idCarpeta, $idUsuario, $idCarpetaSuperior, $nombreCarpeta, $fechaCreacion);
            while ($stmt->fetch()) {
                $carpeta = new Carpeta($idCarpeta, $idUsuario, $idCarpetaSuperior, $nombreCarpeta, $fechaCreacion);
                $stmt->close();
                return $carpeta;
            }
        }
        return;
    }

    //Se creo este método
    public function consultaCarpetaObjeto($carpeta) {
        $idUsuario = $carpeta->getIdUsuario();
        $idCarpetaSup = $carpeta->getIdCarpetaSuperior();
        $nombreCarpeta = $carpeta->getNombreCarpeta();
        $stmt = $this->connection->prepare("select * from carpeta where  idUsuario = ? and  idCarpetaSuperior = ? and nombreCarpeta = ?");
        $stmt->bind_param("sis", $idUsuario, $idCarpetaSup, $nombreCarpeta);
        if ($stmt->execute()) {
            $stmt->bind_result($idCarpeta, $idUsuario, $idCarpetaSuperior, $nombreCarpeta, $fechaCreacion);
            while ($stmt->fetch()) {
                $carpeta = new Carpeta($idCarpeta, $idUsuario, $idCarpetaSuperior, $nombreCarpeta, $fechaCreacion);
                return $carpeta;
            }
            $stmt->close();
        }
        return;
    }

    public function existeCarpeta($carpeta) {
        $idUsuario = $carpeta->getIdUsuario();
        $idCarpetaSup = $carpeta->getIdCarpetaSuperior();
        $nombreNuevaCarpeta = $carpeta->getNombreCarpeta();

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

    public function insertaCarpeta($carpeta) {
        $idUsuario = $carpeta->getIdUsuario();
        $idCarpetaSup = $carpeta->getIdCarpetaSuperior();
        $nombreCarpeta = $carpeta->getNombreCarpeta();
        $stmt = $this->connection->prepare("INSERT INTO carpeta (idUsuario, idCarpetaSuperior,  nombreCarpeta, fechaCreacion) "
                . "VALUES (?,?,?, CURDATE())");
        $stmt->bind_param("sis", $idUsuario, $idCarpetaSup, $nombreCarpeta);
        return $stmt->execute();
    }

    public function listaCarpetas($carpeta) {
        $idUsuario = $carpeta->getIdUsuario();
        $idCarpetaActual = $carpeta->getIdCarpeta();
        $stmt = $this->connection->prepare("SELECT * from carpeta WHERE  idUsuario = ? and  idCarpetaSuperior = ? ORDER BY nombreCarpeta DESC");
        $stmt->bind_param("si", $idUsuario, $idCarpetaActual);
        if ($stmt->execute()) {
            $stack = new SplStack();
            $stmt->bind_result($idCarpeta, $idUsuario, $idCarpetaSuperior, $nombreCarpeta, $fechaCreacion);
            while ($stmt->fetch()) {
                $carpeta = new Carpeta($idCarpeta, $idUsuario, $idCarpetaSuperior, $nombreCarpeta, $fechaCreacion);
                $stack->push($carpeta);
            }
            $stmt->close();
        }
        return $stack;
    }

    public function eliminarCarpeta($carpeta) {
        $idUsuario = $carpeta->getIdUsuario();
        $idCarpetaEliminar = $carpeta->getIdCarpeta();
        $stmt = $this->connection->prepare("DELETE FROM carpeta WHERE idUsuario = ? and idCarpeta = ?");
        $stmt->bind_param("si", $idUsuario, $idCarpetaEliminar);
        return $stmt->execute();
    }

    public function editarCarpeta($carpeta, $nuevoNombreCarpeta) {
        $idUsuario = $carpeta->getIdUsuario();
        $idCarpetaEditar = $carpeta->getIdCarpeta();
        $stmt = $this->connection->prepare("UPDATE carpeta SET nombreCarpeta = ? "
                . "WHERE idCarpeta = ? AND idUsuario = ?");
        $stmt->bind_param("sis", $nuevoNombreCarpeta, $idCarpetaEditar, $idUsuario);
        return $stmt->execute();
    }

    /* Se agregaron los 2 metodos siguientes */

    public function moverCarpeta($usuario, $idCarpeta, $idCarpetaDest) {
        $idUsuario = $usuario->getidUsuario();
        if (!$this->connection->query("UPDATE carpeta SET idCarpetaSuperior = '$idCarpetaDest' WHERE idCarpeta = '$idCarpeta' and idUsuario = '$idUsuario'")) {
            echo "Mistakes were made " . $this->connection->errno . " " . $this->connection->error;
            return false;
        }
        return true;
    }

    public function obtenerSubCarpetas($usuario, $idCarpetaSuperior, $idCarpeta) {
        $idUsuario = $usuario->getidUsuario();
        $result = null;
        if ($sentencia = $this->connection->prepare("SELECT idCarpeta,nombreCarpeta FROM carpeta INNER JOIN (SELECT idCarpetaSuperior FROM carpeta WHERE idCarpeta = '$idCarpetaSuperior' )sup on carpeta.idCarpeta = sup.idCarpetaSuperior")) {
            $sentencia->execute();
            $sentencia->bind_result($idCarpetaPadre, $nombreCarpetaPadre);
            while ($sentencia->fetch()) {
                if ($nombreCarpetaPadre != $idUsuario) {
                    $result = $result . '<option value="' . $idCarpetaPadre . '">' . $nombreCarpetaPadre . '</option>';
                } else {
                    $result = $result . '<option value="' . $idCarpetaPadre . '">Carpeta Raiz</option>';
                }
            }
            $sentencia->close();
        }
        if ($sentencia = $this->connection->prepare("SELECT idCarpeta,nombreCarpeta FROM carpeta WHERE idCarpetaSuperior = '$idCarpetaSuperior' and idCarpeta <> '$idCarpeta' and idUsuario = '$idUsuario'")) {
            $sentencia->execute();
            $sentencia->bind_result($idCarpeta, $nombreCarpeta);
            while ($sentencia->fetch()) {
                $result = $result . '<option value="' . $idCarpeta . '">' . $nombreCarpeta . '</option>';
            }
            $sentencia->close();
        }
        return $result;
    }

    /*     * **********************************  Actions for FILES  *********************************** */

    public function listaArchivos($carpeta) {
        $idUsuario = $carpeta->getIdUsuario();
        $idCarpetaActual = $carpeta->getIdCarpeta();

        $stmt = $this->connection->prepare("SELECT * FROM Archivo WHERE idUsuario = ? AND idCarpeta = ? ORDER BY nombreArchivo DESC");
        $stmt->bind_param("si", $idUsuario, $idCarpetaActual);

        //Pila de archivos
        $stack = new SplStack();

        if ($stmt->execute()) {
            $stmt->bind_result($nombreArchivo, $idCarpeta, $idUsuario, $nombreArchivoGRID, $tamanio, $fechaSubida);
            while ($stmt->fetch()) {
                $archivo = new Archivo($nombreArchivo, $idCarpeta, $idUsuario, $nombreArchivoGRID, $tamanio, $fechaSubida);
                $stack->push($archivo);
            }
            $stmt->close();
        }
        return $stack;
    }

    public function eliminarArchivo($archivo) {
        $nombreArchivo = $archivo->getNombreArchivo();
        $idCarpeta = $archivo->getIdCarpeta();
        $idUsuario = $archivo->getIdUsuario();
        $stmt = $this->connection->prepare("DELETE FROM archivo WHERE nombreArchivo = ? AND idCarpeta = ? AND idUsuario = ? ");
        $stmt->bind_param("sis", $nombreArchivo, $idCarpeta, $idUsuario);
        return $stmt->execute();
    }

    public function actualizaArchivo($archivo, $nuevoNomArch) {
        $idUsuario = $archivo->getIdUsuario();
        $nombreArchivo = $archivo->getNombreArchivo();
        $idCarpeta = $archivo->getIdCarpeta();
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

    public function consultaArchivo($nombreArchivo, $carpeta) {
        $idCarpeta = $carpeta->getIdCarpeta();
        $idUsuario = $carpeta->getIdUsuario();
        $stmt = $this->connection->prepare("SELECT * FROM archivo WHERE nombreArchivo=? AND idCarpeta=? AND idUsuario=?");
        $stmt->bind_param("sis", $nombreArchivo, $idCarpeta, $idUsuario); //s->String, i->Integer        
        $stmt->execute();
        $stmt->bind_result($nombreArchivo, $idCarpeta, $idUsuario, $nombreArchivoGRID, $tamanio, $fechaSubida);
        $archivo = null;
        while ($stmt->fetch()) {
            $archivo = new Archivo($nombreArchivo, $idCarpeta, $idUsuario, $nombreArchivoGRID, $tamanio, $fechaSubida);
        }
        return $archivo;
    }

    public function moverArchivo($archivo, $idCarpetaDest) {
        $idUsuario = $archivo->getIdUsuario();
        $nombreArchivo = $archivo->getNombreArchivo();
        if (!$this->connection->query("UPDATE archivo set idCarpeta = '$idCarpetaDest' where nombreArchivo = '$nombreArchivo' and idUsuario ='$idUsuario'")) {
            echo "Mistakes were made " . $this->connection->errno . " " . $this->connection->error;
            return false;
        }
        return true;
    }

    /*     * ***************************** */
}

?>
