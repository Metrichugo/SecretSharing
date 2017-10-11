<?php
	session_start();  
    if(!isset($_SESSION['usuario'])){
        header('Location: ../index.html');
        exit;  
    }    
	include_once("BaseDeDatos.php");
    include_once("Usuario.php");
    include_once("Archivo.php");
    include_once("Carpeta.php");
    
    $usuario = unserialize($_SESSION["usuario"]);
    $DBConnection = unserialize($_SESSION["DBConnection"]);
    $DBConnection->connect(); // Al finaliza el archivo se cierra la conexion con db
    $operacion = $_POST['Operation'];
    $carpetActual = unserialize($_SESSION["carpetActual"]);
    $dirsubida = "../assets/";
    $timeStamp = new DateTime();
    $renamedFile = basename($_FILES['file']['name']) . $timeStamp->getTimestamp();
    echo $_FILES['file']['name'];
    if($operacion=="SubirArchivo"){
        if(!empty($_FILES['file']['name'])){
            $result = SubirArchivo($carpetActual,$usuario,$renamedFile,$dirsubida);
            echo $result;
            if($result){				
				$carpeta_usuario="/".$usuario->getidUsuario();
				$comando= "python ../python/comparte_archivo.py ".$renamedFile." ".$dirsubida." ".$carpeta_usuario;
				my_shell_exec($comando,$stdout,$stderr); //system()
				echo "<p>".$stdout."</p>";
				echo "<p>".$stderr."</p>";
				exec("rm ".$dirsubida.$renamedFile);
                //echo "UploadSuccesfull";
            }else{
                echo "Error";
            }
        }else{
            echo 'NoFileSelected';
        }
    }


    function SubirArchivo($carpetActual,$usuario,$renamedFile,$dirsubida){        
        $uploadedFile = $dirsubida . $renamedFile;
        echo $uploadedFile;
        return (move_uploaded_file($_FILES['file']['tmp_name'],$uploadedFile));
    }
    
    function my_shell_exec($cmd, &$stdout=null, &$stderr=null) {
	    $proc = proc_open($cmd,[
		1 => ['pipe','w'],
		2 => ['pipe','w'],
	    ],$pipes);
	    $stdout = stream_get_contents($pipes[1]);
	    fclose($pipes[1]);
	    $stderr = stream_get_contents($pipes[2]);
	    fclose($pipes[2]);
	    return proc_close($proc);
	}

?>
