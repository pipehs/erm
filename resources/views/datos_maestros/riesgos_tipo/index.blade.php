@extends('master')

@section('title', 'Riesgos Tipo')

@stop

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Datos Maestros</a></li>
			<li><a href="riskstype">Riesgos Tipo</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-ticket"></i>
					<span>Riesgos Tipo</span>
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

		{!! link_to_route('riskstype.create', $title = 'Agregar Riesgo', $parameters = NULL, $attributes = ['class'=>'btn btn-primary']) !!}

	@if (strpos($_SERVER['REQUEST_URI'],"verbloqueados"))
		{!! link_to_route('riskstype.index', $title = 'Ver Desbloqueados', $parameters = NULL, $attributes = ['class'=>'btn btn-success']) !!}
	@else
		{!! link_to_route('riskstype.index', $title = 'Ver Bloqueados', $parameters = 'verbloqueados', $attributes = ['class'=>'btn btn-danger']) !!}
	@endif
	<table class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
	<thead>
	<th>Nombre</th>
	<th>Descripci&oacute;n</th>
	<th>Categor&iacute;a</th>
	<th>Fecha Creaci&oacute;n</th>
	<th>Fecha Actualizaci&oacute;n</th>
	<th>Fecha Expiraci&oacute;n</th>
	<th>Causa</th>
	<th>Efecto</th>
	<th>Acci&oacute;n</th>
	<th>Acci&oacute;n</th>
	</thead>
	@foreach ($riesgos as $riesgo)
		<tr>
		<td>{{ $riesgo['nombre'] }}</td>
		<td>{{ $riesgo['descripcion'] }}</td>
		<td>{{ $riesgo['categoria'] }}</td>
		<td>{{ $riesgo['fecha_creacion'] }}</td>
		<td>{{ $riesgo['fecha_act'] }}</td>
		<td>{{ $riesgo['fecha_exp'] }}</td>
		<td>{{ $riesgo['causa'] }}</td>
		<td>{{ $riesgo['efecto'] }}</td>
		<td><div>
			@if ($riesgo['estado'] == 0)
	            {!! link_to_route('riskstype.edit', $title = 'Editar', $parameters = $riesgo['id'], $attributes = ['class'=>'btn btn-success']) !!}
	        @else
	        	{!! link_to_route('riskstype.desbloquear', $title = 'Desbloquear', $parameters = $riesgo['id'], $attributes = ['class'=>'btn btn-success']) !!}
	        @endif
	        </div><!-- /btn-group --></td>
		<td><div>
			@if ($riesgo['estado'] == 0)
	             <button class="btn btn-danger" onclick="bloquear({{ $riesgo['id'] }},'{{ $riesgo['nombre'] }}','riskstype','el riesgo tipo')">Bloquear</button>
	        @else
	    
	        @endif
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

