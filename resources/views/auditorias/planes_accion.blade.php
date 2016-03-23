@extends('master')

@section('title', 'Auditor&iacute;as - Planes de acci&oacute;n')

@stop

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="auditorias">Auditor&iacute;as</a></li>
			<li><a href="planes_accion">Planes de acci&oacute;n</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-sm-8">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Planes de acci&oacute;n</span>
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
	      	<p>En esta secci&oacute;n podr&aacute; agregar planes de acci&oacute;n. Agregue s&oacute;lo un plan de acci&oacute;n a la vez.</p>

				@if(Session::has('message'))
					<div class="alert alert-success alert-dismissible" role="alert">
					{{ Session::get('message') }}
					</div>
				@endif
				@if(Session::has('error'))
					<div class="alert alert-danger alert-dismissible" role="alert">
					{{ Session::get('error') }}
					</div>
				@endif

				<div id="cargando"><br></div>

				{!!Form::open(['route'=>'agregar_plan2','method'=>'POST','class'=>'form-horizontal','id'=>'form',
				'enctype'=>'multipart/form-data'])!!}
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

					//obtenemos pruebas relacionadas a la auditoría seleccionada
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
								var audit_test = '<h4><b>' + this.name +'</b></h4>';
								audit_test += '<b>Debilidades encontradas</b><hr>';
								$(this.issues).each( function(i,issue) {
									audit_test += '<li>Clasificación: '+issue.classification+'</li>';
									audit_test += '<li>Nombre: '+issue.name+'</li>';
									audit_test += '<li>Descripción: '+issue.description+'</li>';
									audit_test += '<li>Recomendaciones: '+issue.recommendations+'</li>';
									audit_test += '<div style="cursor:hand" id="btn_crear_'+issue.id+'" onclick="crear_plan('+issue.id+')" class="btn btn-default">Agregar plan de acción</div>';

									audit_test += '<div id="nuevo_plan_'+issue.id+'" style="display: none;"></div>';
									audit_test += '<hr>';

								})
								

								audit_test += '<div style="cursor:hand" id="btn_notas_'+this.id+'" onclick="notas('+this.id+')" class="btn btn-success">Notas</div> ';

								$("#audit_tests").append(audit_test);

								$("#audit_tests").append('<div id="notas_'+this.id+'" style="display: none;"></div>');

								//cont = cont+1;

							});

							//agregamos id de activities
							//input_actividades = '<input type="hidden" value="'+activities_id+'" name="id_activities[]">';

							//agregamos id de pruebas
							//input_pruebas = '<input type="hidden" value="'+tests_id+'" name="tests_id[]">';

							//$('#audit_tests').append(input_actividades);
							//$('#audit_tests').append(input_pruebas);
	
					});

			}
			else
			{
				$("#audit").empty();
			}

});


function notas(id)
{
	$("#notas_"+id).empty();
	$.get('auditorias.get_notes.'+id, function (result) {

			//agregamos div de texto siguiente
			var resultado = '<div id="mensaje" style="clear: left;">';

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
					resultado += 'Estado: '+this.status+'<br>'
					resultado += '<h4>Nota: '+this.description+'</h4>';

					//agregamos evidencias
					if (this.evidences == null)
					{
						resultado += '<font color="red">Esta nota no tiene evidencias agregadas</font><br>';
					}

					else
					{

						$(this.evidences).each( function(i,evidence) {
							resultado += '<div style="cursor:hand" id="descargar_'+id+'" onclick="descargar(0,\''+evidence.url+'\')"><font color="CornflowerBlue"><u>Descargar evidencia</u></font></div><br>';
						});
					}

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

						});
					}

					resultado += '<hr style="border-style: inset; border-width: 1px;">';

				});			
				
			}

			resultado += '<div style="cursor:hand" onclick="ocultar_notas('+id+')"><font color="CornflowerBlue"><u>Ocultar</u></font></div><hr><br>';
			$("#notas_"+id).append(resultado).show(500);
			
		});
}

function ocultar_creacion(id)
{
	$("#nuevo_plan_"+id).hide(500);

	$("#btn_crear_"+id).empty();
	$("#btn_crear_"+id).attr('onclick','crear_plan('+id+')');
	$("#btn_crear_"+id).append('Agregar plan de acción');
	$("#nuevo_plan_"+id).empty();
}
//crea un plan de acción para el issue de id = id
function crear_plan(id)
{
	$("#btn_crear_"+id).empty();
	$("#btn_crear_"+id).attr('onclick','ocultar_creacion('+id+')');
	$("#btn_crear_"+id).append('Ocultar');
	//vaciamos por si existe ya algún formulario
	$("#nuevo_plan_"+id).empty();

	//obtenemos datos de algun plan existente
	$.get('auditorias.get_action_plan.'+id, function (result) {
		if (result == "null") //no existen notas
		{
			var plan = '<div class="form-group col-sm-12">';
			//agregamos atributo hidden que señalará que se está guardando una nota y otro para identificar el id de la prueba
			plan += '<input type="hidden" name="issue_id" value="'+id+'">';
			plan += '<div class="form-group col-sm-12">';
			plan += '<textarea name="description_'+id+'" rows="3" cols="4" class="form-control" placeholder="Describa el plan de acción" required></textarea></div>';

			plan += '<label class="control-label">Responsable</label>';
			plan += '<select class="form-control" name="responsable_'+id+'" required>';
			plan += '<option value="" disabled selected >- Seleccione -</option>'
			@foreach ($stakeholders as $stakeholder)
				plan += '<option value={{ $stakeholder["id"] }}>{{ $stakeholder["name"] }}</option>';
			@endforeach

			plan += '</select>';

			plan += '<label class="control-label">Ingrese fecha de término del plan de acción</label>';
			plan += '<input type="date" class="form-control" name="final_date_'+id+'"></div>';
			plan += '<div class="form-group col-sm-12">';
			plan += '<button class="btn btn-success" name="guardar_'+id+'">Guardar</button></div><hr><br>';
			$("#nuevo_plan_"+id).append(plan);
			$("#nuevo_plan_"+id).show(500);
		}

		else
		{
			//parseamos datos obtenidos
			var datos = JSON.parse(result);
				
			$(datos).each( function() {
				var plan = '<h4><b>Ya existe plan de acción.</b></h4>';
				plan += 'Los datos del plan son los siguientes: <br>';
				plan += '<div class="form-group col-sm-12">';
				//agregamos atributo hidden que señalará que se está guardando una nota y otro para identificar el id de la prueba
				plan += '<input type="hidden" name="issue_id" value="'+id+'">';
				plan += '<div class="form-group col-sm-12">';
				plan += '<textarea name="description_'+id+'" disabled rows="3" cols="4" class="form-control" placeholder="'+this.description+'"></textarea></div>';

				plan += '<label class="control-label">Responsable</label>';
				plan += '<input type="text" value="'+this.stakeholder+'" class="form-control" disabled>';

				plan += '<label class="control-label">Ingrese fecha de término del plan de acción</label>';
				plan += '<input type="date" class="form-control" name="final_date_'+id+'" disabled value="'+this.final_date+'"></div>';
				plan += '<hr><br>';
				$("#nuevo_plan_"+id).append(plan);
				$("#nuevo_plan_"+id).show(500);


			});
		}
	});

	
}
function ocultar_notas(id)
{
	$("#notas_"+id).hide(500);
}

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
}

</script>
@stop
