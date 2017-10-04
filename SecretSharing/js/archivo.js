/**
Variables globales
cuando el usuario quiera hacer alguna operacion sobre un archivo 
se invoca un metodo que actualiza el valor de estas variables luego se hacen
uso de estas variables en cualquier metodo 
**/

var  idCarpetaG = 0; // la carpeta a la que pertenece el archivo
var  nomArchivoG = 0; // El archivo sobre el cual se le hara  la operacion

$(document).ready(function(){
	$(document).on("click", ".btn-sel-arch", function(){
		idCarpetaG  = $(this).attr("data-idCarpeta");
		nomArchivoG = $(this).attr("data-nomArchivo");
		console.log("idCarpeta  del archivo seleccionado: " + idCarpetaG);
		console.log("nombre Archivo: " + nomArchivoG);
	});
});


function eliminarArchivo(){
	idCarpeta = idCarpetaG;
	nombreArchivo = nomArchivoG;

	console.log("En funcion elminar Archivo");
	console.log("Elimando... " + idCarpeta + "/" + "nombreArchivo");
    
    $.ajax({
        type: "POST",
        url: "manejoArchivo.php",
        data: {
           Operation : "eliminarArchivo",
           idCarpeta: idCarpeta,
           nombreArchivo : nombreArchivo
        },
        success: function (response) {
            console.log("Eliminacion del archivo " + response );

            if (response == "correct"){
                actualizarArchivosEnPantalla();
            }else{
                console.log("no pude");
            }
        }
    });

}






