@extends('master')

@section('title', 'Categor&iacute;as de Objetivos')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Datos Maestros</a></li>
			<li><a href="categorias_objetivos">Categor&iacute;as de Objetivos</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-ticket"></i>
					<span>Categor&iacute;as de Objetivos</span>
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
		{!! link_to_route('categorias_objetivos.create', $title = 'Agregar Categor&iacute;a', $parameters = NULL, $attributes = ['class'=>'btn btn-primary']) !!}

		@if (strpos($_SERVER['REQUEST_URI'],"verbloqueados"))
			{!! link_to_route('categorias_objetivos.index', $title = 'Ver Desbloqueadas', $parameters = NULL, $attributes = ['class'=>'btn btn-success']) !!}
		@else
			{!! link_to_route('categorias_objetivos.verbloqueados', $title = 'Ver Bloqueadas', $parameters = 'verbloqueados', $attributes = ['class'=>'btn btn-danger']) !!}
		@endif
	<?php break; ?>
	@endif
@endforeach
		@if(Session::has('message'))
			<div class="alert alert-success alert-dismissible" role="alert">
			{{ Session::get('message') }}
			</div>
		@endif

	<table class="table table-bordered table-striped table-hover table-heading table-datatable" id="datatable-2" style="font-size:11px">
	<thead>
	<th>Nombre<label><input type="text" placeholder="Filtrar" /></label></th>
	<th width="30%">Descripci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
	<th>Fecha Creaci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
	<th>Fecha Actualizaci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
	<th>Fecha Expiraci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
@foreach (Session::get('roles') as $role)
	@if ($role != 6)
	<th style="vertical-align:top;">Acci&oacute;n</th>
	<th style="vertical-align:top;">Acci&oacute;n</th>
	<?php break; ?>
	@endif
@endforeach
	</thead>
	@foreach ($objective_categories as $objective_category)
		<tr>
		<td>{{$objective_category['nombre']}}</td>
		<td>{{$objective_category['descripcion']}}</td>
		<td>{{$objective_category['fecha_creacion']}}</td>
		<td>{{$objective_category['fecha_act']}}</td>
		<td>{{$objective_category['fecha_exp']}}</td>
@foreach (Session::get('roles') as $role)
	@if ($role != 6)
		<td>
			<div>
			@if ($objective_category['estado'] == 0)
	            {!! link_to_route('categorias_objetivos.edit', $title = 'Editar', $parameters = $objective_category['id'], $attributes = ['class'=>'btn btn-success']) !!}
	        @else
	        	{!! link_to_route('categorias_objetivos.desbloquear', $title = 'Desbloquear', $parameters = $objective_category['id'], $attributes = ['class'=>'btn btn-success']) !!}
	        @endif
	        </div><!-- /btn-group -->
		</td>
		<td>
			<div>
			@if ($objective_category['estado'] == 0)
	            <button class="btn btn-danger" onclick="bloquear({{ $objective_category['id'] }},'{{ $objective_category['nombre'] }}','categorias_objetivos','la categoría de objetivo')">Bloquear</button>
	        @else
	       
	        @endif
	        </div><!-- /btn-group -->
		</td>
		</tr>
	<?php break; ?>
	@endif
@endforeach
	@endforeach
	</table>

			</div>
		</div>
	</div>
</div>
@stop

