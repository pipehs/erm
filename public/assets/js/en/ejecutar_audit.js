$("#audit_plans").change(function() {
			
			if ($("#audit_plans").val() != '') //Si es que se ha seleccionado valor válido de plan
			{
				//Añadimos la imagen de carga en el contenedor
					$('#cargando').html('<div><center><img src="/assets/img/loading.gif" width="19" height="19"/></center></div>');
				//se obtienen controles asociados a los riesgos presentes en el plan de prueba seleccionado
					//primero obtenemos controles asociados a los riesgos de negocio

					//obtenemos auditorias relacionadas al plan seleccionado
					$.get('auditorias.auditorias.'+$("#audit_plans").val(), function (result) {

							$("#cargando").html('<br>');
							$("#audit").empty();

							//parseamos datos obtenidos
							var datos = JSON.parse(result);
							$("#audit").append('<option value="" disabled selected>- Select -</option>');
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
					$('#cargando').html('<div><center><img src="/assets/img/loading.gif" width="19" height="19"/></center></div>');
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
										$("#audit_programs").append('<h4>' + this.name +'</h4><div style="cursor:hand" onclick="vermas('+this.id+')"><font color="CornflowerBlue"><u>Show tests</u></font></div><hr>');

										//agregamos las pruebas con sus estados y posibles resultados
										
										var pruebas = '<div id="audit_tests_'+this.id+'" style="display: none;">';
										pruebas += '<table class="table table-bordered table-striped table-hover table-heading table-datatable">';
										pruebas += '<thead><th>Test</th><th>Description</th><th>Responsable</th><th>Status</th>';
										pruebas += '<th>Result</th></thead>';
										$(this.audit_tests).each( function(i, test) {
											audit_tests_id.push(this.id);
											pruebas += '<tr><td>'+test.name+'</td>';
											pruebas += '<td>'+test.description+'</td>';
											if (test.stakeholder == null)
											{
												pruebas += '<td>No stakeholder added</td>';
											}
											else
											{
												pruebas += '<td>'+test.stakeholder+'</td>';
											}
											pruebas += '<td><div class="col-sm-8"><select class="form-control" name="status_'+test.id+'" id="status_'+test.id+'" onchange="result('+test.id+','+test.results+')">';

											if (test.status == 0)
											{
												pruebas += '<option value="0" selected>Open</option>';
												pruebas += '<option value="1">On execution</option>';
												pruebas += '<option value="2">Closed</option>';
											}
											else if (test.status == 1)
											{
												pruebas += '<option value="0">Open</option>';
												pruebas += '<option value="1" selected>On execution</option>';
												pruebas += '<option value="2">Closed</option>';
											}
											else if (test.status == 2)
											{
												pruebas += '<option value="0" selected>Open</option>';
												pruebas += '<option value="1">On execution</option>';
												pruebas += '<option value="2" selected>Closed</option>';
												pruebas += '<script>result('+test.id+','+test.results+')</script>'
											}
											
											pruebas += '</select></div><br><div class="col-sm-8" id="boton_add_'+test.id+'"></div></td>';
											pruebas += '<td><div id="results_'+this.id+'" style="display: none;"></div></td></tr>';

										});
										pruebas += '<div style="cursor:hand" onclick="ocultar('+this.id+')"><font color="CornflowerBlue"><u>Hide</u></font></div>';
										pruebas += '</div>';
										
										$('#audit_programs').append(pruebas);

									//agregamos id de pruebas
									input_pruebas = '<input type="hidden" value="'+audit_tests_id+'" name="id_pruebas[]">';

									//agregamos id de programas
									input_programas = '<input type="hidden" value="'+programs_id+'" name="programs_id[]">';

									$('#audit_programs').append(input_pruebas);
									$('#audit_programs').append(input_programas);
								});	
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
	$("#audit_tests_"+id).show(500);
}

//desactiva las actividades de una prueba
function ocultar(id)
{
	$("#audit_tests_"+id).hide(500);
}

//agrega select de resultado de una prueba (efectiva o inefectiva), en el caso de que ésta haya sido señalada como cerrada.
//En caso de ser inefectiva, se debe agregar los campos para issue y para agregar evidencias
function result(id,result)
{
	if ($("#status_"+id).val() == 2)
	{
		var resultado = '<div class="col-sm-8"><select class="form-control" name="test_result_'+id+'" id="test_result_'+id+'"';
		resultado += 'onchange="testResult('+id+')" required>';

		//seteamos resultado previo si es que existe
		if (result == 0)
		{
			resultado += '<option value="">- Select result -</option>';
			resultado += '<option value="0" selected>Ineffective</option>';
			resultado += '<option value="1">Effective</option></div>';
			resultado += '<script>testResult('+id+');</script>';
		}
		else if (result == 1)
		{
			resultado += '<option value="">- Select result -</option>';
			resultado += '<option value="0">Ineffective</option>';
			resultado += '<option value="1" selected>Effective</option></div>';
		}
		else
		{
			resultado += '<option value="">- Select result -</option>';
			resultado += '<option value="0">Ineffective</option>';
			resultado += '<option value="1">Effective</option></div>';
		}

		resultado += '</select><hr><br>';
		resultado += '<div id="issues_'+id+'" class="col-sm-12"></div>'
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

	if ($("#test_result_"+id).val() == 0 && $("#test_result_"+id).val() != "") //el resultado de la prueba es inefectivo
	{
		//obtenemos issues si es que existen
		$.get('auditorias.get_issue.'+id, function (result) {
			//alert(result);
			//agregamos botón para añadir mas issues en div de botón
			var result_boton = '<br><hr><button type="button" class="btn btn-primary btn-xs" onclick="addNewIssue('+id+')">Add new issue</button>';
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
					//alert(this.evidences)
					issues_id.push(this.id);
					//alert("Entramos2");
					resultado += '<b>Issue N° '+contador+'</b><br>';
					resultado += '<select class="form-control" name="issue_classification_'+this.id+'" required>';

					if (this.classification == 0)
					{
						resultado += '<option value="" disabled>- Weakness Classification -</option>';
						resultado += '<option value="0" selected>Improvement opportunity</option>';
						resultado += '<option value="1">Deficiency</option>';
						resultado += '<option value="2">Significant weakness</option></select><br>';
					}
					else if (this.classification == 1)
					{
						resultado += '<option value="" disabled>- Weakness Classification -</option>';
						resultado += '<option value="0">Improvement opportunity</option>';
						resultado += '<option value="1" selected>Deficiency</option>';
						resultado += '<option value="2">Significant weakness</option></select><br>';
					}
					else if (this.classification == 2)
					{
						resultado += '<option value="" disabled>- Weakness Classification -</option>';
						resultado += '<option value="0">Improvement opportunity</option>';
						resultado += '<option value="1">Deficiency</option>';
						resultado += '<option value="2" selected>Significant weakness</option></select><br>';
					}
					
					resultado += '<input type="text" class="form-control" name="issue_name_'+this.id+'" value="'+this.name+'" required placeholder="Issue name"><br>'; 
					if (this.description == "")
					{
						resultado += '<textarea rows="3" cols="4" class="form-control" name="issue_description_'+this.id+'" placeholder="Issue description (optional)"></textarea><br>';
					}
					else
					{
						resultado += '<textarea rows="3" cols="4" class="form-control" name="issue_description_'+this.id+'" value="'+this.description+'" placeholder="'+this.description+'"></textarea><br>';
					}
					
					if (this.recommendations == "")
					{
						resultado += '<textarea rows="3" cols="4" class="form-control" name="issue_recommendations_'+this.id+'" placeholder="Recommendations (optional)"></textarea><br>';
					}
					else
					{
						resultado += '<textarea rows="3" cols="4" class="form-control" name="issue_recommendations_'+this.id+'" value="'+this.recommendations+'" placeholder="'+this.recommendations+'"></textarea><br>';
					}

					if (this.evidences == null)
					{
						resultado += '<input type="file" id="file'+this.id+'" class="inputfile" name="issue_evidence_'+this.id+'"/><label for="file'+this.id+'">Upload Evidence (optional)</label></div><br>';
					}
					else
					{
						$(this.evidences).each( function(i,evidence) {
							resultado += '<div style="cursor:hand" id="descargar_'+this.id+'" onclick="descargar(2,\''+evidence.url+'\')"><font color="CornflowerBlue"><u>Download Evdence</u></font></div><br>';
						});
					}

					resultado += '<br>';
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
		$("#boton_add_"+id).empty();
		new_issues = 0;
	}
}
//declaramos contador de nuevas issues
//new_issues = 0;
//función para agregar un nuevo issue
function addNewIssue(id)
{
	
	new_issues = 1;
	//vemos número de issue para cada id de prueba
	while (1) 
	{
		if ($('#new_issue_'+id+'_'+new_issues).length > 0)
		{
			new_issues++;
		}
		else
		{
			break;
		}
	}

	var resultado = '<div id="new_issue_'+id+'_'+new_issues+'"><b>Nuevo hallazgo N° '+new_issues+'</b><br>';
	//solo si es el primer new issue haremos ALGUNOS de los campos required (los otros siempre aceptan nulos)
	if (new_issues == 1)
	{
		resultado += '<select class="form-control" name="new_issue_classification_'+id+'_'+new_issues+'" required>';
		resultado += '<option value="" disabled selected>- Weakness classification -</option>';
		resultado += '<option value="0">Improvement opportunity</option>';
		resultado += '<option value="1">Deficiency</option>';
		resultado += '<option value="1">Significant weakness</option></select><br>';

		resultado += '<input type="text" class="form-control" name="new_issue_name_'+id+'_'+new_issues+'" required placeholder="Issue name"><br>'; 
	}
	else
	{
		resultado += '<select class="form-control" name="new_issue_classification_'+id+'_'+new_issues+'">';
		resultado += '<option value="" disabled selected>- Weakness classification -</option>';
		resultado += '<option value="0">Improvement opportunity</option>';
		resultado += '<option value="1">Deficiency</option>';
		resultado += '<option value="1">Significant weakness</option></select><br>';

		resultado += '<input type="text" class="form-control" name="new_issue_name_'+id+'_'+new_issues+'" placeholder="Nombre de debilidad"><br>'; 
	}

	
	resultado += '<textarea rows="3" cols="4" class="form-control" name="new_issue_description_'+id+'_'+new_issues+'" placeholder="Weakness description (optional)"></textarea><br>';
	resultado += '<textarea rows="3" cols="4" class="form-control" name="new_issue_recommendations_'+id+'_'+new_issues+'" placeholder="Recommendations (optional)"></textarea></div><br>';
	resultado += '<input type="file" id="file'+id+'" class="inputfile" name="new_issue_evidence_'+id+'_'+new_issues+'"/><label for="file'+id+'">Upload Evidence (optional)</label></div><br>';

	resultado += '<script>$("html,body").animate({scrollTop: $("#new_issue_'+id+'_'+new_issues+'").offset().top}, 900); </script>';
	$("#issues_"+id).append(resultado);

}