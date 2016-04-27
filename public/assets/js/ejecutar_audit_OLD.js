$("#audit_plans").change(function() {
			
			if ($("#audit_plans").val() != '') //Si es que se ha seleccionado valor válido de plan
			{
				//Añadimos la imagen de carga en el contenedor
					$('#cargando').html('<div><center><img src="../public/assets/img/loading.gif" width="19" height="19"/></center></div>');
				//se obtienen controles asociados a los riesgos presentes en el plan de prueba seleccionado
					//primero obtenemos controles asociados a los riesgos de negocio

					//obtenemos auditorias relacionadas al plan seleccionado
					$.get('auditorias.auditorias.'+$("#audit_plans").val(), function (result) {

							$("#cargando").html('<br>');
							$("#audit").empty();

							//parseamos datos obtenidos
							var datos = JSON.parse(result);
							$("#audit").append('<option value="" disabled selected>- Seleccione -</option>');
							//seteamos datos en select de auditorías
							$(datos).each( function() {
								$("#audit").append('<option value="' + this.id + '">' + this.name +'</option>');
							});
	
					});

			}
			else
			{
				$("#audit").empty();
			}
		});


$("#audit").change(function() {
			if ($("#audit").val() != '') //Si es que se ha seleccionado valor válido de auditoría
			{
				//Añadimos la imagen de carga en el contenedor
					$('#cargando').html('<div><center><img src="../public/assets/img/loading.gif" width="19" height="19"/></center></div>');
				//se obtienen controles asociados a los riesgos presentes en el plan de prueba seleccionado
					//primero obtenemos controles asociados a los riesgos de negocio

					//obtenemos pruebas relacionadas a la auditoría
					$.get('auditorias.get_audit_tests2.'+$("#audit").val(), function (result) {
							//si es que hay pruebas que ejecutar
							if (result.length > 2)
							{	
									$("#btn_guardar").prop('disabled',false);
									$("#cargando").html('<br>');
									$("#audit_tests").empty();

									//parseamos datos obtenidos
									var datos = JSON.parse(result);
									var cont = 1; //contador de pruebas
									activities_id = []; //array con id de actividades para guardar en PHP 
									tests_id = []; //array con id de pruebas para guardar en PHP
									//seteamos datos en select de auditorías
									$(datos).each( function() {

										tests_id.push(this.id);
										$("#audit_tests").append('<h4>' + this.name +'</h4><div style="cursor:hand" onclick="vermas('+this.id+')"><font color="CornflowerBlue"><u>Ver pruebas</u></font></div><hr>');

										//agregamos las actividades con sus estados y posibles resultados
										
										var actividades = '<div id="activities_'+this.id+'" style="display: none;">';
										actividades += '<table class="table table-bordered table-striped table-hover table-heading table-datatable">';
										actividades += '<thead><th>Prueba</th><th>Estado</th><th>Resultado</th></thead>';
										$(this.activities).each( function(i, activity) {
											activities_id.push(this.id);
											actividades += '<tr><td>'+activity.name+'</td>';
											actividades += '<td><div class="col-sm-8"><select class="form-control" name="status_'+activity.id+'" id="status_'+activity.id+'" onchange="result('+activity.id+','+activity.results+')">';

											if (activity.status == 0)
											{
												actividades += '<option value="0" selected>Abierta</option>';
												actividades += '<option value="1">En ejecución</option>';
												actividades += '<option value="2">Cerrada</option>';
											}
											else if (activity.status == 1)
											{
												actividades += '<option value="0">Abierta</option>';
												actividades += '<option value="1" selected>En ejecución</option>';
												actividades += '<option value="2">Cerrada</option>';
											}
											else if (activity.status == 2)
											{
												actividades += '<option value="0" selected>Abierta</option>';
												actividades += '<option value="1">En ejecución</option>';
												actividades += '<option value="2" selected>Cerrada</option>';
												//alert(activity.results);
												actividades += '<script>result('+activity.id+','+activity.results+')</script>'
											}
											
											actividades += '</select></div></td>';
											actividades += '<td><div id="results_'+this.id+'" style="display: none;"></div></td></tr>';

										});
										actividades += '<div style="cursor:hand" onclick="ocultar('+this.id+')"><font color="CornflowerBlue"><u>Ocultar</u></font></div>';
										actividades += '</div>';
										$('#audit_tests').append(actividades);

										//agregamos campo de texto para resultados (issues) de la auditoría

										var test_results = '<h5>Resultados programa de auditor&iacute;a:</h5>';
										test_results += '<table class="table">'
										test_results += '<td width="50%"><select class="form-control" name="test_result_'+this.id+'" id="test_result_'+this.id+'" onchange="testResult('+this.id+')">';

										if (this.results == 0)
										{
											test_results += '<option value="Abierta">En proceso</option>';
											test_results += '<option value="0" selected>Inefectivo</option>';
											test_results += '<option value="1">Efectivo</option>';
											test_results += '</select><br>';
											
										}
										else if (this.results == 1)
										{
											test_results += '<option value="Abierta">En proceso</option>';
											test_results += '<option value="0">Inefectivo</option>';
											test_results += '<option value="1" selected>Efectivo</option>';
											test_results += '</select><br>';
										}
										else
										{
											test_results += '<option value="Abierta" selected>En proceso</option>';
											test_results += '<option value="0">Inefectivo</option>';
											test_results += '<option value="1">Efectivo</option>';
											test_results += '</select><br>';
										}
										//agregamos DIV para botón de agregar más issues
										test_results += '<div id="boton_add_'+this.id+'"></div>';
										test_results += '</td><td width="50%"><div id="issues_'+this.id+'"></div></td>';

										//ejecutamos función que muestra datos de issue
										test_results += '<script>testResult('+this.id+');</script>';
										test_results += '</table>';

												$('#audit_tests').append(test_results);

												cont = cont+1;

											});

											//agregamos id de activities
											input_actividades = '<input type="hidden" value="'+activities_id+'" name="id_activities[]">';

											//agregamos id de pruebas
											input_pruebas = '<input type="hidden" value="'+tests_id+'" name="tests_id[]">';

											$('#audit_tests').append(input_actividades);
											$('#audit_tests').append(input_pruebas);
					
									
							}
							else
							{
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
	$("#activities_"+id).show(500);
}

//desactiva las actividades de una prueba
function ocultar(id)
{
	$("#activities_"+id).hide(500);
}

//agrega select de resultado de una actividad (efectiva o inefectiva), en el caso de que ésta haya sido señalada como cerrada
function result(id,result)
{
	if ($("#status_"+id).val() == 2)
	{
		var resultado = '<div class="col-sm-8"><select class="form-control" name="result_'+id+'" required>';

		//seteamos resultado previo si es que existe
		if (result == 0)
		{
			resultado += '<option value="">- Seleccione resultado -</option>';
			resultado += '<option value="0" selected>Inefectiva</option>';
			resultado += '<option value="1">Efectiva</option></div>';
		}
		else if (result == 1)
		{
			resultado += '<option value="">- Seleccione resultado -</option>';
			resultado += '<option value="0">Inefectiva</option>';
			resultado += '<option value="1" selected>Efectiva</option></div>';
		}
		else
		{
			resultado += '<option value="">- Seleccione resultado -</option>';
			resultado += '<option value="0">Inefectiva</option>';
			resultado += '<option value="1">Efectiva</option></div>';
		}

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
function testResult(id)
{

	if ($("#test_result_"+id).val() == 0)
	{
		//obtenemos issues si es que existen
		$.get('auditorias.get_issue.'+id, function (result) {
			//alert(result);
			//agregamos botón para añadir mas issues en div de botón
			var result_boton = '<button type="button" class="btn btn-primary" onclick="addNewIssue('+id+')">Agregar nuevo hallazgo</button>';
			$("#boton_add_"+id).html(result_boton);

			if (result == "null") //no existe issue
			{
				//agregamos nuevo issue
				addNewIssue(id);
			}

			else
			{
				//parseamos datos obtenidos
				var datos = JSON.parse(result);

				issues_id = []; //array con id de issues para guardar en PHP 
				//contador de issues existentes
				contador = 1;
				resultado = '';
				$(datos).each( function() {
					issues_id.push(this.id);
					//alert("Entramos2");
					resultado += '<b>Hallazgo N° '+contador+'</b><br>';
					resultado += '<select class="form-control" name="issue_classification_'+this.id+'" required>';

					if (this.classification == 0)
					{
						resultado += '<option value="" disabled>- Clasificación debilidad -</option>';
						resultado += '<option value="0" selected>Oportunidad de mejora</option>';
						resultado += '<option value="1">Deficiencia</option>';
						resultado += '<option value="2">Debilidad significativa</option></select><br>';
					}
					else if (this.classification == 1)
					{
						resultado += '<option value="" disabled>- Clasificación debilidad -</option>';
						resultado += '<option value="0">Oportunidad de mejora</option>';
						resultado += '<option value="1" selected>Deficiencia</option>';
						resultado += '<option value="2">Debilidad significativa</option></select><br>';
					}
					else if (this.classification == 2)
					{
						resultado += '<option value="" disabled>- Clasificación debilidad -</option>';
						resultado += '<option value="0">Oportunidad de mejora</option>';
						resultado += '<option value="1">Deficiencia</option>';
						resultado += '<option value="2" selected>Debilidad significativa</option></select><br>';
					}
					
					resultado += '<input type="text" class="form-control" name="issue_name_'+this.id+'" value="'+this.name+'" required placeholder="Nombre de debilidad"><br>'; 
					if (this.description == "")
					{
						resultado += '<textarea rows="3" cols="4" class="form-control" name="issue_description_'+this.id+'" placeholder="Descripción de debilidad (opcional)"></textarea><br>';
					}
					else
					{
						resultado += '<textarea rows="3" cols="4" class="form-control" name="issue_description_'+this.id+'" value="'+this.description+'" placeholder="'+this.description+'"></textarea><br>';
					}
					
					if (this.recommendations == "")
					{
						resultado += '<textarea rows="3" cols="4" class="form-control" name="issue_recommendations_'+this.id+'" placeholder="Recomendaciones (opcional)"></textarea><br>';
					}
					else
					{
						resultado += '<textarea rows="3" cols="4" class="form-control" name="issue_recommendations_'+this.id+'" value="'+this.recommendations+'" placeholder="'+this.recommendations+'"></textarea><br>';
					}

					resultado += '<hr>';
					contador++;
				});

				//agregamos id de issues para el id del programa
				resultado += '<input type="hidden" value="'+issues_id+'" name="'+id+'_issues[]">';
				
				$("#issues_"+id).append(resultado);


			}
			
		});
	}
	else
	{
		$("#issues_"+id).empty();
	}
}
//declaramos contador de nuevas issues
new_issues = 0;
//función para agregar un nuevo issue
function addNewIssue(id)
{
	new_issues++;
	var resultado = '<div id="new_issue_'+new_issues+'"><b>Nuevo hallazgo N° '+new_issues+'</b><br>';
	//solo si es el primer new issue haremos ALGUNOS de los campos required (los otros siempre aceptan nulos)
	if (new_issues == 1)
	{
		resultado += '<select class="form-control" name="new_issue_classification'+new_issues+'_'+id+'" required>';
		resultado += '<option value="" disabled selected>- Clasificación debilidad -</option>';
		resultado += '<option value="0">Oportunidad de mejora</option>';
		resultado += '<option value="1">Deficiencia</option>';
		resultado += '<option value="1">Debilidad significativa</option></select><br>';

		resultado += '<input type="text" class="form-control" name="new_issue_name'+new_issues+'_'+id+'" required placeholder="Nombre de debilidad"><br>'; 
	}
	else
	{
		resultado += '<select class="form-control" name="new_issue_classification'+new_issues+'_'+id+'">';
		resultado += '<option value="" disabled selected>- Clasificación debilidad -</option>';
		resultado += '<option value="0">Oportunidad de mejora</option>';
		resultado += '<option value="1">Deficiencia</option>';
		resultado += '<option value="1">Debilidad significativa</option></select><br>';

		resultado += '<input type="text" class="form-control" name="new_issue_name'+new_issues+'_'+id+'" placeholder="Nombre de debilidad"><br>'; 
	}

	
	resultado += '<textarea rows="3" cols="4" class="form-control" name="new_issue_description'+new_issues+'_'+id+'" placeholder="Descripción de debilidad (opcional)"></textarea><br>';
	resultado += '<textarea rows="3" cols="4" class="form-control" name="new_issue_recommendations'+new_issues+'_'+id+'" placeholder="Recomendaciones (opcional)"></textarea></div><br>';

	resultado += '<script>$("html,body").animate({scrollTop: $("#new_issue_'+new_issues+'").offset().top}, 900); </script>';
	$("#issues_"+id).append(resultado);

}