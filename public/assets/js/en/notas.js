function notas(id)
{
	$("#notas_"+id).empty();
	$.get('auditorias.get_notes.'+id, function (result) {

			//vemos si está en supervisión para poder crear nota
			var str1 = "supervisar";
			var str2 = window.location.href;

			if (str2.indexOf(str1) > 0)
			{
				var resultado = '<div style="cursor:hand" id="crear_nota_'+id+'" onclick="crear_nota('+id+')" class="btn btn-primary">Add Note</div><br>';
			
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
				resultado += 'Still have not created notes for this test.<br><hr>';
				resultado += '</div>';
			}

			else
			{
				//parseamos datos obtenidos
				var datos = JSON.parse(result);
				var cont = 1; //contador de notas 
				//seteamos datos en select de auditorías
				
				$(datos).each( function() {
					resultado += '<b>Name: '+this.name+'</b><br>';
					resultado += 'Creation date: '+this.created_at+'<br>';

					if (this.status == 0)
					{
						resultado += 'Status: Open<br>'
					}
					else if (this.status == 1)
					{
						resultado += 'On execution<br>'
					}
					else if (this.status == 2)
					{
						resultado += 'Status: closed<br>'
					}
					
					resultado += '<h4>Note: '+this.description+'</h4>';

					//agregamos evidencias
					if (this.evidences == null)
					{
						resultado += '<font color="red">This note does not have evidences added</font><br>';
					}

					else
					{

						$(this.evidences).each( function(i,evidence) {
							resultado += '<div style="cursor:hand" id="descargar_'+id+'" onclick="descargar(0,\''+evidence.url+'\')"><font color="CornflowerBlue"><u>Download Evidence</u></font></div><br>';
						});
					}

					if (this.answers == null)
					{
						resultado += '<div class="alert alert-danger alert-dismissible" role="alert">'
						resultado += 'This note does not have answers yet</div>';
					}
					else
					{
						$(this.answers).each( function(i,answer) {
							resultado += '<div class="alert alert-success alert-dismissible" role="alert">'
							resultado += '<b><u>Auditor answer: </u></b><br>';
							resultado += '<font color="black	">'+answer.answer+'</font><br>';
							
							if (answer.ans_evidences != null)
							{
								$(answer.ans_evidences).each( function(i,evidence) {
									resultado += '<div style="cursor:hand" id="descargar_'+id+'" onclick="descargar(1,\''+evidence.url+'\')"><font color="CornflowerBlue"><u>Download Answer Evidence</u></font></div><br>';
								});
							}

							resultado += 'Sending date: '+answer.created_at+'</div>';

						});
					}
					var str1 = "notas";
					var str2 = window.location.href;

					if (str2.indexOf(str1) > 0)
					{
						if (this.status_origin == 0)
						{
							resultado += '<div style="cursor:hand" id="responder_nota_'+this.id+'" onclick="responder_nota('+this.id+','+this.test_id+')" class="btn btn-primary">Answer</div>';
							//agregamos div para formulario de creación de nota
							resultado += '<div id="respuesta_nota_'+this.id+'"  style="display: none; clear: left;"><br><br></div>';
						}
					}
					resultado += '<hr style="border-style: inset; border-width: 1px;">';

				});			
				
			}

			resultado += '<div style="cursor:hand" onclick="ocultar_notas('+id+')"><font color="CornflowerBlue"><u>Hide</u></font></div><hr><br>';
			$("#notas_"+id).append(resultado).show(500);
			
		});
}