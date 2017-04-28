@extends('master')

@section('title', 'Categor&iacute;as de Riesgos')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Datos Maestros</a></li>
			<li><a href="categorias_risks">Categor&iacute;as de Riesgos</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-ticket"></i>
					<span>Categor&iacute;as de Riesgos</span>
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
@foreach (Session::get('roles') as $role)
	@if ($role != 6)
		{!! link_to_route('categorias_risks.create', $title = 'Agregar Categor&iacute;a', $parameters = NULL, $attributes = ['class'=>'btn btn-primary']) !!}

		@if (strpos($_SERVER['REQUEST_URI'],"verbloqueados"))
			{!! link_to_route('categorias_risks.index', $title = 'Ver Desbloqueadas', $parameters = NULL, $attributes = ['class'=>'btn btn-success']) !!}
		@else
			{!! link_to_route('categorias_risks.verbloqueados', $title = 'Ver Bloqueadas', $parameters = 'verbloqueados', $attributes = ['class'=>'btn btn-danger']) !!}
		@endif

		@if(Session::has('message'))
			<div class="alert alert-success alert-dismissible" role="alert">
			{{ Session::get('message') }}
			</div>
		@endif
	<?php break; ?>
	@endif
@endforeach

	<table class="table table-bordered table-striped table-hover table-heading table-datatable" id="datatable-2" style="font-size:11px">
	<thead>
	<th>Nombre<label><input type='text' placeholder='Filtrar' /></label></th>
	<th width="30%">Descripci&oacute;n<label><input type='text' placeholder='Filtrar' /></label></th>
	<th>Fecha Creaci&oacute;n<label><input type='text' placeholder='Filtrar' /></label></th>
	<th>Fecha Expiraci&oacute;n<label><input type='text' placeholder='Filtrar' /></label></th>
	<th>Categor&iacute;as Dependientes<label><input type='text' placeholder='Filtrar' /></label></th>
@foreach (Session::get('roles') as $role)
	@if ($role != 6)
	<th style="vertical-align:top;">Acci&oacute;n</th>
	<th style="vertical-align:top;">Acci&oacute;n</th>
	<?php break; ?>
	@endif
@endforeach
	</thead>
	@foreach ($risk_categories as $risk_category)
		<tr>
		<td>{{$risk_category['nombre']}}</td>
		<td>
		@if (strlen($risk_category['descripcion']) > 100)
			<div id="description_{{$risk_category['id']}}" title="{{ $risk_category['descripcion'] }}">{{ $risk_category['short_des'] }}...
			<div style="cursor:hand" onclick="expandir({{ $risk_category['id'] }},'{{ $risk_category['descripcion'] }}','{{ $risk_category['short_des'] }}')">
			<font color="CornflowerBlue">Ver completo</font>
			</div></div>
		@else
			{{ $risk_category['descripcion'] }}
		@endif
		</td>
		@if ($risk_category['fecha_creacion'] == NULL)
			<td>Error al registrar fecha de creaci&oacute;n</td>
		@else
			<td>{{$risk_category['fecha_creacion']}}</td>
		@endif

		@if ($risk_category['fecha_exp'] == NULL)
			<td>Ninguna</td>
		@else
			<td>{{$risk_category['fecha_exp']}}</td>
		@endif
		<td>
		<ul>
		@if ($categorias_dependientes != NULL)
			@foreach ($categorias_dependientes as $categorias)
				@if ($categorias['risk_category_id'] == $risk_category['id'])
					<li>{{ $categorias['nombre'] }}</li>
				@endif
			@endforeach
		@else
			<li>Ninguna </li>
		@endif
		</td>
@foreach (Session::get('roles') as $role)
	@if ($role != 6)
		<td>
			<div>
			@if ($risk_category['estado'] == 0)
	            {!! link_to_route('categorias_risks.edit', $title = 'Editar', $parameters = $risk_category['id'], $attributes = ['class'=>'btn btn-success']) !!}
	        @else
	        	{!! link_to_route('categorias_risks.desbloquear', $title = 'Desbloquear', $parameters = $risk_category['id'], $attributes = ['class'=>'btn btn-success']) !!}
	        @endif
	        </div><!-- /btn-group -->
		</td>
		<td>
			<div>
			@if ($risk_category['estado'] == 0)
	            <button class="btn btn-danger" onclick="bloquear({{ $risk_category['id'] }},'{{ $risk_category['nombre'] }}','categorias_riesgos','La categoría de riesgo')">Bloquear</button>
	        @else
	        	<button class="btn btn-danger" onclick="eliminar2({{ $risk_category['id'] }},'{{ $risk_category['nombre'] }}','categorias_riesgos','La categoría de riesgo')">Eliminar</button>
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

