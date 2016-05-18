@extends('master')

@section('title', 'Riesgos')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Datos Maestros</a></li>
			<li><a href="riesgos">Identificación de Riesgos</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-ticket"></i>
					<span>Riesgos</span>
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

		<p>En esta secci&oacute;n podr&aacute; identificar un riesgo formal en base al an&aacute;lisis realizado sobre los eventos de riesgo. 
		Tambi&eacute;n podr&aacute; ver los riesgos previamente identificados.</p>
		<center>
		{!! link_to_route('riesgos.create', $title = 'Agregar Riesgo de Proceso', $parameters = 'P', $attributes = ['class'=>'btn btn-primary']) !!}
		&nbsp;&nbsp;
		{!! link_to_route('riesgos.create', $title = 'Agregar Riesgo de Negocio', $parameters = 'N', $attributes = ['class'=>'btn btn-success']) !!}
		</center>
	
	<table class="table table-bordered table-striped table-hover table-heading table-datatable" id="datatable-2" style="font-size:11px">
	<thead>
	<th>Nombre<label><input type="text" placeholder="Filtrar" /></label></th>
	<th>Descripci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
	<th>Tipo<label><input type="text" placeholder="Filtrar" /></label></th>
	<th>Categor&iacute;a<label><input type="text" placeholder="Filtrar" /></label></th>
	<th>Fecha Creaci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
	<th>Fecha Expiraci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
	<th>Responsable<label><input type="text" placeholder="Filtrar" /></label></th>
	<th>Subprocesos u Objetivos Relacionados<label><input type="text" placeholder="Filtrar" /></label></th>
	<th>Causa(s)<label><input type="text" placeholder="Filtrar" /></label></th>
	<th>Efecto(s)<label><input type="text" placeholder="Filtrar" /></label></th>
	<th>Editar</th>
	</thead>
	@foreach ($riesgos as $riesgo)
		<tr>
		<td>{{ $riesgo['nombre'] }}</td>
		<td>{{ $riesgo['descripcion'] }}</td>
		<td>{{ $riesgo['tipo'] }}</td>
		<td>{{ $riesgo['categoria'] }}</td>
		<td>{{ $riesgo['fecha_creacion'] }}</td>
		<td>{{ $riesgo['fecha_exp'] }}</td>
		<td>{{ $riesgo['stakeholder'] }}</td>
		<td>
		<ul>
		@foreach($relacionados as $subonegocio)
			@if ($subonegocio['risk_id'] == $riesgo['id'])
				@if ($subonegocio['org_name'] != "")
						<li>{{ $subonegocio['nombre'] }} - {{ $subonegocio['org_name'] }}</li>
				@else
						<li>{{ $subonegocio['nombre'] }}</li>
				@endif
			@endif
		@endforeach
		</ul>	
		</td>
		<td>
		@if (gettype($riesgo['causas']) == "array") 
			@foreach ($riesgo['causas'] as $causa)
				<li>{{ $causa }}</li>
			@endforeach
		@else
			<li>{{ $riesgo['causas'] }}</li>
		@endif
		</td>
		<td>
		@if (gettype($riesgo['efectos']) == "array") 
			@foreach ($riesgo['efectos'] as $efecto)
				<li>{{ $efecto }}</li>
			@endforeach
		@else
			<li>{{ $riesgo['efectos'] }}
		@endif
		</td>
		<td>{!! link_to_route('riesgos.edit', $title = 'Editar', $parameters = $riesgo['id'], $attributes = ['class'=>'btn btn-success']) !!}</td>
		</tr>
	@endforeach
	</table>

			</div>
		</div>
	</div>
</div>
@stop


