/*
update Pantalla

*/


function actualizarCarpetaActual(idCarpetaMoverse){
   // ajax para actualizar la variable de sesion de la carpeta actual

    $.ajax({
        type: "POST",
        url: "../php/manejoCarpeta.php",
        data: {
            Operation: "actualizarCarpetaActual",
            idCarpetaMoverse: idCarpetaMoverse
        },
        success: function (response) {
          //  document.write(response);
        }
    }); 
}

function actualizarCarpetasEnPantalla(){
    // ajax para actualizar la seccion de carpetas
    $.ajax({
        type: "POST",
        url: "../php/manejoCarpeta.php",
        data: {
            Operation: "actualizarCarpetasEnPantalla"
        },
        success: function (response) {

            //document.write("hola");
            $('#contenedorCarpetas').empty();
            $('#contenedorCarpetas').html(response);
        }
    });
    return true;
}

function actualizarArchivosEnPantalla(){
    // ajax para actualizar la seccion de carpetas
    $.ajax({
        type: "POST",
        url: "../php/manejoCarpeta.php",
        data: {
            Operation: "actualizarArchivosEnPantalla"
        },
        success: function (response) {

            //document.write("hola");
            $('#contenedorArchivos').empty();
            $('#contenedorArchivos').html(response);
        }
    });
    return true;
}

function actualizarContenidoEnPantalla(idCarpetaMoverse) {
    
    actualizarCarpetaActual(idCarpetaMoverse);
    actualizarCarpetasEnPantalla();
    actualizarArchivosEnPantalla();
}





function irCarpetaAtras() {
     // ajax para actualizar volver a la carpeta superior
    var flag = true;
    $.ajax({
        type: "POST",
        url: "../php/manejoCarpeta.php",
        data: {
            Operation: "irCarpetaAtras"
        },
        success: function (response) {
            if(response.localeCompare("incorrect") != 0 ){
                actualizarCarpetasEnPantalla();
                actualizarArchivosEnPantalla();
            }  
        }
    });
}

actualizarContenidoEnPantalla(1);
