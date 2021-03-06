@extends('en.master')

@section('title', 'Stakeholders')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Master Data</a></li>
			<li><a href="stakeholders">Stakeholders</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Stakeholders</span>
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
		{!! link_to_route('stakeholders.create', $title = 'Create Stakeholder', $parameters = NULL, $attributes = ['class'=>'btn btn-primary']) !!}

		@if (strpos($_SERVER['REQUEST_URI'],"verbloqueados"))
			{!! link_to_route('stakeholders.index', $title = 'Unblocked Stakeholders', $parameters = NULL, $attributes = ['class'=>'btn btn-success']) !!}
		@else
			{!! link_to_route('stakeholders.verbloqueados', $title = 'Blocked Stakeholders', $parameters = 'verbloqueados', $attributes = ['class'=>'btn btn-danger']) !!}
		@endif
	<?php break; ?>
	@endif
@endforeach

	<table class="table table-bordered table-striped table-hover table-heading table-datatable" id="datatable-2" style="font-size:11px">
	<thead>
	<th width="8%">Id<label><input type='text' placeholder='Filtrar' /></label></th>
	<th>Name<label><input type='text' placeholder='Filtrar' /></label></th>
	<th>Surnames<label><input type='text' placeholder='Filtrar' /></label></th>
	<th>Kind<label><input type='text' placeholder='Filtrar' /></label></th>
	<th>Created date<label><input type='text' placeholder='Filtrar' /></label></th>
	<th>Updated date<label><input type='text' placeholder='Filtrar' /></label></th>
	<th>E-Mail<label><input type='text' placeholder='Filtrar' /></label></th>
	<th>Organization(s)<label><input type='text' placeholder='Filtrar' /></label></th>
	<th>Position<label><input type='text' placeholder='Filtrar' /></label></th>
@foreach (Session::get('roles') as $role)
	@if ($role != 6)
	<th style="vertical-align:top;">Action</th>
	<th style="vertical-align:top;">Action</th>
	<?php break; ?>
	@endif
@endforeach
	</thead>

	@foreach($stakeholders as $stakeholder)
		<tr>
			<td>{{ $stakeholder['id'] }}-{{ $stakeholder['dv'] }}</td>
			<td>{{ $stakeholder['nombre'] }}</td>
			<td>{{ $stakeholder['apellidos']}}</td>
			<td><ul>
			<?php $cont = 0; ?>
			@foreach ($roles as $role)
				@if ($role['stakeholder_id'] == $stakeholder['id'])
					<li>{{ $role['nombre'] }}</li>
					<?php $cont += 1; //contador para ver si existen roles?>
				@endif
			@endforeach

			@if ($cont == 0)
				No roles was added
			@endif
			</ul></td>
			<td>{{ $stakeholder['fecha_creacion'] }}</td>
			<td>{{ $stakeholder['fecha_act'] }}</td>
			<td>{{ $stakeholder['correo'] }}</td>
			<td><ul>
			@foreach ($organizaciones as $organizacion)
				@if ($organizacion['stakeholder_id'] == $stakeholder['id'])
					<li>{{ $organizacion['nombre'] }}</li>
				@endif
			@endforeach
			</ul></td>
			<td>
			@if ($stakeholder['cargo'] == NULL)
				Not specified
			@else
				{{ $stakeholder['cargo'] }}
			@endif</td>
@foreach (Session::get('roles') as $role)
	@if ($role != 6)
			<td> 
			<div>
			@if ($stakeholder['estado'] == 0)
	            {!! link_to_route('stakeholders.edit', $title = 'Edit', $parameters = $stakeholder['id'], $attributes = ['class'=>'btn btn-success']) !!}
	        @else
	        	{!! link_to_route('stakeholders.desbloquear', $title = 'Unblock', $parameters = $stakeholder['id'], $attributes = ['class'=>'btn btn-success']) !!}
	        @endif
	        </div><!-- /btn-group -->
		</td>
		<td>
			<div>
			@if ($stakeholder['estado'] == 0)
	             <button class="btn btn-danger" onclick="bloquear({{ $stakeholder['id'] }},'{{ $stakeholder['nombre']." ".$stakeholder['apellidos'] }}','stakeholders','The stakeholder')">Block</button>
	        @else
	        	 <button class="btn btn-danger" onclick="eliminar2({{ $stakeholder['id'] }},'{{ $stakeholder['nombre']." ".$stakeholder['apellidos'] }}','stakeholders','The stakeholder')">Delete</button>
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

