@extends('master')

@section('title', 'Auditor&iacute;a de Riesgos')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Auditor&iacute;a de Riesgos</a></li>
			<li><a href="show_program.{{$program['id']}}">Programa</a></li>
		</ol>
	</div>
</div>
<center>
<div class="row">
	<div class="col-xs-12 col-sm-12">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-check"></i>
					<span>Programa: {{ $program['name'] }}</span>
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

			@if(Session::has('message'))
			<div class="alert alert-success alert-dismissible" role="alert">
				{{ Session::get('message') }}
			</div>
			@endif

			<ul style="text-align: left;">
			<li><b>Descripci&oacute;n: {{ $program['description'] }}</b></li>
			<li><b>Fecha creaci&oacute;n: {{ $program['created_at'] }}</b></li>
			<li><b>Fecha fin: {{ $program['expiration_date'] }}</b></li>
			<li><b>Documento asociado: 

			@if ($program['evidence'] == NULL)
				El programa no tiene ning&uacute;n documento asociado
			@else
			<table>
			<tr>
			<td>
				<div style="cursor:hand" id="descargar_{{ $program['id'] }}" onclick="descargar(4,'{{$program['evidence'][0]['url'] }}')"><font color="CornflowerBlue"><u>Descargar</u></font></div>
			</td>
	@foreach (Session::get('roles') as $role)
		@if ($role != 6)
			<td>&nbsp;&nbsp;
				<img src="assets/img/btn_eliminar.png" height="40px" width="40px" onclick="eliminar_ev({{ $program['id'] }},1)">
			</td>
		<?php break; ?>
		@endif
	@endforeach
			</tr>
			</table>
			</br>
			@endif
			</b></li>
	@foreach (Session::get('roles') as $role)
		@if ($role != 6)		
			<li>{!! link_to_route('programas_auditoria.edit', $title = 'Editar programa', $parameters = $program['id'],
				 $attributes = ['class'=>'btn btn-info'])!!}</li>
		<?php break; ?>
		@endif
	@endforeach
			<hr>
			<li><b><u>Pruebas del programa</u></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		@foreach (Session::get('roles') as $role)
			@if ($role != 6)
			{!! link_to_route('programas_auditoria.create_test', $title = 'Agregar prueba', $parameters = $program['id'],
				 $attributes = ['class'=>'btn btn-success'])!!}
			<?php break; ?>
			@endif
		@endforeach
			</li>
			</ul>
			<hr>
			<table class="table table-bordered table-striped table-hover table-heading table-datatable" width="50%">
			<tr>
				<th>Nombre</th>
				<th>Descripci&oacute;n</th>
				<th>Fecha creaci&oacute;n</th>
				<th>Fecha actualizaci&oacute;n</th>
				<th>Tipo</th>
				<th>Estado</th>
				<th>Resultado</th>
				<th>Responsable</th>
				<th>Horas / Hombre</th>
				<th>Documento</th>
		@foreach (Session::get('roles') as $role)
			@if ($role != 6)
				<th>Acci&oacute;n</th>
				<th>Acci&oacute;n</th>
			<?php break; ?>
			@endif
		@endforeach
			</tr>

			@foreach ($program['tests'] as $test)
				<tr>

					<td>{{ $test['name'] }}</td>

					<td>{{ $test['description'] }}</td>

					<td>{{ $test['created_at'] }}</td>

					<td>{{ $test['updated_at'] }}</td>

					<td>{{ $test['type'] }}</td>

					<td>{{ $test['status'] }}</td>				
					
					<td>{{ $test['results'] }}</td>

					<td>{{ $test['stakeholder'] }}</td>

					<td>{{ $test['hh'] }}</td>

					<td>
					@if ($test['evidence'] == NULL)
						No tiene documentos
					@else
						<div style="cursor:hand" id="descargar_{{ $test['id'] }}" onclick="descargar(5,'{{$test['evidence'][0]['url'] }}')"><font color="CornflowerBlue"><u>Descargar</u></font></div>
					@foreach (Session::get('roles') as $role)
						@if ($role != 6)
						<img src="assets/img/btn_eliminar.png" height="40px" width="40px" onclick="eliminar_ev({{ $test['id'] }},0)">
						<?php break; ?>
						@endif
					@endforeach

						</br>
					@endif
					</td>
		@foreach (Session::get('roles') as $role)
			@if ($role != 6)
					<td>{!! link_to_route('programas_auditoria.edit_test', $title = 'Editar', $parameters = $test['id'],
				 $attributes = ['class'=>'btn btn-success'])!!}</td>
				 	<td><button class="btn btn-danger" onclick="eliminar2({{ $test['id'] }},'{{ $test['name'] }}','audit_tests','La prueba de auditoría')">Eliminar</button></td>
			<?php break; ?>
			@endif
		@endforeach

				</tr>
			@endforeach
			</table>
			</br>
			<center>
				{!! link_to('', $title = 'Volver', $attributes = ['class'=>'btn btn-danger', 'onclick' => 'history.back()'])!!}
			<center>
			</div>
		</div>
	</div>
</div>
@stop
