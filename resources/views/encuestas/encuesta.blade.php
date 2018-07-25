<!-- extends('master2') Se utilizará esta en el futuro para que no aparezca el menú de admin -->

@extends('master2')

@section('title', 'Identificaci&oacute;n de eventos de riesgo')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="identificacion.encuesta.{{ $encuesta['id'] }}">Responder Encuesta</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-sm-10">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-check"></i>
					<span>Encuesta: {{ $encuesta['name'] }}</span>
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
				<div class="alert alert-danger alert-dismissible" role="alert">
					{{ Session::get('message') }}
				</div>
			@endif

			@if (empty($user_answers)) <!-- Si es que no hay respuestas se guardará nueva eval, de lo contrario se editará -->
				{!!Form::open(['route'=>'identificacion.guardarEvaluacion','method'=>'POST','class'=>'form-horizontal','onsubmit'=>'return checkSubmit();'])!!}
			@else
				{!!Form::open(['route'=>'identificacion.updateEvaluacion','method'=>'PUT','class'=>'form-horizontal','onsubmit'=>'return checkSubmit();'])!!}
			@endif
			<?php $i = 1; //contador de preguntas ?>
			@foreach ($preguntas as $pregunta)
				{!!Form::hidden('pregunta_id[]',$pregunta->id)!!}
				<p><b>{{$i}}. {{ $pregunta->question }} </b></p>

				@if ($pregunta->answers_type == 1) <!-- verificamos si es radio -->
					<p>
					@foreach ($respuestas as $respuesta) <!-- recorremos todas las respuestas para ver si corresponden a la pregunta -->
						@if ($respuesta['question_id'] == $pregunta->id) <!-- Si la respuesta pertenece a la pregunta -->
								<!-- vemos si es que hay una respuesta pre ingresada -->
								<?php $cont = 0; //verificador para ver si hay respuesta ?>
								@foreach ($user_answers as $answer)
									@if ($answer['question_id'] == $pregunta->id && $answer['answer'] == $respuesta['answer'])
										<div class="radio-inline">
											<label>
												<input type="radio" required="true" name="respuesta{{ $pregunta->id }}" value="{{$respuesta['id']}}" checked> {{ $respuesta['answer'] }}
												<i class="fa fa-circle-o"></i>
											</label>
										</div>
										<?php $cont += 1; ?>
									@endif
								@endforeach

								@if ($cont == 0)
									<div class="radio-inline">
										<label>
											<input type="radio" required="true" name="respuesta{{ $pregunta->id }}" value="{{$respuesta['id']}}"> {{ $respuesta['answer'] }}
											<i class="fa fa-circle-o"></i>
										</label>
									</div>
								@endif
						@endif
					@endforeach
					</p>

				@elseif ($pregunta->answers_type == 2) <!-- verificamos si es checkbox -->
					<p>
					@foreach ($respuestas as $respuesta) <!-- recorremos todas las respuestas para ver si corresponden a la pregunta -->
						@if ($respuesta['question_id'] == $pregunta->id) <!-- Si la respuesta pertenece a la pregunta -->
							<!-- vemos si es que hay una respuesta pre ingresada -->
								<?php $cont = 0; //verificador para ver si hay respuesta ?>
								@foreach ($user_answers as $answer)
									@if ($answer['question_id'] == $pregunta->id && $answer['answer'] == $respuesta['answer'])
										<div class="checkbox">
											<label>
												<input type="checkbox" name="respuesta{{ $pregunta->id }}[]" value="{{$respuesta['id']}}" checked>
												<i class="fa fa-square-o"></i> {{ $respuesta['answer'] }}
											</label>
										</div>
										<?php $cont += 1; ?>
									@endif
								@endforeach

								@if ($cont == 0)
									<div class="checkbox">
										<label>
											<input type="checkbox" name="respuesta{{ $pregunta->id }}[]" value="{{$respuesta['id']}}">
											<i class="fa fa-square-o"></i> {{ $respuesta['answer'] }}
										</label>
									</div>
								@endif
						@endif
					@endforeach
					</p>
				@elseif ($pregunta->answers_type == 0) <!-- verificamos si es text -->
					<!-- vemos si es que hay una respuesta pre ingresada -->
					<?php $cont = 0; //verificador para ver si hay respuesta ?>
					@foreach ($user_answers as $answer)
						@if ($answer['question_id'] == $pregunta->id)
							<p>
							<textarea class="form-control" name="respuesta{{ $pregunta->id }}" required="true" rows="4" cols="50">{{ $answer['answer'] }}</textarea>
							</p>
							<?php $cont += 1; ?>
						@endif
					@endforeach

					@if ($cont == 0)
						<p>
						<textarea class="form-control" name="respuesta{{ $pregunta->id }}" required="true" rows="4" cols="50"></textarea>
						</p>
					@endif
				
				@endif

				<?php $i += 1; ?>
				<hr> 
			@endforeach
					{!!Form::hidden('stakeholder_id',$user)!!}
					{!!Form::hidden('encuesta_id',$encuesta['id'])!!}
			<div class="row form-group">
				<center>
					{!!Form::submit('Enviar Respuestas', ['class'=>'btn btn-primary','id'=>'btnsubmit'])!!}
				</center>
			</div>

			<div class="row form-group">
				<center>
					<p><a href="#" onclick="history.back()" class="btn btn-danger">Volver</a></p>
				<center>
			</div>

			{!!Form::close()!!}

			
			</div>
		</div>
	</div>
</div>
@stop
@section('scripts')
<script>;
}
$(document).ready(function() {

	//función para validar checkboxes (no funciona bien aun)
/*
	$('#btnsubmit').click(function() {
		if ($('#checkbox-inline :checkbox:checked').length > 0)
		{
			alert ("bien");
		}

		else
		{

			alert ("mal");
		}
	})
*/
});
</script>

@stop
