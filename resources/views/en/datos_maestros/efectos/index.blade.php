@extends('en.master')

@section('title', 'Effects')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Master Data</a></li>
			<li><a href="efectos">Effects</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-ticket"></i>
					<span>Effects</span>
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

		<p>On this section you will be able to create, view, edit or block generic effects for the future identified or template risks.</p>
@foreach (Session::get('roles') as $role)
	@if ($role != 6)
		{!! link_to_route('efectos.create', $title = 'Create Effect', $parameters = NULL, $attributes = ['class'=>'btn btn-primary']) !!}

		@if (strpos($_SERVER['REQUEST_URI'],"verbloqueados"))
			{!! link_to_route('efectos.index', $title = 'Unblocked Effects', $parameters = NULL, $attributes = ['class'=>'btn btn-success']) !!}
		@else
			{!! link_to_route('efectos.index', $title = 'Blocked Effects', $parameters = 'verbloqueados', $attributes = ['class'=>'btn btn-danger']) !!}
		@endif
	<?php break; ?>
	@endif
@endforeach
	<table class="table table-bordered table-striped table-hover table-heading table-datatable" id="datatable-2" style="font-size:11px">
	<thead>
	<th>Name<label><input type='text' placeholder='Filtrar' /></label></th>
	<th width="30%">Description<label><input type='text' placeholder='Filtrar' /></label></th>
	<th>Created date<label><input type='text' placeholder='Filtrar' /></label></th>
	<th>Updated date<label><input type='text' placeholder='Filtrar' /></label></th>
@foreach (Session::get('roles') as $role)
	@if ($role != 6)
	<th style="vertical-align:top;">Action</th>
	<th style="vertical-align:top;">Action</th>
	<?php break; ?>
	@endif
@endforeach
	</thead>
	@foreach ($efectos as $efecto)
		<tr>
		<td>{{ $efecto['nombre'] }}</td>
		<td>
		@if ($efecto['descripcion'] == NULL)
			None
		@else
			{{ $efecto['descripcion'] }}
		@endif
		</td>
		@if ($efecto['fecha_creacion'] == NULL)
			<td>Error storing created date</td>
		@else
			<td>{{$efecto['fecha_creacion']}}</td>
		@endif

		@if ($efecto['fecha_act'] == NULL)
			<td>Error storing updated date</td>
		@else
			<td>{{$efecto['fecha_act']}}</td>
		@endif
@foreach (Session::get('roles') as $role)
	@if ($role != 6)
		<td><div>
			@if ($efecto['estado'] == 0)
	            {!! link_to_route('efectos.edit', $title = 'Edit', $parameters = $efecto['id'], $attributes = ['class'=>'btn btn-success']) !!}
	        @else
	        	{!! link_to_route('efectos.desbloquear', $title = 'Unblock', $parameters = $efecto['id'], $attributes = ['class'=>'btn btn-success']) !!}
	        @endif
	        </div><!-- /btn-group --></td>
		<td><div>
			@if ($efecto['estado'] == 0)
	             <button class="btn btn-danger" onclick="bloquear({{ $efecto['id'] }},'{{ $efecto['nombre'] }}','efectos','the effect')">Block</button>
	        @else
	        	
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

