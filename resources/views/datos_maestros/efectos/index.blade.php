@extends('master')

@section('title', 'Efectos')

@stop

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Datos Maestros</a></li>
			<li><a href="efectos">Efectos</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-ticket"></i>
					<span>Efectos</span>
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

		<p>En esta secci&oacute;n podr&aacute; agregar, ver, editar o bloquear efectos gen&eacute;ricas para los futuros riesgos identificados o riesgos tipo.</p>

		{!! link_to_route('efectos.create', $title = 'Agregar Efecto', $parameters = NULL, $attributes = ['class'=>'btn btn-primary']) !!}

	@if (strpos($_SERVER['REQUEST_URI'],"verbloqueados"))
		{!! link_to_route('efectos.index', $title = 'Ver Desbloqueados', $parameters = NULL, $attributes = ['class'=>'btn btn-success']) !!}
	@else
		{!! link_to_route('efectos.index', $title = 'Ver Bloqueados', $parameters = 'verbloqueados', $attributes = ['class'=>'btn btn-danger']) !!}
	@endif
	<table class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
	<thead>
	<th>Nombre</th>
	<th>Descripci&oacute;n</th>
	<th>Fecha Creaci&oacute;n</th>
	<th>Fecha Actualizado</th>
	<th>Acci&oacute;n</th>
	<th>Acci&oacute;n</th>
	</thead>
	@foreach ($efectos as $efecto)
		<tr>
		<td>{{ $efecto['nombre'] }}</td>
		<td>
		@if ($efecto['descripcion'] == NULL)
			Ninguna
		@else
			{{ $efecto['descripcion'] }}
		@endif
		</td>
		<td>{{ $efecto['fecha_creacion'] }}</td>
		<td>{{ $efecto['fecha_act'] }}</td>
		<td><div>
			@if ($efecto['estado'] == 0)
	            {!! link_to_route('efectos.edit', $title = 'Editar', $parameters = $efecto['id'], $attributes = ['class'=>'btn btn-success']) !!}
	        @else
	        	{!! link_to_route('efectos.desbloquear', $title = 'Desbloquear', $parameters = $efecto['id'], $attributes = ['class'=>'btn btn-success']) !!}
	        @endif
	        </div><!-- /btn-group --></td>
		<td><div>
			@if ($efecto['estado'] == 0)
	             <button class="btn btn-danger" onclick="bloquear({{ $efecto['id'] }},'{{ $efecto['nombre'] }}','efectos','el efecto')">Bloquear</button>
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

