@extends('en.master')

@section('title', 'Identification of risk events')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Identification of risk events</a></li>
			<li><a href="ver_encuestas">Polls</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-check"></i>
					<span>Select poll</span>
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

<p>On this section you will be able to check the format on all of the polls of identification of risk events.</p>

@if (isset($polls))

	{!!Form::open(['url'=>'ver_encuestas','method'=>'GET','class'=>'form-horizontal'])!!}
	<div class="row form-group">
		{!!Form::label('Select poll',null,['class'=>'col-sm-4 control-label'])!!}
		<div class="col-sm-4">
			{!!Form::select('encuesta',$polls,
								 	   null,
								 	   ['required' => 'true',
								 	   	'placeholder' => '- Select -',
								 	   	'id' => 'el2'])
							!!}
		</div>
	</div>	

	<center>
		<div class="row form-group">
		  {!!Form::submit('Select', ['class'=>'btn btn-success','name'=>'aplicar'])!!}
		</div>
	</center>
@elseif (isset($encuesta))
	<!-- Mostramos encuesta -->

			<b>Name:  {{ $encuesta['name']}}</b><br><br>

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
			<a href="ver_encuestas" class="btn btn-danger">Return</a>
			</center>
@endif

		

			</div>
		</div>
	</div>
</div>

@stop

@section('scripts')

@stop