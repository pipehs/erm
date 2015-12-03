@extends('master')

@section('title', 'Evaluaci&oacute;n de riesgos')

@stop

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Evaluaci&oacute;n de Riesgos</a></li>
			<li><a href="evaluacion.encuestas">Ver Encuestas</a></li>
		</ol>
	</div>
</div>
<center>
<div class="row">
	<div class="col-xs-12 col-sm-6">
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

			<table class="table table-bordered table-striped table-hover table-heading table-datatable" width="50%">
			<tr>
			<th width="50%">Descripci&oacute;n</th>
			<td>{{ $encuesta['descripcion'] }}</td>
			</tr>
			<tr>
			<th>Fecha Creaci&oacute;n</th>
			<td>{{ $encuesta['fecha_creacion'] }}</td>
			</tr>
			<tr>
			<th>Fecha Expiraci&oacute;n</th>
			@if ($encuesta['fecha_exp'] == "")
				<td>Ninguna</td>
			@else
				<td>{{ $encuesta['fecha_exp'] }}</td>
			@endif
			</tr>
			<tr>
			<th>Niveles Criticidad y Probabilidad</th>
			<td>{{ $encuesta['max_niveles'] }} nivel(es)</td>
			</tr>
			<tr>
			<th>Riesgos Relacionados</th>
			<td><ul>
					@foreach ($riesgos as $riesgo)
						<li>{{ $riesgo['nombre'] }}</li>
					@endforeach
					</ul>					
				</td>
			<tr>
			</table>

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
