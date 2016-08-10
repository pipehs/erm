@extends('en.master')

@section('title', 'Causes')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Master data</a></li>
			<li><a href="causas">Causes</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-ticket"></i>
					<span>Causes</span>
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

		<p>On this section you will be able to create, view, edit or block generic causes for the futures identified or template risks.</p>
@foreach (Session::get('roles') as $role)
	@if ($role != 6)
		{!! link_to_route('causas.create', $title = 'Create Cause', $parameters = NULL, $attributes = ['class'=>'btn btn-primary']) !!}

		@if (strpos($_SERVER['REQUEST_URI'],"verbloqueados"))
			{!! link_to_route('causas.index', $title = 'Unblocked Causes', $parameters = NULL, $attributes = ['class'=>'btn btn-success']) !!}
		@else
			{!! link_to_route('causas.index', $title = 'Blocked Causes', $parameters = 'verbloqueados', $attributes = ['class'=>'btn btn-danger']) !!}
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
	@foreach ($causas as $causa)
		<tr>
		<td>{{ $causa['nombre'] }}</td>
		<td>
		@if ($causa['descripcion'] == NULL)
			None
		@else
			{{ $causa['descripcion'] }}
		@endif
		</td>
		@if ($causa['fecha_creacion'] == NULL)
			<td>Error storing created date</td>
		@else
			<td>{{$causa['fecha_creacion']}}</td>
		@endif

		@if ($causa['fecha_act'] == NULL)
			<td>Error storing updated date</td>
		@else
			<td>{{$causa['fecha_act']}}</td>
		@endif
@foreach (Session::get('roles') as $role)
	@if ($role != 6)
		<td><div>
			@if ($causa['estado'] == 0)
	            {!! link_to_route('causas.edit', $title = 'Edit', $parameters = $causa['id'], $attributes = ['class'=>'btn btn-success']) !!}
	        @else
	        	{!! link_to_route('causas.desbloquear', $title = 'Unblock', $parameters = $causa['id'], $attributes = ['class'=>'btn btn-success']) !!}
	        @endif
	        </div><!-- /btn-group --></td>
		<td><div>
			@if ($causa['estado'] == 0)
	            <button class="btn btn-danger" onclick="bloquear({{ $causa['id'] }},'{{ $causa['nombre'] }}','causas','The cause')">Block</button>
	        @else
	        	<button class="btn btn-danger" onclick="eliminar2({{ $causa['id'] }},'{{ $causa['nombre'] }}','causas','The cause')">Delete</button>
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

