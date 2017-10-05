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
                setTimeout(function(){  $('#modalEliminaArchivo').modal('hide'); }, 500);

            }else{
                console.log("no pude");
            }
        }
    });

}


function validarNombreArchivo(nuevoNomArch, classError ){

    var r1 = true, r2 = true , r3 = true;
    
    if(nuevoNomArch.length >= 255 ){
        r1 = false;
        $("#" + classError ).html('<div class="alert alert-danger"><button type="button" class="close">×</button>El nombre del archivo no puede exceder los 255 caracteres</div>');
            window.setTimeout(function () {
                $(".alert").fadeTo(100, 0).slideUp(100, function () {
                    $(this).remove();
                });
            }, 5000);
            /* Button for close alert */
            $('.alert .close').on("click", function (e) {
                $(this).parent().fadeTo(500, 0).slideUp(500);
            });
    } 
    if(nuevoNomArch.indexOf("/") != -1 ){
        r2 = false;
        $("#" + classError).html('<div class="alert alert-danger"><button type="button" class="close">×</button>El carácter / no es valido</div>');
            window.setTimeout(function () {
                $(".alert").fadeTo(100, 0).slideUp(100, function () {
                    $(this).remove();
                });
            }, 5000);
            /* Button for close alert */
            $('.alert .close').on("click", function (e) {
                $(this).parent().fadeTo(500, 0).slideUp(500);
            });

    } 
    if(nuevoNomArch.localeCompare(".") == 0  || nuevoNomArch.localeCompare("..") == 0 ){
        r3 = false;
        $("#" + classError).html('<div class="alert alert-danger"><button type="button" class="close">×</button>El archivo no puede llamarse . o ..</div>');
            window.setTimeout(function () {
                $(".alert").fadeTo(100, 0).slideUp(100, function () {
                    $(this).remove();
                });
            }, 5000);
            /* Button for close alert */
            $('.alert .close').on("click", function (e) {
                $(this).parent().fadeTo(500, 0).slideUp(500);
            });
    }

    return r1 && r2 && r3;

}

function editarNombreArchivo(){
    var idCarpeta = idCarpetaG;
    var nombreArch = nomArchivoG;
    var nuevoNomArch =  $('#nombreEditarArchivo').val(); 
    var flag = validarNombreArchivo(nuevoNomArch, "ErrorEditarArchivo");

    if(!flag) return false;

    console.log("valores: idCarpeta"+ idCarpeta + " nombreArch " + nombreArch + " nuevoNomArch " +  nuevoNomArch);
    //Nombre de archivo, ahora llamada a ajax para verificar duplicidad
    $.ajax({
        type: "POST",
        url: "manejoArchivo.php",
        data: {

           Operation : "EditarArch",
           nombreArch : nombreArch,
           nuevoNomArch : nuevoNomArch,
           idCarpeta : idCarpeta
        },
        success: function (response) {
            console.log(response);

            if (response == "correct"){

                actualizarArchivosEnPantalla();

                $('#resultadoEditarArchivo').html('<div class="alert alert-success"><button type="button" class="close">×</button>Se ha actualizado del nombre del archivo</div>');
                        window.setTimeout(function () {
                            $(".alert").fadeTo(100, 0).slideUp(100, function () {
                                $(this).remove();
                            });
                        }, 5000);
                        /* Button for close alert */
                        $('.alert .close').on("click", function (e) {
                            $(this).parent().fadeTo(500, 0).slideUp(500);
                        });

                setTimeout(function(){  $('#modalEditarArchivo').modal('hide'); }, 500);
               
            }else{
                $('#resultadoEditarArchivo').html('<div class="alert alert-danger"><button type="button" class="close">×</button>Ya existe un archivo con el mismo nombre</div>');
            window.setTimeout(function () {
                $(".alert").fadeTo(100, 0).slideUp(100, function () {
                    $(this).remove();
                });
            }, 5000);
            /* Button for close alert */
            $('.alert .close').on("click", function (e) {
                $(this).parent().fadeTo(500, 0).slideUp(500);
            });
            }
        }
    });
    return false;
}


