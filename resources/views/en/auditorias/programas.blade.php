@extends('en.master')

@section('title', 'Audit Programs')


@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="auditorias">Audits</a></li>
			<li><a href="programas_auditoria">Audit Programs</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-sm-8">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Audit Programs</span>
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
	      <p>On this section you will be able to see and generate the audit programs.</p>

				@if(Session::has('message'))
					<div class="alert alert-success alert-dismissible" role="alert">
					{{ Session::get('message') }}
					</div>
				@endif
@foreach (Session::get('roles') as $role)
	@if ($role != 6)
		{!! link_to_route('crear_pruebas', $title = 'Create New Program', $parameters = NULL, $attributes = ['class'=>'btn btn-primary']) !!}
	<?php break; ?>
	@endif
@endforeach

	<table class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
	<thead>
		<th>Audit Plan</th>
		<th>Audit</th>
		<th>Name</th>
		<th>Descripti&oacute;n</th>
		<th>Creation date</th>
		<th>Last update</th>
		<th>Final date</th>
		<th>Action</th>
		<th>Action</th>
	</thead>

	@foreach($programs as $program)
		<tr>
			<td>{{ $program['audit_plan'] }}</td>
			<td>{{ $program['audit'] }}</td>
			<td>{{ $program['name'] }}</td>
			<td>{{ $program['description'] }}</td>
			<td>{{ $program['created_at'] }}</td>
			<td>{{ $program['updated_at'] }}</td>
			<td>
			@if ($program['expiration_date'] == NULL)
				None
			@else
				{{ $program['expiration_date'] }}
			@endif
			</td>
			<td>{!! link_to_route('programas_auditoria.show', $title = 'Show', $parameters = $program['id'], $attributes = ['class'=>'btn btn-success']) !!}</td>
			<td><button class="btn btn-danger" onclick="eliminar2({{ $program['id'] }},'{{ $program['name'] }}','audit_program','The audit program')">Delete</button></td>
		</tr>
	@endforeach
	</table>

			</div>
		</div>
	</div>
</div>
@stop
