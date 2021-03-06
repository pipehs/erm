
function notas(id)
{
	$("#notas_"+id).empty();
	$.get('auditorias.get_notes.'+id, function (result) {

			//vemos si está en supervisión para poder crear nota
			var str1 = "supervisar";
			var str2 = window.location.href;

			if (str2.indexOf(str1) > 0)
			{
				var resultado = '<div style="cursor:hand" id="crear_nota_'+id+'" onclick="crear_nota('+id+')" class="btn btn-primary">Agregar nota</div><br>';
			
				//agregamos div para formulario de creación de nota
				resultado += '<div id="nueva_nota_'+id+'"  style="display: none; float: left;"><br><br></div>';

				//agregamos div de texto siguiente
				resultado += '<div id="mensaje" style="clear: left;">';
			}
			else
			{
				//agregamos div de texto siguiente
				var resultado = '<div id="mensaje" style="clear: left;">';
			}
			

			if (result == "null") //no existen notas
			{
				resultado += 'Aun no se han creado notas para esta prueba.<br><hr>';
				resultado += '</div>';
			}

			else
			{
				//parseamos datos obtenidos
				var datos = JSON.parse(result);
				var cont = 1; //contador de notas 
				//seteamos datos en select de auditorías
				
				$(datos).each( function() {
					resultado += '<b>Nombre: '+this.name+'</b><br>';
					resultado += 'Fecha creación: '+this.created_at+'<br>';
					if (this.status == 0)
					{
						resultado += 'Estado: Abierta<br>'
					}
					else if (this.status == 1)
					{
						resultado += 'Estado: En ejecución<br>'
					}
					else if (this.status == 2)
					{
						resultado += 'Estado: Cerrada<br>'
					}
					resultado += 'Realizada por: '+this.user+'<br>';
					resultado += 'Dirigida a: '+this.stakeholder+'<br>';
					
					resultado += '<h4>Nota: '+this.description+'</h4>'

					//agregamos evidencias
					//ACTUALIZACIÓN 30-01-17: No se muestran evidencias ya que pueden ser muchas
					/*if (this.evidences == null)
					{
						resultado += '<font color="red">Esta nota no tiene evidencias agregadas</font><br>';
					}

					else
					{

						$(this.evidences).each( function(i,evidence) {
							resultado += '<div style="cursor:hand" id="descargar_'+id+'" onclick="descargar(0,\''+evidence.url+'\')"><font color="CornflowerBlue"><u>Descargar evidencia</u></font></div><br>';
						});
					}*/

					if (this.answers == null)
					{
						resultado += '<div class="alert alert-danger alert-dismissible" role="alert">'
						resultado += 'Esta nota aun no tiene respuestas</div>';
					}
					else
					{
						$(this.answers).each( function(i,answer) {
							resultado += '<div class="alert alert-success alert-dismissible" role="alert">'
							resultado += '<b><u>Respuesta de auditor: </u></b><br>';
							resultado += '<font color="black	">'+answer.answer+'</font><br>';
							
							if (answer.ans_evidences != null)
							{
								$(answer.ans_evidences).each( function(i,evidence) {
									resultado += '<div style="cursor:hand" id="descargar_'+id+'" onclick="descargar(1,\''+evidence.url+'\')"><font color="CornflowerBlue"><u>Descargar evidencia de respuesta</u></font></div><br>';
								});
							}

							resultado += 'Enviada el: '+answer.created_at+'</div>';

							nombre = "'"+answer.answer+"'";
							nom2 = "'note_answer'";
							nom3 = "'La respuesta definida como '";
							resultado += '<div style="cursor:hand" class="btn btn-danger" onclick="eliminar2('+this.id+','+nombre+','+nom2+','+nom3+')">Eliminar</div>';

						});
					}
					var str1 = "notas";
					var str2 = window.location.href;

					if (str2.indexOf(str1) > 0)
					{
						if (this.status_origin == 0)
						{
							resultado += '<br><div style="cursor:hand" id="responder_nota_'+this.id+'" onclick="responder_nota('+this.id+','+this.test_id+')" class="btn btn-primary">Agregar Respuesta</div>';
							//agregamos div para formulario de creación de nota
							resultado += '<div id="respuesta_nota_'+this.id+'"  style="display: none; clear: left;"><br><br></div>';
						}
					}
					resultado += '<hr style="border-style: inset; border-width: 1px;">';

				});			
				
			}

			resultado += '<div style="cursor:hand" onclick="ocultar_notas('+id+')"><font color="CornflowerBlue"><u>Ocultar</u></font></div><hr><br>';
			$("#notas_"+id).append(resultado).show(500);
			
		});
}