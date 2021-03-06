@extends('master')

@section('title', 'Subprocesos')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Datos Maestros</a></li>
			<li><a href="subprocesos">Subprocesos</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<span>Subprocesos</span>
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
			{!! link_to_route('subprocesos.create', $title = 'Agregar Subproceso', $parameters = NULL, $attributes = ['class'=>'btn btn-primary']) !!}

		@if (strpos($_SERVER['REQUEST_URI'],"verbloqueados"))
			{!! link_to_route('subprocesos.index', $title = 'Ver Desbloqueadas', $parameters = NULL, $attributes = ['class'=>'btn btn-success']) !!}
		@else
			{!! link_to_route('subprocesos.verbloqueados', $title = 'Ver Bloqueados', $parameters = 'verbloqueados', $attributes = ['class'=>'btn btn-danger']) !!}
		@endif
	<?php break; ?>
	@endif
@endforeach
	<table class="table table-bordered table-striped table-hover table-heading table-datatable" id="datatable-2" style="font-size: 11px;">
	<thead>
		<tr>
			<th>Organización - Responsable</small><label><input type="text" placeholder="Filtrar" /></label></th>
			<th>Proceso involucrado</small><label><input type="text" placeholder="Filtrar" /></label></th>
			<th>Subproceso</small><label><input type="text" placeholder="Filtrar" /></label></th>
			<th>Descripci&oacute;n</small><label><input type="text" placeholder="Filtrar" /></label></th>
			<th>Fecha Creaci&oacute;n</small><label><input type="text" placeholder="Filtrar" /></label></th>
			<th>Sistemas/Plataformas</small><label><input type="text" placeholder="Filtrar" /></label></th>
			<th>Habeas Data</small><label><input type="text" placeholder="Filtrar" /></label></th>
			<th>Marco Regulatorio</small><label><input type="text" placeholder="Filtrar" /></label></th>
			<th>Subprocesos Dependientes</small><label><input type="text" placeholder="Filtrar" /></label></th>
		@foreach (Session::get('roles') as $role)
			@if ($role != 6)
			<th style="vertical-align:top;">Asignar atributos</th>
			<th style="vertical-align:top;">Acci&oacute;n</th>
			<th style="vertical-align:top;">Acci&oacute;n</th>
			<?php break; ?>
			@endif
		@endforeach
		</tr>
	</thead>
	<tr style="display:none;">
@foreach (Session::get('roles') as $role)
	@if ($role != 6)
		<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
		<?php break; ?>
	@else
		<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
	@endif
@endforeach
	@foreach ($subprocesos as $subproceso)
		<tr>
		<td><ul>
		@foreach ($subproceso['organizaciones'] as $o)
			<li>{{ $o['nombre'] }} - 

			@if ($o['responsable'] != NULL)
				{{ $o['responsable'] }}
			@else
				No se ha asignado responsable
			@endif
			</li>
		@endforeach
		</ul></td>
		<td>{{ $subproceso['proceso_relacionado'] }}</td>
		<td>{{ $subproceso['nombre'] }}</td>
		<td>
		@if (strlen($subproceso['descripcion']) > 100)
			<div id="description_{{$subproceso['id']}}" title="{{ $subproceso['descripcion'] }}">{{ $subproceso['short_des'] }}...
			<div style="cursor:hand" onclick="expandir({{ $subproceso['id'] }},'{{ $subproceso['descripcion'] }}','{{ $subproceso['short_des'] }}')">
			<font color="CornflowerBlue">Ver completo</font>
			</div></div>
		@else
			{{ $subproceso['descripcion'] }}
		@endif
		</td>
		@if ($subproceso['fecha_creacion'] == NULL)
			<td>Error al registrar fecha de creaci&oacute;n</td>
		@else
			<td>{{ $subproceso['fecha_creacion'] }}</td>
		@endif

		@if ($subproceso['systems'] == NULL)
			<td>No definido</td>
		@else
			<td>{{ $subproceso['systems'] }}</td>
		@endif

		@if ($subproceso['habeas_data'] == NULL)
			<td>No definido</td>
		@else
			<td>{{ $subproceso['habeas_data'] }}</td>
		@endif

		@if ($subproceso['regulatory_framework'] == NULL)
			<td>No definido</td>
		@else
			<td>{{ $subproceso['regulatory_framework'] }}</td>
		@endif

		<td><ul style="none">
		@if (empty($subproceso['sub_dependientes']))
			Ninguno
		@else
			@foreach ($subproceso['sub_dependientes'] as $s)
					<li>{{ $s['nombre'] }}</li>
			@endforeach
		@endif
		</ul></td>
@foreach (Session::get('roles') as $role)
	@if ($role != 6)
		<td>
			@if (!empty($subproceso['organizaciones']))
				{!! link_to_route('subprocesos.attributes', $title = 'Asignar', $parameters = $subproceso['id'], $attributes = ['class'=>'btn btn-info']) !!}
			@else
				Primero debe asignar organizaciones
			@endif
		</td>	
		<td><div>
			@if ($subproceso['estado'] == 0)
	            {!! link_to_route('subprocesos.edit', $title = 'Editar', $parameters = $subproceso['id'], $attributes = ['class'=>'btn btn-success']) !!}
	        @else
	        	{!! link_to_route('subprocesos.desbloquear', $title = 'Desbloquear', $parameters = $subproceso['id'], $attributes = ['class'=>'btn btn-success']) !!}
	        @endif
	        </div><!-- /btn-group --></td>
		<td><div>
			@if ($subproceso['estado'] == 0)
	            <button class="btn btn-danger" onclick="bloquear({{ $subproceso['id'] }},'{{ $subproceso['nombre'] }}','subprocesos','El subproceso')">Bloquear</button>
	        @else
	      		<button class="btn btn-danger" onclick="eliminar2({{ $subproceso['id'] }},'{{ $subproceso['nombre'] }}','subprocesos','El subproceso')">Eliminar</button>
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
