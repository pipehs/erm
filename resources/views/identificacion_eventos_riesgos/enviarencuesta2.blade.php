@extends('master')

@section('title', 'Identificaci&oacute;n de eventos de riesgo')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Gestión de Encuestas</a></li>
			<li><a href="enviar_encuesta">Enviar Encuesta</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-6 col-m-3">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-check"></i>

					<span>Enviar Encuesta</span>
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

{!!Form::open(['route'=>'identificacion.enviarCorreo','method'=>'POST','class'=>'form-horizontal','onsubmit'=>'return checkSubmit();'])!!}

	@if ($tipo == 0)
		Seleccione los destinatarios manualmente a trav&eacute;s de la siguiente lista *.
	@elseif ($tipo == 1)
		Seleccione la organizaci&oacute;n a la que desea enviar la encuesta *.
	@elseif ($tipo == 2)
		Seleccione el rol de los usuarios a los que desea enviar la encuesta *.
	@endif

		<div class="row form-group">
			<div class="col-sm-12">
				{!!Form::select('stakeholder_id[]',$dest,null, ['id' => 'el2','multiple'=>'true','required'=>'true'])!!}
			</div>
		</div>

		{{-- ACT 20-06-18: Agregamos título a la encuesta --}}
		<div class="row form-group">
				<small>
				<label for="title" class="col-sm-4 control-label">Título del correo</label>
				<div class="col-sm-8">
					{!!Form::text('title',$encuesta['name'],['class'=>'form-control','required'=>'true'])!!}
				</div>
		</div>

		<div class="row form-group">
				<label for="mensaje" class="col-sm-4 control-label">Si desea puede cambiar el mensaje predeterminado (no cambie el link de la encuesta)</label>
				<div class="col-sm-8">
					{!!Form::textarea('mensaje',
					$mensaje,['class'=>'form-control','rows'=>'5','cols'=>'8',
					'required'=>'true'])!!}
				</div>
		</div>

		<div class="form-group">
			Haga click para enviar correo con link a encuesta
			<div class="col-sm-3">
				{!!Form::hidden('tipo',$tipo)!!}
				{!!Form::hidden('poll_id',$encuesta['id'])!!}
				{!!Form::submit('Enviar', ['class'=>'btn btn-primary','id'=>'btnsubmit'])!!}
			</div>
		</div>

	

			</div>
		</div>
	</div>

	<div class="col-sm-6 col-m-3">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-check"></i>
					<span>Encuesta seleccionada</span>
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

{!!Form::close()!!}
			</div>
		</div>
	</div>
</div> <!-- end div class="row"> -->

			<center>
				<p><a href="#" onclick="history.back()" class="btn btn-danger">Volver</a></p>
			<center>

@stop

@section('scripts')

@stop