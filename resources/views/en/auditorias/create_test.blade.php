@extends('en.master')

@section('title', 'Create Audit Program')


@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('#','Auditor&iacute;as')!!}</li>
			<li>{!!Html::link('crear_pruebas','Create Audit Program')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Create Audit Program</span>
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

			Select plan, and then select if you want to create a new audit program or reuse existing one.
				{!!Form::open(['route'=>'agregar_prueba','method'=>'POST','class'=>'form-horizontal','id'=>'form','enctype'=>'multipart/form-data'])!!}

					<div id="cargando"><br></div>

					<div class="form-group">
						{!!Form::label('Audit Plan',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('audit_plan_id',$audit_plans,null, 
							 	   ['id' => 'audit_plans','required'=>'true','placeholder'=>'- Seleccione -'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Select audit',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							<select name="audit" id="audit" required="true">
								<!-- Aquí se agregarán las auditorías relacionadas al plan seleccionado a través de Jquery -->
							</select>
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Select if you want to create a new program or reuse existing one',
						null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('kind',$audit_programs,null,
							 	   ['id' => 'kind','placeholder'=>'New'])!!}
						</div>
					</div>

					@include('en.auditorias.form_program')

			<div id="super_tests">		

			<center><b><font color="blue">Information about audit tests</font></b></center><hr>

					<div id="category" style="display:none;">
							<div class="form-group">
								{!!Form::label('Category',null,['class'=>'col-sm-4 control-label'])!!}
								<div class="col-sm-4">
									{!!Form::select('type2_test_1',['1'=>'Control test','2'=>'Risk test',
																'3'=>'Subprocess test'],null,
																['id'=>'type2_test_1','required'=>'true','onchange'=>'getType(1)','placeholder'=>'- Select -','required'=>'true'])!!}
								</div>
							</div>
					</div>	

					<div id="categoria_test_1" style="display:none;"></div>
					<div id="tests">
						
						<div class="form-group">
								{!!Form::label('Test 1: Name',null,['class'=>'col-sm-4 control-label'])!!}
								<div class="col-sm-4">
									{!!Form::text('name_test_1',null,['id'=>'name_test_1','class'=>'form-control',
																'required'=>'true'])!!}
								</div>
						</div>

						<div class="form-group">

								{!!Form::label('Description',null,['class'=>'col-sm-4 control-label'])!!}	
								<div class="col-sm-4">
									{!!Form::textarea('description_test_1',null,['id'=>'description_test_1','class'=>'form-control','rows'=>'3','cols'=>'4','required'=>'true'])!!}
								</div>

						</div>

						<div class="form-group">
							{!!Form::label('Kind',null,['class'=>'col-sm-4 control-label'])!!}
							<div class="col-sm-4">
								{!!Form::select('type_test_1',['0'=>'Design test','1'=>'Operational effectiveness test','2'=>'Compliance test','3'=>'Substantive test'],null,
								['id'=>'type','required'=>'true','placeholder'=>'- Select -'])!!}
							</div>
						</div>

						<div class="form-group">
							{!!Form::label('Responsable',null,['class'=>'col-sm-4 control-label'])!!}
							<div class="col-sm-4">
								{!!Form::select('stakeholder_test_1',$stakeholders,null,['required'=>'true',
								'placeholder'=>'- Select  -','id'=>'stakeholder_test_1','class'=>'first-disabled'])!!}
							</div>
						</div>

						<div class="form-group">
								{!!Form::label('Hours-Man',null,['class'=>'col-sm-4 control-label'])!!}
								<div class="col-sm-4">
									{!!Form::number('hh_test_1',null,['id'=>'hh_test_1','class'=>'form-control','min'=>'1'])!!}
								</div>
						</div>

						<div class="form-group">

							<label for="file_1" class="col-sm-4 control-label">For more detail of the test, you can add a file (optional)</label>

							<div class="col-sm-4">
								<input type="file" name="file_1" id="file1" class="inputfile" />
								<label for="file1">Upload Evidence</label>
							</div>
						</div>

					</div>
					<hr>
					<div id="new_pruebas">
					</div>
					<center>
					<div style="cursor:hand; margin:auto; " id="agregar_prueba"><font color="CornflowerBlue"><u>Add More Tests</u></font></div>
					</center>
			</div>

					{!!Form::hidden('audit_program_id',null,['id'=>'audit_test_id'])!!}

					<div class="form-group">
						<center>
						{!!Form::submit('Save', ['class'=>'btn btn-primary'])!!}
						</center>
					</div>

				{!!Form::close()!!}

				<center>
					{!! link_to_route('programas_auditoria', $title = 'Return', $parameters = NULL,
                 		$attributes = ['class'=>'btn btn-danger'])!!}
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
					$('#cargando').html('<div><center><img src="../public/assets/img/loading.gif" width="19" height="19"/></center></div>');
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
									prueba += '<label for="type2_test_'+(i+1)+'" class="col-sm-4 control-label">Category</label>';
									prueba += '<div class="col-sm-4"><select name="type2_test_'+(i+1)+'" class="form-control" onchange="getType('+(i+1)+')">>';
									
									if (test.category == 1)
									{
										prueba += '<option value="1" selected>Control test</option>';
										prueba += '<option value="2">Risk test</option>';
										prueba += '<option value="3">Subprocess test</option>';
									}
									else if (test.category == 2)
									{
										prueba += '<option value="1">Control test</option>';
										prueba += '<option value="2" selected>Risk test</option>';
										prueba += '<option value="3">Subprocess test</option>';
									}
									else if (test.category == 3)
									{
										prueba += '<option value="1">Control test</option>';
										prueba += '<option value="2">Risk test</option>';
										prueba += '<option value="3" selected>Subprocess test</option>';
									}

									prueba += '</select></div></div>';

									//nombre
									prueba += '<div class="form-group">';
									prueba += '<label for="name_test_'+(i+1)+'" class="col-sm-4 control-label">Test '+(i+1)+': Name</label>';
									prueba += '<div class="col-sm-4">';
									prueba += '<input type="text" name="name_test_'+(i+1)+'" value="'+test.name+'" class="form-control"></div></div>';
									
									//descripción
									prueba += '<div class="form-group">';
									prueba += '<label for="description_test_'+(i+1)+'" class="col-sm-4 control-label">Description</label>';
									prueba += '<div class="col-sm-4"><textarea name="description_test_'+(i+1)+'" class="form-control" cols="4" rows="3">'+test.description+'</textarea></div></div>';

									//tipo
									prueba += '<div class="form-group">';
									prueba += '<label for="type_test_'+(i+1)+'" class="col-sm-4 control-label">Kind</label>';
									prueba += '<div class="col-sm-4"><select name="type_test_'+(i+1)+'" class="form-control">';

									if (test.type == 0)
									{
										prueba += '<option value="" disabled selected>- Select -</option>';
										prueba += '<option value="0" selected>Design test</option>';
										prueba += '<option value="1">Operational effectiveness test</option>';
										prueba += '<option value="2">Compliance test</option>';
										prueba += '<option value="3">Substantive test</option>';
									}
									else if (test.type == 1)
									{
										prueba += '<option value="" disabled selected>- Select -</option>';
										prueba += '<option value="0">Design test</option>';
										prueba += '<option value="1" selected>Operational effectiveness test</option>';
										prueba += '<option value="2">Compliance test</option>';
										prueba += '<option value="3">Substantive test</option>';
									}
									else if (test.type == 2)
									{
										prueba += '<option value="" disabled selected>- Select -</option>';
										prueba += '<option value="0">Design test</option>';
										prueba += '<option value="1">Operational effectiveness test</option>';
										prueba += '<option value="2" selected>Compliance test</option>';
										prueba += '<option value="3">Substantive test</option>';
									}
									else if (test.type == 3)
									{
										prueba += '<option value="" disabled selected>- Select -</option>';
										prueba += '<option value="0">Design test</option>';
										prueba += '<option value="1">Operational effectiveness test</option>';
										prueba += '<option value="2">Compliance test</option>';
										prueba += '<option value="3" selected>Substantive test</option>';;
									}
									
									prueba += '</select>';
									prueba += '</div></div>';

									//hh
									prueba += '<div class="form-group">';
									prueba += '<label for="hh_test_'+(i+1)+'" class="col-sm-4 control-label">Hours-Man</label>';
									prueba += '<div class="col-sm-4">';
									prueba += '<input type="number" name="hh_test_'+(i+1)+'" value="'+test.hh+'" class="form-control" min="1"></div></div>';

									prueba += '<div class="form-group">';
									prueba += '<label for="file_'+(i+1)+'" class="col-sm-4 control-label">For more detail of the test, you can add a file (optional)</label>';
									prueba += '<div class="col-sm-4">';
									prueba += '<input type="file" name="file_'+(i+1)+'" id="file'+(i+1)+'" class="inputfile" />';
									prueba += '<label for="file'+(i+1)+'">Upload Evidence</label>';
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
				prueba += '<label for="description_test_1" class="col-sm-4 control-label">Description</label>';
				prueba += '<div class="col-sm-4"><textarea name="description_test_1" class="form-control" cols="4" rows="3"></textarea></div></div>';

				//tipo
				prueba += '<div class="form-group">';
				prueba += '<label for="type_test_1" class="col-sm-4 control-label">Tipo</label>';
				prueba += '<div class="col-sm-4"><select name="type_test_1" class="form-control">';
				prueba += '<option value="" disabled selected>- Select -</option>';
				prueba += '<option value="0">Design test</option>';
				prueba += '<option value="1">Operational effectiveness test</option>';
				prueba += '<option value="2">Compliance test</option>';
				prueba += '<option value="3">Substantive test</option>';
				prueba += '</select>';
				prueba += '</div></div>';

				//hh
				prueba += '<div class="form-group">';
				prueba += '<label for="hh_test_1" class="col-sm-4 control-label">Hours-Man</label>';
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

$("#audit_plans").change(function() {
			
			if ($("#audit_plans").val() != '') //Si es que se ha seleccionado valor válido de plan
			{
				if ($("#kind").val() == '') // SOLO SI ES QUE NO SE ESTÁ BASANDO EN UN PROGRAMA PREVIO SE AGREGARÁ CATEGORÍA, YA QUE DE LO CONTRARIO LA CATEGORÍA VENDRÁ INCLUIDA EN LOS DATOS DEL PROGRAMA (MEJOR DICHO DE LAS PRUEBAS)
				{	
					//añadimos botón para seleccionar tipo (o categoría de prueba)
					$("#category").show(500);
				}
				
				//Añadimos la imagen de carga en el contenedor
					$('#cargando').html('<div><center><img src="../public/assets/img/loading.gif" width="19" height="19"/></center></div>');
				//se obtienen controles asociados a los riesgos presentes en el plan de prueba seleccionado
					//primero obtenemos controles asociados a los riesgos de negocio
					/*
					$.get('auditorias.objective_controls.'+$("#audit_plans").val(), function (result) {

							$("#cargando").html('<br>');
							$("#control_objective_id").empty();

							//parseamos datos obtenidos
							var datos = JSON.parse(result);

							//seteamos datos en select de riesgos / procesos
							$(datos).each( function() {
								$("#control_objective_id").append('<option value="' + this.id + '">' + this.name +'</option>');
							});
	
					});

					//luego los controles asociados a riesgos de subproceso
					$.get('auditorias.subprocess_controls.'+$("#audit_plans").val(), function (result) {

							$("#cargando").html('<br>');
							$("#control_subprocess_id").empty();

							//parseamos datos obtenidos
							var datos = JSON.parse(result);

							//seteamos datos en select de riesgos / procesos
							$(datos).each( function() {
								$("#control_subprocess_id").append('<option value="' + this.id + '">' + this.name +'</option>');
							});
	
					});
					*/

					//obtenemos auditorias relacionadas al plan seleccionado
					$.get('auditorias.auditorias.'+$("#audit_plans").val(), function (result) {

							$("#cargando").html('<br>');
							$("#audit").empty();

							//parseamos datos obtenidos
							var datos = JSON.parse(result);
							audit = '<option value="" selected disabled>- Select </option>';
							//seteamos datos en select de auditorías
							$(datos).each( function() {
								audit += '<option value="' + this.id + '">' + this.name +'</option>';
							});

							$("#audit").append(audit);
	
					});
			}
			else
			{
				$("audit").empty();
			}
		});

cont = 2; //contador para nuevas actividades
//función para agregar una nueva prueba
$("#agregar_prueba").click(function() {

			//primero verificamos que se haya seleccionado un plan de auditoría
			if ($("#audit_plans").val() != '')
			{
				//insertamos datos para nueva prueba
				var prueba = '<div id="test_'+cont+'">';

				//tipo
				prueba += '<div class="form-group">';
				prueba += '<label for="type2_test_'+cont+'" class="col-sm-4 control-label">Category</label>';
				prueba += '<div class="col-sm-4">';
				prueba += '<select name="type2_test_'+cont+'" id="type2_test_'+cont+'" onchange="getType('+cont+')" class="form-control">';
				prueba += '<option value="" disabled selected>- Select -</option>';
				prueba += '<option value="1">Control test</option>';
				prueba += '<option value="2">Risk test</option>';
				prueba += '<option value="3">Subprocess test</option>';
				prueba += '</select>';
				prueba += '</div></div>';

				prueba += '<div id="categoria_test_'+cont+'" style="display:none;"></div>';

				//insertamos nombre
				prueba += '<div class="form-group">';
				prueba += '<label for="name_test_'+cont+'" class="col-sm-4 control-label">Test '+cont+': Name</label>';
				prueba += '<div class="col-sm-4">';
				prueba += '<input type="text" name="name_test_'+cont+'" class="form-control"></div></div>';

				//descripción
				prueba += '<div class="form-group">';
				prueba += '<label for="description_test_'+cont+'" class="col-sm-4 control-label">Description</label>';
				prueba += '<div class="col-sm-4"><textarea name="description_test_'+cont+'" class="form-control" cols="4" rows="3"></textarea></div></div>';

				//tipo
				prueba += '<div class="form-group">';
				prueba += '<label for="type_test_'+cont+'" class="col-sm-4 control-label">Kind</label>';
				prueba += '<div class="col-sm-4"><select name="type_test_'+cont+'" class="form-control">';
				prueba += '<option value="" disabled selected>- Select -</option>';
				prueba += '<option value="0">Design test</option>';
				prueba += '<option value="1">Operational effectiveness test</option>';
				prueba += '<option value="2">Compliance test</option>';
				prueba += '<option value="3">Substantive test</option>';
				prueba += '</select>';
				prueba += '</div></div>';


				//stakeholder
				prueba += '<div class="form-group">';
				prueba += '{!!Form::label("Responsable",null,["class"=>"col-sm-4 control-label"])!!}';
				prueba += '<div class="col-sm-4">';
				//prueba += '{!!Form::select("stakeholder_test_'+cont+'",$stakeholders,null,["placeholder"=>"- Seleccione -","class"=>"form-control first-disabled"])!!}';

				prueba += '<select name="stakeholder_test_'+cont+'" class="form-control">';
				prueba += '<option value="" disabled selected>- Select -</option>';
				@foreach ($stakeholders as $rut=>$name)
					prueba += '<option value="{{$rut}}">{{$name}}</option>';
				@endforeach

				prueba += '</select>';
				prueba += '</div>';
				prueba += '</select>';
				prueba += '</div>';

				//hh
				prueba += '<div class="form-group">';
				prueba += '<label for="hh_test_'+cont+'" class="col-sm-4 control-label">Hours-Man</label>';
				prueba += '<div class="col-sm-4">';
				prueba += '<input type="number" name="hh_test_'+cont+'" class="form-control" min="1"></div></div>';

				prueba += '<div class="form-group">';
				prueba += '<label for="file_'+cont+'" class="col-sm-4 control-label">>For more detail of the test, you can add a file (optional)</label>';
				prueba += '<div class="col-sm-4">';
				prueba += '<input type="file" name="file_'+cont+'" id="file'+cont+'" class="inputfile" />';
				prueba += '<label for="file'+cont+'">Upload Evidence</label>';
				prueba += '</div></div>';

				prueba += '</div>';
				prueba += '<hr>';
				prueba += '<script>$("html,body").animate({scrollTop: $("#test_'+cont+'").offset().top}, 900);';

				$("#new_pruebas").append(prueba);

				cont = cont + 1;
			}
			else
			{
				swal("Error","First you have to choose audit plan","error");
			}
	});

</script>

{!!Html::script('assets/js/en/type_audit_test.js')!!}

@stop
