@extends('master')

@section('title', 'Definición de objetivos')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Gestión estratégica</a></li>
			<li><a href="objetivos">Planes y objetivos estrat&eacute;gicos</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-puzzle-piece"></i>
					<span>Planes y objetivos estrat&eacute;gicos</span>
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

	@if (!isset($objetivos))
		<center>
		{!!Form::open(['url'=>'plan_estrategico','method'=>'GET','class'=>'form-horizontal'])!!}
			<div class="row form-group">
				<div class="col-sm-6">

				{!!Form::select('organizacion', $organizations,
					 	   null, 
					 	   ['placeholder' => '- Seleccione una organizaci&oacute;n -',
					 	   	'id' => 'el2',
					 	   	'required' => 'true'])
				!!}
				</div>
			</div>
		</center>

		{!!Form::submit('Ver Planes', ['class'=>'btn btn-success'])!!}
		{!!Form::close()!!}
		<hr>
	@else
		<h3><b>Informaci&oacute;n plan estrat&eacute;gico</b></h3>
		<h4><b>{{ $datos_plan->name }}</b></h4>

		@if ($datos_plan->comments == "")
			<b>Comentarios: No se han definido comentarios.</b>
		@else
			<b>Comentarios: {{ $datos_plan->comments }}.</b>
		@endif
		</br>
		<b>Fecha de inicio vigencia: {{ date('d-m-Y',strtotime($datos_plan->initial_date)) }}.</b></br>
		<b>Fecha de t&eacute;rmino de vigencia: {{ date('d-m-Y',strtotime($datos_plan->final_date)) }}.</b></br>
		@if ($datos_plan['status'] != 0)
			{!! link_to_route('plan_estrategico.edit', $title = 'Editar plan', $parameters = $datos_plan->id, $attributes = ['class'=>'btn btn-success']) !!}
		@endif
		<hr>

		@if(!isset($validador))
			@if (!strpos($_SERVER['REQUEST_URI'],"verbloqueados"))
				@foreach (Session::get('roles') as $role)
					@if ($role != 6)
						{!! link_to_route('objetivos_plan.verbloqueados', $title = 'Ver Objetivos Bloqueados', $parameters = $strategic_plan_id, $attributes = ['class'=>'btn btn-danger']) !!}
					<?php break; ?>
					@endif
				@endforeach
			@else
				{!! link_to_route('objetivos_plan', $title = 'Ver Objetivos Desbloqueados', $parameters = $strategic_plan_id, $attributes = ['class'=>'btn btn-success']) !!}
			@endif
			

		@if (isset($objetivos))

			@foreach (Session::get('roles') as $role)
				@if ($role != 6)
				{!!Form::open(['url'=>'objetivos.create','method'=>'GET','class'=>'form-horizontal'])!!}	
					{!!Form::hidden('nombre_organizacion',$nombre_organizacion )!!}

					@if ($datos_plan['status'] != 0)
						{!!Form::submit('Agregar objetivo', ['class'=>'btn btn-primary'])!!}
					@endif
					
					@if (isset($_GET['strategic_plan_id']))
						{!!Form::hidden('strategic_plan_id',$_GET['strategic_plan_id'] )!!}
					@else
						{!!Form::hidden('strategic_plan_id',$strategic_plan_id)!!}
					@endif
						
					{!!Form::close()!!}
				<?php break; ?>
				@endif
			@endforeach
		<hr>
			@if ($probador !== 0) {{-- Si es que existe algún objetivo creado, probador no será cero --}}
				<table class="table table-bordered table-striped table-hover table-heading table-datatable" id="datatable-2" style="font-size:11px">
					<thead>
					<th>C&oacute;digo<label><input type='text' placeholder='Filtrar' /></label></th>
					<th>Perspectiva<label><input type='text' placeholder='Filtrar' /></label></th>
					<th>Nombre<label><input type='text' placeholder='Filtrar' /></label></th>
					<th width="30%">Descripci&oacute;n<label><input type='text' placeholder='Filtrar' /></label></th>				

				@foreach (Session::get('roles') as $role)
					@if ($role != 6)
					@if ($datos_plan['status'] != 0)
					<th style="vertical-align:top;">Acci&oacute;n</th>
					<th style="vertical-align:top;">Acci&oacute;n</th>
					<th style="vertical-align:top;">Acci&oacute;n</th>
					@endif
					<?php break; ?>
					@endif
				@endforeach
					</thead>

					@foreach ($objetivos as $objetivo)
						<tr>
						<td>{{ $objetivo['code'] }}</td>
						@if ($objetivo['perspective'] == NULL)
							<td>No se ha definido perspectiva</td>
						@elseif ($objetivo['perspective'] == 1)
							<td>Financiera</td>
						@elseif ($objetivo['perspective'] == 3)
							<td>Procesos</td>
						@elseif ($objetivo['perspective'] == 2)
							<td>Clientes</td>
						@elseif ($objetivo['perspective'] == 4)
							<td>Aprendizaje</td>
						@endif
						<td>{{$objetivo['nombre']}}</td>
						<td>{{$objetivo['descripcion']}}</td>		

				@foreach (Session::get('roles') as $role)
					@if ($role != 6)
						@if ($datos_plan['status'] != 0)
						<td>
							<div>
							@if ($objetivo['estado'] == 0)
					            {!! link_to_route('objetivos.edit', $title = 'Editar', $parameters = $objetivo['id'], $attributes = ['class'=>'btn btn-success']) !!}
					        @else
					        	<button class="btn btn-success" onclick="desbloquear({{ $objetivo['id'] }},'{{ $objetivo['nombre'] }}','objetivos','El objetivo')">Desbloquear</button>
					        @endif
					        </div><!-- /btn-group -->
						</td>
						<td>
							<div>
							@if ($objetivo['estado'] == 0)
					            <button class="btn btn-danger" onclick="bloquear({{ $objetivo['id'] }},'{{ $objetivo['nombre'] }}','objetivos','El objetivo')">Bloquear</button>
					        @else
					    		<button class="btn btn-danger" onclick="eliminar2({{ $objetivo['id'] }},'{{ $objetivo['nombre'] }}','objetivos','El objetivo')">Eliminar</button>
					        @endif
					        </div><!-- /btn-group -->
						</td>
						<td>
						{!! link_to_route('objective_kpi', $title = 'Gestionar KPI', $parameters = $objetivo['id'], $attributes = ['class'=>'btn btn-primary']) !!}
						</td>
						@endif
					<?php break; ?>
					@endif
				@endforeach
						</tr>
					@endforeach
					</table>
			@else
				@if (strpos($_SERVER['REQUEST_URI'],"verbloqueados"))
					<b>La organizaci&oacute;n {{ $nombre_organizacion }} no posee objetivos bloqueados para el plan estrat&eacute;gico {{ $datos_plan->name }}.</b>
				@else
					<b>Aun no se han creado objetivos para la organizaci&oacute;n {{ $nombre_organizacion }} dentro del plan estrat&eacute;gico {{ $datos_plan->name }}, o bien sus objetivos est&aacute;n bloqueados. </b>
				@endif
			@endif
			<hr>

			<center>
				<a href="plan_estrategico?organizacion={{$datos_plan->organization_id}}" class="btn btn-danger">Volver</a>	
			<center>

		@endif
	@endif
	@endif

	
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts2')
	<script>
		@if (isset($validador))
			swal({   title: "Atención",
		   			   text: "No existe ningún plan estratégico vigente para la organización seleccionada. Por favor cree un plan para poder agregar los objetivos estratégicos",
		   			   type: "warning",   
		   			   showCancelButton: false,   
		   			   confirmButtonColor: "#31B404",   
		   			   confirmButtonText: "Aceptar",   
		   			   closeOnConfirm: false }, 
		   			   function(){   
		   			   	window.location="plan_estrategico.create";
		   				});

		@endif
	</script>
@stop