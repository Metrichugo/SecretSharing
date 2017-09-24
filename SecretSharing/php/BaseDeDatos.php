<?php
    include("Usuario.php");
    class BaseDeDatos{
        protected $DB_NAME = "SecretSharing";
        protected $DB_USER = "root";
        protected $DB_PASS = "";
        protected $DB_HOST = "localhost";
        protected $connection;
    
        public function connect(){
            $this->connection = new mysqli($this->DB_HOST, $this->DB_USER, $this->DB_PASS, $this->DB_NAME);
            if (mysqli_connect_error()) {
                die('Error de Conexión (' . mysqli_connect_errno() . ') '
                        . mysqli_connect_error());
            }
        }
        public function insertaUsuario($Usuario){
            $email = $Usuario->getidUsuario();
            $password = $Usuario->getContraseña();
            $alias = $Usuario->getAlias();
            $status = $Usuario->getStatus();
            $espacioDisp = $Usuario->getEspacioDisp();
            if(!$this->connection->query("INSERT INTO USUARIO VALUES('$email','$password','$alias','$status','$espacioDisp')")){
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
                $sentencia->bind_result($idUsuario,$contraseña,$alias,$status,$espacioDisp);
                while($sentencia->fetch()){
                    $User = new Usuario();
                    $User->setidUsuario($idUsuario);
                    $User->setContraseña($contraseña);
                    $User->setAlias($alias);
                    $User->setStatus($status);
                    $User->setEspacioDisp($espacioDisp);
                    //$User->toString();    
                }             
                $sentencia->close();
                return $User;  
            }
            return;
        }

        public function actualizaUsuario($Usuario){
            $email = $Usuario->getidUsuario();
            $password = $Usuario->getContraseña();
            $alias = $Usuario->getAlias();
            if($sentencia = $this->connection->prepare("UPDATE USUARIO SET idUsuario = '$email', contrasenia = '$password', alias = '$alias'")){
                $sentencia->execute();
                return true;
            }
            return;
        }

        public function existeUsuario($Usuario){
            $email = $Usuario->getidUsuario();
            $password = $Usuario->getContraseña();
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
        
    }
?>