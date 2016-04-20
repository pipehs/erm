@extends('master')

@section('title', 'Auditor&iacute;a de Riesgos')

@stop

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Auditor&iacute;a de Riesgos</a></li>
			<li><a href="show_program.{{$program['id']}}">Ver Programa</a></li>
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

			<!--	FALTA EDITAR 
				{!! link_to_route('plan_auditoria.edit', $title = 'Editar', $parameters = $program['id'],
				 $attributes = ['class'=>'btn btn-success'])!!} -->
			<ul style="text-align: left;">
			<li><b>Descripci&oacute;n: {{ $program['description'] }}</b></li>
			<li><b>Fecha creaci&oacute;n: {{ $program['created_at'] }}</b></li>
			<li><b>Fecha fin: {{ $program['expiration_date'] }}</b></li>
			<hr>
			<li><b><u>Pruebas del programa</u></b></li>
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

				</tr>
			@endforeach
			</table>

			<center>
				{!! link_to_route('programas_auditoria', $title = 'Volver', $parameters = NULL,
				 $attributes = ['class'=>'btn btn-danger'])!!}
			<center>
			</div>
		</div>
	</div>
</div>
@stop
