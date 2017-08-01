$("#audit").change(function() {
			if ($("#audit").val() != '') //Si es que se ha seleccionado valor válido de auditoría
			{
				//Añadimos la imagen de carga en el contenedor
					$('#cargando').html('<div><center><img src="/bgrcdemo/assets/img/loading.gif" width="19" height="19"/></center></div>');
				//se obtienen controles asociados a los riesgos presentes en el plan de prueba seleccionado
					//primero obtenemos controles asociados a los riesgos de negocio

					//obtenemos programas y pruebas de la auditoría
					$.get('auditorias.get_audit_program2.'+$("#audit").val(), function (result) {
							//si es que hay pruebas que ejecutar
							if (result.length > 2)
							{	
									//alert(result);
									$("#btn_guardar").prop('disabled',false);
									$("#cargando").html('<br>');
									$("#audit_programs").empty();

									//parseamos datos obtenidos
									var datos = JSON.parse(result);
									var cont = 1; //contador de programas
									audit_tests_id = []; //array con id de pruebas para guardar en PHP 
									programs_id = []; //array con id de programas para guardar en PHP
									//seteamos datos en select de auditorías
									$(datos).each( function() {

										programs_id.push(this.id);
										$("#audit_programs").append('<h4>' + this.name +'</h4><div style="cursor:hand" onclick="vermas('+this.id+')"><font color="CornflowerBlue"><u>Ver pruebas</u></font></div><hr>');

										//agregamos las pruebas con sus estados y posibles resultados
										
										var pruebas = '<div id="audit_tests_'+this.id+'" style="display: none;">';
										pruebas += '<table class="table table-bordered table-striped table-hover table-heading table-datatable">';
										pruebas += '<thead><th width="12%">Prueba</th><th width="20%">Descripci&oacute;n</th><th width="15%">Responsable</th><th width="13%">Estado</th>';
										pruebas += '<th width="20%">Resultado</th></thead>';
										$(this.audit_tests).each( function(i, test) {
											audit_tests_id.push(this.id);
											pruebas += '<tr><td>'+test.name+'</td>';
											pruebas += '<td>'+test.description+'</td>';

											if (test.stakeholder == null)
											{
												pruebas += '<td>No se ha registrado responsable</td>';
											}
											else
											{
												pruebas += '<td>'+test.stakeholder+'</td>';
											}

											
											if (test.status == 0)
											{
												pruebas += '<td><div class="col-sm-8"><select class="form-control" name="status_'+test.id+'" id="status_'+test.id+'" onchange=\'result('+test.id+','+test.results+',null,null,null)\'>';
												pruebas += '<option value="0" selected>Abierta</option>';
												pruebas += '<option value="2">Cerrada</option>';
											}
											else if (test.status == 2)
											{
												pruebas += '<td><div class="col-sm-8"><select class="form-control" name="status_'+test.id+'" id="status_'+test.id+'" onchange=\'result('+test.id+','+test.results+','+test.hh_real+',"'+test.comments+'","'+test.files+'")\'>';
												pruebas += '<option value="0">Abierta</option>';
												pruebas += '<option value="2" selected>Cerrada</option>';
												//alert(activity.results);
												pruebas += '<script>result('+test.id+','+test.results+','+test.hh_real+',"'+test.comments+'","'+test.files+'")</script>'
											}

											//ACTUALIZACIÓN 02-11: Se eliminó resultado de prueba en ejecución (pero no de la base de datos para evitar inconsistencias),
											//por lo que seguiremos comprobando que el estado de la prueba no sea este, solo que lo mostraremos como abierta
											else if (test.status == 1)
											{
												pruebas += '<option value="0" selected>Abierta</option>';
												pruebas += '<option value="2">Cerrada</option>';
											}
											
											pruebas += '</select></div><br><div class="col-sm-8" id="boton_add_'+test.id+'"></div></td>';
											pruebas += '<td><div id="results_'+this.id+'" style="display: none;"></div></td></tr>';

										});
										pruebas += '<div style="cursor:hand" onclick="ocultar('+this.id+')"><font color="CornflowerBlue"><u>Ocultar</u></font></div>';
										pruebas += '</div>';
										
										$('#audit_programs').append(pruebas);
								});

								//agregamos id de pruebas
									input_pruebas = '<input type="hidden" value="'+audit_tests_id+'" name="id_pruebas[]">';

									//agregamos id de programas
									input_programas = '<input type="hidden" value="'+programs_id+'" name="programs_id[]">';

									$('#audit_programs').append(input_pruebas);
									$('#audit_programs').append(input_programas);	
							}
							else
							{
								$("#audit_programs").empty();
								$("#btn_guardar").prop('disabled',true);
								$("#cargando").html("<br>");
							}
					
					});

			}
			else
			{
				$("#audit").empty();
			}

});

//función para ver las actividades de una prueba
function vermas(id)
{
	$("#audit_tests_"+id).show(500);
}

//desactiva las actividades de una prueba
function ocultar(id)
{
	$("#audit_tests_"+id).hide(500);
}

//agrega select de resultado de una prueba (efectiva o inefectiva), en el caso de que ésta haya sido señalada como cerrada.
//En caso de ser inefectiva, se debe agregar los campos para issue y para agregar evidencias
function result(id,result,hh_real,comments,files)
{
	if (files == 'undefined' || files == null || files == '')
	{
		filestemp = null
	}
	else
	{
		//dividimos files por la coma
		filestemp = files.split(',')
	}
	

	if ($("#status_"+id).val() == 2)
	{
		var resultado = '<div class="col-sm-8"><select class="form-control" name="test_result_'+id+'" id="test_result_'+id+'"';
		if (comments == null)
		{
			resultado += 'onchange="testResult('+id+','+hh_real+',null)" required>';
		}
		else
		{
			resultado += 'onchange="testResult('+id+','+hh_real+',"'+comments+'")" required>';
		}
		

		//seteamos resultado previo si es que existe
		if (result == 0)
		{
			resultado += '<option value="">- Seleccione resultado -</option>';
			resultado += '<option value="0" selected>Inefectiva</option>';
			resultado += '<option value="1">Efectiva</option>';

			resultado += '<script>testResult('+id+','+hh_real+',"");</script>';
		}
		else if (result == 1)
		{
			resultado += '<option value="">- Seleccione resultado -</option>';
			resultado += '<option value="0">Inefectiva</option>';
			resultado += '<option value="1" selected>Efectiva</option>';

			resultado += '<script>testResult('+id+','+hh_real+',"'+comments+'");</script>';
		}
		else
		{
			resultado += '<option value="">- Seleccione resultado -</option>';
			resultado += '<option value="0">Inefectiva</option>';
			resultado += '<option value="1">Efectiva</option></div>';
		}

		resultado += '</select>';
		resultado += '<div id="issues_'+id+'" class="col-sm-12"></div>'

		if (filestemp != null)
		{
			resultado += '<div class="col-sm-6 control-label"></br>';
			resultado += '<label><b>Docs. de trabajo</b></label></div>';
			$(filestemp).each( function(i,file) {

				filetemp = file.split('.')
				filename = filetemp[0].split('/')
				filename = filename[2]

				resultado += '<div class="col-sm-6 control-label"></br>';
				resultado += '<label></label></div>';
				resultado += '<div class="col-sm-6">'

				if (filetemp[1] == 'jpg' || filetemp[1] == 'jpeg' || filetemp[1] == 'JPG')
				{
					resultado += '<div class="col-sm-6"><a href="../storage/app/'+file+'" download><img src="assets/img/jpg.png" width="30" height="30" />'+filename+'</a></div>'
				}
				else if (filetemp[1] == 'pdf')
				{

					resultado += '<div class="col-sm-6"><a href="/bgrcdemo/storage/app/'+file+'" download><img src="assets/img/pdf.png" width="30" height="30" />'+filename+'</a></div>'
				}
				else if (filetemp[1] == 'doc' || filetemp[1] == 'docx')
				{
					resultado += '<div class="col-sm-6"><a href="../storage/app/'+file+'" download><img src="assets/img/word.png" width="30" height="30" />'+filename+'</a></div>'
				}
				else if (filetemp[1] == 'xls' || filetemp[1] == 'xlsx')
				{
					resultado += '<div class="col-sm-6"><a href="../storage/app/'+file+'" download><img src="assets/img/excel.png" width="30" height="30" />'+filename+'</a></div>'
				}
				else if (filetemp[1] == 'ppt' || filetemp[1] == 'pptx')
				{
					resultado += '<div class="col-sm-6"><a href="../storage/app/'+file+'" download><img src="assets/img/powerpoint.png" width="30" height="30" />'+filename+'</a></div>'
				}
				else if (filetemp[1] == 'png' || filetemp[1] == 'PNG')
				{
					resultado += '<div class="col-sm-6"><a href="../storage/app/'+file+'" download><img src="assets/img/png.png" width="30" height="30" />'+filename+'</a></div>'
				}
				else
				{
					resultado += '<div class="col-sm-6"><a href="../storage/app/'+file+'" download><img src="assets/img/desconocido.png" width="30" height="30" />'+filename+'</a></div>'
				}

				if (rol == 1)
				{
					resultado += '<img src="assets/img/btn_eliminar2.png" style="width:20px; height:20px;" onclick="eliminar_ev('+id+',8,\''+filename+'\')"><br/>'
				}
				

				resultado += '</div>'
			});
		}
		

		resultado += '<br><br>'
		
		//resultado += '<label for="file" class="col-sm-2 control-label">Opcionalmente, puede cargar documentos</label>'
		resultado += '<div class="col-sm-2"><br>'
		resultado += '<input id="file-1" type="file" class="file" name="evidence_doc_'+id+'[]" multiple=true data-preview-file-type="any">'
		resultado += '</div>'

		resultado += '<div class="col-sm-2"><br><br><br>'
		resultado += '<input class="btn btn-success" id="btn_guardar" type="submit" value="Guardar">'
		resultado += '</div>'

		$("#results_"+id).append(resultado);
		$("#results_"+id).show(500);
	}
	else
	{
		$("#results_"+id).empty();
	}
}

//declaramos contador para issues
contador = 0;
//agrega campo de issue de una prueba en caso de que esta haya sido mencionada como inefectiva
//ACTUALIZACIÓN 25-10-16: Sacaremos campo de issue y botones de agregar más issues para ordenar un poco la interfaz gráfica: Sólo agregaremos botón para ir a gestionar Issues
//ACTUALIZACIÓN 24-05-17: Agregamos comentarios en caso de ser efectiva 
function testResult(id,hh,comments)
{
	if (hh != undefined && hh != "null")
	{
		resultado = '</br><div class="col-sm-6 control-label"><label for="hh_real_'+id+'"><b>HH utilizadas</b></label></div><div class="col-sm-6"><input type="text" name="hh_real_'+id+'" class="form-control" value="'+hh+'"></input></div>'
	}
	else
	{
		resultado = '</br><div class="col-sm-6 control-label"><label for="hh_real_'+id+'"><b>HH utilizadas</b></label></div><div class="col-sm-6"><input type="text" name="hh_real_'+id+'" class="form-control" placeholder="Ingrese Horas-hombre"></input></div>'
	}
	
	if ($("#test_result_"+id).val() == 0) //el resultado de la prueba es inefectivo
	{
		$("#issues_"+id).empty();
		resultado += '</br></br><a href="#" onclick="gestionarHallazgos('+id+')" class="btn btn-info">Gestionar Hallazgos</button>';
		$("#issues_"+id).append(resultado);
	}
	else if ($("#test_result_"+id).val() == 1)
	{
		$("#issues_"+id).empty();
		$("#boton_add_"+id).empty();
		new_issues = 0;
		resultado += '</br></br><div class="col-sm-6 control-label">';
		resultado += '<label for="comments_'+id+'"><b>Comentarios</b></label></div>';
		resultado += '<div class="col-sm-6">';

		if (comments != "null" && comments != undefined)
		{
			resultado += '<textarea name="comments_'+id+'" class="form-control">'+comments+'</textarea>';
		}
		else
		{
			resultado += '<textarea name="comments_'+id+'" class="form-control"></textarea>';
		}

		resultado += '</div>';

		$("#issues_"+id).append(resultado);
	}
	else
	{
		$("#issues_"+id).empty();
		$("#boton_add_"+id).empty();
	}

	
}