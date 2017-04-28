@extends('master')

@section('title', 'Organizaciones')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Datos Maestros</a></li>
			<li><a href="organization">Organizaciones</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-building-o"></i>
					<span>Organizaciones</span>
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


@foreach (Session::get('roles') as $role)
	@if ($role != 6)
			{!! link_to_route('organization.create', $title = 'Agregar Organizaci&oacute;n', $parameters = NULL, $attributes = ['class'=>'btn btn-primary']) !!}

		@if (strpos($_SERVER['REQUEST_URI'],"verbloqueados"))
			{!! link_to_route('organization.index', $title = 'Ver Desbloqueadas', $parameters = NULL, $attributes = ['class'=>'btn btn-success']) !!}
		@else
			{!! link_to_route('organization.verbloqueados', $title = 'Ver Bloqueadas', $parameters = 'verbloqueados', $attributes = ['class'=>'btn btn-danger']) !!}
		@endif

		<?php break; ?>
	@endif
@endforeach

	<table class="table table-bordered table-striped table-hover table-heading table-datatable" id="datatable-2" style="font-size:11px">
	<thead>
	<th style="vertical-align:top;">Nombre<label><input type="text" placeholder="Filtrar" /></label></th>
	<th style="vertical-align:top;" width="20%">Descripci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
	<th style="vertical-align:top;">Misi&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
	<th style="vertical-align:top;">Visi&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
	<th style="vertical-align:top;">Cliente objetivo<label><input type="text" placeholder="Filtrar" /></label></th>
	<th style="vertical-align:top;">Fecha expiraci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
	<th style="vertical-align:top;">Servicios compartidos<label><input type="text" placeholder="Filtrar" /></label></th>
	<th style="vertical-align:top;">Organizaciones dependientes<label><input type="text" placeholder="Filtrar" /></label></th>

@foreach (Session::get('roles') as $role)
	@if ($role != 6)
	<th style="vertical-align:top;">Acci&oacute;n</th>
	<th style="vertical-align:top;">Acci&oacute;n</th>
	<?php break; ?>
	@endif
@endforeach
	</thead>

	@foreach ($organizations as $organization)
		<tr>
		<td>{{$organization['nombre']}}</td>
		<td>
		@if (strlen($organization['descripcion']) > 100)
			<div id="description_{{$organization['id']}}" title="{{ $organization['descripcion'] }}">{{ $organization['short_des'] }}...
			<div style="cursor:hand" onclick="expandir({{ $organization['id'] }},'{{ $organization['descripcion'] }}','{{ $organization['short_des'] }}')">
			<font color="CornflowerBlue">Ver completo</font>
			</div></div>
		@else
			{{ $organization['descripcion'] }}
		@endif
		</td>
		@if ($organization['mision'] == NULL)
			<td>No se ha especificado la misi&oacute;n</td>
		@else
			<td>{{$organization['mision']}}</td>
		@endif
		
		@if ($organization['vision'] == NULL)
			<td>No se ha especificado visi&oacute;n</td>
		@else
			<td>{{$organization['vision']}}</td>
		@endif

		@if ($organization['target_client'] == NULL)
			<td>No se ha especificado el cliente objetivo</td>
		@else
			<td>{{$organization['target_client']}}</td>
		@endif

		@if ($organization['fecha_exp'] == NULL)
			<td>Ninguna</td>
		@else
			<td>{{$organization['fecha_exp']}}</td>
		@endif
		
		@if ($organization['serv_compartidos'] == 0)
			<td>No</td>
		@else
			<td>Si</td>
		@endif
		
		<td>
		<ul>
		@if ($org_dependientes == NULL)
			Ninguna
		@else
			@foreach ($org_dependientes as $organizaciones)
				<?php $cont = 0; //contador por si es que "Esta" organización no tiene organizaciones dependientes ?>
				@if ($organizaciones['organization_id'] == $organization['id'])
					<li>{{ $organizaciones['nombre'] }}</li>
					<?php $cont += 1; ?>
				@endif
			@endforeach
			@if ($cont == 0)
				Ninguna
			@endif
		@endif
		</td>
	@foreach (Session::get('roles') as $role)
		@if ($role != 6)
			<td>
				<div>
				@if ($organization['estado'] == 0)
		            {!! link_to_route('organization.edit', $title = 'Editar', $parameters = $organization['id'], $attributes = ['class'=>'btn btn-success']) !!}
		        @else
		        	{!! link_to_route('organization.desbloquear', $title = 'Desbloquear', $parameters = $organization['id'], $attributes = ['class'=>'btn btn-success']) !!}
		        @endif
		        </div><!-- /btn-group -->
			</td>
			<td>
				<div>
				@if ($organization['estado'] == 0)
					<button class="btn btn-danger" onclick="bloquear({{ $organization['id'] }},'{{ $organization['nombre'] }}','organization','La organización')">Bloquear</button>
		        @else
		        	<button class="btn btn-danger" onclick="eliminar2({{ $organization['id'] }},'{{ $organization['nombre'] }}','organization','La organización')">Eliminar</button>
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