@extends('master')

@section('title', 'Auditor&iacute;as')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="auditorias">Universo de auditor&iacute;as</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-sm-8">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Auditor&iacute;as</span>
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
	      <p>En esta secci&oacute;n podr&aacute; ver, mantener y generar auditor&iacute;as para lo posterior asignaci&oacute;n 
	      en los distintos planes de auditor&iacute;a.</p>

				@if(Session::has('message'))
			<div class="alert alert-success alert-dismissible" role="alert">
			{{ Session::get('message') }}
			</div>
		@endif

		{!! link_to_route('crear_auditoria', $title = 'Agregar Nueva Auditor&iacute;a', $parameters = NULL, $attributes = ['class'=>'btn btn-primary']) !!}

	<table class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
	<thead>
		<th>Nombre</th>
		<th>Descripci&oacute;n</th>
		<th>Fecha Agregado</th>
		<th>&Uacute;ltima actualizaci&oacute;n</th>
		<th>Ver</th>
	</thead>

	@foreach($audits as $audit)
		<tr>
			<td>{{ $audit['name'] }}</td>
			<td>{{ $audit['description'] }}</td>
			<td>{{ $audit['created_at'] }}</td>
			<td>{{ $audit['updated_at'] }}</td>
			<td>
				<div>
		            {!! link_to_route('auditorias.show', $title = 'Ver', $parameters = $audit['id'], $attributes = ['class'=>'btn btn-warning']) !!}
		        </div><!-- /btn-group -->
			</td>
		</tr>
	@endforeach
	</table>

			</div>
		</div>
	</div>
</div>
@stop
