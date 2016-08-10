@extends('en.master')

@section('title', 'Roles')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Master Data</a></li>
			<li><a href="roles">Roles</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Stakeholder Roles</span>
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

		On this section you will be able to create different roles for the stakeholders of the system.<br><br>
@foreach (Session::get('roles') as $role)
	@if ($role != 6)
		{!! link_to_route('roles.create', $title = 'Create Role', $parameters = NULL, $attributes = ['class'=>'btn btn-primary']) !!}

		@if (strpos($_SERVER['REQUEST_URI'],"verbloqueados"))
			{!! link_to_route('roles.index', $title = 'Unblocked Roles', $parameters = NULL, $attributes = ['class'=>'btn btn-success']) !!}
		@else
			{!! link_to_route('roles.verbloqueados', $title = 'Blocked Roles', $parameters = 'verbloqueados', $attributes = ['class'=>'btn btn-danger']) !!}
		@endif
	<?php break; ?>
	@endif
@endforeach

	<table class="table table-bordered table-striped table-hover table-heading table-datatable" id="datatable-2" style="font-size:11px">
	<thead>
	<th>Name<label><input type='text' placeholder='Filtrar' /></label></th>
	<th>Number of users<label><input type='text' placeholder='Filtrar' /></label></th>
@foreach (Session::get('roles') as $role)
	@if ($role != 6)
	<th style="vertical-align:top;">Action</th>
	<th style="vertical-align:top;">Action</th>
	<?php break; ?>
	@endif
@endforeach
	</thead>

	@foreach($roles as $rol)
		<tr>
			<td>{{ $rol['nombre'] }}</td>
			<td>
			@if ($rol['cantidad'] == 1)
				{{ $rol['cantidad'] }} user
			@else
				{{ $rol['cantidad'] }} users
			@endif
			</td>
@foreach (Session::get('roles') as $role)
	@if ($role != 6)
			<td> 
			<div>
			@if ($rol['status'] == 0)
	            {!! link_to_route('roles.edit', $title = 'Edit', $parameters = $rol['id'], $attributes = ['class'=>'btn btn-success']) !!}
	        @else
	        	{!! link_to_route('roles.desbloquear', $title = 'Unblock', $parameters = $rol['id'], $attributes = ['class'=>'btn btn-success']) !!}
	        @endif
	        </div><!-- /btn-group -->
		</td>
		<td>
			<div>
			@if ($rol['status'] == 0)
	            <button class="btn btn-danger" onclick="bloquear({{ $rol['id'] }},'{{ $rol['nombre'] }}','roles','The role')">Block</button>
	        @else
	        	<button class="btn btn-danger" onclick="eliminar2({{ $rol['id'] }},'{{ $rol['nombre'] }}','roles','The role')">Delete</button>
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