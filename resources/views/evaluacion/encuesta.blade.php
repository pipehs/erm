@extends('master')

@section('title', 'Evaluaci&oacute;n de riesgos')

@stop

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			@if ($tipo == 1)
				<li><a href="evaluacion.encuesta.{{ $encuesta['id'] }}">Responder Encuesta</a></li>
			@else
				<li><a href="evaluacion.encuesta.0">Evaluar riesgos</a></li>
			@endif
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-sm-8">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-check"></i>
					<span>Encuesta: {{ $encuesta }}</span>
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

			<h4><center>Evaluaci&oacute;n manual</center></h4>

			{!!Form::open(['route'=>'evaluacion.guardarEvaluacion','method'=>'POST','class'=>'form-horizontal'])!!}

			<div class="form-group">
				<small>
			    {!!Form::label('Ingrese su Rut (sin dígito verificador)',null,['class'=>'col-sm-4 control-label'])!!}
				<div class="col-sm-3">
					{!!Form::text('rut',null,
					['class'=>'form-control','required'=>'true','input maxlength'=>'8'])!!}
				</div>
				</small>
			</div>

			<p>Por cada riesgo identificado, señale un nivel de probabilidad e impacto del mismo. </p>

			@foreach($riesgos as $riesgo)
				@if ($tipo == 1)
					{!!Form::hidden('evaluation_risk_id[]',$riesgo['evaluation_risk_id'])!!}
				@else
					{!!Form::hidden('evaluation_risk_id[]',$riesgo['risk_id'])!!}
				@endif
				<b>- {{ $riesgo['risk_name'] }} - {{ $riesgo['subobj'] }} - {{ $riesgo['orgproc'] }}:</b><br><br>
				Probabilidad:<br>
				@for($i=1; $i<=5; $i++)
				<div class="radio-inline">
					<label>
						@if ($tipo == 1)
							<input type="radio" name="proba_{{$riesgo['evaluation_risk_id']}}" required="true" value="{{ $i }}"> {{ $i }} ({{ $tipos_proba[$i-1] }})
						@else
							<input type="radio" name="proba_{{$riesgo['risk_id']}}_{{$riesgo['type']}}" required="true" value="{{ $i }}"> {{ $i }} ({{ $tipos_proba[$i-1] }})
						@endif
						<i class="fa fa-circle-o"></i>
					</label>
				</div>
				@endfor
				<br><br>
				Impacto:<br>
				@for($i=1; $i<=5; $i++)
				<div class="radio-inline">
					<label>
						@if ($tipo == 1)
							<input type="radio" name="criticidad_{{$riesgo['evaluation_risk_id']}}" required="true" value="{{ $i }}"> {{ $i }} ({{ $tipos_impacto[$i-1] }})
						@else
							<input type="radio" name="criticidad_{{$riesgo['risk_id']}}_{{$riesgo['type']}}" required="true" value="{{ $i }}"> {{ $i }} ({{ $tipos_impacto[$i-1] }})
						@endif
						<i class="fa fa-circle-o"></i>
					</label>
				</div>
				@endfor
				<hr>
			@endforeach
			@if ($tipo == 1)
				{!!Form::hidden('evaluation_id',$encuesta['id'])!!}
			@endif
				{!!Form::hidden('tipo',$tipo)!!}
			<div class="row form-group">
				<center>
					@if ($tipo == 1)
						{!!Form::submit('Enviar Respuestas', ['class'=>'btn btn-primary'])!!}
					@else
						{!!Form::submit('Enviar Evaluación', ['class'=>'btn btn-primary'])!!}
					@endif
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

});
</script>

@stop
