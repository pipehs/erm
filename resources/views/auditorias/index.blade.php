@extends('master')

@section('title', 'Auditor&iacute;as - Planes de auditor&iacute;as')

@stop

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="auditorias">Auditor&iacute;as</a></li>
			<li><a href="plan_auditoria">Planes de auditor&iacute;as</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-sm-8">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Planes de auditor&iacute;as</span>
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
	      <p>En esta secci&oacute;n podr&aacute; ver los planes de auditor&iacute;as de riesgos y generar nuevos planes.</p>

				@if(Session::has('message'))
					<div class="alert alert-success alert-dismissible" role="alert">
					{{ Session::get('message') }}
					</div>
				@endif

		{!! link_to_route('plan_auditoria.create', $title = 'Agregar Nuevo Plan', $parameters = NULL, $attributes = ['class'=>'btn btn-primary']) !!}

	<table class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
	<thead>
		<th>Nombre</th>
		<th>Descripci&oacute;n</th>
		<th>Fecha Agregado</th>
		<th>&Uacute;ltima actualizaci&oacute;n</th>
		<th>Ver</th>
	</thead>

	@foreach($planes as $plan)
		<tr>
			<td>{{ $plan['name'] }}</td>
			<td>{{ $plan['description'] }}</td>
			<td>{{ $plan['created_at'] }}</td>
			<td>{{ $plan['updated_at'] }}</td>
			<td>
				<div>
		            {!! link_to_route('plan_auditoria.show', $title = 'Ver', $parameters = $plan['id'], $attributes = ['class'=>'btn btn-warning']) !!}
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
