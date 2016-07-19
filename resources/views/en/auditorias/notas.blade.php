@extends('master')

@section('title', 'Auditor&iacute;as - Revisi&oacute;n de notas')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="auditorias">Auditor&iacute;as</a></li>
			<li><a href="notas">Revisi&oacute;n de notas</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-sm-8">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Revisi&oacute;n de notas</span>
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
	      	<p>En esta secci&oacute;n podr&aacute; revisar las notas y evidencias agregadas para cada auditor&iacute;a del sistema</p>

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

				{!!Form::open(['route'=>'responder_nota','method'=>'POST','class'=>'form-horizontal','id'=>'form',
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
					$.get('auditorias.get_audit_program2.'+$("#audit").val(), function (result) {

							$("#cargando").html('<br>');
							$("#audit_programs").empty();

							//parseamos datos obtenidos
							var datos = JSON.parse(result);
							tests_id = []; //array con id de pruebas para guardar en PHP 
							programs_id = []; //array con id de programas para guardar en PHP
							//seteamos datos en select de auditorías
							$(datos).each( function() {

								programs_id.push(this.id);
								var audit_test = '<h4><b>' + this.name +'</b></h4>';

								//por cada prueba del programa
								$(this.audit_tests).each( function(i,test) {
										audit_test += '<h5><b>' + test.name +'</b></h5>';
										audit_test += '<div style="cursor:hand" id="btn_notas_'+test.id+'" onclick="notas('+test.id+')" class="btn btn-success">Notas</div> ';

										audit_test += '<div id="notas_'+this.id+'" style="display: none;"></div>';
								});

								$("#audit_tests").append(audit_test);									

							});


					});
			}
			else
			{
				$("#audit").empty();
			}

});

function evidencias(id)
{
	alert("Hola evidencias "+id);
}

function ocultar_notas(id)
{
	$("#notas_"+id).hide(500);
}

//crea una respuesta para la nota de id = id (prueba id es para bloquear div)
function responder_nota(id,id_prueba)
{
	//borramos si es que hay alguna nota de respuesta
	$("#respuesta_nota_"+id).empty();
	$("#responder_nota_"+id).empty();
	$("#responder_nota_"+id).append('<div style="cursor:hand" id="responder_nota_'+id+'" onclick="ocultar_notas('+id_prueba+')">Ocultar</div>');
	//vaciamos por si existe ya algún formulario
	$("#nueva_nota_"+id).empty();
	var nota = '<div class="form-group col-sm-12">';
	//agregamos atributo hidden que señalará que se está guardando una nota y otro para identificar el id de la prueba
	nota += '<input type="hidden" name="note_id" value="'+id+'">';
	nota += '<div class="form-group col-sm-12">';
	nota += '<textarea name="answer_'+id+'" rows="3" cols="4" class="form-control" placeholder="Ingrese comentarios" required></textarea></div>';
	nota += '<div class="form-group col-sm-12">';
	nota += '<label class="control-label">Cargar evidencia (opcional)</label>';
	nota += '<input type="file" id="input-1a" name="evidencia_'+id+'" class="file" data-show-preview="false"></div>';
	nota += '<div class="form-group col-sm-12">';
	nota += '<button class="btn btn-success">Guardar</button></div><hr><br>';
	$("#respuesta_nota_"+id).append(nota);
	$("#respuesta_nota_"+id).show(500);

}
</script>

{!!Html::script('assets/js/notas.js')!!}
{!!Html::script('assets/js/descargar.js')!!}

@stop
