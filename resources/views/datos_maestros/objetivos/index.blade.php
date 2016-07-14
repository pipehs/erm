@extends('master')

@section('title', 'Organizaciones')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Datos Maestros</a></li>
			<li><a href="objetivos">Objetivos</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-puzzle-piece"></i>
					<span>Objetivos</span>
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
		<center>
		{!!Form::open(['url'=>'objetivos','method'=>'GET','class'=>'form-horizontal'])!!}
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

		{!!Form::submit('Ver Objetivos', ['class'=>'btn btn-success'])!!}
		{!!Form::close()!!}
		<hr>

	@if (isset($_GET['organizacion']))
		@foreach (Session::get('roles') as $role)
			@if ($role != 6)
				{!! link_to_route('objetivos.verbloqueados', $title = 'Ver Objetivos Bloqueados', $parameters = $_GET['organizacion'], $attributes = ['class'=>'btn btn-danger']) !!}
			<?php break; ?>
			@endif
		@endforeach
	@endif
		

	@if (isset($objetivos))
		@if (isset($organizacion))
			@foreach (Session::get('roles') as $role)
				@if ($role != 6)
					{{-- Se hará el botón con un formulario para poder enviar por el método GET a la función index de ObjetivosController --}}
					{!!Form::open(['url'=>'objetivos','method'=>'GET','class'=>'form-horizontal'])!!}
						{!!Form::hidden('organizacion',$organizacion)!!}
						{!!Form::submit('Ver Objetivos Desbloqueados', ['class'=>'btn btn-success'])!!}
					{!!Form::close()!!}
				<?php break; ?>
				@endif
			@endforeach
		@endif

		@foreach (Session::get('roles') as $role)
			@if ($role != 6)
			{!!Form::open(['url'=>'objetivos.create','method'=>'GET','class'=>'form-horizontal'])!!}	
				{!!Form::hidden('nombre_organizacion',$nombre_organizacion )!!}
				{!!Form::submit('Agregar objetivo', ['class'=>'btn btn-primary'])!!}
				@if (isset($_GET['organizacion']))
					{!!Form::hidden('organizacion',$_GET['organizacion'] )!!}
				@else
					{!!Form::hidden('organizacion',$organizacion)!!}
				@endif
					
				{!!Form::close()!!}
			<?php break; ?>
			@endif
		@endforeach
	<hr>
		@if ($probador !== 0) {{-- Si es que existe algún objetivo creado, probador no será cero --}}
			<table class="table table-bordered table-striped table-hover table-heading table-datatable" id="datatable-2" style="font-size:11px">
				<thead>
				<th>Nombre<label><input type='text' placeholder='Filtrar' /></label></th>
				<th width="30%">Descripci&oacute;n<label><input type='text' placeholder='Filtrar' /></label></th>
				<th>Fecha Creaci&oacute;n<label><input type='text' placeholder='Filtrar' /></label></th>
				<th>Fecha Actualizaci&oacute;n<label><input type='text' placeholder='Filtrar' /></label></th>
				<th>Fecha Expiraci&oacute;n<label><input type='text' placeholder='Filtrar' /></label></th>
				<th>Categor&iacute;a<label><input type='text' placeholder='Filtrar' /></label></th>
				<th>Perspectiva<label><input type='text' placeholder='Filtrar' /></label></th>

			@foreach (Session::get('roles') as $role)
				@if ($role != 6)
				<th style="vertical-align:top;">Acci&oacute;n</th>
				<th style="vertical-align:top;">Acci&oacute;n</th>
				<?php break; ?>
				@endif
			@endforeach
				</thead>

				@foreach ($objetivos as $objetivo)
					<tr>
					<td>{{$objetivo['nombre']}}</td>
					<td>{{$objetivo['descripcion']}}</td>
					<td>{{$objetivo['fecha_creacion']}}</td>
					<td>{{$objetivo['fecha_act']}}</td>
					<td>{{$objetivo['fecha_exp']}}</td>
					<td>{{$objetivo['categoria']}}</td>
					<td>{{$objetivo['perspective']}}</td>
			@foreach (Session::get('roles') as $role)
				@if ($role != 6)
					<td>
						<div>
						@if ($objetivo['estado'] == 0)
				            {!! link_to_route('objetivos.edit', $title = 'Editar', $parameters = $objetivo['id'], $attributes = ['class'=>'btn btn-success']) !!}
				        @else
				        	{!! link_to_route('objetivos.desbloquear', $title = 'Desbloquear', $parameters = $objetivo['id'], $attributes = ['class'=>'btn btn-success']) !!}
				        @endif
				        </div><!-- /btn-group -->
					</td>
					<td>
						<div>
						@if ($objetivo['estado'] == 0)
				            <button class="btn btn-danger" onclick="bloquear({{ $objetivo['id'] }},'{{ $objetivo['nombre'] }}','objetivos','el objetivo')">Bloquear</button>
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
		@else
			@if (strpos($_SERVER['REQUEST_URI'],"verbloqueados"))
				<b>La organizaci&oacute;n {{ $nombre_organizacion }} no posee objetivos bloqueados.</b>
			@else
				<b>Aun no se han creado objetivos para la organizaci&oacute;n {{ $nombre_organizacion }}, o bien sus objetivos est&aacute;n bloqueados. </b>
			@endif
		@endif
		<hr>

		<center>

		{!! link_to_route('objetivos.index', $title = 'Volver', $parameters = NULL, $attributes = ['class'=>'btn btn-danger']) !!}
		</center>

	@endif
			</div>
		</div>
	</div>
</div>
@stop

