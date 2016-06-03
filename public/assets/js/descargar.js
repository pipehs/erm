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
	else if (tipo == 3) //evidencia de eval. de controles
	{
		var win = window.open('../storage/app/eval_controles/'+archivo, '_blank');
	 	win.focus();
	}
	else if (tipo == 4) //evidencia de programas de auditoria
	{
		var win = window.open('../storage/app/programas_auditoria/'+archivo, '_blank');
	 	win.focus();
	}
	else if (tipo == 5) //evidencia de pruebas de auditoria
	{
		var win = window.open('../storage/app/pruebas_auditoria/'+archivo, '_blank');
	 	win.focus();
	}
}