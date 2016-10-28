@extends('master')

@section('title', 'Auditor&iacute;as - Programas')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="auditorias">Auditor&iacute;as</a></li>
			<li><a href="programas_auditoria">Programas de auditor&iacute;as</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-sm-8">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Programas de auditor&iacute;as</span>
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


@if (!isset($programs))
	<p>En esta secci&oacute;n podr&aacute; ver los programas de auditor&iacute;as de riesgos y generar nuevos programas.</p>

	<div id="cargando"><br></div>

	{!!Form::open(['route'=>'programas_auditoria2','method'=>'GET','class'=>'form-horizontal'])!!}
	@include('auditorias.form_basico_audit')

	<div class="form-group">
		<center>
			{!!Form::submit('Seleccionar', ['class'=>'btn btn-success','id'=>'guardar'])!!}
		</center>
	</div>
	{!!Form::close()!!}
@else
	<p>
	<ul>
	<li>Organizaci&oacute;n: {{ $org_name }}</li>
	<li>Plan de auditor&iacute;a: {{ $audit_plan_name }}</li>
	<li>Auditor&iacute;a: {{ $audit_name }}</li>
	</ul>
	</p>
	@foreach (Session::get('roles') as $role)
		@if ($role != 6)
			<a href="crear_pruebas.{{ $audit_id }}" class="btn btn-primary">Agregar Nuevo Programa</a>
		<?php break; ?>
		@endif
	@endforeach

	<table class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
	<thead>
		<th>Nombre</th>
		<th>Descripci&oacute;n</th>
		<th>Fecha Agregado</th>
		<th>&Uacute;ltima actualizaci&oacute;n</th>
		<th>Fecha fin programa</th>
		<th>Acci&oacute;n</th>
		<th>Acci&oacute;n</th>
	</thead>

	@foreach($programs as $program)
		<tr>
			<td>{{ $program['name'] }}</td>
			<td>
			@if ($program['expiration_date'] == NULL)
				No se ha definido descripci&oacute;n
			@else
				{{ $program['description'] }}
			@endif
			</td>
			<td>{{ $program['created_at'] }}</td>
			<td>{{ $program['updated_at'] }}</td>
			<td>
			@if ($program['expiration_date'] == NULL)
				Ninguna
			@else
				{{ $program['expiration_date'] }}
			@endif
			</td>
			<td>{!! link_to_route('programas_auditoria.show', $title = 'Ver', $parameters = $program['id'], $attributes = ['class'=>'btn btn-success']) !!}</td>
			<td><button class="btn btn-danger" onclick="eliminar2({{ $program['id'] }},'{{ $program['name'] }}','programas_auditoria','El programa de auditoría')">Eliminar</button></td>
		</tr>
	@endforeach
	</table>

	<center>
		{!! link_to_route('programas_auditoria', $title = 'Volver', $parameters=null, $attributes = ['class'=>'btn btn-danger']) !!}
	</center>
@endif
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts2')
	{!!Html::script('assets/js/audits.js')!!}
@stop
