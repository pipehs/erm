@extends('en.master')

@section('title', 'Organizations')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Master Data</a></li>
			<li><a href="organization">Organizations</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-building-o"></i>
					<span>Organizations</span>
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
			{!! link_to_route('organization.create', $title = 'Create Organization', $parameters = NULL, $attributes = ['class'=>'btn btn-primary']) !!}

		@if (strpos($_SERVER['REQUEST_URI'],"verbloqueados"))
			{!! link_to_route('organization.index', $title = 'Unblocked Organizations', $parameters = NULL, $attributes = ['class'=>'btn btn-success']) !!}
		@else
			{!! link_to_route('organization.verbloqueados', $title = 'Blocked Organizations', $parameters = 'verbloqueados', $attributes = ['class'=>'btn btn-danger']) !!}
		@endif

		<?php break; ?>
	@endif
@endforeach
<!--
		<form class="form-horizontal" method="REQUEST" action="organization.create">
		<input type="hidden" name="_token" value="{{{ csrf_token() }}}" >
				<button type="submit" class="btn btn-primary">Agregar Nueva Organizaci&oacute;n</button>
			</form>

			{!!Form::open()!!}
				<div class="form-group">
					{!!Form::label('Nombre',null,['class' => 'col-sm-4 control-label'])!!}
					<div class="col-sm-3">
						{!!Form::text('name',null,['class' => 'form-control','placeholder' => 'Ingrese nombre de la organizaci&oacute;n'])!!}
					</div>
				</div>			
			{!!Form::close()!!}
-->	

	<table class="table table-bordered table-striped table-hover table-heading table-datatable" id="datatable-2" style="font-size:11px">
	<thead>
	<th style="vertical-align:top;">Name<label><input type="text" placeholder="Filtrar" /></label></th>
	<th style="vertical-align:top;" width="20%">Description<label><input type="text" placeholder="Filtrar" /></label></th>
	<th style="vertical-align:top;">Mission<label><input type="text" placeholder="Filtrar" /></label></th>
	<th style="vertical-align:top;">Vision<label><input type="text" placeholder="Filtrar" /></label></th>
	<th style="vertical-align:top;">Target Client<label><input type="text" placeholder="Filtrar" /></label></th>
	<th style="vertical-align:top;">Expiration Date<label><input type="text" placeholder="Filtrar" /></label></th>
	<th style="vertical-align:top;">Shared Services<label><input type="text" placeholder="Filtrar" /></label></th>
	<th style="vertical-align:top;">Dependent Organizations<label><input type="text" placeholder="Filtrar" /></label></th>

@foreach (Session::get('roles') as $role)
	@if ($role != 6)
	<th style="vertical-align:top;">Action</th>
	<th style="vertical-align:top;">Action</th>
	<?php break; ?>
	@endif
@endforeach
	</thead>

	@foreach ($organizations as $organization)
		<tr>
		<td>{{$organization['nombre']}}</td>
		<td>{{$organization['descripcion']}}</td>

		@if ($organization['mision'] == NULL)
			<td>Mission was not specified</td>
		@else
			<td>{{$organization['mision']}}</td>
		@endif
		
		@if ($organization['vision'] == NULL)
			<td>Vision was not specified</td>
		@else
			<td>{{$organization['vision']}}</td>
		@endif

		@if ($organization['target_client'] == NULL)
			<td>Target client was not specified</td>
		@else
			<td>{{$organization['target_client']}}</td>
		@endif

		@if ($organization['fecha_exp'] == NULL)
			<td>None</td>
		@else
			<td>{{$organization['fecha_exp']}}</td>
		@endif
		
		@if ($organization['serv_compartidos'] == 0)
			<td>No</td>
		@else
			<td>Yes</td>
		@endif
		<td>
		<ul>
		@if ($org_dependientes == NULL)
			None
		@else
			@foreach ($org_dependientes as $organizaciones)
				@if ($organizaciones['organization_id'] == $organization['id'])
					<li>{{ $organizaciones['nombre'] }}</li>
				@endif
			@endforeach
		@endif
		</td>
	@foreach (Session::get('roles') as $role)
		@if ($role != 6)
			<td>
				<div>
				@if ($organization['estado'] == 0)
		            {!! link_to_route('organization.edit', $title = 'Edit', $parameters = $organization['id'], $attributes = ['class'=>'btn btn-success']) !!}
		        @else
		        	{!! link_to_route('organization.desbloquear', $title = 'Unblock', $parameters = $organization['id'], $attributes = ['class'=>'btn btn-success']) !!}
		        @endif
		        </div><!-- /btn-group -->
			</td>
			<td>
				<div>
				@if ($organization['estado'] == 0)
					<button class="btn btn-danger" onclick="bloquear({{ $organization['id'] }},'{{ $organization['nombre'] }}','organization','The organization')">Block</button>
		        @else
		        	<button class="btn btn-danger" onclick="eliminar2({{ $organization['id'] }},'{{ $organization['nombre'] }}','organization','The organization')">Delete</button>
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

