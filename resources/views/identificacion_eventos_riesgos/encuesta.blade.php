<!-- extends('master2') Se utilizará esta en el futuro para que no aparezca el menú de admin -->

@extends('master')

@section('title', 'Evaluaci&oacute;n de riesgos')

@stop

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
					<span>Encuesta: {{ $encuesta['nombre'] }}</span>
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

			{!!Form::open(['route'=>'identificacion.guardarEvaluacion','method'=>'POST','class'=>'form-horizontal'])!!}

			<div class="form-group">
				<small>
			    {!!Form::label('Ingrese su Rut (sin dígito verificador)',null,['class'=>'col-sm-4 control-label'])!!}
				<div class="col-sm-3">
					{!!Form::text('id',null,
					['class'=>'form-control','required'=>'true','input maxlength'=>'8'])!!}
				</div>
				</small>
			</div>

			<?php $i = 1; //contador de preguntas ?>
			@foreach ($preguntas as $pregunta)
				{!!Form::hidden('pregunta_id[]',$pregunta->id)!!}
				<p><b>{{$i}}. {{ $pregunta->pregunta }} </b></p>

				@if ($pregunta->tipo_respuestas == 1) <!-- verificamos si es radio -->
					<p>
					@foreach ($respuestas as $respuesta) <!-- recorremos todas las respuestas para ver si corresponden a la pregunta -->
						@if ($respuesta['question_id'] == $pregunta->id) <!-- Si la respuesta pertenece a la pregunta -->
								<div class="radio-inline">
									<label>
										<input type="radio" required="true" name="respuesta{{ $pregunta->id }}" value="{{$respuesta['id']}}"> {{ $respuesta['respuesta'] }}
										<i class="fa fa-circle-o"></i>
									</label>
								</div>
						@endif
					@endforeach
					</p>

				@elseif ($pregunta->tipo_respuestas == 2) <!-- verificamos si es checkbox -->
					<p>
					@foreach ($respuestas as $respuesta) <!-- recorremos todas las respuestas para ver si corresponden a la pregunta -->
						@if ($respuesta['question_id'] == $pregunta->id) <!-- Si la respuesta pertenece a la pregunta -->
							<div class="checkbox">
								<label>
									<input type="checkbox" name="respuesta{{ $pregunta->id }}[]" value="{{$respuesta['id']}}">
									<i class="fa fa-square-o"></i> {{ $respuesta['respuesta'] }}
								</label>
							</div>
						@endif
					@endforeach
					</p>
				@elseif ($pregunta->tipo_respuestas == 0) <!-- verificamos si es text -->
				<p>
				<textarea class="form-control" name="respuesta{{ $pregunta->id }}" required="true" rows="4" cols="50"></textarea>
				</p>
				@endif

				<?php $i += 1; ?>
				<hr> 
			@endforeach

					{!!Form::hidden('encuesta_id',$encuesta['id'])!!}
			<div class="row form-group">
				<center>
					{!!Form::submit('Enviar Respuestas', ['class'=>'btn btn-primary','id'=>'responder'])!!}
				</center>
			</div>

			{!!Form::close()!!}

			
			</div>
		</div>
	</div>
</div>
@stop
@section('scripts')
<script>
// Run Datables plugin and create 3 variants of settings
function AllTables(){
	TestTable1();
	TestTable2();
	TestTable3();
	LoadSelect2Script(MakeSelect);
}
function MakeSelect2(){
	$('select').select2();
	$('.dataTables_filter').each(function(){
		$(this).find('label input[type=text]').attr('placeholder', 'Search');
	});
}
$(document).ready(function() {
	// Add slider for change test input length
	FormLayoutExampleInputLength($( ".slider-style" ));
	// Initialize datepicker
	$('#input_date').datepicker({setDate: new Date()});
	// Initialize datepicker
	$('#input_date2').datepicker({setDate: new Date()});
	// Load Timepicker plugin
	LoadTimePickerScript(DemoTimePicker);
	// Add tooltip to form-controls
	$('.form-control').tooltip();
	LoadSelect2Script(DemoSelect2);
	// Load example of form validation
	LoadBootstrapValidatorScript(DemoFormValidator);
	// Add Drag-n-Drop feature
	WinMove();

	//función para validar checkboxes (no funciona bien aun)
	$('#responder').click(function() {
		if ($('#checkbox-inline :checkbox:checked').length > 0)
		{
			alert ("bien");
		}

		else
		{

			alert ("mal");
		}
	})

});
</script>

@stop
