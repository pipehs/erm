@extends('master')

@section('title', 'Evaluaci&oacute;n de riesgos')

@stop

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-9">
		<ol class="breadcrumb">
			<li><a href="#">Evaluaci&oacute;n de Riesgos</a></li>
			<li><a href="evaluacion.enviar.{{$encuesta_id}}">Enviar Encuesta</a></li>
		</ol>
	</div>
</div>
<center>
<div class="row">
	<div class="col-xs-12 col-sm-12">
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

			{!!Form::open(['route'=>'evaluacion.enviarCorreo','method'=>'POST','class'=>'form-horizontal'])!!}

				{!!Form::label('Seleccione stakeholders 
						(para seleccionar m&aacute;s de una presione ctrl + click)',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="row form-group">
						<div class="col-sm-6">
							{!!Form::select('stakeholder_id[]',$stakeholders,
							 	   null, 
							 	   ['id' => 'el2', 'class' => 'form-control','multiple'=>'true',
							 	   'required'=>'true','placeholder'=>'- Seleccione -'])!!}
						</div>
					</div>

					<div class="row form-group">
						{!!Form::label('Si desea puede cambiar el mensaje predeterminado
						(no cambie el link de la encuesta)',
						null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-6">
							{!!Form::textarea('mensaje',
							$mensaje,['class'=>'form-control','rows'=>'10','cols'=>'6',
							'required'=>'true'])!!}
						</div>
					</div>

				{!!Form::hidden('encuesta_id',$encuesta_id)!!}

				<div class="row form-group">
						<center>
						{!!Form::submit('Enviar', ['class'=>'btn btn-primary'])!!}
						</center>
				</div>

			{!!Form::close()!!}

			<div>
			<center>
				{!! link_to_route('evaluacion.encuestas', $title = 'Volver', $parameters = NULL,
				 $attributes = ['class'=>'btn btn-success'])!!}
			<center>
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
