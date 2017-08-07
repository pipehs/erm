@extends('master')

@section('title', 'Agregar programa de auditor&iacute;a')


@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('#','Auditor&iacute;as')!!}</li>
			<li>{!!Html::link('crear_pruebas','Agregar programa de auditor&iacute;a')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Agregar Programa de Auditor&iacute;a</span>
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
				<div class="no-move"></div>
			</div>
			<div class="box-content">

			@if ($errors->any())
				<div class="alert alert-danger alert-dismissible" role="alert">
					<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
					</ul>
				</div>
			@endif

			@if(Session::has('message'))
				<div class="alert alert-success alert-dismissible" role="alert">
				{{ Session::get('message') }}
				</div>
			@endif

			Seleccione el plan, y luego seleccione si desea crear un nuevo programa de auditoría o reutilizar uno existente.
				{!!Form::open(['route'=>'agregar_prueba','method'=>'POST','class'=>'form-horizontal','id'=>'form','enctype'=>'multipart/form-data','onsubmit'=>'return checkSubmit();'])!!}

					<div id="cargando"><br></div>

					<div class="form-group">
						{!!Form::label('Seleccione si desea crear un nuevo programa o crear en base a alguno previo',
						null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('kind',$audit_programs,null,
							 	   ['id' => 'kind','placeholder'=>'Nueva'])!!}
						</div>
					</div>

					@include('auditorias.form_program')

					{!!Form::hidden('audit_program_id',null,['id'=>'audit_test_id'])!!}

					{!!Form::hidden('audit_id',$audit_id)!!}
					<div class="form-group">
						<center>
						{!!Form::submit('Guardar', ['class'=>'btn btn-primary','id' => 'btnsubmit'])!!}
						</center>
					</div>

				{!!Form::close()!!}

				<center>
					{!! link_to('', $title = 'Volver', $attributes = ['class'=>'btn btn-danger', 'onclick' => 'history.back()'])!!}
				<center>
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts2')
<script>

$(document).ready(function () {
	type_id = "NULL";
});

$("#kind").change(function() {
			
			if ($("#kind").val() != '') //Si es que se ha seleccionado un programa previo y no generar uno nuevo
			{
				//Añadimos la imagen de carga en el contenedor
					$('#cargando').html('<div><center><img src="/assets/img/loading.gif" width="19" height="19"/></center></div>');
				//se obtienen datos de prueba de auditoría
					$.get('auditorias.get_audit_program.'+$("#kind").val(), function (result) {
							//alert(result);
							$("#cargando").html('<br>');
							$("#name").empty();
							$("#description").empty();
							$("#tests").empty();
							$("#new_pruebas").empty();
							$("#agregar_prueba").hide();
							$("#categoria_test_1").empty();
							//ocultamos categoria
							$("#category").hide();
							
							//parseamos datos obtenidos
							var datos = JSON.parse(result);
							//alert(datos.type_name);
							$("#name").val(datos.name);
							$("#description").val(datos.description);
							//$("#type").val(datos.type).change();

							//$("#name").attr('disabled','disabled');
							//$("#description").attr('disabled','disabled');
							//$("#type").attr('disabled','disabled');

							//asignamos id del programa de auditoria para almacenarla en audit_audit_plan_audit_program
							$("#audit_program_id").val(datos.id);
							//seteamos datos de cada prueba
							$(datos.tests).each( function(i, test) {
									
									if (i == 0)
									{
										$('#tests').append('<div id="new_pruebas"></div>');
									}

									var prueba = '<div class="form-group">';
									
									//categoría
									prueba += '<label for="type2_test_'+(i+1)+'" class="col-sm-4 control-label">Categor&iacute;a</label>';
									prueba += '<div class="col-sm-4"><select name="type2_test_'+(i+1)+'" class="form-control" onchange="getType('+(i+1)+')">>';
									
									if (test.category == 1)
									{
										prueba += '<option value="1" selected>Prueba de control</option>';
										prueba += '<option value="2">Prueba de riesgo</option>';
										prueba += '<option value="3">Prueba de subproceso</option>';
									}
									else if (test.category == 2)
									{
										prueba += '<option value="1">Prueba de control</option>';
										prueba += '<option value="2" selected>Prueba de riesgo</option>';
										prueba += '<option value="3">Prueba de subproceso</option>';
									}
									else if (test.category == 3)
									{
										prueba += '<option value="1">Prueba de control</option>';
										prueba += '<option value="2">Prueba de riesgo</option>';
										prueba += '<option value="3" selected>Prueba de subproceso</option>';
									}

									prueba += '</select></div></div>';

									//nombre
									prueba += '<div class="form-group">';
									prueba += '<label for="name_test_'+(i+1)+'" class="col-sm-4 control-label">Prueba '+(i+1)+': Nombre</label>';
									prueba += '<div class="col-sm-4">';
									prueba += '<input type="text" name="name_test_'+(i+1)+'" value="'+test.name+'" class="form-control"></div></div>';
									
									//descripción
									prueba += '<div class="form-group">';
									prueba += '<label for="description_test_'+(i+1)+'" class="col-sm-4 control-label">Descripción</label>';
									prueba += '<div class="col-sm-4"><textarea name="description_test_'+(i+1)+'" class="form-control" cols="4" rows="3">'+test.description+'</textarea></div></div>';

									//tipo
									prueba += '<div class="form-group">';
									prueba += '<label for="type_test_'+(i+1)+'" class="col-sm-4 control-label">Tipo</label>';
									prueba += '<div class="col-sm-4"><select name="type_test_'+(i+1)+'" class="form-control">';

									if (test.type == 0)
									{
										prueba += '<option value="" disabled selected>- Seleccione -</option>';
										prueba += '<option value="0" selected>Prueba de diseño</option>';
										prueba += '<option value="1">Prueba de efectividad operativa</option>';
										prueba += '<option value="2">Prueba de cumplimiento</option>';
										prueba += '<option value="3">Prueba sustantiva</option>';
									}
									else if (test.type == 1)
									{
										prueba += '<option value="" disabled selected>- Seleccione -</option>';
										prueba += '<option value="0">Prueba de diseño</option>';
										prueba += '<option value="1" selected>Prueba de efectividad operativa</option>';
										prueba += '<option value="2">Prueba de cumplimiento</option>';
										prueba += '<option value="3">Prueba sustantiva</option>';
									}
									else if (test.type == 2)
									{
										prueba += '<option value="" disabled selected>- Seleccione -</option>';
										prueba += '<option value="0">Prueba de diseño</option>';
										prueba += '<option value="1" selected>Prueba de efectividad operativa</option>';
										prueba += '<option value="2" selected>Prueba de cumplimiento</option>';
										prueba += '<option value="3">Prueba sustantiva</option>';
									}
									else if (test.type == 3)
									{
										prueba += '<option value="" disabled selected>- Seleccione -</option>';
										prueba += '<option value="0">Prueba de diseño</option>';
										prueba += '<option value="1" selected>Prueba de efectividad operativa</option>';
										prueba += '<option value="2">Prueba de cumplimiento</option>';
										prueba += '<option value="3" selected>Prueba sustantiva</option>';
									}
									
									prueba += '</select>';
									prueba += '</div></div>';

									//hh
									prueba += '<div class="form-group">';
									prueba += '<label for="hh_test_'+(i+1)+'" class="col-sm-4 control-label">Horas-hombre</label>';
									prueba += '<div class="col-sm-4">';
									prueba += '<input type="number" name="hh_test_'+(i+1)+'" value="'+test.hh+'" class="form-control" min="1"></div></div>';

									prueba += '<div class="form-group">';
									prueba += '<label for="file_'+(i+1)+'" class="col-sm-4 control-label">Para mayor detalle de la prueba, puede agregar un archivo (opcional)</label>';
									prueba += '<div class="col-sm-4">';
									prueba += '<input type="file" name="file_'+(i+1)+'" id="file'+(i+1)+'" class="inputfile" />';
									prueba += '<label for="file'+(i+1)+'">Cargue evidencia</label>';
									prueba += '</div></div>';

									$('#tests').append(prueba);

								});
					});

			}
			else //se volvió a seleccionar generar una nueva prueba, por lo que se dejan los valores del comienzo
			{
				cont = 2; //contador para nuevas actividades

				$("#tests").empty();
				$("#agregar_prueba").show();
				$("#category").show();

				//agregamos campos de prueba 1
				var prueba = '<div class="form-group"><label for="name_test_1" class="col-sm-4 control-label">Prueba 1: Nombre</label>';
				prueba += '<div class="col-sm-4"><input id="test_1" class="form-control" required="true" name="name_test_1" type="text"></div></div>';

				//descripción
				prueba += '<div class="form-group">';
				prueba += '<label for="description_test_1" class="col-sm-4 control-label">Descripción</label>';
				prueba += '<div class="col-sm-4"><textarea name="description_test_1" class="form-control" cols="4" rows="3"></textarea></div></div>';

				//tipo
				prueba += '<div class="form-group">';
				prueba += '<label for="type_test_1" class="col-sm-4 control-label">Tipo</label>';
				prueba += '<div class="col-sm-4"><select name="type_test_1" class="form-control">';
				prueba += '<option value="" disabled selected>- Seleccione -</option>';
				prueba += '<option value="0">Prueba de diseño</option>';
				prueba += '<option value="1">Prueba de efectividad operativa</option>';
				prueba += '<option value="2">Prueba de cumplimiento</option>';
				prueba += '<option value="3">Prueba sustantiva</option>';
				prueba += '</select>';
				prueba += '</div></div>';

				//hh
				prueba += '<div class="form-group">';
				prueba += '<label for="hh_test_1" class="col-sm-4 control-label">Horas-hombre</label>';
				prueba += '<div class="col-sm-4">';
				prueba += '<input type="number" name="hh_test_1" class="form-control" min="1"></div></div>';

				$("#tests").append(prueba);

				$("audit_test_id").val("");

				$("#name").removeAttr('disabled');
				$("#description").removeAttr('disabled');
				$("#activities").removeAttr('disabled');
				$("#type").removeAttr('disabled');
			}
		});

cont = 2; //contador para nuevas pruebas
//función para agregar una nueva prueba
$("#agregar_prueba").click(function() {

			//primero verificamos que se haya seleccionado un plan de auditoría
			if ($("#audit_plans").val() != '')
			{
				//insertamos datos para nueva prueba
				var prueba = '<div id="test_'+cont+'">';

				//tipo
				prueba += '<div class="form-group">';
				prueba += '<label for="type2_test_'+cont+'" class="col-sm-4 control-label">Categor&iacute;a</label>';
				prueba += '<div class="col-sm-4">';
				prueba += '<select name="type2_test_'+cont+'" id="type2_test_'+cont+'" onchange="getType('+cont+')" class="form-control">';
				prueba += '<option value="" disabled selected>- Seleccione -</option>';
				prueba += '<option value="1">Prueba de control</option>';
				prueba += '<option value="2">Prueba de riesgo</option>';
				prueba += '<option value="3">Prueba de subproceso</option>';
				prueba += '</select>';
				prueba += '</div></div>';

				prueba += '<div id="categoria_test_'+cont+'" style="display:none;"></div>';

				//insertamos nombre
				prueba += '<div class="form-group">';
				prueba += '<label for="name_test_'+cont+'" class="col-sm-4 control-label">Prueba '+cont+': Nombre</label>';
				prueba += '<div class="col-sm-4">';
				prueba += '<input type="text" name="name_test_'+cont+'" class="form-control"></div></div>';

				//descripción
				prueba += '<div class="form-group">';
				prueba += '<label for="description_test_'+cont+'" class="col-sm-4 control-label">Descripción</label>';
				prueba += '<div class="col-sm-4"><textarea name="description_test_'+cont+'" class="form-control" cols="4" rows="3"></textarea></div></div>';

				//tipo
				prueba += '<div class="form-group">';
				prueba += '<label for="type_test_'+cont+'" class="col-sm-4 control-label">Tipo</label>';
				prueba += '<div class="col-sm-4"><select name="type_test_'+cont+'" class="form-control">';
				prueba += '<option value="" disabled selected>- Seleccione -</option>';
				prueba += '<option value="0">Prueba de diseño</option>';
				prueba += '<option value="1">Prueba de efectividad operativa</option>';
				prueba += '<option value="2">Prueba de cumplimiento</option>';
				prueba += '<option value="3">Prueba sustantiva</option>';
				prueba += '</select>';
				prueba += '</div></div>';


				//stakeholder
				prueba += '<div class="form-group">';
				prueba += '{!!Form::label("Responsable",null,["class"=>"col-sm-4 control-label"])!!}';
				prueba += '<div class="col-sm-4">';
				//prueba += '{!!Form::select("stakeholder_test_'+cont+'",$stakeholders,null,["placeholder"=>"- Seleccione -","class"=>"form-control first-disabled"])!!}';

				prueba += '<select name="stakeholder_test_'+cont+'" class="form-control">';
				prueba += '<option value="" disabled selected>- Seleccione -</option>';
				@foreach ($stakeholders as $rut=>$name)
					prueba += '<option value="{{$rut}}">{{$name}}</option>';
				@endforeach

				prueba += '</select>';
				prueba += '</div>';
				prueba += '</select>';
				prueba += '</div>';

				//hh
				prueba += '<div class="form-group">';
				prueba += '<label for="hh_test_'+cont+'" class="col-sm-4 control-label">Horas-hombre</label>';
				prueba += '<div class="col-sm-4">';
				prueba += '<input type="number" name="hh_test_'+cont+'" class="form-control" min="1"></div></div>';

				prueba += '<div class="form-group">';
				prueba += '<label for="file_'+cont+'" class="col-sm-4 control-label">Para mayor detalle de la prueba, puede agregar un archivo (opcional)</label>';
				prueba += '<div class="col-sm-4">';
				prueba += '<input type="file" name="file_'+cont+'" id="file'+cont+'" class="inputfile" />';
				prueba += '<label for="file'+cont+'">Cargue evidencia</label>';
				prueba += '</div></div>';

				prueba += '</div>';
				prueba += '<hr>';
				prueba += '<script>$("html,body").animate({scrollTop: $("#test_'+cont+'").offset().top}, 900);';

				$("#new_pruebas").append(prueba);

				cont = cont + 1;
			}
			else
			{
				swal("Error","Primero debe seleccionar el plan de auditoría","error");
			}
	});

</script>

{!!Html::script('assets/js/type_audit_test.js')!!}

@stop
