@extends('master')

@section('title', 'Causas')

@stop

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Datos Maestros</a></li>
			<li><a href="causas">Causas</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-ticket"></i>
					<span>Causas</span>
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

		<p>En esta secci&oacute;n podr&aacute; agregar, ver, editar o bloquear causas gen&eacute;ricas para los futuros riesgos identificados o riesgos tipo.</p>

		{!! link_to_route('causas.create', $title = 'Agregar Causa', $parameters = NULL, $attributes = ['class'=>'btn btn-primary']) !!}

	@if (strpos($_SERVER['REQUEST_URI'],"verbloqueados"))
		{!! link_to_route('causas.index', $title = 'Ver Desbloqueados', $parameters = NULL, $attributes = ['class'=>'btn btn-success']) !!}
	@else
		{!! link_to_route('causas.index', $title = 'Ver Bloqueados', $parameters = 'verbloqueados', $attributes = ['class'=>'btn btn-danger']) !!}
	@endif
	<table class="table table-bordered table-striped table-hover table-heading table-datatable" id="datatable-2" style="font-size:11px">
	<thead>
	<th>Nombre<label><input type='text' placeholder='Filtrar' /></label></th>
	<th width="30%">Descripci&oacute;n<label><input type='text' placeholder='Filtrar' /></label></th>
	<th>Fecha Creaci&oacute;n<label><input type='text' placeholder='Filtrar' /></label></th>
	<th>Fecha de Actualizaci&oacute;n<label><input type='text' placeholder='Filtrar' /></label></th>
	<th>Acci&oacute;n</th>
	<th>Acci&oacute;n</th>
	</thead>
	@foreach ($causas as $causa)
		<tr>
		<td>{{ $causa['nombre'] }}</td>
		<td>
		@if ($causa['descripcion'] == NULL)
			Ninguna
		@else
			{{ $causa['descripcion'] }}
		@endif
		</td>
		<td>{{ $causa['fecha_creacion'] }}</td>
		<td>{{ $causa['fecha_act'] }}</td>
		<td><div>
			@if ($causa['estado'] == 0)
	            {!! link_to_route('causas.edit', $title = 'Editar', $parameters = $causa['id'], $attributes = ['class'=>'btn btn-success']) !!}
	        @else
	        	{!! link_to_route('causas.desbloquear', $title = 'Desbloquear', $parameters = $causa['id'], $attributes = ['class'=>'btn btn-success']) !!}
	        @endif
	        </div><!-- /btn-group --></td>
		<td><div>
			@if ($causa['estado'] == 0)
	             <button class="btn btn-danger" onclick="bloquear({{ $causa['id'] }},'{{ $causa['nombre'] }}','causas','la causa')">Bloquear</button>
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

