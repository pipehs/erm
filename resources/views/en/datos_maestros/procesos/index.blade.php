@extends('en.master')

@section('title', 'Processes')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Master Data</a></li>
			<li><a href="procesos">Processes</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<span>Processes</span>
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
				<div class="no-move"></div>
			</div>
			<div class="box-content">

		@if(Session::has('message'))
			<div class="alert alert-success alert-dismissible" role="alert">
			{{ Session::get('message') }}
			</div>
		@endif
@foreach (Session::get('roles') as $role)
	@if ($role != 6)
		{!! link_to_route('procesos.create', $title = 'Create Process', $parameters = NULL, $attributes = ['class'=>'btn btn-primary']) !!}

	@if (strpos($_SERVER['REQUEST_URI'],"verbloqueados"))
		{!! link_to_route('procesos.index', $title = 'Unblocked Processes', $parameters = NULL, $attributes = ['class'=>'btn btn-success']) !!}
	@else
		{!! link_to_route('procesos.verbloqueados', $title = 'Blocked Processes', $parameters = 'verbloqueados', $attributes = ['class'=>'btn btn-danger']) !!}
	@endif

	<?php break; ?>
	@endif
@endforeach
	<table class="table table-bordered table-striped table-hover table-heading table-datatable" id="datatable-2" style="font-size:11px" >
					<thead>
							<th style="vertical-align:top;">Organizations<label><input type="text" placeholder="Filtrar" /></label></th>
							<th style="vertical-align:top;">Process<label><input type="text" placeholder="Filtrar" /></label></th>
							<th style="vertical-align:top;">Subprocesses<label><input type="text" placeholder="Filtrar" /></label></th>
							<th style="vertical-align:top;">Description<label><input type="text" placeholder="Filtrar" /></label></th>
							<th style="vertical-align:top;">Created date<label><input type="text" placeholder="Filtrar" /></label></th>
							<th style="vertical-align:top;">Updated date<label><input type="text" placeholder="Filtrar" /></label></th>
							<th style="vertical-align:top;">Expiration date<label><input type="text" placeholder="Filtrar" /></label></th>
							<th style="vertical-align:top;">Depends on other process?<label><input type="text" placeholder="Filtrar" /></label></th>
							@foreach (Session::get('roles') as $role)
								@if ($role != 6)
								<th style="vertical-align:top;">Action</th>
								<th style="vertical-align:top;">Action</th>
								<?php break; ?>
								@endif
							@endforeach
					</thead>
	<tr style="display:none;">
@foreach (Session::get('roles') as $role)
	@if ($role != 6)
		<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
		<?php break; ?>
	@else
		<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
	@endif
@endforeach
	@foreach ($procesos as $proceso)
		<tr>
		<td><ul>
		<?php $cont = 0; //contador para verificar si hay organizaciones ?>
		@foreach ($organizaciones as $organizacion)
			@if ($organizacion['proceso_id'] == $proceso['id'] || $organizacion['proceso_id'] == $proceso['proceso_dependiente_id'])
				<li>{{ $organizacion['nombre'] }}</li>
				<?php $cont += 1;  //contador para verificar si hay organizaciones ?>
			@endif
		@endforeach
		@if ($cont == 0)
				No related organization.
		@endif
		</ul></td>
		<td>{{ $proceso['nombre'] }}</td>
		<td>
		@if ($subprocesos != NULL)
			<ul style="none">
			@foreach ($subprocesos as $subproceso)
				@if ($subproceso['proceso_id'] == $proceso['id'])
					<li>{{ $subproceso['nombre'] }}</li>
				@endif
			@endforeach
			</ul>
		@else
			No related subprocesses. 
		@endif
		</td>
		<td>{{ $proceso['descripcion'] }}</td>
		@if ($proceso['fecha_creacion'] == NULL)
			<td>Error storing created date</td>
		@else
			<td>{{$proceso['fecha_creacion']}}</td>
		@endif

		@if ($proceso['fecha_act'] == NULL)
			<td>Error storing updated date</td>
		@else
			<td>{{$proceso['fecha_act']}}</td>
		@endif

		@if ($proceso['fecha_exp'] == NULL)
			<td>None</td>
		@else
			<td>{{$proceso['fecha_exp']}}</td>
		@endif
		<td>{{ $proceso['proceso_dependiente'] }}</td>
@foreach (Session::get('roles') as $role)
	@if ($role != 6)		
		<td><div>
			@if ($proceso['estado'] == 0)
	            {!! link_to_route('procesos.edit', $title = 'Edit', $parameters = $proceso['id'], $attributes = ['class'=>'btn btn-success']) !!}
	        @else
	        	{!! link_to_route('procesos.desbloquear', $title = 'Unblock', $parameters = $proceso['id'], $attributes = ['class'=>'btn btn-success']) !!}
	        @endif
	        </div><!-- /btn-group --></td>
		<td><div>
			@if ($proceso['estado'] == 0)
	           <button class="btn btn-danger" onclick="bloquear({{ $proceso['id'] }},'{{ $proceso['nombre'] }}','procesos','the process')">Block</button>
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
