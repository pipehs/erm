@extends('master')

@section('title', 'Auditor&iacute;as - Ejecuci&oacute;n de auditor&iacute;as')

@stop

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="auditorias">Auditor&iacute;as</a></li>
			<li><a href="ejecutar_pruebas">Ejecuci&oacute;n de auditor&iacute;as</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-sm-8">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Ejecuci&oacute;n de auditor&iacute;as</span>
				</div>
				<div class="box-icons">
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
					<a class="expand-link">
						<i class="fa fa-expand"></i>
					</a>
					<a class="close-link">
						<i class="fa fa-times"></i>
					</a>
				</div>
				<div class="move"></div>
			</div>
			<div class="box-content box ui-draggable ui-droppable" style="top: 0px; left: 0px; opacity: 1; z-index: 1999;">
	      	<p>En esta secci&oacute;n podr&aacute; ejecutar los planes de auditor&iacute;as de riesgos.</p>

				@if(Session::has('message'))
					<div class="alert alert-success alert-dismissible" role="alert">
					{{ Session::get('message') }}
					</div>
				@endif

				<div id="cargando"><br></div>

				{!!Form::open(['route'=>'agregar_ejecucion','method'=>'POST','class'=>'form-horizontal','id'=>'form'])!!}
	      			<div class="form-group">
						{!!Form::label('Plan de auditor&iacute;a',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('audit_plan_id',$audit_plans,null, 
							 	   ['id' => 'audit_plans','required'=>'true','placeholder'=>'- Seleccione -'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Auditor&iacute;a',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							<select name="audit" id="audit" required>
								<!-- Aquí se agregarán las auditorías relacionadas al plan seleccionado a través de Jquery -->
							</select>
						</div>
					</div>

					<div id="audit_tests"></div>

					<div class="form-group">
						<center>
						{!!Form::submit('Guardar', ['class'=>'btn btn-primary'])!!}
						</center>
					</div>
					
				{!!Form::close()!!}

			</div>
		</div>
	</div>
</div>
@stop

@section('scripts2')
<script>
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
			if ($("#audit").val() != '') //Si es que se ha seleccionado valor válido de plan
			{
				//Añadimos la imagen de carga en el contenedor
					$('#cargando').html('<div><center><img src="../public/assets/img/loading.gif" width="19" height="19"/></center></div>');
				//se obtienen controles asociados a los riesgos presentes en el plan de prueba seleccionado
					//primero obtenemos controles asociados a los riesgos de negocio

					//obtenemos auditorias relacionadas al plan seleccionado
					$.get('auditorias.get_audit_tests2.'+$("#audit").val(), function (result) {

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
								$("#audit_tests").append('<h4>' + this.name +'</h4><div style="cursor:hand" onclick="vermas('+this.id+')"><font color="CornflowerBlue"><u>Ver actividades</u></font></div><hr>');

								//agregamos las actividades con sus estados y posibles resultados
								
								var actividades = '<div id="activities_'+this.id+'" style="display: none;">';
								actividades += '<table class="table table-bordered table-striped table-hover table-heading table-datatable">';
								actividades += '<thead><th>Actividad</th><th>Estado</th><th>Resultado</th></thead>';
								$(this.activities).each( function(i, activity) {

									activities_id.push(this.id);
									actividades += '<tr><td>'+activity.name+'</td>';
									actividades += '<td><div class="col-sm-8"><select class="form-control" name="status_'+this.id+'" id="status_'+this.id+'" onchange="result('+this.id+')">';

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
									}
									
									actividades += '</select></div></td>';
									actividades += '<td><div id="results_'+this.id+'" style="display: none;"></div></td></tr>';

								});
								actividades += '<div style="cursor:hand" onclick="ocultar('+this.id+')"><font color="CornflowerBlue"><u>Ocultar</u></font></div>';
								actividades += '</div>';
								$('#audit_tests').append(actividades);

								//agregamos campo de texto para resultados (issues) de la auditoría

								var test_results = '<h5>Resultados prueba de auditor&iacute;a:</h5>';
								test_results += '<table class="table">'
								test_results += '<td width="50%"><select class="form-control" name="test_result_'+this.id+'" id="test_result_'+this.id+'" onchange="testResult('+this.id+')">';

								if (this.results == 0)
								{
									test_results += '<option value="Abierta">En proceso</option>';
									test_results += '<option value="0" selected>Inefectiva</option>';
									test_results += '<option value="1">Efectiva</option>';
									test_results += '</select></td><td width="50%"><div id="issues_'+this.id+'"></div></td>';

									//------prueba------//
									/* AUN NO PUEDO MOSTRAR ISSUES CUANDO YA ESTÁN AGREGADOS (MOSTRARLOS DESDE UN COMIENZO)

									//obtenemos issues si es que existen
									$.get('auditorias.get_issue.'+this.id, function (result) {
										//alert(result);
											//parseamos datos obtenidos
											var datos = JSON.parse(result);
											//seteamos datos en select de auditorías
											

											var resultado = '<select class="form-control" name="issue_classification_'+this.id+'" required>';
											resultado += '<option value="" disabled selected>- Clasificación debilidad -</option>';
											resultado += '<option value="0">Oportunidad de mejora</option>';
											resultado += '<option value="1">Deficiencia</option>';
											resultado += '<option value="1">Debilidad significativa</option></select><br>';
											resultado += '<input type="text" class="form-control" name="issue_name_'+this.id+'" value="'+datos.name+'" required placeholder="Nombre de debilidad"><br>'; 
											resultado += '<textarea rows="3" cols="4" class="form-control" name="issue_description_'+this.id+'" value="'+datos.description+'" placeholder="'+datos.description+'"></textarea><br>';
											resultado += '<textarea rows="3" cols="4" class="form-control" name="issue_recommendations_'+this.id+'" value="'+datos.recommendations+'" placeholder="'+datos.recommendations+'"></textarea><br>';
											
										$("#issues_"+this.id).append(resultado);
										
									}); */
								}
								else if (this.results == 1)
								{
									test_results += '<option value="Abierta">En proceso</option>';
									test_results += '<option value="0">Inefectiva</option>';
									test_results += '<option value="1" selected>Efectiva</option>';
									test_results += '</select></td><td width="50%"><div id="issues_'+this.id+'"></div></td>';
								}
								else
								{
									test_results += '<option value="Abierta" selected>En proceso</option>';
									test_results += '<option value="0">Inefectiva</option>';
									test_results += '<option value="1">Efectiva</option>';
									test_results += '</select></td><td width="50%"><div id="issues_'+this.id+'"></div></td>';
								}
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
	$("#issues")
}

//desactiva las actividades de una prueba
function ocultar(id)
{
	$("#activities_"+id).hide(500);
}

//agrega select de resultado de una actividad (efectiva o inefectiva), en el caso de que ésta haya sido señalada como cerrada
function result(id)
{
	if ($("#status_"+id).val() == 2)
	{
		var resultado = '<div class="col-sm-8"><select class="form-control" name="result_'+id+'" required>';
		resultado += '<option value="">- Seleccione resultado -</option>';
		resultado += '<option value="0">Inefectiva</option>';
		resultado += '<option value="1">Efectiva</option></div>';

		$("#results_"+id).append(resultado);
		$("#results_"+id).show(500);
	}
	else
	{
		$("#results_"+id).empty();
	}
}

//agrega campo de issue de una prueba en caso de que esta haya sido mencionada como inefectiva
function testResult(id)
{
	if ($("#test_result_"+id).val() == 0)
	{
		//obtenemos issues si es que existen
		$.get('auditorias.get_issue.'+id, function (result) {
			//alert(result);
			if (result == "null") //no existe issue
			{
				//alert(result);
				var resultado = '<select class="form-control" name="issue_classification_'+id+'" required>';
				resultado += '<option value="" disabled selected>- Clasificación debilidad -</option>';
				resultado += '<option value="0">Oportunidad de mejora</option>';
				resultado += '<option value="1">Deficiencia</option>';
				resultado += '<option value="1">Debilidad significativa</option></select><br>';
				resultado += '<input type="text" class="form-control" name="issue_name_'+id+'" required placeholder="Nombre de debilidad"><br>'; 
				resultado += '<textarea rows="3" cols="4" class="form-control" name="issue_description_'+id+'" placeholder="Descripción de debilidad (opcional)"></textarea><br>';
				resultado += '<textarea rows="3" cols="4" class="form-control" name="issue_recommendations_'+id+'" placeholder="Recomendaciones (opcional)"></textarea><br>';
			}

			else
			{
				//parseamos datos obtenidos
				var datos = JSON.parse(result);
				var cont = 1; //contador de pruebas
				activities_id = []; //array con id de actividades para guardar en PHP 
				tests_id = []; //array con id de pruebas para guardar en PHP
				//seteamos datos en select de auditorías
				

				var resultado = '<select class="form-control" name="issue_classification_'+id+'" required>';
				resultado += '<option value="" disabled selected>- Clasificación debilidad -</option>';
				resultado += '<option value="0">Oportunidad de mejora</option>';
				resultado += '<option value="1">Deficiencia</option>';
				resultado += '<option value="1">Debilidad significativa</option></select><br>';
				resultado += '<input type="text" class="form-control" name="issue_name_'+id+'" value="'+datos.name+'" required placeholder="Nombre de debilidad"><br>'; 
				resultado += '<textarea rows="3" cols="4" class="form-control" name="issue_description_'+id+'" value="'+datos.description+'" placeholder="'+datos.description+'"></textarea><br>';
				resultado += '<textarea rows="3" cols="4" class="form-control" name="issue_recommendations_'+id+'" value="'+datos.recommendations+'" placeholder="'+datos.recommendations+'"></textarea><br>';
				
			}

			$("#issues_"+id).append(resultado);
			
		});
	}
	else
	{
		$("#issues_"+id).empty();
	}
}
</script>
@stop
