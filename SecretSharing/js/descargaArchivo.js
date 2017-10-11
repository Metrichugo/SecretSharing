$(document).ready(function(){
	$(document).on("click", ".descargaArch", function(){

		var idCarpeta = $(this).attr("data-idCarpeta");
		var nombreArchivo = $(this).attr("data-nomArchivo");

		console.log("idCarpeta " + idCarpeta);
		console.log("nombreArchivo " + nombreArchivo);

		console.log("Ejecutando ...");
		$.AjaxDownloader({
		    url  : "../php/descargaArchivo.php",
		    data : {
				idCarpeta : idCarpeta,
				nombreArchivo : nombreArchivo		        
		    }
		});

	});
});