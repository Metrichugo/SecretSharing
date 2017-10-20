#!/usr/bin/env python
#-*- coding: utf-8 -*-
import drmaa
import os, sys

def main(argv):
	"""
	Submit a job and wait for it to finish.
	Note, need file called sleeper.sh in home directory.
	"""
	nombre_archivo=argv[0] 	
	ruta_archivo_php=argv[1] 	
	ruta_destino_grid=argv[2] 	
	ruta_y_nombre=ruta_archivo_php+'/'+nombre_archivo

	ruta_ejecutable="/home/victor/Documentos/Ejecutables/Share"
	ruta_servidores="/home/victor/Documentos/Ejecutables/servidores.txt"
	numero_shares=3
	umbral=2

	print("Nombre del archivo: "+nombre_archivo +" \nUbicacion: "+ruta_archivo_php+ "\nRuta destino: "+ruta_destino_grid)

	with drmaa.Session() as s:
		print('Creando platilla de trabajo')
		jt = s.createJobTemplate()
		jt.remoteCommand = ruta_ejecutable
		jt.args = [nombre_archivo,"/var/www/SecretSharing/files","rcss@Maestro",umbral ,numero_shares, ruta_destino_grid , "servidores.txt"]
		jt.nativeSpecification = "should_transfer_files   = Yes\n" \
			"when_to_transfer_output = ON_EXIT\n" \
			"transfer_input_files = "+ruta_servidores+"\n"\
			"initialdir = "+ruta_archivo_php+"\n"\
			"jobLeaseDuration = 10\n"\
			"output = "+nombre_archivo+".out\n"\
			"error  = "+nombre_archivo+".err\n"


		jobid = s.runJob(jt)
		print('El trabajo fue enviado con el ID %s' % jobid)

		retval = s.wait(jobid, drmaa.Session.TIMEOUT_WAIT_FOREVER)
		print('El trabajo {0} terminó con status {1}'.format(retval.jobId, retval.hasExited))

		print('Cerrando sesión de trabajo')
		s.deleteJobTemplate(jt)


if __name__ == "__main__":
	main(sys.argv[1:])
