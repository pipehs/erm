@extends('master')

@section('title', 'Agregar pruebas de auditor&iacute;a')

@stop

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('#','Auditor&iacute;as')!!}</li>
			<li>{!!Html::link('crear_pruebas','Agregar pruebas de auditor&iacute;as')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Agregar Pruebas de Auditor&iacute;as</span>
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

			Seleccione el plan, y luego seleccione si desea crear una nueva prueba de auditoría o reutilizar una existente de auditor&iacute;a.
				{!!Form::open(['route'=>'agregar_prueba','method'=>'POST','class'=>'form-horizontal','id'=>'form'])!!}

					<div id="cargando"><br></div>

					<div class="form-group">
						{!!Form::label('Plan de auditor&iacute;a',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('audit_plan_id',$audit_plans,null, 
							 	   ['id' => 'audit_plans','required'=>'true','placeholder'=>'- Seleccione -'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Seleccione auditor&iacute;a',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							<select name="audit" id="audit">
								<!-- Aquí se agregarán las auditorías relacionadas al plan seleccionado a través de Jquery -->
							</select>
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Seleccione si desea crear una nueva prueba o crear en base a alguna previa',
						null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('kind',$audit_tests,null,
							 	   ['id' => 'kind','placeholder'=>'Nueva'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Nombre',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::text('name',null,['id'=>'name','class'=>'form-control',
														'required'=>'true'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Descripci&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::textarea('description',null,['id'=>'description','class'=>'form-control',
							'rows'=>'3','cols'=>'4','required'=>'true'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Tipo',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('type',['0'=>'Prueba de diseño','1'=>'Prueba de efectividad operativa',
														'2'=>'Prueba de cumplimiento','3'=>'Prueba sustantiva'],null,
														['id'=>'type','required'=>'true','placeholder'=>'- Seleccione -'])!!}
						</div>
					</div>

			<div id="super_activities">
					<center>
					<div style="cursor:hand; margin:auto; " id="agregar_actividad"><font color="CornflowerBlue"><u>Agregar m&aacute;s actividades</u></font></div>
					</center>
					<div class="form-group">
					<div id="activities">
								{!!Form::label('Actividad 1',null,['class'=>'col-sm-4 control-label'])!!}
								<div class="col-sm-4">
									{!!Form::text('activity_1',null,['id'=>'activity_1','class'=>'form-control',
																'required'=>'true'])!!}
								</div>	
					</div>
					</div>

					<div id="new_actividades">
						
					</div>
			</div>

					<div class="form-group">
						{!!Form::label('Seleccione controles para riesgos de negocio auditados (opcional)',
						null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							<select name="control_objective_id[]" id="control_objective_id" multiple="multiple">
								<!-- Aquí se agregarán los riesgos de negocio de la org seleccionada a través de Jquery -->
							</select>
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Seleccione controles para riesgos de proceso auditados (opcional)',
						null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							<select name="control_subprocess_id[]" id="control_subprocess_id" multiple="multiple">
								<!-- Aquí se agregarán los riesgos de negocio de la org seleccionada a través de Jquery -->
							</select>
						</div>
					</div>

					{!!Form::hidden('audit_test_id',null,['id'=>'audit_test_id'])!!}

					<div class="form-group">
						<center>
						{!!Form::submit('Guardar', ['class'=>'btn btn-primary'])!!}
						</center>
					</div>

				{!!Form::close()!!}

				<center>
					{!! link_to_route('plan_auditoria', $title = 'Volver', $parameters = NULL,
                 		$attributes = ['class'=>'btn btn-success'])!!}
				<center>
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts2')
<script>
$("#kind").change(function() {
			
			if ($("#kind").val() != '') //Si es que se ha seleccionado una prueba previa y no generar una nueva prueba
			{
				//Añadimos la imagen de carga en el contenedor
					$('#cargando').html('<div><center><img src="../public/assets/img/loading.gif" width="19" height="19"/></center></div>');
				//se obtienen datos de prueba de auditoría
					$.get('auditorias.get_audit_test.'+$("#kind").val(), function (result) {

							$("#cargando").html('<br>');
							$("#name").empty();
							$("#description").empty();
							$("#activities").empty();
							$("#new_actividades").empty();
							$("#agregar_actividad").hide();
							//$("#type").remove();
							
							//parseamos datos obtenidos
							var datos = JSON.parse(result);
							//alert(datos.type_name);
							$("#name").val(datos.name);
							$("#description").val(datos.description);
							$("#type").val(datos.type).change();

							$("#name").attr('disabled','disabled');
							$("#description").attr('disabled','disabled');
							$("#type").attr('disabled','disabled');

							//asignamos id de la prueba de auditoria para almacenarla en audit_audit_plan_audit_test
							$("#audit_test_id").val(datos.id);

							//seteamos datos de cada actividad
							$(datos.activities).each( function(i, activity) {
								//nombre
									if (i == 0)
									{
										$('#activities').append('<div id="new_actividades"></div>');
									}

									$('#activities').append('<div class="form-group">');
									$('#activities').append('<label for="activity_'+(i+1)+'" class="col-sm-4 control-label">Actividad '+(i+1)+'</label>');
									$('#activities').append('<div class="col-sm-4"><input type="text" name="activity_'+(i+1)+'" value="'+activity+'" class="form-control" disabled></div></div></br>');
								});
					});

			}
			else
			{
				cont = 2; //contador para nuevas actividades
				$("#name").val("");
				$("#description").val("");
				$("#type").val("");

				$("#activities").empty();
				$("#agregar_actividad").show();
				$("#activities").append('<div class="form-group"><label for="Actividad 1" class="col-sm-4 control-label">Actividad 1</label><div class="col-sm-4"><input id="activity_1" class="form-control" required="true" name="activity_1" type="text"></div></div>');

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
				//Añadimos la imagen de carga en el contenedor
					$('#cargando').html('<div><center><img src="../public/assets/img/loading.gif" width="19" height="19"/></center></div>');
				//se obtienen controles asociados a los riesgos presentes en el plan de prueba seleccionado
					//primero obtenemos controles asociados a los riesgos de negocio
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

					//obtenemos auditorias relacionadas al plan seleccionado
					$.get('auditorias.auditorias.'+$("#audit_plans").val(), function (result) {

							$("#cargando").html('<br>');
							$("#audit").empty();

							//parseamos datos obtenidos
							var datos = JSON.parse(result);

							//seteamos datos en select de auditorías
							$(datos).each( function() {
								$("#audit").append('<option value="' + this.id + '">' + this.name +'</option>');
							});
	
					});

			}
			else
			{
				$("control_id").empty();
			}
		});

cont = 2; //contador para nuevas actividades
//función para agregar una nueva auditoría
$("#agregar_actividad").click(function() {

			//nombre
			$('#new_actividades').append('<div class="form-group">');
			$('#new_actividades').append('<label for="activity_'+cont+'" class="col-sm-4 control-label">Actividad '+cont+'</label>');
			$('#new_actividades').append('<div class="col-sm-4"><input type="text" name="activity_'+cont+'" class="form-control"></div></div></br>');

			cont = cont + 1;
	});
</script>
@stop
