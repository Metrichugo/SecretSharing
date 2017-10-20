/**
 Variables globales
 cuando el usuario quiera hacer alguna operacion sobre un archivo 
 se invoca un metodo que actualiza el valor de estas variables luego se hacen
 uso de estas variables en cualquier metodo 
 **/

var idCarpetaG = 0; // la carpeta a la que pertenece el archivo
var nomArchivoG = 0; // El archivo sobre el cual se le hara  la operacion

$(document).ready(function () {
    $(document).on("click", ".btn-sel-arch", function () {
        idCarpetaG = $(this).attr("data-idCarpeta");
        nomArchivoG = $(this).attr("data-nomArchivo");
        console.log("idCarpeta  del archivo seleccionado: " + idCarpetaG);
        console.log("nombre Archivo: " + nomArchivoG);
    });
});


function eliminarArchivo(){
    idCarpeta = $('#deleteFile').attr('data-idCarpeta');
    nombreArchivo = $('#deleteFile').attr('data-oldName');
    console.log("En funcion elminar Archivo");
    console.log("Elimando... " + idCarpeta + "/" + "nombreArchivo" + nombreArchivo);
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
                $('#row'+nombreArchivo).remove();
                setTimeout(function(){  $('#modalEliminaArchivo').modal('hide'); }, 500);
            }else{
                console.log("No se pudo eliminar el archivo");
            }
        }
    });

}


function validarNombreArchivo(nuevoNomArch, classError) {
    var r1 = true, r2 = true, r3 = true, r4 = true;
    if (nuevoNomArch.length >= 255) {
        r1 = false;
        $("#" + classError).html('<div class="alert alert-danger"><button type="button" class="close">×</button>El nombre del archivo no puede exceder los 255 caracteres</div>');
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
    if (nuevoNomArch.indexOf("/") !== -1) {
        r2 = false;
        $("#" + classError).html('<div class="alert alert-danger"><button type="button" class="close">×</button>El carácter / no es válido</div>');
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
    if (nuevoNomArch.localeCompare(".") === 0 || nuevoNomArch.localeCompare("..") === 0) {
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

    if (nuevoNomArch.trim().length === 0) {
        r1 = false;
        $("#" + classError).html('<div class="alert alert-danger"><button type="button" class="close">×</button>El nombre del archivo debe contener al menos un caracter</div>');
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


    return r1 && r2 && r3 && r4;
}


function editarNombreArchivo() {
    var idCarpeta = $('#nombreEditarArchivo').attr('data-idCarpeta');
    var nombreArch = $('#nombreEditarArchivo').attr('data-oldName');
    var nuevoNomArch = $('#nombreEditarArchivo').val();
    var flag = validarNombreArchivo(nuevoNomArch, "ErrorEditarArchivo");
    if (!flag)
        return false;
    console.log("Nombre del Archivo " + nombreArch + " Nuevo Nombre " + nuevoNomArch);
    //Nombre de archivo, ahora llamada a ajax para verificar duplicidad
    $.ajax({
        type: "POST",
        url: "manejoArchivo.php",
        data: {

            Operation: "EditarArch",
            nombreArch: nombreArch,
            nuevoNomArch: nuevoNomArch,
            idCarpeta: idCarpeta
        },
        success: function (response) {
            console.log(response);
            if (response === "correct") {
                $('#row' + nombreArch).attr('id', 'row' + nuevoNomArch);
                $('#arch' + nombreArch).text(nuevoNomArch);
                $('#arch' + nombreArch).attr('id', 'arch' + nuevoNomArch);
                /* Cundo se tenga lo de mover hacer lo mismo
                 $('#mov'+nombreArch).attr('data-nomArchivo',nuevoNomArch);
                 */
                $('#down' + nombreArch).attr('data-nomArchivo', nuevoNomArch);
                $('#edit' + nombreArch).attr('data-nomArchivo', nuevoNomArch);
                $('#del' + nombreArch).attr('data-nomArchivo', nuevoNomArch);
                $('#down' + nombreArch).attr('id', 'down' + nuevoNomArch);
                $('#edit' + nombreArch).attr('id', 'edit' + nuevoNomArch);
                $('#del' + nombreArch).attr('id', 'del' + nuevoNomArch);

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

                setTimeout(function () {
                    $('#modalEditarArchivo').modal('hide');
                }, 500);

            } else {
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


function subirArchivo() {
    //var filedata = $('#fileToUpload')[0];
    var inputFile = document.getElementById("fileToUpload");
    var file = inputFile.files[0];
    console.log("Contenido:");
    console.log(file);

    var form_data = new FormData();
    form_data.append('file', file);
    form_data.append('Operation', 'SubirArchivo');
    //document.write(inputFile);
    $.ajax({
        type: "POST",
        url: "../php/ManejadorArchivo.php",
        data: form_data,
        contentType: false,
        cache: false,
        processData: false,
        success: function (response) {
            console.log(response);
            if (response === "UploadSuccesfull") {
                muestaMensajeOk("Archivo subido correctamente", "resultadoSubirArchivo");
                //Timeout cerrar modal
                setTimeout(function () {
                    $('#UploadFile').modal('hide');
                }, 1000);
            } else if (response === "UploadFailed") {
                muestraMensajeError("Ocurrió un error interno en el servidor, intentelo más tarde", "errorSubirArchivo");
            } else if (response === "NoFileSelected") {
                muestraMensajeError("Seleccione un archivo", "errorSubirArchivo");
            }
        }

    });
    return false;
}

function muestraMensajeError(mensaje, classError) {
    $("#" + classError).html('<div class="alert alert-danger"><button type="button" class="close">×</button>' + mensaje + '</div>');
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

function muestaMensajeOk(mensaje, classOK) {
    $('#' + classOK).html('<div class="alert alert-success"><button type="button" class="close">×</button>' + mensaje + '</div>');
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

/* Se obtiene la referencia del objeto que invocó al modal, se obtienen sus
 valores y se ponen como atributos para ser utilizados posteriormente*/
$(document).ready(function () {
    $('#modalEditarArchivo').on('show.bs.modal', function (e) {
        var opener = e.relatedTarget;
        var oldName = $(opener).attr('data-nomArchivo');
        var idCarpeta = $(opener).attr('data-idCarpeta');
        $('#nombreEditarArchivo').attr("data-oldName", oldName);
        $('#nombreEditarArchivo').attr("data-idCarpeta", idCarpeta);
    });
});

$(document).ready(function () {
    $('#modalEliminaArchivo').on('show.bs.modal', function (e) {
        var opener = e.relatedTarget;
        var oldName = $(opener).attr('data-nomArchivo');
        var idCarpeta = $(opener).attr('data-idCarpeta');
        $('#deleteFile').attr("data-oldName", oldName);
        $('#deleteFile').attr("data-idCarpeta", idCarpeta);
    });
});