@extends('master')

@section('title', 'Mantenedor de Hallazgos')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('hallazgos','Hallazgos')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Hallazgos</span>
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

			@if(Session::has('message'))
				<div class="alert alert-success alert-dismissible" role="alert">
				{{ Session::get('message') }}
				</div>
			@endif

			En esta secci&oacute;n podr&aacute; crear, modificar y cerrar hallazgos para los distintos datos maestros relevantes a una organizaci&oacute;n. Estos hallazgos pueden estar relacionados a un proceso, subproceso, o a la organizaci&oacute;n misma<br><br>
			<div id="cargando"><br></div>

			{!!Form::open(['route'=>'hallazgos_lista','method'=>'GET','class'=>'form-horizontal'])!!}
			<div class="form-group">
				{!!Form::label('Seleccione organización',null,['class'=>'col-sm-4 control-label'])!!}
				<div class="col-sm-3">
					{!!Form::select('organization_id',$organizations,null, 
							 	   ['id' => 'orgs','required'=>'true','placeholder'=>'- Seleccione -','onChange'=>'lala()'])!!}
				</div>
			</div>

			<div class="form-group">
				{!!Form::label('Seleccione un tipo',null,['class'=>'col-sm-4 control-label'])!!}
				<div class="col-sm-3">
					{!!Form::select('kind',['0'=>'Procesos','1'=>'Subprocesos','2'=>'Organización','3'=>'Controles de proceso','4'=>'Controles de entidad','5'=>'Programas de auditoría','6'=>'Auditorías','7'=>'Pruebas de auditoría','8'=>'Riesgos','9'=>'Compliance','10'=>'Canal de denuncia'],null, 
							 	   ['id' => 'kind','required'=>'true','placeholder'=>'- Seleccione -','onChange'=>'lala()'])!!}
				</div>
			</div>

			<div class="form-group" id="secondGroup" style="display: none;">
              <div class="row">
                 <label for="second_select" class='col-sm-4 control-label' id="label_second_select"></label>
                 <div class="col-sm-3">
                   <select id="second_select" name="second_select">
                   </select>
                 </div>
              </div>
            </div>

			<div class="form-group">
				<center>
					{!!Form::submit('Seleccionar', ['class'=>'btn btn-success','id'=>'guardar'])!!}
				</center>
			</div>
			{!!Form::close()!!}

			<div id="tipo" style="display:none;">
			
			</div>

@if (isset($issues))
				@if ($kind == 0)
					<h4><b>{{ $org }}: Hallazgos de procesos</b></h4>
				@elseif($kind == 1)
					<h4><b>{{ $org }}: Hallazgos de subprocesos</b></h4>
				@elseif ($kind == 2)
					<h4><b>{{ $org }}: Hallazgos de organizaci&oacute;n</b></h4>
				@elseif ($kind == 3)
					<h4><b>{{ $org }}: Hallazgos de controles de procesos</b></h4>
				@elseif ($kind == 4)
					<h4><b>{{ $org }}: Hallazgos de controles de entidad</b></h4>
				@elseif ($kind == 5)
					<h4><b>{{ $org }}: Hallazgos de programas de auditor&iacute;a</b></h4>
				@elseif ($kind == 6)
					<h4><b>{{ $org }}: Hallazgos de auditor&iacute;as</b></h4>
				@elseif ($kind == 7)
					<h4><b>{{ $org }}: Hallazgos de pruebas de auditoría</b></h4>
				@elseif ($kind == 8)
					<h4><b>{{ $org }}: Hallazgos de Riesgos</b></h4>
				@elseif ($kind == 9)
					<h4><b>{{ $org }}: Hallazgos de Compliance</b></h4>
				@elseif ($kind == 10)
					<h4><b>{{ $org }}: Hallazgos de Canal de denuncia</b></h4>
				@endif

				@foreach (Session::get('roles') as $role)
					@if ($role != 6)
						{!! link_to_route('create_hallazgo', $title = 'Agregar Hallazgo', $parameters = ['org'=>$org_id,'kind'=>$kind],$attributes = ['class'=>'btn btn-primary'])!!}
					<?php break; ?>
					@endif
				@endforeach
				<table id="datatable-2" class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
				<thead>
					@if ($kind == 0)
						<th>Proceso<label><input type="text" placeholder="Filtrar" /></label></th>
					@elseif($kind == 1)
						<th>Subproceso<label><input type="text" placeholder="Filtrar" /></label></th>
					@elseif ($kind == 2)
						<th>Organizaci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
					@elseif ($kind == 3 || $kind == 4)
						<th>Control<label><input type="text" placeholder="Filtrar" /></label></th>
					@elseif ($kind == 5)
						<th>Programa de auditor&iacute;a<label><input type="text" placeholder="Filtrar" /></label></th>
					@elseif ($kind == 6)
						<th>Plan de auditor&iacute;a - Auditor&iacute;a<label><input type="text" placeholder="Filtrar" /></label></th>
					@elseif ($kind == 7)
						<th>Prueba de auditor&iacute;a<label><input type="text" placeholder="Filtrar" /></label></th>
					@elseif ($kind == 8)
						<th>Riesgo(s)<label><input type="text" placeholder="Filtrar" /></label></th>
					@else
						<th>Origen<label><input type="text" placeholder="Filtrar" /></label></th>
					@endif
					<th>Hallazgo<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Descripci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Clasificaci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Recomendaciones<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Plan de acci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Estado<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Fecha final plan<label><input type="text" placeholder="Filtrar" /></label></th>
					@foreach (Session::get('roles') as $role)
						@if ($role != 6)
								<th>Editar</th>
								<th>Eliminar</th>
							<?php break; ?>
						@endif
					@endforeach
				</thead>

				@foreach ($issues as $issue)
					<tr>
						<td>
						@if ($kind == 8)
							@foreach ($issue['origin'] as $risk)
								<li>{{ $risk->name }}: {{ $risk->description }}</li>
							@endforeach
						@else
							{{ $issue['origin'] }}
						@endif
						</td>
						<td>{{ $issue['name'] }}</td>
						<td>
						@if (strlen($issue['description']) > 100)
							<div id="description_{{$issue['id']}}" title="{{ $issue['description'] }}">{{ $issue['short_des'] }}...
							<div style="cursor:hand" onclick="expandir({{ $issue['id'] }},'{{ $issue['description'] }}','{{ $issue['short_des'] }}')">
							<font color="CornflowerBlue">Ver completo</font>
							</div></div>
						@else
							{{ $issue['description'] }}
						@endif
						</td>
						<td>{{ $issue['classification'] }}</td>
						<td>
						@if (strlen($issue['recommendations']) > 100)
							<div id="recommendation_{{$issue['id']}}" title="{{ $issue['recommendations'] }}">{{ $issue['short_rec'] }}...
							<div style="cursor:hand" onclick="expandir2({{ $issue['id'] }},'{{ $issue['recommendations'] }}','{{ $issue['short_rec'] }}')">
							<font color="CornflowerBlue">Ver completo</font>
							</div></div>
						@else
							{{ $issue['recommendations'] }}
						@endif
						</td>
						<td>
						@if (strlen($issue['plan']) > 100)
							<div id="action_plan_{{$issue['id']}}" title="{{ $issue['plan'] }}">{{ $issue['short_plan'] }}...
							<div style="cursor:hand" onclick="expandir3({{ $issue['id'] }},'{{ $issue['plan'] }}','{{ $issue['short_plan'] }}')">
							<font color="CornflowerBlue">Ver completo</font>
							</div></div>
						@else
							{{ $issue['plan'] }}
						@endif
						</td>
						<td>{{ $issue['status'] }}</td>
						<td>{{ $issue['final_date'] }}</td>
					@foreach (Session::get('roles') as $role)
						@if ($role != 6)
							<td>{!! link_to_route('edit_hallazgo', $title = 'Editar', $parameters = ['org'=>$org_id,'id'=>$issue['id'],'kind'=>$kind],$attributes = ['class'=>'btn btn-success'])!!}</td>
							<td>
							<button class="btn btn-danger" onclick="eliminar2({{ $issue['id'] }},'{{ $issue['name'] }}','hallazgo','El hallazgo')">Eliminar</button></td>
						<?php break; ?>
						@endif
					@endforeach
					</tr>
				@endforeach

@endif


			</div>
		</div>
	</div>
</div>
@stop


@section('scripts2')
<script>
function lala() {
	
	if ($('#orgs').val() != '')
	{
		if ($('#kind').val() != '')
		{
			if ($('#kind').val() == 0) //selección de proceso
			{
				$.get('get_processes.'+$("#orgs").val(), function (result) {
					$("#second_select").empty();
					$("#second_select").change();

					//agregamos label
					$("#label_second_select").html('Seleccione proceso')
					//parseamos datos obtenidos
					select = '<option value="" selected>- Seleccione -</option>';
					var datos = JSON.parse(result);
					$(datos).each( function() {
						select += '<option value="'+this.id+'"">'+this.name+'</option>';
					});

					$("#second_select").html(select);
					$("#secondGroup").show(500);
				});
			}
			else if ($('#kind').val() == 1) //subproceso
			{
				$.get('get_subprocesses.'+$("#orgs").val(), function (result) {
					$("#second_select").empty();
					$("#second_select").change();

					//agregamos label
					$("#label_second_select").html('Seleccione subproceso')
					//parseamos datos obtenidos
					select = '<option value="" selected>- Seleccione -</option>';
					var datos = JSON.parse(result);
					$(datos).each( function() {
						select += '<option value="'+this.id+'"">'+this.name+'</option>';
					});

					$("#second_select").html(select);
					$("#secondGroup").show(500);
				});
			}
			else if ($('#kind').val() == 2) //organización
			{
				$("#second_select").empty();
				$("#second_select").change();
				$("#secondGroup").hide(500);
			}	
			else if ($('#kind').val() == 3) //controles de proceso (obtendremos lista de procesos igual que en hallazgos de proceso)
			{
				$.get('get_processes.'+$("#orgs").val(), function (result) {
					$("#second_select").empty();
					$("#second_select").change();

					//agregamos label
					$("#label_second_select").html('Seleccione proceso')
					//parseamos datos obtenidos
					select = '<option value="" selected>- Seleccione -</option>';
					var datos = JSON.parse(result);
					$(datos).each( function() {
						select += '<option value="'+this.id+'"">'+this.name+'</option>';
					});

					$("#second_select").html(select);
					$("#secondGroup").show(500);
				});
			}	
			else if ($('#kind').val() == 4) //controles de negocio
			{
				$.get('get_objectives.'+$("#orgs").val(), function (result) {
					$("#second_select").empty();
					$("#second_select").change();

					//agregamos label
					$("#label_second_select").html('Seleccione objetivo corporativo')
					//parseamos datos obtenidos
					select = '<option value="" selected>- Seleccione -</option>';
					var datos = JSON.parse(result);
					$(datos).each( function() {
						select += '<option value="'+this.id+'"">'+this.name+'</option>';
					});

					$("#second_select").html(select);
					$("#secondGroup").show(500);
				});
			}	
			else if ($('#kind').val() == 5) //programas de auditoría
			{
				$.get('get_audit_programs.'+$("#orgs").val(), function (result) {
					$("#second_select").empty();
					$("#second_select").change();

					//agregamos label
					$("#label_second_select").html('Seleccione programa')
					//parseamos datos obtenidos
					select = '<option value="" selected>- Seleccione -</option>';
					var datos = JSON.parse(result);
					$(datos).each( function() {
						select += '<option value="'+this.id+'"">'+this.name+'</option>';
					});

					$("#second_select").html(select);
					$("#secondGroup").show(500);
				});
			}	
			else if ($('#kind').val() == 6) //auditorías
			{
				$.get('get_audits.'+$("#orgs").val(), function (result) {
					$("#second_select").empty();
					$("#second_select").change();

					//agregamos label
					$("#label_second_select").html('Seleccione auditoría')
					//parseamos datos obtenidos
					select = '<option value="" selected>- Seleccione -</option>';
					var datos = JSON.parse(result);
					$(datos).each( function() {
						select += '<option value="'+this.id+'"">'+this.name+'</option>';
					});

					$("#second_select").html(select);
					$("#secondGroup").show(500);
				});
			}	
			else if ($('#kind').val() == 7) //pruebas de auditoría
			{
				$.get('get_audit_tests.'+$("#orgs").val(), function (result) {
					$("#second_select").empty();
					$("#second_select").change();

					//agregamos label
					$("#label_second_select").html('Seleccione prueba de auditoría')

					//parseamos datos obtenidos
					select = '<option value="" selected>- Seleccione -</option>';
					var datos = JSON.parse(result);
					$(datos).each( function() {
						select += '<option value="'+this.id+'"">'+this.name+'</option>';
					});

					$("#second_select").html(select);
					$("#secondGroup").show(500);
				});
			}			
		}
		else
		{
			$("#second_select").empty();
			$("#second_select").change();
		}
	}
	else
	{
		swal('Cuidado','Primero debe seleccionar la organización','warning');
	}
}
</script>
@stop