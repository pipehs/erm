@extends('en.master')

@section('title', 'Template Risks')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Master Data</a></li>
			<li><a href="riskstype">Template Risks</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-ticket"></i>
					<span>Template Risks</span>
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
		{!! link_to_route('riskstype.create', $title = 'Create Risk', $parameters = NULL, $attributes = ['class'=>'btn btn-primary']) !!}

		@if (strpos($_SERVER['REQUEST_URI'],"verbloqueados"))
			{!! link_to_route('riskstype.index', $title = 'Unblocked Risks', $parameters = NULL, $attributes = ['class'=>'btn btn-success']) !!}
		@else
			{!! link_to_route('riskstype.index', $title = 'Blocked Risks', $parameters = 'verbloqueados', $attributes = ['class'=>'btn btn-danger']) !!}
		@endif
	<?php break; ?>
	@endif
@endforeach
	<table class="table table-bordered table-striped table-hover table-heading table-datatable" id="datatable-2" style="font-size:11px">
	<thead>
	<th>Name<label><input type='text' placeholder='Filtrar' /></label></th>
	<th width="30%">Description<label><input type='text' placeholder='Filtrar' /></label></th>
	<th>Category<label><input type='text' placeholder='Filtrar' /></label></th>
	<th>Created date<label><input type='text' placeholder='Filtrar' /></label></th>
	<th>Updated date<label><input type='text' placeholder='Filtrar' /></label></th>
	<th>Expiration date<label><input type='text' placeholder='Filtrar' /></label></th>
	<th>Cause(s)<label><input type='text' placeholder='Filtrar' /></label></th>
	<th>Effect(s)<label><input type='text' placeholder='Filtrar' /></label></th>
@foreach (Session::get('roles') as $role)
	@if ($role != 6)
	<th style="vertical-align:top;">Action</th>
	<th style="vertical-align:top;">Action</th>
	<?php break; ?>
	@endif
@endforeach
	</thead>
	@foreach ($riesgos as $riesgo)
		<tr>
		<td>{{ $riesgo['nombre'] }}</td>
		<td>{{ $riesgo['descripcion'] }}</td>
		<td>{{ $riesgo['categoria'] }}</td>
		@if ($riesgo['fecha_creacion'] == NULL)
			<td>Error storing created date</td>
		@else
			<td>{{$riesgo['fecha_creacion']}}</td>
		@endif

		@if ($riesgo['fecha_act'] == NULL)
			<td>Error storing updated date</td>
		@else
			<td>{{$riesgo['fecha_act']}}</td>
		@endif

		@if ($riesgo['fecha_exp'] == NULL)
			<td>None</td>
		@else
			<td>{{$riesgo['fecha_exp']}}</td>
		@endif
		<td>
		@if (gettype($riesgo['causas']) == "array") 
			@foreach ($riesgo['causas'] as $causa)
				<li>{{ $causa }}</li>
			@endforeach
		@else
			@if ($riesgo['causas'] == NULL)
				No causes have been added
			@else
				<li>{{ $riesgo['causas'] }}</li>
			@endif
		@endif
		</td>
		<td>
		@if (gettype($riesgo['efectos']) == "array") 
			@foreach ($riesgo['efectos'] as $efecto)
				<li>{{ $efecto }}</li>
			@endforeach
		@else
			@if ($riesgo['causas'] == NULL)
				No effects have been added
			@else
				<li>{{ $riesgo['efectos'] }}
			@endif
		@endif
		</td>
@foreach (Session::get('roles') as $role)
	@if ($role != 6)
		<td><div>
			@if ($riesgo['estado'] == 0)
	            {!! link_to_route('riskstype.edit', $title = 'Edit', $parameters = $riesgo['id'], $attributes = ['class'=>'btn btn-success']) !!}
	        @else
	        	{!! link_to_route('riskstype.desbloquear', $title = 'Unblock', $parameters = $riesgo['id'], $attributes = ['class'=>'btn btn-success']) !!}
	        @endif
	        </div><!-- /btn-group --></td>
		<td><div>
			@if ($riesgo['estado'] == 0)
	            <button class="btn btn-danger" onclick="bloquear({{ $riesgo['id'] }},'{{ $riesgo['nombre'] }}','riskstype','The template risk')">Block</button>
	        @else
	    		<button class="btn btn-danger" onclick="eliminar2({{ $riesgo['id'] }},'{{ $riesgo['nombre'] }}','riskstype','The template risk')">Delete</button>
	        @endif
	        </div><!-- /btn-group -->
	<?php break; ?>
	@endif
@endforeach
	    </td>
		</tr>
	@endforeach
	</table>

			</div>
		</div>
	</div>
</div>
@stop

