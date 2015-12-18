@extends('master')

@section('title', 'Agregar Riesgo')

@stop

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('riesgos','Identificaci&oacute;n de Riesgos')!!}</li>
			<li>{!!Html::link('riesgos.create','Agregar Riesgo')!!}</li>
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
					@include('riesgos.form')
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
@stop

@section('scripts2')
<script>
//bloqueamos opciones de llenado si es que se esta ingresando un riesgo tipo
	$("#riesgo_tipo").change(function() {

			if ($("#riesgo_tipo").val() != "")
			{
				$("#nombre").prop("disabled",true);
				$("#nombre").removeAttr("required");

				$("#descripcion").prop("disabled",true);
				$("#descripcion").removeAttr("required");

				$("#categoria").prop("disabled",true);
				$("#categoria").removeAttr("required");

				$("#input_date").prop("disabled",true);
				$("#input_date").removeAttr("required");

				$("#input_date2").prop("disabled",true);
				$("#input_date2").removeAttr("required");

				$("#cause_id").prop("disabled",true);
				$("#cause_id").removeAttr("required");

				$("#effect_id").prop("disabled",true);
				$("#effect_id").removeAttr("required");
			}

			else
			{
				$("#nombre").prop("disabled",false);
				$("#nombre").prop("required",true);

				$("#descripcion").prop("disabled",false);
				$("#descripcion").prop("required",true);

				$("#categoria").prop("disabled",false);
				$("#categoria").prop("required",true);

				$("#input_date").prop("disabled",false);
				$("#input_date").prop("required",true);

				$("#input_date2").prop("disabled",false);
				$("#input_date2").prop("required",true);

				$("#cause_id").prop("disabled",false);
				$("#cause_id").prop("required",true);

				$("#effect_id").prop("disabled",false);
				$("#effect_id").prop("required",true);
			}
			
	    });

	$("#agregar_causa").click(function() {
		$("#causa").empty();
		$("#causa").append('<div class="form-group">{!!Form::label("Causa",null,["class"=>"col-sm-4 control-label"])!!}<div class="col-sm-3">{!!Form::textarea("causa_nueva",null,["class"=>"form-control","rows"=>"3","cols"=>"4","required"=>"true"])!!}</div></div>');
		});

	$("#agregar_efecto").click(function() {
		$("#efecto").empty();
		$("#efecto").append('<div class="form-group">{!!Form::label("Efecto",null,["class"=>"col-sm-4 control-label"])!!}<div class="col-sm-3">{!!Form::textarea("efecto_nuevo",null,["class"=>"form-control","rows"=>"3","cols"=>"4","required"=>"true"])!!}</div></div>');
		});

</script>
@stop

