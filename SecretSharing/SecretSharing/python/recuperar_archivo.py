#!/usr/bin/env python
# -*- coding: utf-8 -*-
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

	ruta_ejecutable="/home/victor/Documentos/Ejecutables/Recover"
	ruta_servidores="/home/victor/Documentos/Ejecutables/servidores.txt"
	numero_shares=3
	umbral=2

	print("Nombre del archivo: "+nombre_archivo +" \nUbicacion de salida: "+ruta_archivo_php+ "\nRuta grid: "+ruta_destino_grid)

	with drmaa.Session() as s:
		print('Creando platilla de trabajo')
		jt = s.createJobTemplate()
		jt.remoteCommand = ruta_ejecutable
		jt.args = [nombre_archivo, umbral, numero_shares, ruta_destino_grid , "servidores.txt"]
		jt.nativeSpecification = "should_transfer_files   = Yes\n" \
			"when_to_transfer_output = ON_EXIT\n" \
			"transfer_input_files = "+ruta_servidores+"\n"\
			"transfer_output_files = "+nombre_archivo+"\n"\
			"initialdir = "+ruta_archivo_php+"\n"\
			"output = out.txt\n"\
			"error  = err.txt\n"\
			"jobLeaseDuration = 10\n"

		jobid = s.runJob(jt)
		print('Your job has been submitted with ID %s' % jobid)
		
		retval = s.wait(jobid, drmaa.Session.TIMEOUT_WAIT_FOREVER)
		print('Job: {0} finished with status {1}'.format(retval.jobId, retval.hasExited))
		
		print('Cleaning up')
		s.deleteJobTemplate(jt)


if __name__ == "__main__":
	main(sys.argv[1:])
