function descargar(tipo,archivo)
{
	//window.open = ('../storage/app/evidencias_notas/'+archivo,'_blank');
	if (tipo == 0) //evidencia de nota
	{
		var win = window.open('../storage/app/evidencias_notas/'+archivo, '_blank');
	 	win.focus();
	}
	else if (tipo == 1) //evidencia de respuesta
	{
		var win = window.open('../storage/app/evidencias_resp_notas/'+archivo, '_blank');
	 	win.focus();
	}
	else if (tipo == 2) //evidencia de issues
	{
		var win = window.open('../storage/app/evidencias_hallazgos/'+archivo, '_blank');
	 	win.focus();
	}
}