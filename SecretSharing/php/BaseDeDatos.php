<?php
	include("Usuario.php");
	include("Carpeta.php");
	include("Archivo.php");

	class BaseDeDatos{
		protected $DB_NAME = "SecretSharing";
		protected $DB_USER = "root";
		protected $DB_PASS = "1@Javier";
		protected $DB_HOST = "localhost";
		/************************************ Methods for DB *****************************************/

		public function connect(){
			$this->connection = new mysqli($this->DB_HOST, $this->DB_USER, $this->DB_PASS, $this->DB_NAME);
			if (mysqli_connect_error()) {
				die('Error de Conexión (' . mysqli_connect_errno() . ') '
						. mysqli_connect_error());
			}
		}

		public function close(){
			if(!mysqli_close($this->connection) ){
				die('Error de cierre de Conexión');
			}

		}
	   
		/************************************  Actions for USERS  ************************************/

		public function insertaUsuario($Usuario){
			$email = $Usuario->getidUsuario();
			$password = $Usuario->getContrasenia();
			$alias = $Usuario->getAlias();
			$status = $Usuario->getStatus();
			$espacioUtilizado = $Usuario->getEspacioUtilizado();
			if(!$this->connection->query("INSERT INTO USUARIO VALUES('$email','$password','$alias','$status','$espacioUtilizado')")){
				echo "Mistakes Were Made " . $this->connection->errno . " ". $this->connection->error;
				return false;
			}
			return true;             
		}

		public function borraUsuario($Usuario){
			$email = $Usuario->getIdUsuario();
			if(!$this->connection->query("DELETE FROM USUARIO WHERE idUsuario = '$email'")){
				echo "Mistakes Were Made " . $this->connection->errno . " " . $this->connection->error;
				return false;
			}
			return true;
		}

		public function consultaUsuario($Usuario){
			$email = $Usuario->getidUsuario();
			if($sentencia = $this->connection->prepare("SELECT * FROM USUARIO WHERE idUsuario = '$email'") ){
				$sentencia->execute();
				$sentencia->bind_result($idUsuario,$contrasenia,$alias,$status,$espacioUtilizado);
				while($sentencia->fetch()){
					$User = new Usuario();
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

		public function actualizaUsuario($Usuario){
			$email = $Usuario->getidUsuario();
			$password = $Usuario->getContrasenia();
			$alias = $Usuario->getAlias();
			if($sentencia = $this->connection->prepare("UPDATE USUARIO SET idUsuario = '$email', contrasenia = '$password', alias = '$alias'")){
				$sentencia->execute();
				return true;
			}
			return;
		}

		public function existeUsuario($Usuario){
			$email = $Usuario->getidUsuario();
			$password = $Usuario->getContrasenia();
			if($sentencia = $this->connection->prepare("SELECT COUNT(idUsuario) AS RESULT FROM USUARIO WHERE idUsuario = '$email' AND contrasenia = '$password'")){
				$sentencia->execute();
				$sentencia->bind_result($result);
				while($sentencia->fetch()){
					if($result==1){
						$isUnique = true;
					}else{
						$isUnique = false;
					}
				}
			}
			return $isUnique;
		}

		/************************************  Actions for CARPETAS  ************************************/
		public function consultaCarpetaRaiz($Usuario){
			$idUsuario = $Usuario->getidUsuario();
			if($sentencia = $this->connection->prepare(" select * from carpeta where idUsuario = '$idUsuario' and idCarpetaSuperior IS NULL") ){
				$sentencia->execute();
				$sentencia->bind_result($idCarpeta, $idUsuario,  $idCarpetaSuperior , $nombreCarpeta , $fechaCreacion );
				while($sentencia->fetch()){
				   $carpeta = new Carpeta($idCarpeta, $idCarpetaSuperior, $nombreCarpeta, $fechaCreacion);
				}             
				$sentencia->close();
				return $carpeta;  
			}
			return;

		}


		public function consultaCarpeta($Usuario, $idCarpeta){
			$idUsuario = $Usuario->getidUsuario();
			if($sentencia = $this->connection->prepare("select * from Carpeta where idCarpeta = '$idCarpeta' and idUsuario = '$idUsuario' ") ){
				$sentencia->execute();
				$sentencia->bind_result($idCarpeta, $idUsuario,  $idCarpetaSuperior , $nombreCarpeta , $fechaCreacion );
				while($sentencia->fetch()){
				   $carpeta = new Carpeta($idCarpeta, $idCarpetaSuperior, $nombreCarpeta, $fechaCreacion);
				}             
				$sentencia->close();
				return $carpeta;  
			}
			return;

		}

		public function existeCarpeta($Usuario, $carpetaActual, $nombreNuevaCarpeta){

			$idUsuario = $Usuario->getidUsuario();
			$idCarpetaSup = $carpetaActual->getIdCarpeta();

			if($sentencia = $this->connection->prepare("select count(idCarpeta) as result  from carpeta where  idUsuario = '$idUsuario' and nombreCarpeta = '$nombreNuevaCarpeta' and idCarpetaSuperior = '$idCarpetaSup' ")){
				$sentencia->execute();
				$sentencia->bind_result($result);
				while($sentencia->fetch()){
					if($result==1){
						return  true;
					}else{
						return false;
					}
				}
			}
		}


		public function insertaCarpetaRaiz($Usuario ){
			$idUsuario = $Usuario->getidUsuario();
			$nombreNuevaCarpeta = $Usuario->getidUsuario();
			if(!$this->connection->query("insert into carpeta (idUsuario,  nombreCarpeta, fechaCreacion) values  ('$idUsuario', '$nombreNuevaCarpeta', CURDATE() )")){
				echo "Mistakes Were Made " . $this->connection->errno . " ". $this->connection->error;
				return false;
			}
			return true;  
		}

		public function insertaCarpeta($Usuario, $carpetaActual, $nombreNuevaCarpeta){
			$idUsuario = $Usuario->getidUsuario();
			$idCarpetaSup = $carpetaActual->getIdCarpeta();
			
			if(!$this->connection->query("insert into carpeta (idUsuario, idCarpetaSuperior,  nombreCarpeta, fechaCreacion) values  ('$idUsuario', '$idCarpetaSup'  ,'$nombreNuevaCarpeta', CURDATE() )")){
				echo "Mistakes Were Made " . $this->connection->errno . " ". $this->connection->error;
				return false;
			}
			return true;  
		}

		public function listaCarpetas($Usuario, $carpetaActual){
			$ans = '<table class="table table-striped custab">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Creación</th>
                                    <th class="text-center">Acción</th>
                                </tr>
                            </thead>';

			$idUsuario = $Usuario->getidUsuario();
			$idCarpetaActual = $carpetaActual->getIdCarpeta();

			if($sentencia = $this->connection->prepare("select * from carpeta where  idUsuario = '$idUsuario' and  idCarpetaSuperior = '$idCarpetaActual' ")){
				$sentencia->execute();
				$sentencia->bind_result($idCarpeta, $idUsuario, $idCarpetaSuperior, $nombreCarpeta, $fechaCreacion);
				while($sentencia->fetch()){
					$carpeta = new Carpeta($idCarpeta, $idCarpetaSuperior, $nombreCarpeta, $fechaCreacion);
					//$carpeta->toString(); // impresion de valores de carpeta
					$ans = $ans.'<tr>
							<td><a href = "#"> <p id ='.$carpeta->getIdCarpeta().'  onclick = "actualizarContenidoEnPantalla('.$carpeta->getIdCarpeta().')" >'.$carpeta->getNombreCarpeta().'</p></a></td>
							<td>'.$carpeta->getFechaCreacion().'</td>
							<td class="text-center">
								<a class="btn btn-primary btn-sm btn-sel-carp" href="#"><span class="glyphicon glyphicon-remove"></span> Mover</a>
								<a class="btn btn-info    btn-sm btn-sel-carp" href="#" data-toggle="modal" data-target="#modalEditarCarpeta"  data-idCarpeta='.$carpeta->getIdCarpeta().' ><span class="glyphicon glyphicon-edit"></span> Editar</a>								
								<a class="btn btn-danger  btn-sm btn-sel-carp" href="#" data-toggle="modal" data-target="#modalEliminarCarpeta"  data-idCarpeta='.$carpeta->getIdCarpeta().'  ><span class="glyphicon glyphicon-remove"></span> Eliminar</a>
							</td>
						</tr>';
					
				}             
				$sentencia->close();
			}
			$ans = $ans.'</table>';
			return $ans;
		}

		public function eliminarCarpeta($usuario, $carpeta){
			$idUsuario = $usuario->getidUsuario();
			$idCarpetaEliminar = $carpeta->getIdCarpeta();
			if(!$this->connection->query("delete from carpeta where idUsuario = '$idUsuario' and idCarpeta = '$idCarpetaEliminar'" ) ){
				echo "Mistakes were made ".$this->connection->errno. " " . $this->connection->error;
				return false;
			}
			return true;
		}

		public function editarCarpeta($usuario, $idCarpetaEditar, $nombreCarpeta){
			$idUsuario = $usuario->getidUsuario();
			if(!$this->connection->query("UPDATE carpeta SET nombreCarpeta = '$nombreCarpeta' WHERE idCarpeta = '$idCarpetaEditar' AND idUsuario = '$idUsuario'" ) ){
				echo "Mistakes were made ".$this->connection->errno. " " . $this->connection->error;
				return false;
			}
			return true;
		}
		/************************************  Actions for FILES  ************************************/
		
		public function listaArchivos($Usuario, $carpetaActual){

			$ans = '<table class="table table-striped custab">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Tamaño</th>
                                <th>Subida</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>';
                    
			$idUsuario = $Usuario->getidUsuario();
			$idCarpetaActual = $carpetaActual->getIdCarpeta();

			if($sentencia = $this->connection->prepare("select * from Archivo where  idUsuario = '$idUsuario' and  idCarpeta = '$idCarpetaActual' ")){
				$sentencia->execute();
				$sentencia->bind_result($nombreArchivo, $idCarpeta, $idUsuario, $nombreArchivoGRID, $tamanio, $fechaSubida);
				while($sentencia->fetch()){
					$archivo = new Archivo($nombreArchivo, $tamanio, $fechaSubida);
					//$archivo->toString(); // impresion de valores de carpeta
					
				  $ans = $ans.'<tr>
								<td>'.$archivo->getNombreArchivo().'</td>
								<td>'.$archivo->getTamanio().'</td>
								<td>'.$archivo->getFechaSubida().'</td>
								<td class="text-center">
									<a class="btn btn-primary btn-sm  btn-sel-arch" href="#"><span class="glyphicon glyphicon-remove"></span> Mover</a>
									<a class="btn btn-success btn-sm  btn-sel-arch" href="#" data-idCarpeta='.$idCarpeta.' data-nomArchivo='.$nombreArchivo.' ><span class="glyphicon glyphicon-edit"></span> Descargar</a>
									<a class="btn btn-danger  btn-sm  btn-sel-arch" href="#" data-toggle="modal" data-target="#modalEliminaArchivo"  data-idCarpeta='.$idCarpeta.' data-nomArchivo='.$nombreArchivo.'><span class="glyphicon glyphicon-remove"></span> Eliminar</a>
								</td>
							</tr>';
				
				}             
				$sentencia->close();
			}
			$ans = $ans.'</table>';	
			return $ans;
		}

		public function eliminarArchivo($usuario, $idCarpeta, $nombreArchivo){
			$idUsuario = $usuario->getidUsuario();
			if(!$this->connection->query("DELETE FROM archivo WHERE nombreArchivo = '$nombreArchivo' AND idCarpeta = '$idCarpeta' AND idUsuario = '$idUsuario' " )){
				echo "Mistakes were made ".$this->connection->errno. " " . $this->connection->error;
				return false;
			}
			return true;
		}
		
		/********************************/

	}

?>