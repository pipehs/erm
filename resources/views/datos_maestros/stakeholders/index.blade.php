@extends('master')

@section('title', 'Stakeholders')

@stop

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Datos Maestros</a></li>
			<li><a href="stakeholders">Stakeholders</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Stakeholders</span>
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

		{!! link_to_route('stakeholders.create', $title = 'Agregar Stakeholder', $parameters = NULL, $attributes = ['class'=>'btn btn-primary']) !!}

	@if (strpos($_SERVER['REQUEST_URI'],"verbloqueados"))
		{!! link_to_route('stakeholders.index', $title = 'Ver Desbloqueadas', $parameters = NULL, $attributes = ['class'=>'btn btn-success']) !!}
	@else
		{!! link_to_route('stakeholders.verbloqueados', $title = 'Ver Bloqueados', $parameters = 'verbloqueados', $attributes = ['class'=>'btn btn-danger']) !!}
	@endif

	<table class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
	<thead>
	<th width="8%">Rut</th>
	<th>Nombre</th>
	<th>Apellidos</th>
	<th>Tipo</th>
	<th>Fecha Agregado</th>
	<th>Fecha Actualizado</th>
	<th>Correo Electr&oacute;nico</th>
	<th>Organizacion(es)</th>
	<th>Cargo</th>
	<th>Acci&oacute;n</th>
	<th>Acci&oacute;n</th>
	</thead>

	@foreach($stakeholders as $stakeholder)
		<tr>
			<td>{{ $stakeholder['id'] }}-{{ $stakeholder['dv'] }}</td>
			<td>{{ $stakeholder['nombre'] }}</td>
			<td>{{ $stakeholder['apellidos']}}</td>
			<td><ul>
			@foreach ($roles as $role)
				@if ($role['stakeholder_id'] == $stakeholder['id'])
					<li>{{ $role['nombre'] }}</li>
				@endif
			@endforeach
			</ul></td>
			<td>{{ $stakeholder['fecha_creacion'] }}></td>
			<td>{{ $stakeholder['fecha_act'] }}</td>
			<td>{{ $stakeholder['correo'] }}</td>
			<td><ul>
			@foreach ($organizaciones as $organizacion)
				@if ($organizacion['stakeholder_id'] == $stakeholder['id'])
					<li>{{ $organizacion['nombre'] }}</li>
				@endif
			@endforeach
			</ul></td>
			<td>{{ $stakeholder['cargo'] }}</td>
			<td> 
			<div>
			@if ($stakeholder['estado'] == 0)
	            {!! link_to_route('stakeholders.edit', $title = 'Editar', $parameters = $stakeholder['id'], $attributes = ['class'=>'btn btn-success']) !!}
	        @else
	        	{!! link_to_route('stakeholders.desbloquear', $title = 'Desbloquear', $parameters = $stakeholder['id'], $attributes = ['class'=>'btn btn-success']) !!}
	        @endif
	        </div><!-- /btn-group -->
		</td>
		<td>
			<div>
			@if ($stakeholder['estado'] == 0)
	             <button class="btn btn-danger" onclick="bloquear({{ $stakeholder['id'] }},'{{ $stakeholder['nombre']." ".$stakeholder['apellidos'] }}','stakeholders','el usuario')">Bloquear</button>
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

