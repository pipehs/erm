@extends('master')

@section('title', 'Identificaci&oacute;n de eventos de riesgo')

@stop

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Identificaci&oacute;n de Eventos de Riesgo</a></li>
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
{!!Form::open(['route'=>'identificacion.enviarCorreo','method'=>'POST','class'=>'form-horizontal'])!!}

	@if ($tipo == 0)
		Seleccione los destinatarios manualmente a trav&eacute;s de la siguiente lista.
	@elseif ($tipo == 1)
		Seleccione la organizaci&oacute;n a la que desea enviar la encuesta.
	@elseif ($tipo == 2)
		Seleccione el rol de los usuarios a los que desea enviar la encuesta.
	@endif

		<div class="form-group">

							{!!Form::select('stakeholder_id[]',$dest,
							 	   null, 
							 	   ['id' => 'el2','multiple'=>'true',])!!}
		</div>

		<div class="row form-group">
				<small>
				{!!Form::label('Si desea puede cambiar el mensaje predeterminado
				(no cambie el link de la encuesta)',
				null,['class'=>'col-sm-4 control-label'])!!}
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
				{!!Form::submit('Enviar', ['class'=>'btn btn-primary'])!!}
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

{!!Form::open(['url'=>'enviar_encuesta','method'=>'GET','class'=>'form-horizontal'])!!}
<center>
	<div class="row form-group">
	  {!!Form::submit('Volver', ['class'=>'btn btn-danger','name'=>'volver'])!!}
	</div>
</center>
{!!Form::close()!!}

@stop

@section('scripts')

<script>
// Run Select2 on element
function Select2Test(){
	$("#el2").select2();
	$("#el3").select2();
}

function MakeSelect2(){
	$('select').select2();
	$('.dataTables_filter').each(function(){
		$(this).find('label input[type=text]').attr('placeholder', 'Search');
	});
}
$(document).ready(function() {
	// Load script of Select2 and run this
	LoadSelect2Script(Select2Test);
	// Add Drag-n-Drop feature
	WinMove();
});
</script>

@stop