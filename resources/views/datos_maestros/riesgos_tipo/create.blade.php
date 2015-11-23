@extends('master')

@section('title', 'Agregar Riesgo')

@stop

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('#','Datos Maestros')!!}</li>
			<li>{!!Html::link('riesgos_tipo','Riesgos')!!}</li>
			<li>{!!Html::link('riesgos_tipo.create','Agregar Riesgo')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-folder"></i>
					<span>Agregar Riesgo</span>
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
				<div class="no-move"></div>
			</div>
			<div class="box-content">
			Ingrese los datos del riesgo.
				{!!Form::open(['route'=>'riesgos.store','method'=>'POST','class'=>'form-horizontal'])!!}

					<div class="form-group">
						{!!Form::label('Nombre',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::text('nombre',null,['class'=>'form-control','required'=>'true'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Descripci&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::textarea('descripcion',null,['class'=>'form-control','rows'=>'3','cols'=>'4','required'=>'true'])!!}
						</div>
					</div>
					<div class="form-group">
						{!!Form::label('Categor&iacute;a',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::select('categoria_riesgo', 
							array('' => '- Seleccione -',
								  '1' => 'Estrat&eacute;gico',
					 	  		  '2' => 'Financiero',
					 	  		  '3' => 'Operacional',
					 	  		  '4' => 'De Cumplimiento'),
							 	   null, 
							 	   ['id' => 'el2', 'class' => 'form-control','required'=>'true'])!!}
						</div>
					</div>
					<div class="form-group">
						{!!Form::label('Fecha Creaci&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::text('fecha_creacion',null,['class'=>'form-control','id'=>'input_date','required'=>'true'])!!}
						</div>
					</div>
					<div class="form-group">
						{!!Form::label('Fecha Expiraci&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::text('fecha_expiracion',null,['class'=>'form-control','id'=>'input_date2'])!!}
						</div>
					</div>
					<div id="causa">
						<div class="form-group">
							{!!Form::label('Causa ',null,['class'=>'col-sm-4 control-label'])!!}
							<div class="col-sm-3">
								{!!Form::select('causa', 
								array('' => '- Seleccione -',
									  '1' => 'Causa 1',
						 	  		  '2' => 'Causa 2',
						 	  		  '3' => 'Causa 3',
						 	  		  '4' => 'Causa 4'),
								 	   null, 
								 	   ['id' => 'el2', 'class' => 'form-control','required'=>'true'])!!}
							</div>
							<a href="#" id="agregar_causa">Agregar Nueva Causa</a> <br>
						</div>
					</div>
					<div id="efecto">
						<div class="form-group">
							{!!Form::label('Efecto ',null,['class'=>'col-sm-4 control-label'])!!}
							<div class="col-sm-3">
								{!!Form::select('efecto', 
								array('' => '- Seleccione -',
									  '1' => 'Efecto 1',
						 	  		  '2' => 'Efecto 2',
						 	  		  '3' => 'Efecto 3'),
								 	   null, 
								 	   ['id' => 'el2', 'class' => 'form-control','required'=>'true'])!!}
							</div>
							<a href="#" id="agregar_efecto">Agregar Nuevo Efecto</a> <br>
						</div>
					</div>
					
					<div class="form-group">
						<center>
						{!!Form::submit('Agregar', ['class'=>'btn btn-primary'])!!}
						</center>
					</div>
				{!!Form::close()!!}

				<center>
				{!!Form::open(['url'=>'riesgos','method'=>'GET'])!!}
					{!!Form::submit('Volver', ['class'=>'btn btn-danger'])!!}
				{!!Form::close()!!}
				<center>
			</div>
		</div>
	</div>
</div>
<script>
$(document).ready(function() {

	$("#agregar_causa").click(function() {
		$("#causa").empty();
		$("#causa").append('<div class="form-group">{!!Form::label("Causa",null,["class"=>"col-sm-4 control-label"])!!}<div class="col-sm-3">{!!Form::textarea("causa_nueva",null,["class"=>"form-control","rows"=>"3","cols"=>"4","required"=>"true"])!!}</div></div>');
		});

	$("#agregar_efecto").click(function() {
		$("#efecto").empty();
		$("#efecto").append('<div class="form-group">{!!Form::label("Efecto",null,["class"=>"col-sm-4 control-label"])!!}<div class="col-sm-3">{!!Form::textarea("efecto_nuevo",null,["class"=>"form-control","rows"=>"3","cols"=>"4","required"=>"true"])!!}</div></div>');
		});

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

