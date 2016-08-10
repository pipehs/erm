@extends('en.master')

@section('title', 'Risk Categories')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Master data</a></li>
			<li><a href="categorias_risks">Risk Categories</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-ticket"></i>
					<span>Risk Categories</span>
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
		{!! link_to_route('categorias_risks.create', $title = 'Create Category', $parameters = NULL, $attributes = ['class'=>'btn btn-primary']) !!}

		@if (strpos($_SERVER['REQUEST_URI'],"verbloqueados"))
			{!! link_to_route('categorias_risks.index', $title = 'Unblocked Categories', $parameters = NULL, $attributes = ['class'=>'btn btn-success']) !!}
		@else
			{!! link_to_route('categorias_risks.verbloqueados', $title = 'Blocked Categories', $parameters = 'verbloqueados', $attributes = ['class'=>'btn btn-danger']) !!}
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
	<th>Name<label><input type='text' placeholder='Filtrar' /></label></th>
	<th width="30%">Description<label><input type='text' placeholder='Filtrar' /></label></th>
	<th>Created date<label><input type='text' placeholder='Filtrar' /></label></th>
	<th>Expiration date<label><input type='text' placeholder='Filtrar' /></label></th>
	<th>Dependent categories<label><input type='text' placeholder='Filtrar' /></label></th>
@foreach (Session::get('roles') as $role)
	@if ($role != 6)
	<th style="vertical-align:top;">Action</th>
	<th style="vertical-align:top;">Action</th>
	<?php break; ?>
	@endif
@endforeach
	</thead>
	@foreach ($risk_categories as $risk_category)
		<tr>
		<td>{{$risk_category['nombre']}}</td>
		<td>{{$risk_category['descripcion']}}</td>
		@if ($risk_category['fecha_creacion'] == NULL)
			<td>Error storing created date</td>
		@else
			<td>{{$risk_category['fecha_creacion']}}</td>
		@endif

		@if ($risk_category['fecha_exp'] == NULL)
			<td>None</td>
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
			<li>None </li>
		@endif
		</td>
@foreach (Session::get('roles') as $role)
	@if ($role != 6)
		<td>
			<div>
			@if ($risk_category['estado'] == 0)
	            {!! link_to_route('categorias_risks.edit', $title = 'Edit', $parameters = $risk_category['id'], $attributes = ['class'=>'btn btn-success']) !!}
	        @else
	        	{!! link_to_route('categorias_risks.desbloquear', $title = 'Unblock', $parameters = $risk_category['id'], $attributes = ['class'=>'btn btn-success']) !!}
	        @endif
	        </div><!-- /btn-group -->
		</td>
		<td>
			<div>
			@if ($risk_category['estado'] == 0)
	            <button class="btn btn-danger" onclick="bloquear({{ $risk_category['id'] }},'{{ $risk_category['nombre'] }}','categorias_risks','The risk category')">Block</button>
	        @else
	        	<button class="btn btn-danger" onclick="eliminar2({{ $risk_category['id'] }},'{{ $risk_category['nombre'] }}','categorias_risks','The risk category')">Delete</button>
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

