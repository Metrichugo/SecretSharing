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
        if (!$this->connection->query("INSERT INTO USUARIO VALUES('$email','$password','$alias','$status','$espacioUtilizado')")) {
            echo "Mistakes Were Made " . $this->connection->errno . " " . $this->connection->error;
            return false;
        }
        return true;
    }

    public function borraUsuario($Usuario) {
        $email = $Usuario->getIdUsuario();
        if (!$this->connection->query("DELETE FROM USUARIO WHERE idUsuario = '$email'")) {
            echo "Mistakes Were Made " . $this->connection->errno . " " . $this->connection->error;
            return false;
        }
        return true;
    }

    public function consultaUsuario($Usuario) {
        $email = $Usuario->getidUsuario();
        if ($sentencia = $this->connection->prepare("SELECT * FROM USUARIO WHERE idUsuario = '$email'")) {
            $sentencia->execute();
            $sentencia->bind_result($idUsuario, $contrasenia, $alias, $status, $espacioUtilizado);
            while ($sentencia->fetch()) {
                $User = new Usuario($idUsuario, $contrasenia, $alias, $status, $espacioUtilizado);
                $User->setidUsuario($idUsuario);
                $User->setContrasenia($contrasenia);
                $User->setAlias($alias);
                $User->setStatus($status);
                $User->setEspacioUtilizado($espacioUtilizado);
            }
            $sentencia->close();
            return $User;
        }
        return;
    }

    public function actualizaUsuario($Usuario) {
        $email = $Usuario->getidUsuario();
        $password = $Usuario->getContrasenia();
        $alias = $Usuario->getAlias();
        if ($sentencia = $this->connection->prepare("UPDATE USUARIO SET idUsuario = '$email', contrasenia = '$password', alias = '$alias'")) {
            $sentencia->execute();
            return true;
        }
        return;
    }

    public function existeUsuario($Usuario) {
        $email = $Usuario->getidUsuario();
        $password = $Usuario->getContrasenia();
        if ($sentencia = $this->connection->prepare("SELECT COUNT(idUsuario) AS RESULT FROM USUARIO WHERE idUsuario = '$email' AND contrasenia = '$password'")) {
            $sentencia->execute();
            $sentencia->bind_result($result);
            while ($sentencia->fetch()) {
                if ($result == 1) {
                    $isUnique = true;
                } else {
                    $isUnique = false;
                }
            }
        }
        return $isUnique;
    }

    /*     * **********************************  Actions for CARPETAS  *********************************** */

    public function consultaCarpetaRaiz($Usuario) {
        $idUsuario = $Usuario->getidUsuario();
        if ($sentencia = $this->connection->prepare(" select * from carpeta where idUsuario = '$idUsuario' and idCarpetaSuperior IS NULL")) {
            $sentencia->execute();
            $sentencia->bind_result($idCarpeta, $idUsuario, $idCarpetaSuperior, $nombreCarpeta, $fechaCreacion);
            while ($sentencia->fetch()) {
                $carpeta = new Carpeta($idCarpeta, $idCarpetaSuperior, $nombreCarpeta, $fechaCreacion);
            }
            $sentencia->close();
            return $carpeta;
        }
        return;
    }

    public function consultaCarpeta($Usuario, $idCarpeta) {
        $idUsuario = $Usuario->getidUsuario();
        if ($sentencia = $this->connection->prepare("select * from Carpeta where idCarpeta = '$idCarpeta' and idUsuario = '$idUsuario' ")) {
            $sentencia->execute();
            $sentencia->bind_result($idCarpeta, $idUsuario, $idCarpetaSuperior, $nombreCarpeta, $fechaCreacion);
            while ($sentencia->fetch()) {
                $carpeta = new Carpeta($idCarpeta, $idCarpetaSuperior, $nombreCarpeta, $fechaCreacion);
            }
            $sentencia->close();
            return $carpeta;
        }
        return;
    }

    public function existeCarpeta($Usuario, $carpetaActual, $nombreNuevaCarpeta) {

        $idUsuario = $Usuario->getidUsuario();
        $idCarpetaSup = $carpetaActual->getIdCarpeta();

        if ($sentencia = $this->connection->prepare("select count(idCarpeta) as result  from carpeta where  idUsuario = '$idUsuario' and nombreCarpeta = '$nombreNuevaCarpeta' and idCarpetaSuperior = '$idCarpetaSup' ")) {
            $sentencia->execute();
            $sentencia->bind_result($result);
            while ($sentencia->fetch()) {
                if ($result == 1) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }

    public function insertaCarpetaRaiz($Usuario) {
        $idUsuario = $Usuario->getidUsuario();
        $nombreNuevaCarpeta = $Usuario->getidUsuario();
        if (!$this->connection->query("insert into carpeta (idUsuario,  nombreCarpeta, fechaCreacion) values  ('$idUsuario', '$nombreNuevaCarpeta', CURDATE() )")) {
            echo "Mistakes Were Made " . $this->connection->errno . " " . $this->connection->error;
            return false;
        }
        return true;
    }

    public function insertaCarpeta($Usuario, $carpetaActual, $nombreNuevaCarpeta) {
        $idUsuario = $Usuario->getidUsuario();
        $idCarpetaSup = $carpetaActual->getIdCarpeta();

        if (!$this->connection->query("insert into carpeta (idUsuario, idCarpetaSuperior,  nombreCarpeta, fechaCreacion) values  ('$idUsuario', '$idCarpetaSup'  ,'$nombreNuevaCarpeta', CURDATE() )")) {
            echo "Mistakes Were Made " . $this->connection->errno . " " . $this->connection->error;
            return false;
        }
        return true;
    }

    public function listaCarpetas($Usuario, $carpetaActual) {
        $idUsuario = $Usuario->getidUsuario();
        $idCarpetaActual = $carpetaActual->getIdCarpeta();
        $ans = null;
        if ($sentencia = $this->connection->prepare("select * from carpeta where  idUsuario = '$idUsuario' and  idCarpetaSuperior = '$idCarpetaActual' ")) {
            $sentencia->execute();
            $sentencia->bind_result($idCarpeta, $idUsuario, $idCarpetaSuperior, $nombreCarpeta, $fechaCreacion);
            while ($sentencia->fetch()) {
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
            $sentencia->close();
        }
        return $ans;
    }

    public function eliminarCarpeta($usuario, $carpeta) {
        $idUsuario = $usuario->getidUsuario();
        $idCarpetaEliminar = $carpeta->getIdCarpeta();
        if (!$this->connection->query("delete from carpeta where idUsuario = '$idUsuario' and idCarpeta = '$idCarpetaEliminar'")) {
            echo "Mistakes were made " . $this->connection->errno . " " . $this->connection->error;
            return false;
        }
        return true;
    }

    public function editarCarpeta($usuario, $idCarpetaEditar, $nombreCarpeta) {
        $idUsuario = $usuario->getidUsuario();
        if (!$this->connection->query("UPDATE carpeta SET nombreCarpeta = '$nombreCarpeta' WHERE idCarpeta = '$idCarpetaEditar' AND idUsuario = '$idUsuario'")) {
            echo "Mistakes were made " . $this->connection->errno . " " . $this->connection->error;
            return false;
        }
        return true;
    }

    /*     * **********************************  Actions for FILES  *********************************** */

    public function listaArchivos($Usuario, $carpetaActual) {

        $ans = null;

        $idUsuario = $Usuario->getidUsuario();
        $idCarpetaActual = $carpetaActual->getIdCarpeta();

        if ($sentencia = $this->connection->prepare("select * from Archivo where  idUsuario = '$idUsuario' and  idCarpeta = '$idCarpetaActual' ")) {
            $sentencia->execute();
            $sentencia->bind_result($nombreArchivo, $idCarpeta, $idUsuario, $nombreArchivoGRID, $tamanio, $fechaSubida);
            while ($sentencia->fetch()) {
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
            $sentencia->close();
        }
        return $ans;
    }

    public function eliminarArchivo($usuario, $idCarpeta, $nombreArchivo) {
        $idUsuario = $usuario->getidUsuario();
        if (!$this->connection->query("DELETE FROM archivo WHERE nombreArchivo = '$nombreArchivo' AND idCarpeta = '$idCarpeta' AND idUsuario = '$idUsuario' ")) {
            echo "Mistakes were made " . $this->connection->errno . " " . $this->connection->error;
            return false;
        }
        return true;
    }

    public function editarArchivo($usuario, $idCarpeta, $nombreArchivo, $nuevoNomArch) {
        $idUsuario = $usuario->getidUsuario();
        if (!$this->connection->query("update archivo set nombreArchivo = '$nuevoNomArch' where nombreArchivo = '$nombreArchivo' and idCarpeta = '$idCarpeta' and idUsuario = '$idUsuario'")) {
            echo "Mistakes were made " . $this->connection->errno . " " . $this->connection->error;
            return false;
        }
        return true;
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
    public function getHTMLCarpeta($Usuario,$carpetaActual,$nombreCarpeta){
        $idUsuario = $Usuario->getidUsuario();
        $idCarpetaSup = $carpetaActual->getIdCarpeta();
        $htmlCarpeta = null;
        if ($sentencia = $this->connection->prepare("select * from carpeta where  idUsuario = '$idUsuario' and  idCarpetaSuperior = '$idCarpetaSup' and nombreCarpeta = '$nombreCarpeta'")) {
            $sentencia->execute();
            $sentencia->bind_result($idCarpeta, $idUsuario, $idCarpetaSuperior, $nombreCarpeta, $fechaCreacion);
            while ($sentencia->fetch()) {
                $carpeta = new Carpeta($idCarpeta, $idCarpetaSuperior, $nombreCarpeta, $fechaCreacion);
                $htmlCarpeta = '<tr id=row'.$carpeta->getIdCarpeta().'>
							<td class="text-center"><a href = "#"> <p id =' . $carpeta->getIdCarpeta() . '  onclick = "actualizarContenidoEnPantalla(' . $carpeta->getIdCarpeta() . ')" >' . $carpeta->getNombreCarpeta() . '</p></a></td>
							<td class="text-center">' . $carpeta->getFechaCreacion() . '</td>
							<td class="text-center">
								<a class="btn btn-primary btn-sm btn-sel-carp" href="#"><span class="glyphicon glyphicon-remove"></span> Mover</a>
								<a class="btn btn-info    btn-sm btn-sel-carp" href="#" data-toggle="modal" data-target="#modalEditarCarpeta"  data-idCarpeta=' . $carpeta->getIdCarpeta() . ' ><span class="glyphicon glyphicon-edit"></span> Editar</a>								
								<a class="btn btn-danger  btn-sm btn-sel-carp" href="#" data-toggle="modal" data-target="#modalEliminarCarpeta"  data-idCarpeta=' . $carpeta->getIdCarpeta() . '  ><span class="glyphicon glyphicon-remove"></span> Eliminar</a>
							</td>
						</tr>';
            }
            $sentencia->close();
        }
        return $htmlCarpeta;
    }
}

?>
