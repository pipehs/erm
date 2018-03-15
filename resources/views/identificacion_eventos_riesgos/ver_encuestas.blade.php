@extends('master')

@section('title', 'Encuestas de evaluación de Riesgos')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Identificación de eventos de riesgo</a></li>
			<li><a href="ver_encuestas">Encuestas</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-check"></i>
					<span>Seleccione Encuesta</span>
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

		@if ($errors->any())
				<div class="alert alert-danger alert-dismissible" role="alert">
					<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
					</ul>
				</div>
		@endif

		@if(Session::has('error'))
			<div class="alert alert-danger alert-dismissible" role="alert">
			{{ Session::get('error') }}
			</div>
		@endif

		@if(Session::has('message'))
			<div class="alert alert-success alert-dismissible" role="alert">
			{{ Session::get('message') }}
			</div>
		@endif

<p>En esta secci&oacute;n podr&aacute; revisar el formato de todas las encuestas de identificaci&oacute;n de eventos de riesgos agregadas.</p>

@if (isset($polls))
	
	<table class="table table-bordered table-striped table-hover table-heading table-datatable" style="margin: 0 auto; width:50% ">
			<thead>
			<th>Nombre</th>
			<th>Fecha de creaci&oacute;n</th>
			<th>Ver</th>
	@foreach (Session::get('roles') as $role)
		@if ($role != 6)
			<th>Eliminar</th>
		<?php break; ?>
		@endif
	@endforeach
			</thead>
	@foreach ($polls as $poll)
		<tr>
			<td>{{ $poll['name'] }}</td>
			<td>{{ $poll['created_at'] }}</td>
			<td>{!! link_to_route('ver_encuesta', $title = 'Ver', $parameters = $poll['id'], $attributes = ['class'=>'btn btn-success']) !!}</td>
			@foreach (Session::get('roles') as $role)
			@if ($role != 6)
				<td><button class="btn btn-danger" onclick="eliminar2({{ $poll['id'] }},'{{ $poll['name'] }}','encuestas','La encuesta')">Eliminar</button></td>
			<?php break; ?>
			@endif
			@endforeach
		</tr>
	@endforeach
	</table>
<br><br><br><br><br>

@elseif (isset($encuesta))
	<!-- Mostramos encuesta -->

			<b>Nombre:  {{ $encuesta['name']}}</b><br><br>

			<?php $i = 1; //contador de preguntas ?>
			@foreach ($preguntas as $pregunta)

				<p>{{$i}}. {{ $pregunta->question }} </p>

				@if ($pregunta->answers_type == 1) <!-- verificamos si es radio -->
					<p>
					@foreach ($respuestas as $respuesta) <!-- recorremos todas las respuestas para ver si corresponden a la pregunta -->
						@if ($respuesta['question_id'] == $pregunta->id) <!-- Si la respuesta pertenece a la pregunta -->
								<div class="radio-inline">
									<label>
										<input type="radio" name="{{ $pregunta->id }}"> {{ $respuesta['respuesta'] }}
										<i class="fa fa-circle-o"></i>
									</label>
								</div>
						@endif
					@endforeach
					</p>

				@elseif ($pregunta->answers_type == 2) <!-- verificamos si es checkbox -->
					<p>
					@foreach ($respuestas as $respuesta) <!-- recorremos todas las respuestas para ver si corresponden a la pregunta -->
						@if ($respuesta['question_id'] == $pregunta->id) <!-- Si la respuesta pertenece a la pregunta -->
							<div class="checkbox-inline">
								<label>
									<input type="checkbox" name="{{ $pregunta->id }}">
									<i class="fa fa-square-o"></i> {{ $respuesta['respuesta'] }}
								</label>
							</div>
						@endif
					@endforeach
					</p>
				@elseif ($pregunta->answers_type == 0) <!-- verificamos si es text -->
				<p>
				<textarea class="form-control" name="{{ $pregunta->id }}" rows="4" cols="50"></textarea>
				</p>
				@endif

				<?php $i += 1; ?> 
			@endforeach
			<br>
			<center>
				<p><a href="#" onclick="history.back()" class="btn btn-danger">Volver</a></p>
			<center>
@endif

		

			</div>
		</div>
	</div>
</div>

@stop

@section('scripts')

@stop