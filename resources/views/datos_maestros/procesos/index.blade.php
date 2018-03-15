@extends('master')

@section('title', 'Procesos')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Datos Maestros</a></li>
			<li><a href="procesos">Procesos</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<span>Procesos</span>
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
				<div class="no-move"></div>
			</div>
			<div class="box-content">

		@if(Session::has('message'))
			<div class="alert alert-success alert-dismissible" role="alert">
			{{ Session::get('message') }}
			</div>
		@endif
@foreach (Session::get('roles') as $role)
	@if ($role != 6)
		{!! link_to_route('procesos.create', $title = 'Agregar Proceso', $parameters = NULL, $attributes = ['class'=>'btn btn-primary']) !!}

	@if (strpos($_SERVER['REQUEST_URI'],"verbloqueados"))
		{!! link_to_route('procesos.index', $title = 'Ver Desbloqueadas', $parameters = NULL, $attributes = ['class'=>'btn btn-success']) !!}
	@else
		{!! link_to_route('procesos.verbloqueados', $title = 'Ver Bloqueados', $parameters = 'verbloqueados', $attributes = ['class'=>'btn btn-danger']) !!}
	@endif

	<?php break; ?>
	@endif
@endforeach
	<table class="table table-bordered table-striped table-hover table-heading table-datatable" id="datatable-2" style="font-size:11px" >
					<thead>
							<th>Organizaciones<label><input type="text" placeholder="Filtrar" /></label></th>
							<th>Proceso<label><input type="text" placeholder="Filtrar" /></label></th>
							<th>Subprocesos<label><input type="text" placeholder="Filtrar" /></label></th>
							<th>Descripci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
							<th>Fecha Creaci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
							<th>Fecha Actualizaci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
							<th>Fecha Expiraci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
							<th>¿Depende de otro proceso?<label><input type="text" placeholder="Filtrar" /></label></th>
							@foreach (Session::get('roles') as $role)
								@if ($role != 6)
								<th style="vertical-align:top;">Acci&oacute;n</th>
								<th style="vertical-align:top;">Acci&oacute;n</th>
								<?php break; ?>
								@endif
							@endforeach
					</thead>
	<tr style="display:none;">
@foreach (Session::get('roles') as $role)
	@if ($role != 6)
		<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
		<?php break; ?>
	@else
		<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
	@endif
@endforeach
	@foreach ($procesos as $proceso)
		<tr>
		<td><ul>
		<?php $cont = 0; //contador para verificar si hay organizaciones ?>
		@foreach ($proceso['organizaciones'] as $organizacion)
			<li>{{ $organizacion['nombre'] }}</li>
			<?php $cont += 1;  //contador para verificar si hay organizaciones ?>
		@endforeach
		@if ($cont == 0)
				No hay organización relacionada
		@endif
		</ul></td>
		<td>{{ $proceso['nombre'] }}</td>
		<td>
		@if ($subprocesos != NULL)
			<ul style="none">
			@foreach ($subprocesos as $subproceso)
				@if ($subproceso['proceso_id'] == $proceso['id'])
					<li>{{ $subproceso['nombre'] }}</li>
				@endif
			@endforeach
			</ul>
		@else
			Aun no hay subprocesos relacionados
		@endif
		</td>
		<td>
		@if (strlen($proceso['descripcion']) > 100)
			<div id="description_{{$proceso['id']}}" title="{{ $proceso['descripcion'] }}">{{ $proceso['short_des'] }}...
			<div style="cursor:hand" onclick="expandir({{ $proceso['id'] }},'{{ $proceso['descripcion'] }}','{{ $proceso['short_des'] }}')">
			<font color="CornflowerBlue">Ver completo</font>
			</div></div>
		@else
			{{ $proceso['descripcion'] }}
		@endif
		</td>
		@if ($proceso['fecha_creacion'] == NULL)
			<td>Error al guardar fecha de creaci&oacute;n</td>
		@else
			<td>{{$proceso['fecha_creacion']}}</td>
		@endif

		@if ($proceso['fecha_act'] == NULL)
			<td>Error al guardar fecha de &uacute;ltima actualizaci&oacute;n</td>
		@else
			<td>{{$proceso['fecha_act']}}</td>
		@endif

		@if ($proceso['fecha_exp'] == NULL)
			<td>Ninguna</td>
		@else
			<td>{{$proceso['fecha_exp']}}</td>
		@endif
		<td>{{ $proceso['proceso_dependiente'] }}</td>
@foreach (Session::get('roles') as $role)
	@if ($role != 6)		
		<td><div>
			@if ($proceso['estado'] == 0)
	            {!! link_to_route('procesos.edit', $title = 'Editar', $parameters = $proceso['id'], $attributes = ['class'=>'btn btn-success']) !!}
	        @else
	        	{!! link_to_route('procesos.desbloquear', $title = 'Desbloquear', $parameters = $proceso['id'], $attributes = ['class'=>'btn btn-success']) !!}
	        @endif
	        </div><!-- /btn-group --></td>
		<td><div>
			@if ($proceso['estado'] == 0)
	           	<button class="btn btn-danger" onclick="bloquear({{ $proceso['id'] }},'{{ $proceso['nombre'] }}','procesos','El proceso')">Bloquear</button>
	        @else
	        	<button class="btn btn-danger" onclick="eliminar2({{ $proceso['id'] }},'{{ $proceso['nombre'] }}','procesos','El proceso')">Eliminar</button>
	        @endif
	        </div><!-- /btn-group -->
	    </td>
	<?php break; ?>
	@endif
@endforeach
		</tr>
	@endforeach
	</table>

			</div>
		</div>
	</div>
</div>
@stop

@section('scripts2')
@stop
